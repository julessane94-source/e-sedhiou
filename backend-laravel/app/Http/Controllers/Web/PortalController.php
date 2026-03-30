<?php
namespace App\Http\Controllers\Web;

use App\Models\CitizenRegistry;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\AgentAttendance;
use App\Models\CivicActivity;
use App\Models\CivicCourse;
use App\Models\Demande;
use App\Models\MailMergeTemplate;
use App\Models\Message;
use App\Models\User;
use App\Models\WpContactMessage;
use App\Services\ActivityLogger;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;
use Tymon\JWTAuth\Facades\JWTAuth;

class PortalController extends Controller
{
    private const SESSION_KEY = 'mairie_portal_auth';

    private ?array $portalSettingsCache = null;

    private ?User $currentUserCache = null;

    private bool $currentUserResolved = false;

    public function showAuth(Request $request): View|RedirectResponse
    {
        $settings = $this->portalSettings();
        $user = $this->currentUser($request);
        if ($user) {
            if ($redirect = $this->ensureCitizenPortalAvailable($request, $user, $settings)) {
                return $redirect;
            }

            return redirect()->to($this->portalUrlForRole($user->role));
        }

        return view('portal.auth', [
            ...$this->baseViewData(null),
            'pageTitle' => $settings['site_name'],
            'pageDescription' => $settings['site_description'] !== ''
                ? $settings['site_description']
                : 'Accedez a votre espace securise.',
            'redirectTo' => $this->sanitizeRedirect($request, (string) $request->query('redirect_to', '')),
            'settings' => $settings,
            'registrationOpen' => $this->isRegistrationOpen($settings),
            'maintenanceMode' => $this->isCitizenMaintenanceEnabled($settings),
        ]);
    }

