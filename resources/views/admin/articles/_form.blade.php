{{--
    Shared Features/Journals article form (create + edit). Expects: $type
    ('features'|'journals'), $article (Article, new for create — with
    sections.image/sections.galleryImages/sections.videoCompanionImage/
    faqs/featuredImage eager loaded for edit), $categories.

    The Essentials tab is mandatory; the Content tab (sections) and FAQs are
    entirely optional — an article's body can be filled in later. Each
    section's Active/Inactive toggle only appears on the edit form (a
    section always starts active, so it isn't needed on Add Article).
--}}
@php
    $isEdit = $article->exists;
    $action = $isEdit ? route("admin.{$type}.update", $article) : route("admin.{$type}.store");
    $canBeDrafted = ! $isEdit || $article->canBeDrafted();
    $canApprove = auth()->user()?->can("approve-{$type}");
    $submitLabel = $canApprove ? __('Publish') : __('Submit for Review');
@endphp

<style>
    .article-block-enter { opacity: 0; transform: translateY(-12px); transition: opacity .25s ease, transform .25s ease; }
    .article-block-enter-active { opacity: 1; transform: none; }
    .article-section--inactive { background-color: rgba(108, 117, 125, .1); transition: background-color .2s ease; }
    .article-section--inactive > .card-body { opacity: .75; }
    .article-section-drag-handle { cursor: grab; }
    .article-section-drag-handle:active { cursor: grabbing; }
    .article-section-drag-handle [data-no-drag] { cursor: default; }
</style>

{{-- Submitted via AJAX (see admin-articles.js) — no full-page reload for
     either validation errors or a successful save; data-ajax-form and
     data-loading-text drive that, so data-page-loading (the classic
     full-reload loader trigger used elsewhere in the admin) is deliberately
     left off this form. --}}
