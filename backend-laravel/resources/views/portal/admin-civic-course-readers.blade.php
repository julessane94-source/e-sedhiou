@extends('portal.layout')

@section('content')
    @include('portal.components.admin-nav')

    <section class="hero">
        <div class="hero__panel">
            <span class="eyebrow">Administration - Citoyenneté</span>
            <h1 class="tw:font-semibold">{{ $pageTitle }}</h1>
            <p class="muted">Liste des citoyens qui ont consulté ce cours</p>
            <div class="actions tw:mt-3">
                <a class="button button--ghost" href="{{ route('portal.admin.civic_courses') }}">← Retour aux cours</a>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="panel tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95 tw:p-6">
            <div class="tw:mb-6">
                <div class="tw:flex tw:items-center tw:gap-3">
                    <span class="tw:text-4xl">{{ $course->icon_emoji ?? '📚' }}</span>
                    <div>
                        <h2 class="tw:text-lg tw:font-semibold">{{ $course->title }}</h2>
                        <p class="tw:text-sm tw:text-gray-600">
                            📝 {{ $course->description }}
                        </p>
                    </div>
                </div>
            </div>

            @if($viewers->count() > 0)
                <div class="tw:overflow-x-auto">
                    <table class="tw:w-full tw:text-sm">
                        <thead class="tw:bg-emerald-50 tw:border-b tw:border-emerald-200">
                            <tr>
                                <th class="tw:text-left tw:px-4 tw:py-3 tw:font-semibold">Nom</th>
                                <th class="tw:text-left tw:px-4 tw:py-3 tw:font-semibold">Email</th>
                                <th class="tw:text-left tw:px-4 tw:py-3 tw:font-semibold">Téléphone</th>
                                <th class="tw:text-left tw:px-4 tw:py-3 tw:font-semibold">Première consultation</th>
                                <th class="tw:text-left tw:px-4 tw:py-3 tw:font-semibold">Dernière consultation</th>
                                <th class="tw:text-center tw:px-4 tw:py-3 tw:font-semibold">Nombre de vues</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($viewers as $view)
                                <tr class="tw:border-b tw:border-gray-200 tw:hover:tw:bg-gray-50">
                                    <td class="tw:px-4 tw:py-3 tw:font-medium">
                                        {{ $view->first_name }} {{ $view->last_name }}
                                    </td>
                                    <td class="tw:px-4 tw:py-3">
                                        <a href="mailto:{{ $view->email }}" class="tw:text-emerald-600 tw:hover:tw:underline">
                                            {{ $view->email }}
                                        </a>
                                    </td>
                                    <td class="tw:px-4 tw:py-3">
                                        @if($view->phone)
                                            <a href="tel:{{ $view->phone }}" class="tw:text-emerald-600 tw:hover:tw:underline">
                                                {{ $view->phone }}
                                            </a>
                                        @else
                                            <span class="tw:text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="tw:px-4 tw:py-3">
                                        @if($view->created_at)
                                            {{ \Carbon\Carbon::parse($view->created_at)->format('d/m/Y H:i') }}
                                        @else
                                            <span class="tw:text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="tw:px-4 tw:py-3">
                                        @if($view->viewed_at)
                                            <div class="tw:flex tw:items-center tw:gap-1">
                                                <i class="bi bi-calendar3 tw:text-emerald-600"></i>
                                                {{ \Carbon\Carbon::parse($view->viewed_at)->format('d/m/Y H:i') }}
                                            </div>
                                        @else
                                            <span class="tw:text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="tw:text-center">
                                        <span class="tw:inline-block tw:px-3 tw:py-1 tw:bg-emerald-100 tw:text-emerald-800 tw:rounded-full tw:text-xs tw:font-medium">
                                            {{ $view->view_count }} 👁
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="tw:mt-6 tw:p-4 tw:bg-emerald-50 tw:rounded-lg tw:border tw:border-emerald-200">
                    <p class="tw:text-sm tw:text-emerald-900">
                        <strong>Nombre de citoyens qui ont consulté:</strong> {{ $viewers->count() }}
                    </p>
                    <p class="tw:text-sm tw:text-emerald-900 tw:mt-1">
                        <strong>Nombre total de consultations:</strong> {{ $viewers->sum('view_count') }}
                    </p>
                </div>
            @else
                <div class="panel tw:rounded-2xl tw:border tw:border-amber-200/60 tw:bg-amber-50/50 tw:p-6">
                    <p class="muted tw:text-center">
                        <i class="bi bi-info-circle me-2"></i>
                        Aucun citoyen n'a consulté ce cours pour le moment.
                    </p>
                </div>
            @endif
        </div>
    </section>
@endsection
