{{-- Navigation Admin avec scroll horizontal --}}
<div class="tw:mb-6 tw:overflow-x-auto tw:pb-2 -tw:mx-4 tw:px-4 md:tw:mx-0 md:tw:px-0">
    <div class="tw:flex tw:gap-2 tw:flex-nowrap">
        <a href="{{ route('portal.admin') }}" class="tw:inline-flex tw:items-center tw:px-4 tw:py-2 tw:rounded-full tw:font-semibold tw:whitespace-nowrap {{ request()->routeIs('portal.admin') ? 'tw:bg-emerald-700 tw:text-white tw:shadow-lg' : 'tw:bg-gray-200 tw:text-gray-800 hover:tw:bg-gray-300' }} tw:transition-all">
            📊 Tableau de bord
        </a>
        <a href="{{ route('portal.admin.demandes') }}" class="tw:inline-flex tw:items-center tw:px-4 tw:py-2 tw:rounded-full tw:font-semibold tw:whitespace-nowrap {{ request()->routeIs('portal.admin.demandes') ? 'tw:bg-emerald-700 tw:text-white tw:shadow-lg' : 'tw:bg-gray-200 tw:text-gray-800 hover:tw:bg-gray-300' }} tw:transition-all">
            📋 Demandes
        </a>
        <a href="{{ route('portal.admin.citoyens') }}" class="tw:inline-flex tw:items-center tw:px-4 tw:py-2 tw:rounded-full tw:font-semibold tw:whitespace-nowrap {{ request()->routeIs('portal.admin.citoyens') ? 'tw:bg-emerald-700 tw:text-white tw:shadow-lg' : 'tw:bg-gray-200 tw:text-gray-800 hover:tw:bg-gray-300' }} tw:transition-all">
            👥 Utilisateurs
        </a>
        <a href="{{ route('portal.admin.citizen_registry') }}" class="tw:inline-flex tw:items-center tw:px-4 tw:py-2 tw:rounded-full tw:font-semibold tw:whitespace-nowrap {{ request()->routeIs('portal.admin.citizen_registry') ? 'tw:bg-emerald-700 tw:text-white tw:shadow-lg' : 'tw:bg-gray-200 tw:text-gray-800 hover:tw:bg-gray-300' }} tw:transition-all">
            📝 Registre
        </a>
        <a href="{{ route('portal.admin.agents') }}" class="tw:inline-flex tw:items-center tw:px-4 tw:py-2 tw:rounded-full tw:font-semibold tw:whitespace-nowrap {{ request()->routeIs('portal.admin.agents') ? 'tw:bg-emerald-700 tw:text-white tw:shadow-lg' : 'tw:bg-gray-200 tw:text-gray-800 hover:tw:bg-gray-300' }} tw:transition-all">
            👨‍💼 Agents
        </a>
        <a href="{{ route('portal.admin.messages') }}" class="tw:inline-flex tw:items-center tw:px-4 tw:py-2 tw:rounded-full tw:font-semibold tw:whitespace-nowrap {{ request()->routeIs('portal.admin.messages') ? 'tw:bg-emerald-700 tw:text-white tw:shadow-lg' : 'tw:bg-gray-200 tw:text-gray-800 hover:tw:bg-gray-300' }} tw:transition-all">
            💬 Messages
        </a>
        <a href="{{ route('portal.admin.stats') }}" class="tw:inline-flex tw:items-center tw:px-4 tw:py-2 tw:rounded-full tw:font-semibold tw:whitespace-nowrap {{ request()->routeIs('portal.admin.stats') ? 'tw:bg-emerald-700 tw:text-white tw:shadow-lg' : 'tw:bg-gray-200 tw:text-gray-800 hover:tw:bg-gray-300' }} tw:transition-all">
            📈 Statistiques
        </a>
        <a href="{{ route('portal.admin.civic_courses') }}" class="tw:inline-flex tw:items-center tw:px-4 tw:py-2 tw:rounded-full tw:font-semibold tw:whitespace-nowrap {{ request()->routeIs('portal.admin.civic_courses*') ? 'tw:bg-emerald-700 tw:text-white tw:shadow-lg' : 'tw:bg-gray-200 tw:text-gray-800 hover:tw:bg-gray-300' }} tw:transition-all">
            📚 Cours citoyens
        </a>
        <a href="{{ route('portal.admin.civic_activities') }}" class="tw:inline-flex tw:items-center tw:px-4 tw:py-2 tw:rounded-full tw:font-semibold tw:whitespace-nowrap {{ request()->routeIs('portal.admin.civic_activities*') ? 'tw:bg-emerald-700 tw:text-white tw:shadow-lg' : 'tw:bg-gray-200 tw:text-gray-800 hover:tw:bg-gray-300' }} tw:transition-all">
            🎯 Activités
        </a>
        <a href="{{ route('portal.admin.settings') }}" class="tw:inline-flex tw:items-center tw:px-4 tw:py-2 tw:rounded-full tw:font-semibold tw:whitespace-nowrap {{ request()->routeIs('portal.admin.settings') ? 'tw:bg-emerald-700 tw:text-white tw:shadow-lg' : 'tw:bg-gray-200 tw:text-gray-800 hover:tw:bg-gray-300' }} tw:transition-all">
            ⚙️ Paramètres
        </a>
    </div>
</div>