<form method="POST" action="{{ $action }}" enctype="multipart/form-data" id="article-form" data-ajax-form data-loading-text="{{ __('Saving…') }}">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <ul class="nav nav-tabs mb-3" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" type="button" data-bs-toggle="tab" data-bs-target="#tab-essentials">{{ __('Essentials') }}</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" type="button" data-bs-toggle="tab" data-bs-target="#tab-content">{{ __('Content') }}</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" type="button" data-bs-toggle="tab" data-bs-target="#tab-faqs">{{ __('FAQs') }}</button>
        </li>
        @if ($isEdit && ($canManageHomeSections ?? false))
            <li class="nav-item" role="presentation">
                <button class="nav-link" type="button" data-bs-toggle="tab" data-bs-target="#tab-home-sections">{{ __('Page Sections') }}</button>
            </li>
        @endif
        @if ($isEdit)
            <li class="nav-item" role="presentation">
                <button class="nav-link" type="button" data-bs-toggle="tab" data-bs-target="#tab-seo">{{ __('SEO') }}</button>
            </li>
        @endif
    </ul>

    <div class="tab-content">
        {{-- Tab 1: Essentials --}}
        <div class="tab-pane fade show active" id="tab-essentials">
            <x-adminlte-card>
                <div class="mb-3">
                    <label class="form-label">{{ __('Category') }} *</label>
                    <select name="category_id" class="form-select" required>
                        <option value="">{{ __('— Select —') }}</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id', $article->category_id) == $category->id)>{{ $category->title }}</option>
                        @endforeach
                    </select>
                </div>

                <x-adminlte-input name="title" label="{{ __('Title') }} *" maxlength="255" required :value="old('title', $article->title)" />

                <div class="mb-3">
                    <label class="form-label">{{ __('Introduction') }} *</label>
                    <div class="quill-editor" data-quill-target="introduction" style="min-height:150px;">{!! old('introduction', $article->introduction) !!}</div>
                    <textarea name="introduction" class="d-none" data-quill-input>{{ old('introduction', $article->introduction) }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('Featured Image') }} {{ $isEdit ? '' : '*' }}</label>
                    <div class="d-flex align-items-start gap-2">
                        @if ($isEdit && $article->featuredImage)
                            <a href="{{ $article->featuredImage->large_url }}" data-fancybox="article-images" data-caption="{{ $article->featuredImage->caption }}" class="flex-shrink-0">
                                <img src="{{ $article->featuredImage->thumbnail_url }}" alt="" class="rounded border" style="height:80px; width:80px; object-fit:cover;">
                            </a>
                        @endif
                        <input type="file" name="featured_image" class="form-control" accept="image/png,image/jpeg,image/webp" @unless ($isEdit) required @endunless>
                    </div>
                    <p class="form-text mb-0">
                        {{ __('Accepted formats:') }} {{ strtoupper(implode(', ', config("media.{$type}.formats"))) }}.
                        {{ __('Max size:') }} {{ number_format(config("media.{$type}.max_size_kb") / 1024, 1) }} MB.
                        @if ($isEdit)
                            {{ __('Leave empty to keep the current image.') }}
                        @endif
                    </p>
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __("Editor's Note") }} *</label>
                    <div class="quill-editor" data-quill-target="editors_note" style="min-height:150px;">{!! old('editors_note', $article->editors_note) !!}</div>
                    <textarea name="editors_note" class="d-none" data-quill-input>{{ old('editors_note', $article->editors_note) }}</textarea>
                </div>

                <div class="mb-0">
                    <label class="form-label">{{ __("Author's Note") }} *</label>
                    <div class="quill-editor" data-quill-target="authors_note" style="min-height:150px;">{!! old('authors_note', $article->authors_note) !!}</div>
                    <textarea name="authors_note" class="d-none" data-quill-input>{{ old('authors_note', $article->authors_note) }}</textarea>
                </div>
            </x-adminlte-card>
        </div>

        {{-- Tab 2: Content --}}
        <div class="tab-pane fade" id="tab-content">
            <div id="article-sections" @if ($isEdit) data-reorder-url="{{ route("admin.{$type}.sections.reorder", $article) }}" @endif>
                @if ($isEdit)
                    @foreach ($article->sections as $index => $section)
                        @include('admin.articles._section_fields', ['index' => $index, 'section' => $section, 'type' => $type, 'isEdit' => $isEdit, 'article' => $article])
                    @endforeach
                @elseif (old('sections'))
                    @foreach (old('sections') as $index => $sectionInput)
                        @include('admin.articles._section_fields', ['index' => $index, 'section' => null, 'type' => $type, 'isEdit' => $isEdit, 'article' => $article])
                    @endforeach
                @else
                    {{-- A fresh article opens with one section already in
                         view, rather than an empty tab the user has to know
                         to fill via "Add Section". --}}
                    @include('admin.articles._section_fields', ['index' => 0, 'section' => null, 'type' => $type, 'isEdit' => $isEdit, 'article' => $article])
                @endif
            </div>

            <button type="button" class="btn btn-outline-primary" id="add-section">
                <i class="bi bi-plus-lg me-1" aria-hidden="true"></i> {{ __('Add Section') }}
            </button>

            <template id="section-template">
                @include('admin.articles._section_fields', ['index' => '__INDEX__', 'section' => null, 'type' => $type, 'isEdit' => $isEdit, 'article' => $article])
            </template>
        </div>

        {{-- Tab 3: FAQs --}}
        <div class="tab-pane fade" id="tab-faqs">
            <div id="article-faqs">
                @if ($isEdit)
                    @foreach ($article->faqs as $index => $faq)
                        @include('admin.articles._faq_fields', ['index' => $index, 'faq' => $faq])
                    @endforeach
                @elseif (old('faqs'))
                    @foreach (old('faqs') as $index => $faqInput)
                        @include('admin.articles._faq_fields', ['index' => $index, 'faq' => null])
                    @endforeach
                @endif
            </div>

            <button type="button" class="btn btn-outline-primary" id="add-faq">
                <i class="bi bi-plus-lg me-1" aria-hidden="true"></i> {{ __('Add FAQ') }}
            </button>

            <template id="faq-template">
                @include('admin.articles._faq_fields', ['index' => '__INDEX__', 'faq' => null])
            </template>
        </div>

        {{-- Tab 4: Page Sections — carousels/sliders this article can appear
             in, both on the homepage and (for Features articles) the
             Features page itself. Only on the edit form (a new article has
             no id to pick with yet), and only for admins who hold
             'manage-home-sections'. Each switch is an instant AJAX toggle
             (see admin-articles.js), independent of the rest of this form's
             Save. --}}
        @if ($isEdit && ($canManageHomeSections ?? false))
            <div class="tab-pane fade" id="tab-home-sections">
                <x-adminlte-card>
                    <h6 class="mb-2">{{ __('Home Page') }}</h6>
                    <p class="text-body-secondary">
                        {{ __('Show this article in the following homepage carousels. Each toggle saves instantly.') }}
                    </p>

                    @foreach (\App\Support\HomeSections::labelsFor($article->type) as $section => $label)
                        <div class="form-check form-switch mb-2">
                            <input
                                type="checkbox" class="form-check-input" role="switch"
                                id="home-section-{{ $section }}"
                                @checked(in_array($section, $articleHomeSections ?? [], true))
                                data-home-section-toggle
                                data-section-label="{{ $label }}"
                                data-toggle-url="{{ route("admin.{$type}.home-sections.toggle", [$article, $section]) }}"
                            >
                            <label class="form-check-label" for="home-section-{{ $section }}">{{ $label }}</label>
                        </div>
                    @endforeach

                    @if ($type === 'features')
                        <hr class="my-3">

                        <h6 class="mb-2">{{ __('Features Page') }}</h6>
                        <p class="text-body-secondary">
                            {{ __('Show this article in the following sections of the Features page itself. Each toggle saves instantly.') }}
                        </p>

                        @foreach (\App\Support\HomeSections::FEATURES_PAGE_LABELS as $section => $label)
                            <div class="form-check form-switch mb-2">
                                <input
                                    type="checkbox" class="form-check-input" role="switch"
                                    id="home-section-{{ $section }}"
                                    @checked(in_array($section, $articleHomeSections ?? [], true))
                                    data-home-section-toggle
                                    data-section-label="{{ $label }}"
                                    data-toggle-url="{{ route("admin.{$type}.home-sections.toggle", [$article, $section]) }}"
                                >
                                <label class="form-check-label" for="home-section-{{ $section }}">{{ Str::before($label, ' (Features page)') }}</label>
                            </div>
                        @endforeach
                    @endif
                </x-adminlte-card>
            </div>
        @endif

        {{-- Tab: SEO — this content page's own meta title/description/
             keywords. Only on the edit form, saved along with the rest of
             this form. --}}
        @if ($isEdit)
            <div class="tab-pane fade" id="tab-seo">
                <x-adminlte-input name="meta_title" label="{{ __('Meta Title') }}" :value="old('meta_title', $meta->title)" />
                <x-adminlte-textarea name="meta_description" label="{{ __('Meta Description') }}" rows="3">{{ old('meta_description', $meta->description) }}</x-adminlte-textarea>
                <x-adminlte-input name="meta_keywords" label="{{ __('Meta Keywords') }}" :value="old('meta_keywords', $meta->keywords)" />
            </div>
        @endif
    </div>

    @unless ($canApprove)
        <div class="alert alert-info mt-3">
            {{ __("Submitting (not saving as a draft) will be marked as pending until a super admin approves it.") }}
        </div>
    @endunless

    {{-- Which button was clicked drives save_action — see admin-articles.js. --}}
    <input type="hidden" name="save_action" value="submit" data-save-action-field>

    <div class="d-flex gap-2 mt-3">
        <a href="{{ route("admin.{$type}.index") }}" class="btn btn-outline-secondary">{{ __('adminlte.cancel') }}</a>
        @if ($canBeDrafted)
            <button type="submit" class="btn btn-outline-primary" data-save-action-button="draft">
                <i class="bi bi-file-earmark me-1" aria-hidden="true"></i> {{ __('Save as Draft') }}
            </button>
        @endif
        <button type="submit" class="btn btn-primary" data-save-action-button="submit">
            <i class="bi bi-check-lg me-1" aria-hidden="true"></i> {{ $isEdit && ! $canBeDrafted ? __('adminlte.save') : $submitLabel }}
        </button>
    </div>
</form>
