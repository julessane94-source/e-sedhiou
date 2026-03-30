<?php

namespace App\Http\Controllers\Web;

use App\Models\CivicActivity;
use App\Models\CivicCourse;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CivicAdminController extends \App\Http\Controllers\Controller
{
    private const SESSION_KEY = 'mairie_portal_auth';
    private ?User $currentUserCache = null;
    private bool $currentUserResolved = false;
    // ════════════════════════════════════════════════════════════════
    // COURSES
    // ════════════════════════════════════════════════════════════════

    public function indexCourses(Request $request): View
    {
        $courses = CivicCourse::orderBy('sort_order')->orderBy('created_at')->get();
        $user = $this->requireCurrentUser($request);

        return view('portal.admin-civic-courses', [
            ...$this->getBaseViewData($request, $user),
            'courses' => $courses,
            'pageTitle' => 'Gestion des cours citoyens',
        ]);
    }

    public function createCourse(Request $request): View
    {
        $user = $this->requireCurrentUser($request);

        return view('portal.admin-civic-course-form', [
            ...$this->getBaseViewData($request, $user),
            'course' => null,
            'pageTitle' => 'Créer un nouveau cours',
        ]);
    }

    public function storeCourse(Request $request): RedirectResponse
    {
        $payload = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:120', 'unique:civic_courses'],
            'description' => ['required', 'string', 'max:1000'],
            'icon_emoji' => ['required', 'string', 'max:10'],
            'content' => ['nullable', 'string'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
            'topics' => ['nullable', 'string'],
            'course_type' => ['required', 'string', 'in:online,hybrid,offline'],
            'difficulty_level' => ['required', 'string', 'in:beginner,intermediate,advanced'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'max:5120'], // 5 MB
            'document' => ['nullable', 'file', 'max:10240', 'mimes:pdf,doc,docx,xls,xlsx,txt,ppt,pptx'], // 10 MB
        ]);

        // Convertir les topics en array
        $topics = [];
        if (! empty($payload['topics'])) {
            $topics = array_filter(array_map('trim', explode("\n", $payload['topics'])));
        }

        // Nettoyer les champs numériques: convertir les chaînes vides en 0 ou 30
        $payload['sort_order'] = empty($payload['sort_order']) ? 0 : $payload['sort_order'];
        $payload['duration_minutes'] = empty($payload['duration_minutes']) ? 30 : $payload['duration_minutes'];

        // Traiter les fichiers uploadés
        $courseData = [
            ...$payload,
            'topics' => $topics ?: null,
            'is_active' => (bool) ($payload['is_active'] ?? true),
            'created_by' => $this->requireCurrentUser($request)->id,
        ];

        unset($courseData['image'], $courseData['document']);

        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $imagePath = $imageFile->store('civic-courses/images', 'public');
            $courseData['image_path'] = $imagePath;
            $courseData['image_name'] = $imageFile->getClientOriginalName();
        }

        if ($request->hasFile('document')) {
            $docFile = $request->file('document');
            $docPath = $docFile->store('civic-courses/documents', 'public');
            $courseData['document_path'] = $docPath;
            $courseData['document_name'] = $docFile->getClientOriginalName();
        }

        CivicCourse::create($courseData);

        return redirect()->route('portal.admin.civic_courses')->with([
            'status' => 'Cours créé avec succès.',
            'status_type' => 'success',
        ]);
    }

    public function editCourse(Request $request, CivicCourse $civicCourse): View
    {
        $user = $this->requireCurrentUser($request);

        return view('portal.admin-civic-course-form', [
            ...$this->getBaseViewData($request, $user),
            'course' => $civicCourse,
            'pageTitle' => 'Modifier le cours: ' . $civicCourse->title,
        ]);
    }

    public function updateCourse(Request $request, CivicCourse $civicCourse): RedirectResponse
    {
        $payload = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:120', 'unique:civic_courses,slug,' . $civicCourse->id],
            'description' => ['required', 'string', 'max:1000'],
            'icon_emoji' => ['required', 'string', 'max:10'],
            'content' => ['nullable', 'string'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
            'topics' => ['nullable', 'string'],
            'course_type' => ['required', 'string', 'in:online,hybrid,offline'],
            'difficulty_level' => ['required', 'string', 'in:beginner,intermediate,advanced'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'max:5120'], // 5 MB
            'document' => ['nullable', 'file', 'max:10240', 'mimes:pdf,doc,docx,xls,xlsx,txt,ppt,pptx'], // 10 MB
        ]);

        // Convertir les topics en array
        $topics = [];
        if (! empty($payload['topics'])) {
            $topics = array_filter(array_map('trim', explode("\n", $payload['topics'])));
        }

        // Nettoyer les champs numériques: convertir les chaînes vides en 0 ou 30
        $payload['sort_order'] = empty($payload['sort_order']) ? 0 : $payload['sort_order'];
        $payload['duration_minutes'] = empty($payload['duration_minutes']) ? 30 : $payload['duration_minutes'];

        // Préparer les données de mise à jour
        $updateData = [
            ...$payload,
            'topics' => $topics ?: null,
            'is_active' => (bool) ($payload['is_active'] ?? true),
            'updated_by' => $this->requireCurrentUser($request)->id,
        ];

        unset($updateData['image'], $updateData['document']);

        // Gérer l'image
        if ($request->hasFile('image')) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($civicCourse->image_path);
            $imageFile = $request->file('image');
            $imagePath = $imageFile->store('civic-courses/images', 'public');
            $updateData['image_path'] = $imagePath;
            $updateData['image_name'] = $imageFile->getClientOriginalName();
        }

        // Gérer le document
        if ($request->hasFile('document')) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($civicCourse->document_path);
            $docFile = $request->file('document');
            $docPath = $docFile->store('civic-courses/documents', 'public');
            $updateData['document_path'] = $docPath;
            $updateData['document_name'] = $docFile->getClientOriginalName();
        }

        $civicCourse->update($updateData);

        return redirect()->route('portal.admin.civic_courses')->with([
            'status' => 'Cours modifié avec succès.',
            'status_type' => 'success',
        ]);
    }

    public function destroyCourse(CivicCourse $civicCourse): RedirectResponse
    {
        $title = $civicCourse->title;
        
        // Supprimer les fichiers associés
        if ($civicCourse->image_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($civicCourse->image_path);
        }
        if ($civicCourse->document_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($civicCourse->document_path);
        }
        
        $civicCourse->delete();

        return redirect()->route('portal.admin.civic_courses')->with([
            'status' => "Cours \"$title\" supprimé avec succès.",
            'status_type' => 'success',
        ]);
    }

    // ════════════════════════════════════════════════════════════════
    // ACTIVITIES
    // ════════════════════════════════════════════════════════════════

    public function indexActivities(Request $request): View
    {
        $activities = CivicActivity::orderBy('sort_order')->orderBy('event_date')->get();
        $user = $this->requireCurrentUser($request);

        return view('portal.admin-civic-activities', [
            ...$this->getBaseViewData($request, $user),
            'activities' => $activities,
            'pageTitle' => 'Gestion des activités communautaires',
        ]);
    }

    public function createActivity(Request $request): View
    {
        $user = $this->requireCurrentUser($request);

        return view('portal.admin-civic-activity-form', [
            ...$this->getBaseViewData($request, $user),
            'activity' => null,
            'pageTitle' => 'Créer une nouvelle activité',
        ]);
    }

    public function storeActivity(Request $request): RedirectResponse
    {
        $payload = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:120', 'unique:civic_activities'],
            'description' => ['required', 'string', 'max:1000'],
            'icon_emoji' => ['required', 'string', 'max:10'],
            'content' => ['nullable', 'string'],
            'event_date' => ['nullable', 'date_format:Y-m-d'],
            'event_start_time' => ['nullable', 'date_format:H:i'],
            'event_end_time' => ['nullable', 'date_format:H:i'],
            'location' => ['required', 'string', 'max:500'],
            'location_details' => ['nullable', 'string'],
            'target_audience' => ['required', 'string', 'max:255'],
            'max_participants' => ['nullable', 'integer', 'min:1'],
            'status' => ['required', 'string', 'in:upcoming,ongoing,completed,cancelled'],
            'activity_type' => ['required', 'string', 'in:community,workshop,forum,celebration'],
            'frequency' => ['required', 'string', 'in:once,weekly,monthly,quarterly'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'max:5120'], // 5 MB
            'document' => ['nullable', 'file', 'max:10240', 'mimes:pdf,doc,docx,xls,xlsx,txt,ppt,pptx'], // 10 MB
        ]);

        // Nettoyer les champs numériques: convertir les chaînes vides en NULL ou 0
        $payload['max_participants'] = empty($payload['max_participants']) ? null : $payload['max_participants'];
        $payload['sort_order'] = empty($payload['sort_order']) ? 0 : $payload['sort_order'];

        // Préparer les données de création
        $activityData = [
            ...$payload,
            'is_active' => (bool) ($payload['is_active'] ?? true),
            'created_by' => $this->requireCurrentUser($request)->id,
        ];

        unset($activityData['image'], $activityData['document']);

        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $imagePath = $imageFile->store('civic-activities/images', 'public');
            $activityData['image_path'] = $imagePath;
            $activityData['image_name'] = $imageFile->getClientOriginalName();
        }

        if ($request->hasFile('document')) {
            $docFile = $request->file('document');
            $docPath = $docFile->store('civic-activities/documents', 'public');
            $activityData['document_path'] = $docPath;
            $activityData['document_name'] = $docFile->getClientOriginalName();
        }

        CivicActivity::create($activityData);

        return redirect()->route('portal.admin.civic_activities')->with([
            'status' => 'Activité créée avec succès.',
            'status_type' => 'success',
        ]);
    }

    public function editActivity(Request $request, CivicActivity $civicActivity): View
    {
        $user = $this->requireCurrentUser($request);

        return view('portal.admin-civic-activity-form', [
            ...$this->getBaseViewData($request, $user),
            'activity' => $civicActivity,
            'pageTitle' => 'Modifier l\'activité: ' . $civicActivity->title,
        ]);
    }

    public function updateActivity(Request $request, CivicActivity $civicActivity): RedirectResponse
    {
        $payload = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:120', 'unique:civic_activities,slug,' . $civicActivity->id],
            'description' => ['required', 'string', 'max:1000'],
            'icon_emoji' => ['required', 'string', 'max:10'],
            'content' => ['nullable', 'string'],
            'event_date' => ['nullable', 'date_format:Y-m-d'],
            'event_start_time' => ['nullable', 'date_format:H:i'],
            'event_end_time' => ['nullable', 'date_format:H:i'],
            'location' => ['required', 'string', 'max:500'],
            'location_details' => ['nullable', 'string'],
            'target_audience' => ['required', 'string', 'max:255'],
            'max_participants' => ['nullable', 'integer', 'min:1'],
            'status' => ['required', 'string', 'in:upcoming,ongoing,completed,cancelled'],
            'activity_type' => ['required', 'string', 'in:community,workshop,forum,celebration'],
            'frequency' => ['required', 'string', 'in:once,weekly,monthly,quarterly'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'max:5120'], // 5 MB
            'document' => ['nullable', 'file', 'max:10240', 'mimes:pdf,doc,docx,xls,xlsx,txt,ppt,pptx'], // 10 MB
        ]);

        // Nettoyer les champs numériques: convertir les chaînes vides en NULL ou 0
        $payload['max_participants'] = empty($payload['max_participants']) ? null : $payload['max_participants'];
        $payload['sort_order'] = empty($payload['sort_order']) ? 0 : $payload['sort_order'];

        // Préparer les données de mise à jour
        $updateData = [
            ...$payload,
            'is_active' => (bool) ($payload['is_active'] ?? true),
            'updated_by' => $this->requireCurrentUser($request)->id,
        ];

        unset($updateData['image'], $updateData['document']);

        // Gérer l'image
        if ($request->hasFile('image')) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($civicActivity->image_path);
            $imageFile = $request->file('image');
            $imagePath = $imageFile->store('civic-activities/images', 'public');
            $updateData['image_path'] = $imagePath;
            $updateData['image_name'] = $imageFile->getClientOriginalName();
        }

        // Gérer le document
        if ($request->hasFile('document')) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($civicActivity->document_path);
            $docFile = $request->file('document');
            $docPath = $docFile->store('civic-activities/documents', 'public');
            $updateData['document_path'] = $docPath;
            $updateData['document_name'] = $docFile->getClientOriginalName();
        }

        $civicActivity->update($updateData);

        return redirect()->route('portal.admin.civic_activities')->with([
            'status' => 'Activité modifiée avec succès.',
            'status_type' => 'success',
        ]);
    }

    public function destroyActivity(CivicActivity $civicActivity): RedirectResponse
    {
        $title = $civicActivity->title;
        
        // Supprimer les fichiers associés
        if ($civicActivity->image_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($civicActivity->image_path);
        }
        if ($civicActivity->document_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($civicActivity->document_path);
        }
        
        $civicActivity->delete();

        return redirect()->route('portal.admin.civic_activities')->with([
            'status' => "Activité \"$title\" supprimée avec succès.",
            'status_type' => 'success',
        ]);
    }

    /**
     * Récupérer les données de base pour les vues
     */
    private function getBaseViewData(Request $request, ?User $user): array
    {
        $runtimeWordpressUrl = rtrim($request->getSchemeAndHttpHost() . '/mairie_wp', '/');
        $configuredWordpressUrl = rtrim((string) config('mairie.wordpress_url'), '/');

        return [
            'currentUser' => $user,
            'portalLinks' => [],
            'wordpressUrl' => '' !== $runtimeWordpressUrl ? $runtimeWordpressUrl : $configuredWordpressUrl,
        ];
    }

    /**
     * Récupérer l'utilisateur actuellement connecté
     */
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

    /**
     * Exiger que l'utilisateur soit connecté
     */
    private function requireCurrentUser(Request $request): User
    {
        return $this->currentUser($request) ?? abort(401);
    }

    /**
     * View registrations for a specific activity
     */
    public function viewActivityRegistrations(Request $request, CivicActivity $civicActivity): View
    {
        $user = $this->requireCurrentUser($request);

        // Get all registrations for this activity
        $registrations = \DB::table('activity_registrations')
            ->where('civic_activity_id', $civicActivity->id)
            ->join('users', 'activity_registrations.user_id', '=', 'users.id')
            ->select(
                'activity_registrations.*',
                'users.first_name',
                'users.last_name',
                'users.email',
                'users.phone'
            )
            ->orderBy('activity_registrations.registered_at', 'desc')
            ->get();

        return view('portal.admin-civic-activity-registrations', [
            ...$this->getBaseViewData($request, $user),
            'activity' => $civicActivity,
            'registrations' => $registrations,
            'pageTitle' => 'Inscriptions - ' . $civicActivity->title,
        ]);
    }

    /**
     * View who has read/accessed a specific course
     */
    public function viewCourseReaders(Request $request, CivicCourse $civicCourse): View
    {
        $user = $this->requireCurrentUser($request);

        // Get all users who have viewed this course
        $viewers = \DB::table('course_views')
            ->where('civic_course_id', $civicCourse->id)
            ->join('users', 'course_views.user_id', '=', 'users.id')
            ->select(
                'course_views.*',
                'users.first_name',
                'users.last_name',
                'users.email',
                'users.phone'
            )
            ->orderBy('course_views.viewed_at', 'desc')
            ->get();

        return view('portal.admin-civic-course-readers', [
            ...$this->getBaseViewData($request, $user),
            'course' => $civicCourse,
            'viewers' => $viewers,
            'pageTitle' => 'Lecteurs - ' . $civicCourse->title,
        ]);
    }
}
