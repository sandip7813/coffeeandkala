{{--
    One "FAQs" tab row. Expects: $index (int|string, use '__INDEX__' for the
    blank <template> used by admin-articles.js) and optional $faq (ArticleFaq)
    when rendering an existing FAQ on the edit form.
--}}
@php
    $faq ??= null;
@endphp
<div class="card mb-3 article-faq" data-faq>
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <h6 class="mb-0">{{ __('FAQ') }} <span data-faq-number>{{ is_numeric($index) ? $index + 1 : '' }}</span></h6>
            <button type="button" class="btn btn-sm btn-outline-danger" data-remove-faq>
                <i class="bi bi-trash" aria-hidden="true"></i> {{ __('Remove') }}
            </button>
        </div>

        @if ($faq?->id)
            <input type="hidden" name="faqs[{{ $index }}][id]" value="{{ $faq->id }}">
        @endif

        <div class="mb-3">
            <label class="form-label">{{ __('Question') }}</label>
            <input type="text" name="faqs[{{ $index }}][question]" class="form-control" maxlength="255" value="{{ old("faqs.{$index}.question", $faq->question ?? '') }}">
        </div>
        <div>
            <label class="form-label">{{ __('Answer') }}</label>
            <textarea name="faqs[{{ $index }}][answer]" class="form-control" rows="3">{{ old("faqs.{$index}.answer", $faq->answer ?? '') }}</textarea>
        </div>
    </div>
</div>
