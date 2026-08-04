@props([
    'align' => 'end',
])

<div {{ $attributes->class(['dropdown', 'd-inline-block']) }}>
    <button type="button"
            class="btn btn-sm btn-light border shadow-sm admin-row-actions-toggle"
            data-bs-toggle="dropdown"
            data-bs-popper-config='{"strategy":"fixed"}'
            aria-expanded="false"
            aria-label="{{ __('adminlte.actions') }}">
        <i class="bi bi-three-dots-vertical" aria-hidden="true"></i>
    </button>
    <ul class="dropdown-menu dropdown-menu-{{ $align }} shadow border-0 admin-row-actions-menu py-2">
        {{ $slot }}
    </ul>
</div>