    public function login(Request $request): RedirectResponse
    {
        $settings = $this->portalSettings();

        $payload = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'redirect_to' => ['nullable', 'string', 'max:2048'],
        ]);

        $user = User::where('email', $payload['email'])->first();

        if (! $user || ! Hash::check($payload['password'], $user->password)) {
            return back()
                ->withErrors(['email' => 'Identifiants incorrects.'])
                ->withInput($request->except('password'));
        }

        if (! $user->is_active) {
            return back()
                ->withErrors(['email' => 'Ce compte est desactive. Contactez l administration.'])
                ->withInput($request->except('password'));
        }

        if ($user->isCitoyen() && $this->isCitizenMaintenanceEnabled($settings)) {
            return back()
                ->withErrors(['email' => 'Le portail citoyen est temporairement indisponible pour maintenance.'])
                ->withInput($request->except('password'));
        }

        $request->session()->regenerate();
        $this->storeAuthenticatedUser($request, $user);
        $user->update(['last_login_at' => now()]);

        ActivityLogger::log('portal_login', $user->id, null, null, [], $request);

        return redirect()->to($this->resolvePostAuthRedirect($request, $user, $payload['redirect_to'] ?? null))
            ->with([
                'status' => 'Connexion reussie.',
                'status_type' => 'success',
            ]);
    }

    public function register(Request $request): RedirectResponse
    {
        $settings = $this->portalSettings();
        if (! $this->isRegistrationOpen($settings)) {
            $message = $this->isCitizenMaintenanceEnabled($settings)
                ? 'Le portail citoyen est temporairement indisponible. Les inscriptions sont suspendues.'
                : 'Les inscriptions citoyennes sont actuellement fermees.';

            return back()
                ->withErrors(['register' => $message])
                ->withInput($request->except('password', 'password_confirmation'));
        }

        $payload = $request->validate([
            'first_name' => ['nullable', 'string', 'max:120'],
            'last_name' => ['nullable', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:190', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:30'],
            'address' => ['required', 'string', 'max:500'],
            'birth_date' => ['required', 'date'],
            'birth_place' => ['required', 'string', 'max:190'],
            'register_number' => ['required', 'string', 'max:120'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $fullName = trim(($payload['first_name'] ?? '') . ' ' . ($payload['last_name'] ?? ''));
        if ($fullName === '') {
            $fullName = (string) Str::before($payload['email'], '@');
        }

        $user = User::create([
            'name' => $fullName,
            'first_name' => $payload['first_name'] ?? null,
            'last_name' => $payload['last_name'] ?? null,
            'email' => $payload['email'],
            'password' => Hash::make($payload['password']),
            'phone' => $payload['phone'],
            'address' => $payload['address'],
            'birth_date' => $payload['birth_date'],
            'birth_place' => $payload['birth_place'],
            'register_number' => $payload['register_number'],
            'citizen_number' => $this->generateCitizenNumber($payload['birth_date'], $payload['register_number']),
            'role' => User::ROLE_CITOYEN,
            'is_active' => true,
            'last_login_at' => now(),
        ]);

        $request->session()->regenerate();
        $this->storeAuthenticatedUser($request, $user);

        ActivityLogger::log('portal_register', $user->id, User::class, $user->id, ['role' => $user->role], $request);

        return redirect()->route('portal.citizen')->with([
            'status' => 'Compte cree avec succes.',
            'status_type' => 'success',
        ]);
    }

    public function logout(Request $request): RedirectResponse
    {
        $user = $this->currentUser($request);
        if ($user) {
            ActivityLogger::log('portal_logout', $user->id, null, null, [], $request);
        }

        $request->session()->forget(self::SESSION_KEY);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('portal.auth')->with([
            'status' => 'Deconnexion reussie.',
            'status_type' => 'success',
        ]);
    }

    public function recensement(Request $request): View|RedirectResponse
    {
        // Permettre aux utilisateurs connectés d'accéder au formulaire aussi
        // (ils peuvent se recenser ou aider quelqu'un)

        return view('portal.recensement', [
            ...$this->baseViewData(null),
            'pageTitle' => 'Me faire connaître de la mairie',
            'pageDescription' => 'Enregistrez-vous auprès de la mairie pour accéder aux services en ligne.',
            'settings' => $this->portalSettings(),
        ]);
    }

    public function storeRecensement(Request $request): RedirectResponse
    {
        $payload = $request->validate([
            // Informations civiles
            'first_name' => ['required', 'string', 'max:120'],
            'last_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:190'],
            'phone' => ['required', 'string', 'max:30'],
            'birth_date' => ['required', 'date'],
            'birth_place' => ['required', 'string', 'max:190'],
            'register_number' => ['required', 'string', 'max:120'],
            'address' => ['required', 'string', 'max:500'],
            
            // Informations professionnelles
            'profession_sector' => ['required', 'string', 'max:100'],
            'profession_title' => ['required', 'string', 'max:255'],
            'education_level' => ['required', 'string', 'max:100'],
            'years_experience' => ['required', 'integer', 'min:0', 'max:100'],
            
            // Compétences & Disponibilité
            'skills' => ['required', 'string', 'max:1000'],
            'current_status' => ['required', 'string', 'max:50'],
            'available_for_municipality' => ['nullable', 'boolean'],
            
            // Justificatifs
            'cv_file' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
            'portfolio_url' => ['nullable', 'url', 'max:500'],
        ]);

        // Gérer l'upload du CV
        $cvFilePath = null;
        $cvFileName = null;
        if ($request->hasFile('cv_file')) {
            $file = $request->file('cv_file');
            $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            $filePath = 'cvs/' . $fileName;
            $file->storeAs('public/cvs', $fileName);
            $cvFilePath = $filePath;
            $cvFileName = $file->getClientOriginalName();
        }

        // Enregistrer dans le registre des citoyens (visible uniquement à l'admin)
        $registry = CitizenRegistry::updateOrCreate(
            ['email' => $payload['email']],
            [
                'first_name' => $payload['first_name'],
                'last_name' => $payload['last_name'],
                'phone' => $payload['phone'],
                'birth_date' => $payload['birth_date'],
                'birth_place' => $payload['birth_place'],
                'register_number' => $payload['register_number'],
                'address' => $payload['address'],
                'profession_sector' => $payload['profession_sector'],
                'profession_title' => $payload['profession_title'],
                'education_level' => $payload['education_level'],
                'years_experience' => $payload['years_experience'],
                'skills' => $payload['skills'],
                'current_status' => $payload['current_status'],
                'available_for_municipality' => $payload['available_for_municipality'] ?? false,
                'cv_file_path' => $cvFilePath,
                'cv_file_name' => $cvFileName,
                'portfolio_url' => $payload['portfolio_url'] ?? null,
            ]
        );

        ActivityLogger::log(
            'citizen_registry_enrollment',
            null,
            CitizenRegistry::class,
            $registry->id,
            [
                'email' => $payload['email'],
                'profession_sector' => $payload['profession_sector'],
                'profession_title' => $payload['profession_title'],
                'available_for_municipality' => $payload['available_for_municipality'] ?? false,
            ],
            $request
        );

        return redirect()->route('portal.citizen')->with([
            'status' => 'Votre enregistrement a été reçu. Les responsables de la mairie l\'examineront pour vous contacter.',
            'status_type' => 'success',
        ]);
    }

    public function entry(Request $request): RedirectResponse
    {
        $user = $this->currentUser($request);

        return redirect()->to($this->portalUrlForRole($user?->role ?? User::ROLE_CITOYEN));
    }

    public function citizenDashboard(Request $request): View|RedirectResponse
    {
        $user = $this->requireCurrentUser($request);
        if ($redirect = $this->ensureCitizenPortalAvailable($request, $user)) {
            return $redirect;
        }

        $query = Demande::where('user_id', $user->id);
        $stats = $this->buildDemandeCounts((clone $query));
        $paymentStats = $this->buildPaymentStats((clone $query));

        $demandes = Demande::with(['agent:id,name,first_name,last_name'])
            ->where('user_id', $user->id)
            ->latest()
            ->limit(12)
            ->get();
        $citizenMessageDemandes = Demande::where('user_id', $user->id)
            ->latest()
            ->limit(50)
            ->get(['id', 'reference', 'request_type', 'agent_id']);
        $receivedMessages = Message::with([
            'sender:id,name,first_name,last_name,role',
            'demande:id,reference,request_type',
        ])
            ->where('receiver_id', $user->id)
            ->latest()
            ->limit(6)
            ->get();

        $sentMessages = Message::with([
            'receiver:id,name,first_name,last_name,role',
            'demande:id,reference,request_type',
        ])
            ->where('sender_id', $user->id)
            ->latest()
            ->limit(6)
            ->get();

        // Load civic courses and activities
        $civicCourses = CivicCourse::active()
            ->orderBy('sort_order')
            ->orderBy('created_at')
            ->get();

        $civicActivities = CivicActivity::active()
            ->orderBy('sort_order')
            ->orderBy('event_date')
            ->get();

        return view('portal.dashboard', [
            ...$this->baseViewData($user),
            'pageTitle' => 'Espace citoyen',
            'role' => 'citoyen',
            'stats' => $stats,
            'demandes' => $demandes,
            'citizenMessageDemandes' => $citizenMessageDemandes,
            'receivedMessages' => $receivedMessages,
            'sentMessages' => $sentMessages,
            'paymentStats' => $paymentStats,
            'civicCourses' => $civicCourses,
            'civicActivities' => $civicActivities,
            'requestTypes' => $this->requestTypes(),
            'statusLabels' => $this->statusLabels(),
        ]);
    }

    public function agentDashboard(Request $request): View
    {
        $user = $this->requireCurrentUser($request);
        $assignedQuery = Demande::where('agent_id', $user->id);
        $assignedStats = $this->buildDemandeCounts((clone $assignedQuery));
        $agentMetrics = $this->buildAgentMetrics((clone $assignedQuery));

        $assignedDemandes = Demande::with(['citoyen:id,name,first_name,last_name,email'])
            ->where('agent_id', $user->id)
            ->latest()
            ->limit(16)
            ->get();
        $agentMessageDemandes = Demande::with(['citoyen:id,name,first_name,last_name'])
            ->where('agent_id', $user->id)
            ->latest()
            ->limit(50)
            ->get(['id', 'reference', 'user_id']);
        $agentMessagesReceived = Message::with([
            'sender:id,name,first_name,last_name,role',
            'demande:id,reference,request_type',
        ])
            ->where('receiver_id', $user->id)
            ->latest()
            ->limit(8)
            ->get();
        $agentMessagesSent = Message::with([
            'receiver:id,name,first_name,last_name,role',
            'demande:id,reference,request_type',
        ])
            ->where('sender_id', $user->id)
            ->latest()
            ->limit(8)
            ->get();

        $mailMergeByType = $this->buildAgentMailMergeByType($user);
        $templateByType = MailMergeTemplate::where('user_id', $user->id)
            ->get()
            ->keyBy('request_type');

        return view('portal.dashboard', [
            ...$this->baseViewData($user),
            'pageTitle' => 'Espace agent',
            'role' => 'agent',
            'stats' => $assignedStats,
            'pendingDemandes' => Demande::with(['citoyen:id,name,first_name,last_name,email'])
                ->where('status', Demande::STATUS_PENDING)
                ->whereNull('agent_id')
                ->latest()
                ->limit(10)
                ->get(),
            'assignedDemandes' => $assignedDemandes,
            'agentMessageDemandes' => $agentMessageDemandes,
            'agentMessagesReceived' => $agentMessagesReceived,
            'agentMessagesSent' => $agentMessagesSent,
            'agentPerformance' => [
                'completed' => $agentMetrics['completed'],
                'rejected' => $agentMetrics['rejected'],
                'monthly_completed' => $agentMetrics['monthly_completed'],
                'avg_processing_hours' => $agentMetrics['avg_processing_hours'],
                'completion_rate' => $this->ratePercent($agentMetrics['completed'], $agentMetrics['completed'] + $agentMetrics['rejected']),
            ],
            'paymentValidationStats' => [
                'pending' => $agentMetrics['paid_pending'],
                'validated' => $agentMetrics['paid'],
            ],
            'agentMailMergeByType' => $mailMergeByType,
            'agentTemplatesByType' => $templateByType,
            'statusLabels' => $this->statusLabels(),
            'requestTypes' => $this->requestTypes(),
        ]);
    }

    public function adminDashboard(Request $request): View
    {
        $user = $this->requireCurrentUser($request);
        $userCounts = $this->buildUserCounts();
        $demandeCounts = $this->buildDemandeCounts(Demande::query());

        $adminReceivedMessages = Message::with([
                'sender:id,name,first_name,last_name,role',
                'demande:id,reference,request_type',
            ])
            ->where('receiver_id', $user->id)
            ->latest()
            ->limit(20)
            ->get();

        $adminSentMessages = Message::with([
                'receiver:id,name,first_name,last_name,role',
                'demande:id,reference,request_type',
            ])
            ->where('sender_id', $user->id)
            ->latest()
            ->limit(20)
            ->get();

        $adminMessageUsers = User::where('id', '!=', $user->id)
            ->where('is_active', true)
            ->orderBy('role')
            ->orderBy('name')
            ->get(['id', 'name', 'first_name', 'last_name', 'role']);

        return view('portal.dashboard', [
            ...$this->baseViewData($user),
            'pageTitle' => 'Espace administration',
            'role' => 'admin',
            'userCounts' => $userCounts,
            'demandeCounts' => $demandeCounts,
            'recentUsers' => User::latest()->limit(10)->get(),
            'recentDemandes' => Demande::with(['citoyen:id,name,first_name,last_name,email', 'agent:id,name,first_name,last_name'])
                ->latest()
                ->limit(10)
                ->get(),
            'recentActivity' => ActivityLog::with('user:id,name,role')
                ->latest('created_at')
                ->limit(12)
                ->get(),
            'statusLabels' => $this->statusLabels(),
            'requestTypes' => $this->requestTypes(),
            'adminReceivedMessages' => $adminReceivedMessages,
            'adminSentMessages'     => $adminSentMessages,
            'adminMessageUsers'     => $adminMessageUsers,
        ]);
    }

    public function superviseurDashboard(Request $request): View
    {
        $superviseur = $this->requireCurrentUser($request);

        $attendanceDate = now()->toDateString();
        $attendanceAgents = User::where('role', User::ROLE_AGENT)
            ->where('is_active', true)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get(['id', 'name', 'first_name', 'last_name']);

        $recentAttendances = AgentAttendance::with([
                'agent:id,name,first_name,last_name',
                'markedBy:id,name,first_name,last_name',
            ])
            ->latest('attendance_date')
            ->latest('id')
            ->limit(20)
            ->get();

        $todayRows = AgentAttendance::whereDate('attendance_date', $attendanceDate)->get();

        return view('portal.superviseur-pointage', [
            ...$this->baseViewData($superviseur),
            'pageTitle' => 'Pointage des agents',
            'attendanceDate' => $attendanceDate,
            'attendanceAgents' => $attendanceAgents,
            'recentAttendances' => $recentAttendances,
            'attendanceStatusLabels' => $this->attendanceStatusLabels(),
            'todaySummary' => [
                'present' => $todayRows->where('status', AgentAttendance::STATUS_PRESENT)->count(),
                'late' => $todayRows->where('status', AgentAttendance::STATUS_LATE)->count(),
                'absent' => $todayRows->where('status', AgentAttendance::STATUS_ABSENT)->count(),
            ],
        ]);
    }

    public function agentPerformance(Request $request): View
    {
        $agent = $this->requireCurrentUser($request);

        $assignedQuery = Demande::where('agent_id', $agent->id);
        $stats = $this->buildDemandeCounts((clone $assignedQuery));
        $metrics = $this->buildAgentMetrics((clone $assignedQuery));

        $monthStart = now()->startOfMonth()->toDateString();
        $monthEnd = now()->endOfMonth()->toDateString();

        $attendanceRows = AgentAttendance::where('user_id', $agent->id)
            ->whereBetween('attendance_date', [$monthStart, $monthEnd])
            ->orderByDesc('attendance_date')
            ->limit(31)
            ->get();

        $attendanceSummary = [
            'present' => $attendanceRows->where('status', AgentAttendance::STATUS_PRESENT)->count(),
            'late' => $attendanceRows->where('status', AgentAttendance::STATUS_LATE)->count(),
            'absent' => $attendanceRows->where('status', AgentAttendance::STATUS_ABSENT)->count(),
        ];

        return view('portal.agent-performance', [
            ...$this->baseViewData($agent),
            'pageTitle' => 'Mes performances',
            'stats' => $stats,
            'agentPerformance' => [
                'completed' => $metrics['completed'],
                'rejected' => $metrics['rejected'],
                'monthly_completed' => $metrics['monthly_completed'],
                'avg_processing_hours' => $metrics['avg_processing_hours'],
                'completion_rate' => $this->ratePercent($metrics['completed'], $metrics['completed'] + $metrics['rejected']),
            ],
            'attendanceRows' => $attendanceRows,
            'attendanceSummary' => $attendanceSummary,
            'attendanceStatusLabels' => $this->attendanceStatusLabels(),
            'periodLabel' => now()->translatedFormat('F Y'),
        ]);
    }

    public function storeCitizenDemande(Request $request): RedirectResponse
    {
        $user = $this->requireCurrentUser($request);
        if ($redirect = $this->ensureCitizenPortalAvailable($request, $user)) {
            return $redirect;
        }

        $payload = $request->validate([
            'request_type' => ['required', 'string', 'max:120'],
            'for_other_person' => ['nullable', 'boolean'],
            'representative_link' => ['nullable', 'string', 'max:120'],
            'first_name' => ['required', 'string', 'max:120'],
            'last_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:190'],
            'birth_date' => ['required', 'date'],
            'birth_place' => ['required', 'string', 'max:190'],
            'register_number' => ['required', 'string', 'max:120'],
            'address' => ['required', 'string', 'max:1000'],
            'parent_one_first_name' => ['required', 'string', 'max:120'],
            'parent_one_last_name' => ['required', 'string', 'max:120'],
            'parent_two_first_name' => ['required', 'string', 'max:120'],
            'parent_two_last_name' => ['required', 'string', 'max:120'],
            'details' => ['nullable', 'string', 'max:4000'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:10240'],
        ]);

        $isThirdPartyRequest = (bool) ($payload['for_other_person'] ?? false);
        $requestDetails = trim((string) ($payload['details'] ?? ''));

        if ($isThirdPartyRequest) {
            $relation = trim((string) ($payload['representative_link'] ?? ''));
            $thirdPartyNote = 'Demande deposee pour une autre personne';
            if ($relation !== '') {
                $thirdPartyNote .= ' (lien avec le demandeur: ' . $relation . ')';
            }

            $requestDetails = $requestDetails !== ''
                ? $thirdPartyNote . "\n\n" . $requestDetails
                : $thirdPartyNote;
        }

        // Gérer l'upload du fichier
        $attachmentPath = null;
        $attachmentName = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            $directory = 'public/demandes/' . now()->year . '/' . now()->month;
            $filePath = $file->storeAs($directory, $fileName);
            $attachmentPath = $filePath;
            $attachmentName = $file->getClientOriginalName();
        }

        $demandePayload = [
            'request_type' => $payload['request_type'],
            'first_name' => $payload['first_name'],
            'last_name' => $payload['last_name'],
            'email' => $payload['email'],
            'birth_date' => $payload['birth_date'],
            'birth_place' => $payload['birth_place'],
            'register_number' => $payload['register_number'],
            'address' => $payload['address'],
            'parent_one_first_name' => $payload['parent_one_first_name'],
            'parent_one_last_name' => $payload['parent_one_last_name'],
            'parent_two_first_name' => $payload['parent_two_first_name'],
            'parent_two_last_name' => $payload['parent_two_last_name'],
            'details' => $requestDetails,
            'attachment_url' => $attachmentPath,
            'attachment_name' => $attachmentName,
        ];

        $demande = Demande::create([
            ...$demandePayload,
            'user_id' => $user->id,
            'reference' => 'REQ-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6)),
            'status' => Demande::STATUS_PENDING,
            'payment_status' => 'unpaid',
            'source' => 'portal-laravel',
        ]);

        if (! $isThirdPartyRequest) {
            $user->update($this->buildCitizenProfilePayload($payload, $user));
        }

        ActivityLogger::log(
            'portal_create_demande',
            $user->id,
            Demande::class,
            $demande->id,
            ['reference' => $demande->reference],
            $request
        );

        return redirect()->route('portal.demandes.show', $demande)->with([
            'status' => 'Votre demande a ete enregistree.',
            'status_type' => 'success',
        ]);
    }

    public function saveCitizenProfile(Request $request): RedirectResponse
    {
        $user = $this->requireCurrentUser($request);
        if ($redirect = $this->ensureCitizenPortalAvailable($request, $user)) {
            return $redirect;
        }

        $payload = $request->validate([
            'first_name' => ['nullable', 'string', 'max:120'],
            'last_name' => ['nullable', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:190', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
            'birth_date' => ['nullable', 'date'],
            'birth_place' => ['nullable', 'string', 'max:190'],
            'register_number' => ['nullable', 'string', 'max:120'],
        ]);

        $user->update($this->buildCitizenProfilePayload($payload, $user));

        ActivityLogger::log('portal_update_citizen_profile', $user->id, User::class, $user->id, [], $request);

        return back()->with([
            'status' => 'Profil citoyen mis a jour.',
            'status_type' => 'success',
        ]);
    }

    public function payCitizenDemande(Request $request, Demande $demande): RedirectResponse
    {
        $user = $this->requireCurrentUser($request);
        if ($redirect = $this->ensureCitizenPortalAvailable($request, $user)) {
            return $redirect;
        }

        $this->authorizeDemandeAccess($user, $demande);

        if (in_array((string) $demande->payment_status, ['paid_pending', 'paid'], true)) {
            return back()->with([
                'status' => 'Un paiement a deja ete enregistre pour cette demande.',
                'status_type' => 'warning',
            ]);
        }

        $reference = 'PAY-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(4));
        $demande->update([
            'payment_status' => 'paid_pending',
            'payment_reference' => $reference,
            'paid_at' => now(),
            'payment_validated_by' => null,
            'payment_validated_at' => null,
        ]);

        ActivityLogger::log(
            'portal_pay_demande',
            $user->id,
            Demande::class,
            $demande->id,
            ['payment_reference' => $reference],
            $request
        );

        return back()->with([
            'status' => 'Paiement soumis. En attente de validation par un agent.',
            'status_type' => 'success',
        ]);
    }

    public function saveAgentProfile(Request $request): RedirectResponse
    {
        $agent = $this->requireCurrentUser($request);

        if (! $agent->isAgent() && ! $agent->isAdmin()) {
            abort(403, 'Action reservee aux agents.');
        }

        $payload = $request->validate([
            'first_name' => ['nullable', 'string', 'max:120'],
            'last_name' => ['nullable', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:190', 'unique:users,email,' . $agent->id],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        $fullName = trim(($payload['first_name'] ?? '') . ' ' . ($payload['last_name'] ?? ''));
        $update = [
            'first_name' => $payload['first_name'] ?? null,
            'last_name' => $payload['last_name'] ?? null,
            'name' => $fullName !== '' ? $fullName : $agent->name,
            'email' => $payload['email'],
            'phone' => $payload['phone'] ?? null,
            'address' => $payload['address'] ?? null,
        ];

        if ($request->filled('password')) {
            $request->validate([
                'current_password' => ['required', 'string'],
                'password' => ['required', 'confirmed', Password::min(8)],
            ]);
            if (! Hash::check($request->input('current_password'), $agent->password)) {
                return back()->withErrors(['current_password' => 'Mot de passe actuel incorrect.']);
            }
            $update['password'] = Hash::make((string) $request->input('password'));
        }

        $agent->update($update);
        ActivityLogger::log('portal_agent_update_profile', $agent->id, User::class, $agent->id, [], $request);

        return back()->with([
            'status' => 'Profil agent mis a jour.',
            'status_type' => 'success',
        ]);
    }

    public function validateAgentPayment(Request $request, Demande $demande): RedirectResponse
    {
        $agent = $this->requireCurrentUser($request);
        $this->authorizeAgentAction($agent, $demande, true);

        if ((string) $demande->payment_status !== 'paid_pending') {
            return back()->with([
                'status' => 'Aucun paiement en attente de validation pour ce dossier.',
                'status_type' => 'warning',
            ]);
        }

        $demande->update([
            'payment_status' => 'paid',
            'payment_validated_by' => $agent->id,
            'payment_validated_at' => now(),
        ]);

        ActivityLogger::log(
            'portal_agent_validate_payment',
            $agent->id,
            Demande::class,
            $demande->id,
            ['payment_reference' => $demande->payment_reference],
            $request
        );

        return back()->with([
            'status' => 'Paiement valide avec succes.',
            'status_type' => 'success',
        ]);
    }

    public function uploadAgentMailMergeTemplate(Request $request, string $requestType): RedirectResponse
    {
        $agent = $this->requireCurrentUser($request);
        if (! $agent->isAgent() && ! $agent->isAdmin()) {
            abort(403, 'Action reservee aux agents.');
        }

        if (! array_key_exists($requestType, $this->requestTypes())) {
            return back()->withErrors(['template' => 'Type de demande invalide pour le publipostage.']);
        }

        $validated = $request->validate([
            'template' => ['required', 'file', 'mimes:doc,docx', 'max:5120'],
        ]);

        $file = $validated['template'];
        $filePath = 'mail-merge/templates/agent-' . $agent->id . '/' . $requestType . '.docx';
        Storage::disk('local')->put($filePath, file_get_contents($file->getRealPath()));

        MailMergeTemplate::updateOrCreate(
            ['user_id' => $agent->id, 'request_type' => $requestType],
            ['file_path' => $filePath, 'original_name' => (string) $file->getClientOriginalName()]
        );

        ActivityLogger::log(
            'portal_agent_upload_mailmerge_template',
            $agent->id,
            null,
            null,
            ['request_type' => $requestType],
            $request
        );

        return back()->with([
            'status' => 'Modele Word televerse pour le type selectionne.',
            'status_type' => 'success',
        ]);
    }

    public function downloadAgentMailMergeTemplate(Request $request, string $requestType)
    {
        $agent = $this->requireCurrentUser($request);
        if (! $agent->isAgent() && ! $agent->isAdmin()) {
            abort(403, 'Action reservee aux agents.');
        }

        $template = MailMergeTemplate::where('user_id', $agent->id)
            ->where('request_type', $requestType)
            ->first();

        if (! $template || ! Storage::disk('local')->exists($template->file_path)) {
            return back()->withErrors(['template' => 'Aucun modele Word disponible pour ce type.']);
        }

        return response()->download(
            storage_path('app/' . $template->file_path),
            $template->original_name
        );
    }

    public function agentMailingExport(Request $request, string $requestType): StreamedResponse|RedirectResponse
    {
        $agent = $this->requireCurrentUser($request);
        if (! $agent->isAgent() && ! $agent->isAdmin()) {
            abort(403, 'Action reservee aux agents.');
        }

        if (! array_key_exists($requestType, $this->requestTypes())) {
            return back()->withErrors(['publipostage' => 'Type de demande invalide.']);
        }

        $lot = max(1, (int) $request->query('lot', 1));
        $perLot = 10;
        $recordsQuery = Demande::where('agent_id', $agent->id)
            ->where('request_type', $requestType)
            ->where('payment_status', 'paid')
            ->orderBy('created_at');

        $recordCount = (clone $recordsQuery)->count();
        $fullLotCount = intdiv($recordCount, $perLot);
        if ($fullLotCount < 1) {
            return back()->withErrors(['publipostage' => 'Aucun lot complet de 10 demandeurs pour ce type.']);
        }

        if ($lot > $fullLotCount) {
            return back()->withErrors(['publipostage' => 'Le lot demande n existe pas.']);
        }

        $batch = (clone $recordsQuery)
            ->offset(($lot - 1) * $perLot)
            ->limit($perLot)
            ->get();

        $typeLabel = $this->requestTypes()[$requestType] ?? $requestType;
        $filename = 'publipostage-' . Str::slug($typeLabel) . '-lot-' . $lot . '.csv';
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($batch) {
            $fp = fopen('php://output', 'wb');
            fwrite($fp, "\xEF\xBB\xBF");
            fputcsv($fp, [
                'Reference', 'Nom', 'Prenom', 'Email', 'Date de naissance', 'Lieu de naissance',
                'Numero registre', 'Adresse', 'Parent 1', 'Parent 2', 'Statut dossier', 'Reference paiement'
            ], ';');

            foreach ($batch as $d) {
                fputcsv($fp, [
                    $d->reference,
                    $d->last_name,
                    $d->first_name,
                    $d->email,
                    optional($d->birth_date)->format('d/m/Y'),
                    $d->birth_place,
                    $d->register_number,
                    $d->address,
                    trim(($d->parent_one_first_name ?? '') . ' ' . ($d->parent_one_last_name ?? '')),
                    trim(($d->parent_two_first_name ?? '') . ' ' . ($d->parent_two_last_name ?? '')),
                    $d->status,
                    $d->payment_reference,
                ], ';');
            }
            fclose($fp);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function showDemande(Request $request, Demande $demande): View|RedirectResponse
    {
        $user = $this->requireCurrentUser($request);
        if ($redirect = $this->ensureCitizenPortalAvailable($request, $user)) {
            return $redirect;
        }

        $demande->load([
            'citoyen:id,name,first_name,last_name,email',
            'agent:id,name,first_name,last_name,email',
            'messages.sender:id,name,first_name,last_name,role',
        ]);

        $this->authorizeDemandeAccess($user, $demande);

        return view('portal.demande', [
            ...$this->baseViewData($user),
            'pageTitle' => 'Dossier ' . $demande->reference,
            'demande' => $demande,
            'paymentAmount' => $this->paymentAmountFor($demande),
            'hasMayorSignature' => $this->hasMayorSignatureConfigured(),
            'statusLabels' => $this->statusLabels(),
            'requestTypes' => $this->requestTypes(),
            'agentOptions' => $user->isAdmin()
                ? User::where('role', User::ROLE_AGENT)->orderBy('name')->get(['id', 'name', 'first_name', 'last_name'])
                : collect(),
        ]);
    }

    public function downloadDemandeDocument(Request $request, Demande $demande)
    {
        $user = $this->requireCurrentUser($request);
        if ($redirect = $this->ensureCitizenPortalAvailable($request, $user)) {
            return $redirect;
        }

        $this->authorizeDemandeAccess($user, $demande);

        $filePath = (string) ($demande->processed_document_path ?? '');
        if ($filePath === '' || ! Storage::disk('local')->exists($filePath)) {
            return back()->withErrors([
                'document' => 'Le document traite n est pas encore disponible pour ce dossier.',
            ]);
        }

        $downloadName = (string) ($demande->processed_document_name ?: basename($filePath));
        $fileExtension = strtolower((string) pathinfo($downloadName, PATHINFO_EXTENSION));

        // Add mayor signature if it's a PDF and signature is configured
        if ($fileExtension === 'pdf' && $this->hasMayorSignatureConfigured()) {
            $signedPdfPath = $this->addSignatureToPdf($filePath);
            if ($signedPdfPath) {
                return Storage::disk('local')->download($signedPdfPath, $downloadName);
            }
        }

        return Storage::disk('local')->download($filePath, $downloadName);
    }

    public function storeDemandeMessage(Request $request, Demande $demande): RedirectResponse
    {
        $user = $this->requireCurrentUser($request);
        if ($redirect = $this->ensureCitizenPortalAvailable($request, $user)) {
            return $redirect;
        }

        $this->authorizeDemandeAccess($user, $demande);

        $payload = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $receiverId = $this->resolveMessageReceiverId($user, $demande);
        if (! $receiverId) {
            return back()->withErrors([
                'body' => $user->isCitoyen()
                    ? 'Aucun agent n est encore rattache a ce dossier.'
                    : 'Aucun citoyen n est rattache a ce dossier.',
            ]);
        }

        Message::create([
            'demande_id' => $demande->id,
            'sender_id' => $user->id,
            'receiver_id' => $receiverId,
            'body' => $payload['body'],
        ]);

        ActivityLogger::log(
            'portal_send_message',
            $user->id,
            Demande::class,
            $demande->id,
            [],
            $request
        );

        return back()->with([
            'status' => 'Message envoye.',
            'status_type' => 'success',
        ]);
    }

    public function storePortalMessage(Request $request): RedirectResponse
    {
        $user = $this->requireCurrentUser($request);
        if ($redirect = $this->ensureCitizenPortalAvailable($request, $user)) {
            return $redirect;
        }

        if ($user->isAdmin()) {
            $payload = $request->validate([
                'receiver_id' => ['required', 'integer', 'exists:users,id'],
                'demande_id' => ['nullable', 'integer', 'exists:demandes,id'],
                'body' => ['required', 'string', 'max:5000'],
            ]);

            $receiver = User::findOrFail((int) $payload['receiver_id']);
            $demande = null;

            if (! empty($payload['demande_id'])) {
                $demande = Demande::findOrFail((int) $payload['demande_id']);
            }

            Message::create([
                'demande_id' => $demande?->id,
                'sender_id' => $user->id,
                'receiver_id' => $receiver->id,
                'body' => $payload['body'],
            ]);

            ActivityLogger::log(
                'portal_send_message',
                $user->id,
                $demande ? Demande::class : User::class,
                $demande?->id ?? $receiver->id,
                ['source' => 'admin_messages', 'receiver_id' => $receiver->id],
                $request
            );

            return back()->with([
                'status' => 'Message envoye.',
                'status_type' => 'success',
            ]);
        }

        $payload = $request->validate([
            'demande_id' => ['required', 'integer', 'exists:demandes,id'],
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $demande = Demande::findOrFail((int) $payload['demande_id']);
        $this->authorizeDemandeAccess($user, $demande);

        $receiverId = $this->resolveMessageReceiverId($user, $demande);
        if (! $receiverId) {
            return back()->withErrors([
                'body' => $user->isCitoyen()
                    ? 'Aucun agent n est encore rattache a ce dossier.'
                    : 'Aucun citoyen n est rattache a ce dossier.',
            ]);
        }

        Message::create([
            'demande_id' => $demande->id,
            'sender_id' => $user->id,
            'receiver_id' => $receiverId,
            'body' => $payload['body'],
        ]);

        ActivityLogger::log(
            'portal_send_message',
            $user->id,
            Demande::class,
            $demande->id,
            ['source' => 'dashboard'],
            $request
        );

        return back()->with([
            'status' => 'Message envoye.',
            'status_type' => 'success',
        ]);
    }

    public function assignDemande(Request $request, Demande $demande): RedirectResponse
    {
        $user = $this->requireCurrentUser($request);
        $this->authorizeAgentAction($user, $demande, false);

        $agentId = $user->id;
        if ($user->isAdmin()) {
            $validated = $request->validate([
                'agent_id' => ['required', 'integer', 'exists:users,id'],
            ]);

            $agent = User::where('id', $validated['agent_id'])
                ->where('role', User::ROLE_AGENT)
                ->first();

            if (! $agent) {
                return back()->withErrors(['agent_id' => 'Agent introuvable.']);
            }

            $agentId = $agent->id;
        }

        $demande->update([
            'agent_id' => $agentId,
            'status' => Demande::STATUS_ASSIGNED,
            'assigned_at' => now(),
        ]);

        $this->notifyCitizenDemandeStatus($user, $demande, Demande::STATUS_ASSIGNED);

        ActivityLogger::log(
            'portal_assign_demande',
            $user->id,
            Demande::class,
            $demande->id,
            ['agent_id' => $agentId],
            $request
        );

        return back()->with([
            'status' => 'Dossier assigne.',
            'status_type' => 'success',
        ]);
    }

    public function processDemande(Request $request, Demande $demande): RedirectResponse
    {
        $user = $this->requireCurrentUser($request);
        $this->authorizeAgentAction($user, $demande, true);

        $payload = $request->validate([
            'status' => ['required', 'in:processing,completed,rejected'],
            'processing_channel' => ['nullable', 'in:counter,online'],
            'processed_document' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
            'agent_notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $processingChannel = (string) ($payload['processing_channel'] ?? ($demande->processing_channel ?? 'counter'));

        if ($payload['status'] === Demande::STATUS_COMPLETED && $processingChannel === 'online') {
            if (! $this->hasMayorSignatureConfigured()) {
                return back()->withErrors([
                    'processing_channel' => 'La signature numerique du maire doit etre configuree dans les parametres avant un traitement en ligne.',
                ]);
            }

            if (! $request->hasFile('processed_document') && empty($demande->processed_document_path)) {
                return back()->withErrors([
                    'processed_document' => 'Ajoutez le document final traite pour activer le telechargement citoyen.',
                ]);
            }
        }

        $update = [
            'status' => $payload['status'],
            'processing_channel' => $processingChannel,
            'agent_notes' => $payload['agent_notes'] ?? $demande->agent_notes,
        ];

        if ($payload['status'] === Demande::STATUS_COMPLETED && $processingChannel === 'online' && $request->hasFile('processed_document')) {
            $file = $request->file('processed_document');
            $extension = strtolower((string) $file->getClientOriginalExtension());
            $storedPath = 'demandes/processed/' . $demande->id . '/final-' . now()->format('YmdHis') . '-' . Str::random(5) . '.' . $extension;
            Storage::disk('local')->put($storedPath, file_get_contents($file->getRealPath()));

            $update['processed_document_path'] = $storedPath;
            $update['processed_document_name'] = (string) $file->getClientOriginalName();
        }

        if ($processingChannel !== 'online' || $payload['status'] !== Demande::STATUS_COMPLETED) {
            $update['processed_document_path'] = null;
            $update['processed_document_name'] = null;
        }

        if (in_array($payload['status'], [Demande::STATUS_COMPLETED, Demande::STATUS_REJECTED], true)) {
            $update['processed_at'] = now();
        }

        $demande->update($update);

        $this->notifyCitizenDemandeStatus($user, $demande, (string) $payload['status']);

        ActivityLogger::log(
            'portal_process_demande',
            $user->id,
            Demande::class,
            $demande->id,
            ['status' => $payload['status']],
            $request
        );

        return back()->with([
            'status' => 'Dossier mis a jour.',
            'status_type' => 'success',
        ]);
    }

    // ------------------------------------------------------------------ admin sub-pages

    public function adminAgents(Request $request): View
    {
        $admin = $this->requireCurrentUser($request);

        return view('portal.admin-agents', [
            ...$this->baseViewData($admin),
            'pageTitle' => 'Gestion des agents et superviseurs',
            'agents' => User::whereIn('role', [User::ROLE_AGENT, User::ROLE_SUPERVISEUR])
                ->orderBy('role')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function updateAgent(Request $request, User $user): RedirectResponse
    {
        $admin = $this->requireCurrentUser($request);

        if (! in_array($user->role, [User::ROLE_AGENT, User::ROLE_SUPERVISEUR], true)) {
            return back()->withErrors(['user' => 'Cet utilisateur n est ni agent ni superviseur.']);
        }

        $payload = $request->validate([
            'first_name' => ['nullable', 'string', 'max:120'],
            'last_name'  => ['nullable', 'string', 'max:120'],
            'email'      => ['required', 'email:rfc', 'max:190', 'unique:users,email,'. $user->id],
            'is_active'  => ['nullable', 'boolean'],
        ]);

        $fullName = trim(($payload['first_name'] ?? '') . ' ' . ($payload['last_name'] ?? ''));
        $update = [
            'first_name' => $payload['first_name'] ?? null,
            'last_name'  => $payload['last_name'] ?? null,
            'name'       => $fullName ?: $user->name,
            'email'      => $payload['email'],
            'is_active'  => isset($payload['is_active']) ? (bool) $payload['is_active'] : $user->is_active,
        ];

        if ($request->filled('password')) {
            $request->validate(['password' => [Password::min(8)]]);
            $update['password'] = Hash::make($request->input('password'));
        }

        $user->update($update);
        ActivityLogger::log('portal_admin_update_agent', $admin->id, User::class, $user->id, ['role' => $user->role], $request);

        return back()->with(['status' => 'Compte mis a jour.', 'status_type' => 'success']);
    }

    public function deleteUser(Request $request, User $user): RedirectResponse
    {
        $admin = $this->requireCurrentUser($request);

        if ($admin->id === $user->id) {
            return back()->withErrors(['user' => 'Vous ne pouvez pas supprimer votre propre compte.']);
        }

        ActivityLogger::log('portal_admin_delete_user', $admin->id, User::class, $user->id, ['role' => $user->role], $request);
        $user->delete();

        return back()->with(['status' => 'Compte supprime.', 'status_type' => 'success']);
    }

    public function adminDemandes(Request $request): View
    {
        $admin = $this->requireCurrentUser($request);
        $query = Demande::with(['citoyen:id,name,first_name,last_name,email', 'agent:id,name,first_name,last_name']);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('type')) {
            $query->where('request_type', $request->input('type'));
        }

        return view('portal.admin-demandes', [
            ...$this->baseViewData($admin),
            'pageTitle'    => 'Suivi des demandes',
            'demandes'     => $query->latest()->paginate(25),
            'statusLabels' => $this->statusLabels(),
            'requestTypes' => $this->requestTypes(),
            'counts'       => $this->buildDemandeCounts(Demande::query()),
        ]);
    }

    public function adminMessages(Request $request): View
    {
        $admin = $this->requireCurrentUser($request);

        return view('portal.admin-messages', [
            ...$this->baseViewData($admin),
            'pageTitle' => 'Messagerie',
            'messages'  => Message::with([
                'sender:id,name,first_name,last_name,role',
                'receiver:id,name,first_name,last_name,role',
                'demande:id,reference,request_type',
            ])->latest()->paginate(30, ['*'], 'messages_page'),
            'messageUsers' => User::where('is_active', true)
                ->where('id', '!=', $admin->id)
                ->orderBy('role')
                ->orderBy('name')
                ->get(['id', 'name', 'first_name', 'last_name', 'role']),
            'messageDemandes' => Demande::with(['citoyen:id,name,first_name,last_name', 'agent:id,name,first_name,last_name'])
                ->latest()
                ->limit(80)
                ->get(['id', 'reference', 'user_id', 'agent_id']),
            'wpContactMessages' => WpContactMessage::with('repliedBy:id,name,first_name,last_name,email')
                ->latest('received_at')
                ->paginate(20, ['*'], 'contacts_page'),
        ]);
    }

    public function replyWordPressContactMessage(Request $request, WpContactMessage $contactMessage): RedirectResponse
    {
        $admin = $this->requireCurrentUser($request);

        $payload = $request->validate([
            'subject' => ['nullable', 'string', 'max:190'],
            'body' => ['required', 'string', 'max:10000'],
        ]);

        if (! filter_var($contactMessage->sender_email, FILTER_VALIDATE_EMAIL)) {
            return back()->with([
                'status' => 'Email expediteur invalide. Reponse impossible.',
                'status_type' => 'warning',
            ]);
        }

        $subject = trim((string) ($payload['subject'] ?? ''));
        if ($subject === '') {
            $subject = 'Reponse a votre message';
        }

        try {
            Mail::raw((string) $payload['body'], function ($mail) use ($admin, $contactMessage, $subject): void {
                $mail->to($contactMessage->sender_email)
                    ->subject($subject);

                if (filter_var($admin->email, FILTER_VALIDATE_EMAIL)) {
                    $mail->replyTo($admin->email, $admin->name);
                }
            });

            $contactMessage->update([
                'replied_at' => now(),
                'reply_subject' => $subject,
                'reply_body' => (string) $payload['body'],
                'reply_status' => 'sent',
                'reply_error' => null,
                'replied_by' => $admin->id,
            ]);

            ActivityLogger::log(
                'admin_reply_wp_contact_message',
                $admin->id,
                WpContactMessage::class,
                $contactMessage->id,
                ['sender_email' => $contactMessage->sender_email],
                $request
            );

            return back()->with([
                'status' => 'Reponse email envoyee.',
                'status_type' => 'success',
            ]);
        } catch (Throwable $exception) {
            $contactMessage->update([
                'reply_status' => 'failed',
                'reply_error' => Str::limit($exception->getMessage(), 1800),
                'replied_by' => $admin->id,
            ]);

            return back()->with([
                'status' => 'Echec de l envoi email. Verifiez la configuration mail Laravel.',
                'status_type' => 'warning',
            ]);
        }
    }

    public function adminCitoyens(Request $request): View
    {
        $admin = $this->requireCurrentUser($request);



        $query = User::where('role', User::ROLE_CITOYEN)
            ->withCount(['demandes' => function ($q) use ($request) {
                if ($request->filled('type')) {
                    $q->where('request_type', $request->input('type'));
                }
            }]);

        // Filtre catégorie
        if ($request->filled('category')) {
            $query->where('role', $request->input('category'));
        }
        // Filtre localisation
        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->input('location') . '%');
        }

        return view('portal.admin-citoyens', [
            ...$this->baseViewData($admin),
            'pageTitle'    => 'Liste des citoyens',
            'citoyens'     => $query->orderBy('name')->paginate(30),
            'requestTypes' => $this->requestTypes(),
            'filterType'   => $request->input('type', ''),
            'filterCategory' => $request->input('category', ''),
            'filterLocation' => $request->input('location', ''),
        ]);
    }

    public function adminCitoyensExport(Request $request): Response|\Symfony\Component\HttpFoundation\Response
    {
        $this->requireCurrentUser($request);

        $type   = $request->input('type', '');
        $format = $request->input('format', 'csv');

        $query = User::where('role', User::ROLE_CITOYEN)
            ->withCount('demandes');

        if ($type !== '') {
            $query->whereHas('demandes', fn ($q) => $q->where('request_type', $type));
        }

        $citoyens = $query->orderBy('name')->get();
        $typeLabel = $this->requestTypes()[$type] ?? ($type ?: 'Tous');

        if ($format === 'pdf') {
            $pdf = App::make('dompdf.wrapper');
            $pdf->loadView('portal.export-citoyens-pdf', [
                'citoyens'  => $citoyens,
                'typeLabel' => $typeLabel,
                'date'      => now()->format('d/m/Y'),
            ]);

            return $pdf->download('citoyens-' . Str::slug($typeLabel) . '.pdf');
        }

        // CSV
        $filename = 'citoyens-' . Str::slug($typeLabel) . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $rows   = [];
        $rows[] = [
            'Nom', 'Prenom', 'Email', 'Telephone', 'Adresse',
            'Date de naissance', 'Lieu de naissance',
            'N° de registre', 'N° citoyen',
            'Demandes', 'Compte actif', 'Inscrit le',
        ];
        foreach ($citoyens as $c) {
            $rows[] = [
                $c->last_name ?? $c->name,
                $c->first_name ?? '',
                $c->email,
                $c->phone ?? '',
                $c->address ?? '',
                optional($c->birth_date)->format('d/m/Y') ?? '',
                $c->birth_place ?? '',
                $c->register_number ?? '',
                $c->citizen_number ?? '',
                $c->demandes_count,
                $c->is_active ? 'Oui' : 'Non',
                optional($c->created_at)->format('d/m/Y'),
            ];
        }

        $callback = function () use ($rows) {
            $fp = fopen('php://output', 'wb');
            fwrite($fp, "\xEF\xBB\xBF"); // BOM UTF-8 for Excel
            foreach ($rows as $row) {
                fputcsv($fp, $row, ';');
            }
            fclose($fp);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function adminCitizenRegistry(Request $request): View
    {
        $admin = $this->requireCurrentUser($request);

        $query = CitizenRegistry::query()
            ->orderByDesc('created_at');

        // Filtrage par secteur
        if ($request->filled('sector')) {
            $query->where('profession_sector', $request->input('sector'));
        }

        // Filtrage par disponibilité
        if ($request->filled('available')) {
            $available = $request->input('available') === '1';
            $query->where('available_for_municipality', $available);
        }

        // Recherche
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $registrations = $query->paginate(20);
        $totalRegistrations = CitizenRegistry::count();
        $availableCount = CitizenRegistry::where('available_for_municipality', true)->count();

        $sectors = CitizenRegistry::selectRaw('profession_sector')
            ->distinct()
            ->orderBy('profession_sector')
            ->pluck('profession_sector');

        return view('portal.admin-citizen-registry', [
            ...$this->baseViewData($admin),
            'pageTitle' => 'Registre des citoyens',
            'registrations' => $registrations,
            'totalRegistrations' => $totalRegistrations,
            'availableCount' => $availableCount,
            'sectors' => $sectors,
            'filterSector' => $request->input('sector'),
            'filterAvailable' => $request->input('available'),
            'searchTerm' => $request->input('search'),
        ]);
    }

    public function downloadCitizenCV(Request $request, CitizenRegistry $registry): StreamedResponse|RedirectResponse
    {
        $admin = $this->requireCurrentUser($request);

        if (!$registry->cv_file_path) {
            return back()->with([
                'status' => 'Ce citoyen n\'a pas fourni de CV.',
                'status_type' => 'warning',
            ]);
        }

        $filePath = 'public/cvs/' . basename($registry->cv_file_path);

        if (!Storage::exists($filePath)) {
            return back()->with([
                'status' => 'Le fichier n\'existe pas.',
                'status_type' => 'error',
            ]);
        }

        ActivityLogger::log(
            'admin_download_citizen_cv',
            $admin->id,
            CitizenRegistry::class,
            $registry->id,
            [
                'file_name' => $registry->cv_file_name,
                'citizen_email' => $registry->email,
            ],
            $request
        );

        return Storage::download($filePath, $registry->cv_file_name);
    }

    public function adminStats(Request $request): View
    {
        $admin = $this->requireCurrentUser($request);
        $userCounts = $this->buildUserCounts();
        $demandeCounts = $this->buildDemandeCounts(Demande::query());

        $byType = Demande::selectRaw('request_type, count(*) as total')
            ->groupBy('request_type')
            ->orderByDesc('total')
            ->get();

        $byMonth = Demande::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, count(*) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->limit(12)
            ->get();

        $byAgent = User::where('role', User::ROLE_AGENT)
            ->withCount('assignedDemandes as total')
            ->orderByDesc('total')
            ->get();

        $recentAttendances = AgentAttendance::with([
                'agent:id,name,first_name,last_name',
                'markedBy:id,name,first_name,last_name',
            ])
            ->latest('attendance_date')
            ->latest('id')
            ->limit(16)
            ->get();

        $monthStart = now()->startOfMonth()->toDateString();
        $monthEnd = now()->endOfMonth()->toDateString();
        $agentPerformanceRows = $byAgent->map(function (User $agent) use ($monthStart, $monthEnd) {
            $query = Demande::where('agent_id', $agent->id);
            $metrics = $this->buildAgentMetrics((clone $query));

            $presentDays = AgentAttendance::where('user_id', $agent->id)
                ->whereBetween('attendance_date', [$monthStart, $monthEnd])
                ->whereIn('status', [AgentAttendance::STATUS_PRESENT, AgentAttendance::STATUS_LATE])
                ->count();

            $absentDays = AgentAttendance::where('user_id', $agent->id)
                ->whereBetween('attendance_date', [$monthStart, $monthEnd])
                ->where('status', AgentAttendance::STATUS_ABSENT)
                ->count();

            return [
                'id' => $agent->id,
                'name' => trim(($agent->first_name ?? '') . ' ' . ($agent->last_name ?? '')) ?: $agent->name,
                'assigned_total' => (int) ($agent->total ?? 0),
                'completed' => $metrics['completed'],
                'rejected' => $metrics['rejected'],
                'completion_rate' => $this->ratePercent($metrics['completed'], $metrics['completed'] + $metrics['rejected']),
                'monthly_completed' => $metrics['monthly_completed'],
                'present_days' => $presentDays,
                'absent_days' => $absentDays,
            ];
        });

        return view('portal.admin-stats', [
            ...$this->baseViewData($admin),
            'pageTitle'  => 'Statistiques',
            'byType'     => $byType,
            'byMonth'    => $byMonth,
            'byAgent'    => $byAgent,
            'recentAttendances' => $recentAttendances,
            'attendanceStatusLabels' => $this->attendanceStatusLabels(),
            'agentPerformanceRows' => $agentPerformanceRows,
            'hasArchitectureDoc' => is_file(base_path('docs/architecture-portail.md')),
            'global'     => [
                'total'      => $demandeCounts['total'],
                'completed'  => $demandeCounts['completed'],
                'rejected'   => $demandeCounts['rejected'],
                'pending'    => $demandeCounts['pending'],
                'citoyens'   => $userCounts['citoyens'],
                'agents'     => $userCounts['agents'],
            ],
            'requestTypes' => $this->requestTypes(),
        ]);
    }

    public function storeSuperviseurAgentAttendance(Request $request): RedirectResponse
    {
        $superviseur = $this->requireCurrentUser($request);

        $payload = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'attendance_date' => ['required', 'date'],
            'status' => ['required', 'in:present,late,absent'],
            'check_in_time' => ['nullable', 'date_format:H:i'],
            'check_out_time' => ['nullable', 'date_format:H:i'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $agent = User::findOrFail((int) $payload['user_id']);
        if (! $agent->isAgent()) {
            return back()->withErrors(['user_id' => 'Le pointage est reserve aux agents.']);
        }

        $attendance = AgentAttendance::updateOrCreate(
            [
                'user_id' => $agent->id,
                'attendance_date' => $payload['attendance_date'],
            ],
            [
                'status' => $payload['status'],
                'check_in_time' => $payload['check_in_time'] ?? null,
                'check_out_time' => $payload['check_out_time'] ?? null,
                'notes' => $payload['notes'] ?? null,
                'marked_by' => $superviseur->id,
            ]
        );

        ActivityLogger::log(
            'portal_superviseur_mark_agent_attendance',
            $superviseur->id,
            AgentAttendance::class,
            $attendance->id,
            [
                'agent_id' => $agent->id,
                'attendance_date' => $attendance->attendance_date?->toDateString(),
                'status' => $attendance->status,
            ],
            $request
        );

        return back()->with([
            'status' => 'Pointage enregistre.',
            'status_type' => 'success',
        ]);
    }

    public function downloadAdminArchitectureDoc(Request $request)
    {
        $admin = $this->requireCurrentUser($request);

        $path = base_path('docs/architecture-portail.md');
        if (! is_file($path)) {
            return back()->with([
                'status' => 'Le document est introuvable ou deja supprime.',
                'status_type' => 'error',
            ]);
        }

        $markdown = (string) @file_get_contents($path);
        if ($markdown === '') {
            return back()->with([
                'status' => 'Le document est vide ou illisible.',
                'status_type' => 'error',
            ]);
        }

        $htmlContent = Str::markdown($markdown);
        $html = '<!doctype html><html lang="fr"><head><meta charset="utf-8">'
            . '<style>body{font-family:DejaVu Sans,sans-serif;font-size:12px;line-height:1.45;color:#18222c;margin:24px;}h1,h2,h3{color:#0a6c74;margin:0 0 10px;}h1{font-size:20px;}h2{font-size:15px;margin-top:16px;}h3{font-size:13px;margin-top:12px;}p{margin:0 0 9px;}ul,ol{margin:0 0 10px 18px;}code{background:#f3f5f6;padding:1px 4px;border-radius:4px;}hr{border:none;border-top:1px solid #d9dee2;margin:16px 0;}</style>'
            . '</head><body>' . $htmlContent . '</body></html>';

        $pdf = App::make('dompdf.wrapper');
        $pdf->setPaper('a4');
        $pdf->loadHTML($html);

        ActivityLogger::log(
            'portal_download_admin_architecture_doc',
            $admin->id,
            User::class,
            $admin->id,
            ['file' => 'docs/architecture-portail.md', 'delivered_as' => 'pdf'],
            $request
        );

        @unlink($path);

        return $pdf->download('architecture-portail.pdf');
    }

    public function adminSettings(Request $request): View
    {
        $admin = $this->requireCurrentUser($request);

        return view('portal.admin-settings', [
            ...$this->baseViewData($admin),
            'pageTitle' => 'Parametres du site',
            'settings'  => $this->loadSettings(),
            'requestTypes' => $this->requestTypes(),
            'requestFees' => $this->requestFees(),
            'hasMayorSignature' => $this->hasMayorSignatureConfigured(),
        ]);
    }

    public function saveAdminSettings(Request $request): RedirectResponse
    {
        $admin = $this->requireCurrentUser($request);

        $payload = $request->validate([
            'site_name'        => ['required', 'string', 'max:250'],
            'site_description' => ['nullable', 'string', 'max:500'],
            'contact_email'    => ['required', 'email:rfc', 'max:190'],
            'contact_phone'    => ['nullable', 'string', 'max:30'],
            'contact_address'  => ['nullable', 'string', 'max:500'],
            'allow_register'   => ['nullable', 'boolean'],
            'maintenance_mode' => ['nullable', 'boolean'],
            'request_fees' => ['required', 'array'],
            'request_fees.*' => ['nullable', 'integer', 'min:0', 'max:500000'],
            'mayor_signature' => ['nullable', 'file', 'mimes:png,jpg,jpeg', 'max:2048'],
        ]);

        $savedSettings = $this->loadSettings();
        $feesByType = [];
        foreach ($this->requestTypes() as $typeKey => $_label) {
            $feesByType[$typeKey] = max(0, (int) ($payload['request_fees'][$typeKey] ?? 0));
        }

        $signaturePath = (string) ($savedSettings['mayor_signature_path'] ?? '');
        $signatureName = (string) ($savedSettings['mayor_signature_name'] ?? '');

        if ($request->hasFile('mayor_signature')) {
            $signature = $request->file('mayor_signature');
            $extension = strtolower((string) $signature->getClientOriginalExtension());
            $newPath = 'portal/signatures/maire-signature.' . $extension;

            Storage::disk('local')->put($newPath, file_get_contents($signature->getRealPath()));
           
	    $payload['mayor_signature_path'] =$newPath;
	    $payload['mayor_signature_name'] =$signature->getClientOriginalName();

            if ($signaturePath !== '' && $signaturePath !== $newPath && Storage::disk('local')->exists($signaturePath)) {
                Storage::disk('local')->delete($signaturePath);
            }

            $signaturePath = $newPath;
            $signatureName = (string) $signature->getClientOriginalName();
        }

        $this->saveSettings([
            'site_name'        => $payload['site_name'],
            'site_description' => $payload['site_description'] ?? '',
            'contact_email'    => $payload['contact_email'],
            'contact_phone'    => $payload['contact_phone'] ?? '',
            'contact_address'  => $payload['contact_address'] ?? '',
            'allow_register'   => (int) ($payload['allow_register'] ?? 0),
            'maintenance_mode' => (int) ($payload['maintenance_mode'] ?? 0),
            'request_fees'     => $feesByType,
            'mayor_signature_path' => $signaturePath,
            'mayor_signature_name' => $signatureName,
        ]);

        ActivityLogger::log('portal_admin_save_settings', $admin->id, null, null, [], $request);

        return back()->with(['status' => 'Parametres enregistres.', 'status_type' => 'success']);
    }

    public function adminProfile(Request $request): View
    {
        $admin = $this->requireCurrentUser($request);

        return view('portal.admin-profil', [
            ...$this->baseViewData($admin),
            'pageTitle' => 'Mon profil',
        ]);
    }

    public function saveAdminProfile(Request $request): RedirectResponse
    {
        $admin = $this->requireCurrentUser($request);

        $payload = $request->validate([
            'first_name' => ['nullable', 'string', 'max:120'],
            'last_name'  => ['nullable', 'string', 'max:120'],
            'email'      => ['required', 'email:rfc', 'max:190', 'unique:users,email,' . $admin->id],
        ]);

        $fullName = trim(($payload['first_name'] ?? '') . ' ' . ($payload['last_name'] ?? ''));
        $update   = [
            'first_name' => $payload['first_name'] ?? null,
            'last_name'  => $payload['last_name'] ?? null,
            'name'       => $fullName ?: $admin->name,
            'email'      => $payload['email'],
        ];

        if ($request->filled('password')) {
            $request->validate([
                'current_password' => ['required', 'string'],
                'password'         => ['required', 'confirmed', Password::min(8)],
            ]);
            if (! Hash::check($request->input('current_password'), $admin->password)) {
                return back()->withErrors(['current_password' => 'Mot de passe actuel incorrect.']);
            }
            $update['password'] = Hash::make($request->input('password'));
        }

        $admin->update($update);
        ActivityLogger::log('portal_admin_update_profile', $admin->id, User::class, $admin->id, [], $request);

        return back()->with(['status' => 'Profil mis a jour.', 'status_type' => 'success']);
    }

    // ------------------------------------------------------------------ user management

    public function storeUser(Request $request): RedirectResponse
    {
        $admin = $this->requireCurrentUser($request);

        $payload = $request->validate([
            'first_name' => ['nullable', 'string', 'max:120'],
            'last_name' => ['nullable', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:190', 'unique:users,email'],
            'password' => ['required', Password::min(8)],
            'role' => ['required', 'in:admin,agent,superviseur,citoyen'],
        ]);

        $fullName = trim(($payload['first_name'] ?? '') . ' ' . ($payload['last_name'] ?? ''));
        if ($fullName === '') {
            $fullName = (string) Str::before($payload['email'], '@');
        }

        $user = User::create([
            'name' => $fullName,
            'first_name' => $payload['first_name'] ?? null,
            'last_name' => $payload['last_name'] ?? null,
            'email' => $payload['email'],
            'password' => Hash::make($payload['password']),
            'role' => $payload['role'],
            'is_active' => true,
        ]);

        ActivityLogger::log(
            'portal_admin_create_user',
            $admin->id,
            User::class,
            $user->id,
            ['role' => $user->role],
            $request
        );

        $redirect = $user->role === User::ROLE_AGENT
            ? route('portal.admin.agents')
            : route('portal.admin');

        return redirect()->to($redirect)->with([
            'status' => 'Utilisateur cree.',
            'status_type' => 'success',
        ]);
    }

    public function toggleUser(Request $request, User $user): RedirectResponse
    {
        $admin = $this->requireCurrentUser($request);

        if ($admin->id === $user->id) {
            return back()->withErrors(['user' => 'Vous ne pouvez pas desactiver votre propre compte.']);
        }

        $user->update(['is_active' => ! $user->is_active]);

        ActivityLogger::log(
            'portal_admin_toggle_user',
            $admin->id,
            User::class,
            $user->id,
            ['is_active' => $user->is_active],
            $request
        );

        return back()->with([
            'status' => $user->is_active ? 'Compte active.' : 'Compte desactive.',
            'status_type' => 'success',
        ]);
    }

    public function changeUserRole(Request $request, User $user): RedirectResponse
    {
        $admin = $this->requireCurrentUser($request);

        if ($admin->id === $user->id) {
            return back()->withErrors(['role' => 'Vous ne pouvez pas modifier votre propre role.']);
        }

        $payload = $request->validate([
            'role' => ['required', 'in:admin,agent,superviseur,citoyen'],
        ]);

        $oldRole = $user->role;
        $user->update(['role' => $payload['role']]);

        ActivityLogger::log(
            'portal_admin_change_role',
            $admin->id,
            User::class,
            $user->id,
            ['old_role' => $oldRole, 'new_role' => $payload['role']],
            $request
        );

        return back()->with([
            'status' => 'Role mis a jour.',
            'status_type' => 'success',
        ]);
    }

    private function baseViewData(?User $user): array
    {
        $request = request();
        $runtimeWordpressUrl = rtrim($request->getSchemeAndHttpHost() . '/mairie_wp', '/');
        $configuredWordpressUrl = rtrim((string) config('mairie.wordpress_url'), '/');
        $baseUrl = '' !== $runtimeWordpressUrl ? $runtimeWordpressUrl : $configuredWordpressUrl;
        $laravel_public_url = rtrim($request->getSchemeAndHttpHost() . '/mairie_wp/backend-laravel/public', '/');

        return [
            'currentUser' => $user,
            'portalLinks' => $this->portalLinksFor($user),
            'portalSettings' => $this->portalSettings(),
            'wordpressUrl' => $baseUrl,
            'laravelPublicUrl' => $laravel_public_url,
            'senegalFlagUrl' => $laravel_public_url . '/senegal-flag.svg',
        ];
    }

    private function storeAuthenticatedUser(Request $request, User $user): void
    {
        $request->session()->put(self::SESSION_KEY, [
            'user_id' => $user->id,
            'role' => $user->role,
            'token' => JWTAuth::fromUser($user),
        ]);
    }

    private function currentUser(Request $request): ?User
    {
        if ($this->currentUserResolved) {
            return $this->currentUserCache;
        }

        $session = $request->session()->get(self::SESSION_KEY);
        $userId = is_array($session) ? (int) ($session['user_id'] ?? 0) : 0;

        if ($userId < 1) {
            $this->currentUserResolved = true;
            return null;
        }

        $user = User::find($userId);
        if (! $user || ! $user->is_active) {
            $request->session()->forget(self::SESSION_KEY);
            $this->currentUserResolved = true;
            return null;
        }

        $this->currentUserCache = $user;
        $this->currentUserResolved = true;

        return $this->currentUserCache;
    }



    private function requireCurrentUser(Request $request): User
    {
        return $this->currentUser($request) ?? abort(401);
    }

    private function portalUrlForRole(string $role): string
    {
        return match (User::normalizeRoleValue($role)) {
            User::ROLE_ADMIN => route('portal.admin'),
            User::ROLE_AGENT => route('portal.agent'),
            User::ROLE_SUPERVISEUR => route('portal.superviseur'),
            default => route('portal.citizen'),
        };
    }

    private function resolvePostAuthRedirect(Request $request, User $user, ?string $redirectTo): string
    {
        $fallback = $this->portalUrlForRole($user->role);
        $sanitized = $this->sanitizeRedirect($request, (string) $redirectTo);

        return $sanitized !== '' ? $sanitized : $fallback;
    }

    private function sanitizeRedirect(Request $request, string $redirectTo): string
    {
        if ($redirectTo === '') {
            return '';
        }

        $parsed = parse_url($redirectTo);
        if ($parsed === false) {
            return '';
        }

        if (! isset($parsed['host'])) {
            return str_starts_with($redirectTo, '/') ? url($redirectTo) : '';
        }

        if ((string) ($parsed['host'] ?? '') !== (string) $request->getHost()) {
            return '';
        }

        return $redirectTo;
    }

    private function portalLinksFor(?User $user): array
    {
        if (! $user) {
            return [];
        }

        $links = [];

        if ($user->isCitoyen()) {
            $links[] = ['label' => 'Espace citoyen', 'url' => route('portal.citizen')];
        }

        if ($user->isAgent()) {
            $links[] = ['label' => 'Espace agent', 'url' => route('portal.agent')];
            $links[] = ['label' => 'Mes performances', 'url' => route('portal.agent.performance')];
        }

        if ($user->isSuperviseur()) {
            $links[] = ['label' => 'Pointage agents', 'url' => route('portal.superviseur')];
        }

        if ($user->isAdmin()) {
            $links[] = ['label' => 'Administration',  'url' => route('portal.admin')];
            $links[] = ['label' => 'Agents',          'url' => route('portal.admin.agents')];
            $links[] = ['label' => 'Demandes',        'url' => route('portal.admin.demandes')];
            $links[] = ['label' => 'Messages',        'url' => route('portal.admin.messages')];
            $links[] = ['label' => 'Citoyens',        'url' => route('portal.admin.citoyens')];
            $links[] = ['label' => 'Statistiques',    'url' => route('portal.admin.stats')];
            $links[] = ['label' => 'Parametres',      'url' => route('portal.admin.settings')];
            $links[] = ['label' => 'Mon profil',      'url' => route('portal.admin.profile')];
        }

        return $links;
    }

    private function requestTypes(): array
    {
        return [
            'copie-extrait' => 'Copie d extrait',
            'certificat-mariage' => 'Certificat de mariage',
            'declaration-naissance' => 'Declaration de naissance',
            'certificat-deces' => 'Certificat de deces',
            'autre' => 'Autre demande',
        ];
    }

    private function defaultRequestFees(): array
    {
        return [
            'copie-extrait' => 1500,
            'certificat-mariage' => 2500,
            'declaration-naissance' => 2000,
            'certificat-deces' => 2000,
            'autre' => 3000,
        ];
    }

    private function requestFees(): array
    {
        $defaults = $this->defaultRequestFees();
        $settings = $this->portalSettings();
        $configured = is_array($settings['request_fees'] ?? null) ? $settings['request_fees'] : [];

        $fees = [];
        foreach ($defaults as $requestType => $defaultFee) {
            $fees[$requestType] = max(0, (int) ($configured[$requestType] ?? $defaultFee));
        }

        return $fees;
    }

    private function statusLabels(): array
    {
        return [
            Demande::STATUS_PENDING => 'En attente',
            Demande::STATUS_ASSIGNED => 'Assignee',
            Demande::STATUS_PROCESSING => 'En cours',
            Demande::STATUS_COMPLETED => 'Traitee',
            Demande::STATUS_REJECTED => 'Rejetee',
        ];
    }

    private function attendanceStatusLabels(): array
    {
        return [
            AgentAttendance::STATUS_PRESENT => 'Present',
            AgentAttendance::STATUS_LATE => 'Retard',
            AgentAttendance::STATUS_ABSENT => 'Absent',
        ];
    }

    private function notifyCitizenDemandeStatus(User $actor, Demande $demande, string $status): void
    {
        $citoyenId = (int) ($demande->user_id ?? 0);
        if ($citoyenId < 1 || $citoyenId === (int) $actor->id) {
            return;
        }

        $statusLabel = $this->statusLabels()[$status] ?? $status;
        $body = sprintf(
            'Mise a jour du dossier %s : votre demande est maintenant "%s".',
            (string) $demande->reference,
            $statusLabel
        );

        Message::create([
            'demande_id' => $demande->id,
            'sender_id' => $actor->id,
            'receiver_id' => $citoyenId,
            'body' => $body,
        ]);
    }

    private function paymentStatusLabel(string $status): string
    {
        return match ($status) {
            'paid' => 'Valide',
            'paid_pending' => 'A valider',
            default => 'Non regle',
        };
    }

    private function buildDemandeCounts($query): array
    {
        $stats = (clone $query)
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as pending", [Demande::STATUS_PENDING])
            ->selectRaw("SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as assigned", [Demande::STATUS_ASSIGNED])
            ->selectRaw("SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as processing", [Demande::STATUS_PROCESSING])
            ->selectRaw("SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as completed", [Demande::STATUS_COMPLETED])
            ->selectRaw("SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as rejected", [Demande::STATUS_REJECTED])
            ->first();

        return [
            'total' => (int) ($stats->total ?? 0),
            'pending' => (int) ($stats->pending ?? 0),
            'assigned' => (int) ($stats->assigned ?? 0),
            'processing' => (int) ($stats->processing ?? 0),
            'completed' => (int) ($stats->completed ?? 0),
            'rejected' => (int) ($stats->rejected ?? 0),
        ];
    }

    private function buildPaymentStats($query): array
    {
        $stats = (clone $query)
            ->selectRaw("SUM(CASE WHEN payment_status = 'paid' THEN 1 ELSE 0 END) as paid")
            ->selectRaw("SUM(CASE WHEN payment_status = 'paid_pending' THEN 1 ELSE 0 END) as pending")
            ->selectRaw("SUM(CASE WHEN payment_status = 'unpaid' THEN 1 ELSE 0 END) as unpaid")
            ->first();

        return [
            'paid' => (int) ($stats->paid ?? 0),
            'pending' => (int) ($stats->pending ?? 0),
            'unpaid' => (int) ($stats->unpaid ?? 0),
        ];
    }

    private function paymentAmountFor(Demande $demande): int
    {
        return $this->requestFees()[$demande->request_type] ?? 3000;
    }

    private function buildAgentMailMergeByType(User $agent): array
    {
        $types = $this->requestTypes();
        $result = [];
        $counts = Demande::where('agent_id', $agent->id)
            ->where('payment_status', 'paid')
            ->selectRaw('request_type, COUNT(*) as total')
            ->groupBy('request_type')
            ->pluck('total', 'request_type');

        foreach ($types as $typeKey => $typeLabel) {
            $count = (int) ($counts[$typeKey] ?? 0);
            $fullLots = intdiv($count, 10);

            $result[$typeKey] = [
                'label' => $typeLabel,
                'count' => $count,
                'full_lots' => $fullLots,
                'remainder' => $count % 10,
            ];
        }

        return $result;
    }

    private function buildAgentMetrics($query): array
    {
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();
        $driver = (string) DB::connection()->getDriverName();
        $avgProcessingExpression = match ($driver) {
            'mysql', 'mariadb' => "AVG(CASE WHEN assigned_at IS NOT NULL AND processed_at IS NOT NULL THEN TIMESTAMPDIFF(SECOND, assigned_at, processed_at) / 3600 END) as avg_processing_hours",
            'sqlite' => "AVG(CASE WHEN assigned_at IS NOT NULL AND processed_at IS NOT NULL THEN (julianday(processed_at) - julianday(assigned_at)) * 24.0 END) as avg_processing_hours",
            'pgsql' => "AVG(CASE WHEN assigned_at IS NOT NULL AND processed_at IS NOT NULL THEN EXTRACT(EPOCH FROM (processed_at - assigned_at)) / 3600 END) as avg_processing_hours",
            default => "AVG(CASE WHEN assigned_at IS NOT NULL AND processed_at IS NOT NULL THEN TIMESTAMPDIFF(SECOND, assigned_at, processed_at) / 3600 END) as avg_processing_hours",
        };

        $stats = (clone $query)
            ->selectRaw("SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as completed", [Demande::STATUS_COMPLETED])
            ->selectRaw("SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as rejected", [Demande::STATUS_REJECTED])
            ->selectRaw("SUM(CASE WHEN status = ? AND processed_at BETWEEN ? AND ? THEN 1 ELSE 0 END) as monthly_completed", [Demande::STATUS_COMPLETED, $monthStart, $monthEnd])
            ->selectRaw($avgProcessingExpression)
            ->selectRaw("SUM(CASE WHEN payment_status = 'paid_pending' THEN 1 ELSE 0 END) as paid_pending")
            ->selectRaw("SUM(CASE WHEN payment_status = 'paid' THEN 1 ELSE 0 END) as paid")
            ->first();

        return [
            'completed' => (int) ($stats->completed ?? 0),
            'rejected' => (int) ($stats->rejected ?? 0),
            'monthly_completed' => (int) ($stats->monthly_completed ?? 0),
            'avg_processing_hours' => $stats->avg_processing_hours !== null ? (int) round((float) $stats->avg_processing_hours) : null,
            'paid_pending' => (int) ($stats->paid_pending ?? 0),
            'paid' => (int) ($stats->paid ?? 0),
        ];
    }

    private function buildUserCounts(): array
    {
        $stats = User::query()
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN role = ? THEN 1 ELSE 0 END) as admins", [User::ROLE_ADMIN])
            ->selectRaw("SUM(CASE WHEN role = ? THEN 1 ELSE 0 END) as agents", [User::ROLE_AGENT])
            ->selectRaw("SUM(CASE WHEN role = ? THEN 1 ELSE 0 END) as citoyens", [User::ROLE_CITOYEN])
            ->selectRaw('SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active')
            ->first();

        return [
            'total' => (int) ($stats->total ?? 0),
            'admins' => (int) ($stats->admins ?? 0),
            'agents' => (int) ($stats->agents ?? 0),
            'citoyens' => (int) ($stats->citoyens ?? 0),
            'active' => (int) ($stats->active ?? 0),
        ];
    }

    private function ratePercent(int $value, int $total): int
    {
        if ($total < 1) {
            return 0;
        }

        return (int) round(($value / $total) * 100);
    }

    private function loadSettings(): array
    {
        $path = storage_path('app/portal-settings.json');
        if (! file_exists($path)) {
            return $this->defaultSettings();
        }

        $decoded = json_decode(file_get_contents($path), true);
        if (! is_array($decoded)) {
            return $this->defaultSettings();
        }

        return array_replace($this->defaultSettings(), $decoded);
    }

    private function saveSettings(array $data): void
    {
        file_put_contents(
            storage_path('app/portal-settings.json'),
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }

    private function defaultSettings(): array
    {
        return [
            'site_name'        => config('app.name', 'Portail Mairie'),
            'site_description' => '',
            'contact_email'    => '',
            'contact_phone'    => '',
            'contact_address'  => '',
            'allow_register'   => 1,
            'maintenance_mode' => 0,
            'request_fees'     => $this->defaultRequestFees(),
            'mayor_signature_path' => '',
            'mayor_signature_name' => '',
        ];
    }

    private function hasMayorSignatureConfigured(?array $settings = null): bool
    {
        $settings ??= $this->portalSettings();
        $path = (string) ($settings['mayor_signature_path'] ?? '');

        return $path !== '' && Storage::disk('local')->exists($path);
    }

    private function portalSettings(): array
    {
        if ($this->portalSettingsCache !== null) {
            return $this->portalSettingsCache;
        }

        $this->portalSettingsCache = $this->loadSettings();

        return $this->portalSettingsCache;
    }

    private function buildCitizenProfilePayload(array $payload, User $user): array
    {
        $firstName = $payload['first_name'] ?? $user->first_name;
        $lastName = $payload['last_name'] ?? $user->last_name;
        $email = $payload['email'] ?? $user->email;
        $fullName = trim(($firstName ?? '') . ' ' . ($lastName ?? ''));
        $birthDate = $payload['birth_date'] ?? $user->birth_date;
        $registerNumber = $payload['register_number'] ?? $user->register_number;

        return [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'name' => $fullName !== '' ? $fullName : (string) Str::before((string) $email, '@'),
            'email' => $email,
            'phone' => $payload['phone'] ?? $user->phone,
            'address' => $payload['address'] ?? $user->address,
            'birth_date' => $birthDate,
            'birth_place' => $payload['birth_place'] ?? $user->birth_place,
            'register_number' => $registerNumber,
            'citizen_number' => $this->generateCitizenNumber($birthDate, $registerNumber),
        ];
    }

    private function generateCitizenNumber(mixed $birthDate, ?string $registerNumber): ?string
    {
        if (! $birthDate || ! $registerNumber) {
            return null;
        }

        $birthPart = $birthDate instanceof \DateTimeInterface
            ? $birthDate->format('Ymd')
            : preg_replace('/[^0-9]/', '', (string) $birthDate);

        $registerPart = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $registerNumber));
        if ($birthPart === '' || $registerPart === '') {
            return null;
        }

        $tail = Str::padLeft(Str::substr($registerPart, -6), 6, '0');
        $checksum = strtoupper(substr(hash('crc32b', $birthPart . '|' . $registerPart), 0, 4));

        return 'CIT-' . $birthPart . '-' . $tail . '-' . $checksum;
    }

    private function isRegistrationOpen(?array $settings = null): bool
    {
        $settings ??= $this->portalSettings();

        return (bool) ($settings['allow_register'] ?? false) && ! $this->isCitizenMaintenanceEnabled($settings);
    }

    private function isCitizenMaintenanceEnabled(?array $settings = null): bool
    {
        $settings ??= $this->portalSettings();

        return (bool) ($settings['maintenance_mode'] ?? false);
    }

    private function ensureCitizenPortalAvailable(Request $request, User $user, ?array $settings = null): ?RedirectResponse
    {
        if (! $user->isCitoyen() || ! $this->isCitizenMaintenanceEnabled($settings)) {
            return null;
        }

        $request->session()->forget(self::SESSION_KEY);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('portal.auth')->with([
            'status' => 'Le portail citoyen est temporairement indisponible pour maintenance.',
            'status_type' => 'warning',
        ]);
    }

    private function authorizeDemandeAccess(User $user, Demande $demande): void
    {
        if ($user->isAdmin()) {
            return;
        }

        if ($user->isCitoyen() && $demande->user_id !== $user->id) {
            abort(403, 'Acces non autorise.');
        }

        if ($user->isAgent()) {
            $isOpenPending = $demande->status === Demande::STATUS_PENDING && $demande->agent_id === null;
            if ($demande->agent_id !== $user->id && ! $isOpenPending) {
                abort(403, 'Ce dossier n est pas rattache a votre compte.');
            }
        }
    }

    private function resolveMessageReceiverId(User $user, Demande $demande): ?int
    {
        if ($user->isCitoyen()) {
            return $demande->agent_id ?: User::where('role', User::ROLE_ADMIN)->value('id');
        }

        if ($user->isAgent() || $user->isAdmin()) {
            return $demande->user_id;
        }

        return null;
    }

    private function authorizeAgentAction(User $user, Demande $demande, bool $mustBeAssigned): void
    {
        if ($user->isAdmin()) {
            return;
        }

        if (! $user->isAgent()) {
            abort(403, 'Action reservee aux agents.');
        }

        if ($mustBeAssigned && $demande->agent_id !== $user->id) {
            abort(403, 'Vous devez etre assigne a ce dossier.');
        }

        if (! $mustBeAssigned && $demande->agent_id !== null && $demande->agent_id !== $user->id) {
            abort(403, 'Ce dossier est deja assigne a un autre agent.');
        }
    }

    /**
     * Add mayor signature to a PDF document
     * 
     * @param string $filePath Path to the original PDF in storage
     * @return string|null Path to the signed PDF, or null if failed
     */
    private function addSignatureToPdf(string $filePath): ?string
    {
        try {
            $settings = $this->portalSettings();
            $signaturePath = (string) ($settings['mayor_signature_path'] ?? '');
            
            if ($signaturePath === '' || ! Storage::disk('local')->exists($signaturePath)) {
                return null;
            }

            // Get the original PDF content
            $pdfContent = Storage::disk('local')->get($filePath);
            $signatureImageContent = Storage::disk('local')->get($signaturePath);

            // Create temporary directory for processing
            $tempDir = storage_path('app/temp/signatures/');
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            // Write temporary files
            $tempPdfPath = $tempDir . 'temp-' . uniqid() . '.pdf';
            $tempImagePath = $tempDir . 'temp-sig-' . uniqid() . '.png';
            $signedPdfPath = $tempDir . 'signed-' . uniqid() . '.pdf';

            file_put_contents($tempPdfPath, $pdfContent);
            file_put_contents($tempImagePath, $signatureImageContent);

            // Use FPDI to manipulate the PDF
            $pdf = new \setasign\Fpdi\Fpdi();
            
            // Get page count and process each page
            $pageCount = $pdf->setSourceFile($tempPdfPath);
            
            for ($i = 1; $i <= $pageCount; $i++) {
                $templateId = $pdf->importPage($i);
                $pdf->addPage();
                $pdf->useTemplate($templateId);
            }

            // Add signature image on the last page
            $lastPageWidth = $pdf->getPageWidth();
            $lastPageHeight = $pdf->getPageHeight();

            // Position signature at bottom right with some padding
            $signatureWidth = 40;
            $signatureHeight = 20;
            $x = $lastPageWidth - $signatureWidth - 10;
            $y = $lastPageHeight - $signatureHeight - 10;

            $pdf->setPage($pageCount);
            $pdf->image($tempImagePath, $x, $y, $signatureWidth, $signatureHeight);

            // Save the signed PDF
            $pdf->output('F', $signedPdfPath);

            // Move signed PDF to a storage-accessible location
            $storagePath = 'temp/signatures/' . basename($signedPdfPath);
            $signedContent = file_get_contents($signedPdfPath);
            Storage::disk('local')->put($storagePath, $signedContent);

            // Clean up temporary files
            @unlink($tempPdfPath);
            @unlink($tempImagePath);
            @unlink($signedPdfPath);

            // Schedule cleanup of the signed PDF after download (in 1 hour)
            // For now, we'll return the path and let the system clean up old temp files

            return $storagePath;
        } catch (\Exception $e) {
            \Log::error('Failed to add signature to PDF: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Display course detail page for citizen
     *
     * @param Request $request
     * @param CivicCourse $civicCourse
     * @return View
     */
    public function viewCourse(Request $request, CivicCourse $civicCourse): View
    {
        $user = $this->requireCurrentUser($request);
        
        // Verify course is active (citizen should only access active courses)
        if (!$civicCourse->is_active) {
            abort(404, 'This course is not available');
        }

        // Track course view
        $this->recordCourseView($user->id, $civicCourse->id);

        return view('portal.course-detail', [
            ...$this->baseViewData($user),
            'course' => $civicCourse,
            'pageTitle' => $civicCourse->title,
            'role' => 'citoyen',
        ]);
    }

    /**
     * Register citizen for an activity
     *
     * @param Request $request
     * @param CivicActivity $civicActivity
     * @return RedirectResponse
     */
    public function registerActivity(Request $request, CivicActivity $civicActivity): RedirectResponse
    {
        $user = $this->requireCurrentUser($request);
        
        // Verify activity is active
        if (!$civicActivity->is_active) {
            return redirect()->route('portal.citizen')
                ->with('error', 'This activity is not available');
        }

        // Check if user already registered
        $existingRegistration = \DB::table('activity_registrations')
            ->where('user_id', $user->id)
            ->where('civic_activity_id', $civicActivity->id)
            ->exists();

        if ($existingRegistration) {
            return redirect()->route('portal.citizen')
                ->with('warning', 'You are already registered for this activity');
        }

        // Check max participants limit if set
        if ($civicActivity->max_participants) {
            $registrationCount = \DB::table('activity_registrations')
                ->where('civic_activity_id', $civicActivity->id)
                ->count();

            if ($registrationCount >= $civicActivity->max_participants) {
                return redirect()->route('portal.citizen')
                    ->with('error', 'This activity is at full capacity');
            }
        }

        // Create registration
        \DB::table('activity_registrations')->insert([
            'user_id' => $user->id,
            'civic_activity_id' => $civicActivity->id,
            'registered_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Log the activity
        \Log::info('User registered for activity', [
            'user_id' => $user->id,
            'activity_id' => $civicActivity->id,
            'activity_title' => $civicActivity->title,
        ]);

        return redirect()->route('portal.citizen')
            ->with('success', 'You have successfully registered for: ' . $civicActivity->title);
    }

    /**
     * Record course view for tracking purposes
     */
    private function recordCourseView(int $userId, int $courseId): void
    {
        try {
            // Check if already viewed today
            $existingView = \DB::table('course_views')
                ->where('user_id', $userId)
                ->where('civic_course_id', $courseId)
                ->first();

            if ($existingView) {
                // Increment view count and update timestamp
                \DB::table('course_views')
                    ->where('id', $existingView->id)
                    ->update([
                        'view_count' => $existingView->view_count + 1,
                        'viewed_at' => now(),
                        'updated_at' => now(),
                    ]);
            } else {
                // Create new view record
                \DB::table('course_views')->insert([
                    'user_id' => $userId,
                    'civic_course_id' => $courseId,
                    'viewed_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } catch (\Exception $e) {
            // Log error but don't fail the request
            \Log::warning('Failed to record course view', [
                'user_id' => $userId,
                'course_id' => $courseId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}

