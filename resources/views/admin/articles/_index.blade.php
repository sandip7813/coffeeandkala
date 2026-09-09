{{-- Shared Features/Journals article list. Expects: $articles, $categories, $filters, $hasActiveFilters, $type ('features'|'journals'), $icon. --}}
@php
    $user = auth()->user();
    $canEdit = $user?->can("edit-{$type}");
    $canDelete = $user?->can("delete-{$type}");
    $canChangeStatus = $user?->can("change-{$type}-status");
    $canApprove = $user?->can("approve-{$type}");
    // Public route names are singular for journals ('journal.article') but
    // plural for features ('features.article'), unlike everything else
    // here which is keyed off the plural $type ('features'|'journals').
    $frontendArticleRoute = $type === 'features' ? 'features.article' : 'journal.article';
@endphp

<div class="collapse {{ $hasActiveFilters ? 'show' : '' }}" id="{{ $type }}Search">
    <x-adminlte-card class="mb-3">
        <form method="GET" action="{{ route("admin.{$type}.index") }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label for="filter-{{ $type }}-title" class="form-label">{{ __('adminlte.name') }}</label>
                <input type="text" id="filter-{{ $type }}-title" name="title" class="form-control" placeholder="{{ __('Search by title') }}" value="{{ $filters['title'] ?? '' }}">
            </div>
            <div class="col-md-2">
                <label for="filter-{{ $type }}-category" class="form-label">{{ __('Category') }}</label>
                <select id="filter-{{ $type }}-category" name="category_id" class="form-select">
                    <option value="">{{ __('All') }}</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(($filters['category_id'] ?? '') == $category->id)>{{ $category->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-1">
                <label for="filter-{{ $type }}-status" class="form-label">{{ __('adminlte.status') }}</label>
                <select id="filter-{{ $type }}-status" name="status" class="form-select">
                    <option value="">{{ __('All') }}</option>
                    <option value="draft" @selected(($filters['status'] ?? '') === 'draft')>{{ __('Draft') }}</option>
                    <option value="pending" @selected(($filters['status'] ?? '') === 'pending')>{{ __('Pending') }}</option>
                    <option value="active" @selected(($filters['status'] ?? '') === 'active')>{{ __('Active') }}</option>
                    <option value="inactive" @selected(($filters['status'] ?? '') === 'inactive')>{{ __('Inactive') }}</option>
                </select>
            </div>
            <div class="col-md-4">
                <label for="filter-{{ $type }}-created-by" class="form-label">{{ __('Created By') }}</label>
                <select id="filter-{{ $type }}-created-by" name="created_by" class="form-control"
                        data-select2-search
                        data-select2-url="{{ route("admin.{$type}.search-creators") }}"
                        data-placeholder="{{ __('Type at least 3 characters…') }}">
                    @if (filled($filters['created_by'] ?? null))
                        <option value="{{ $filters['created_by'] }}" selected>{{ $creatorFilterLabel }}</option>
                    @endif
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-1" aria-hidden="true"></i> {{ __('Search') }}
                </button>
                <a href="{{ route("admin.{$type}.index") }}" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-arrow-counterclockwise me-1" aria-hidden="true"></i> {{ __('Reset') }}
                </a>
            </div>
        </form>
    </x-adminlte-card>
</div>

<x-adminlte-card :icon="$icon" :title="__(ucfirst($type))" bodyClass="p-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>{{ __('adminlte.name') }}</th>
                    <th>{{ __('Category') }}</th>
                    <th>{{ __('Created By') }}</th>
                    @if ($canManageHomeSections ?? false)
                        <th>{{ __('Home Page') }}</th>
                        @if ($type === 'features')
                            <th>{{ __('Features Page') }}</th>
                        @endif
                    @endif
                    <th>{{ __('adminlte.status') }}</th>
                    <th class="text-end">{{ __('Action') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($articles as $article)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                @if ($article->featuredImage)
                                    <a href="{{ $article->featuredImage->large_url }}" data-fancybox="{{ $type }}" data-caption="{{ $article->title }}">
                                        <img src="{{ $article->featuredImage->thumbnail_url }}" alt="" class="rounded border" style="height:40px; width:40px; object-fit:cover; cursor:zoom-in;">
                                    </a>
                                @endif
                                <span class="fw-semibold">{{ $article->title }}</span>
                            </div>
                        </td>
                        <td>{{ $article->category?->title }}</td>
                        <td>
                            {{ $article->creator?->name ?? __('Unknown') }}
                            @if ($article->approver)
                                <div class="small text-muted">{{ __('Approved by') }} {{ $article->approver->name }}</div>
                            @endif
                        </td>
                        @if ($canManageHomeSections ?? false)
                            @php $memberships = $homeSectionMemberships[$article->id] ?? []; @endphp
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    @foreach (\App\Support\HomeSections::labelsFor($article->type) as $section => $label)
                                        <div class="form-check form-switch mb-0" title="{{ $article->status !== 'active' && ! in_array($section, $memberships, true) ? __('Only active articles can be added.') : $label }}">
                                            <input
                                                type="checkbox" class="form-check-input" role="switch"
                                                id="home-section-{{ $article->id }}-{{ $section }}"
                                                @checked(in_array($section, $memberships, true))
                                                @disabled($article->status !== 'active' && ! in_array($section, $memberships, true))
                                                data-home-section-toggle
                                                data-section-label="{{ $label }}"
                                                data-article-title="{{ $article->title }}"
                                                data-toggle-url="{{ route("admin.{$type}.home-sections.toggle", [$article, $section]) }}"
                                            >
                                            <label class="form-check-label small" for="home-section-{{ $article->id }}-{{ $section }}">{{ $label }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                            @if ($type === 'features')
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        @foreach (\App\Support\HomeSections::FEATURES_PAGE_LABELS as $section => $label)
                                            <div class="form-check form-switch mb-0" title="{{ $article->status !== 'active' && ! in_array($section, $memberships, true) ? __('Only active articles can be added.') : $label }}">
                                                <input
                                                    type="checkbox" class="form-check-input" role="switch"
                                                    id="home-section-{{ $article->id }}-{{ $section }}"
                                                    @checked(in_array($section, $memberships, true))
                                                    @disabled($article->status !== 'active' && ! in_array($section, $memberships, true))
                                                    data-home-section-toggle
                                                    data-section-label="{{ $label }}"
                                                    data-article-title="{{ $article->title }}"
                                                    data-toggle-url="{{ route("admin.{$type}.home-sections.toggle", [$article, $section]) }}"
                                                >
                                                <label class="form-check-label small" for="home-section-{{ $article->id }}-{{ $section }}">{{ Str::before($label, ' (Features page)') }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                            @endif
                        @endif
                        <td>
                            @if ($article->status === 'active')
                                <span class="badge bg-success">{{ __('Active') }}</span>
                            @elseif ($article->status === 'pending')
                                <span class="badge bg-warning text-dark">{{ __('Pending') }}</span>
                            @elseif ($article->status === 'draft')
                                <span class="badge bg-info text-dark">{{ __('Draft') }}</span>
                            @else
                                <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <x-admin.row-actions>
                                @if ($article->status === 'active')
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route($frontendArticleRoute, ['category' => $article->category->slug, 'article' => $article->slug]) }}" target="_blank" rel="noopener">
                                            <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i>
                                            <span>{{ $type === 'features' ? __('View Article') : __('View Blog') }}</span>
                                        </a>
                                    </li>
                                @endif
                                @if ($canEdit)
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route("admin.{$type}.edit", $article) }}">
                                            <i class="bi bi-pencil" aria-hidden="true"></i>
                                            <span>{{ __('adminlte.edit') }}</span>
                                        </a>
                                    </li>
                                @endif
                                @if ($canApprove && $article->status === 'pending')
                                    <li>
                                        <form method="POST" action="{{ route("admin.{$type}.approve", $article) }}">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="dropdown-item d-flex align-items-center gap-2">
                                                <i class="bi bi-check-circle" aria-hidden="true"></i>
                                                <span>{{ __('Approve') }}</span>
                                            </button>
                                        </form>
                                    </li>
                                @endif
                                @if ($canChangeStatus && ! in_array($article->status, ['pending', 'draft'], true))
                                    <li>
                                        <form method="POST" action="{{ route("admin.{$type}.status.update", $article) }}"
                                              data-confirm-toggle
                                              data-confirm-title="{{ $article->status === 'active' ? __('Deactivate this article?') : __('Activate this article?') }}"
                                              data-confirm-text="{{ $article->title }} will be marked as {{ $article->status === 'active' ? __('inactive') : __('active') }}."
                                              data-confirm-button="{{ __('Yes, change status') }}"
                                              data-cancel-button="{{ __('adminlte.cancel') }}">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="dropdown-item d-flex align-items-center gap-2">
                                                <i class="bi bi-toggle2-on" aria-hidden="true"></i>
                                                <span>{{ __('Change status') }}</span>
                                            </button>
                                        </form>
                                    </li>
                                @endif
                                @if ($canDelete)
                                    <li>
                                        <form method="POST" action="{{ route("admin.{$type}.destroy", $article) }}"
                                              data-confirm-delete
                                              data-confirm-title="{{ __('Delete this article?') }}"
                                              data-confirm-text="{{ $article->title }} {{ __('will be permanently deleted.') }}"
                                              data-confirm-button="{{ __('adminlte.delete') }}"
                                              data-cancel-button="{{ __('adminlte.cancel') }}"
                                              data-loading-text="{{ __('Deleting article…') }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item d-flex align-items-center gap-2 text-danger">
                                                <i class="bi bi-trash" aria-hidden="true"></i>
                                                <span>{{ __('adminlte.delete') }}</span>
                                            </button>
                                        </form>
                                    </li>
                                @endif
                            </x-admin.row-actions>
                        </td>
                    </tr>
                @empty
                    <tr>
                        @php
                            $colspan = 5;
                            if ($canManageHomeSections ?? false) {
                                $colspan += $type === 'features' ? 2 : 1;
                            }
                        @endphp
                        <td colspan="{{ $colspan }}" class="text-center text-muted py-4">{{ __('No articles found.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($articles->hasPages())
        <div class="p-3">{{ $articles->links() }}</div>
    @endif
</x-adminlte-card>
