@php
    $reopenModalId = 'role-permissions-'.$role->id;
@endphp
<div class="modal fade" id="{{ $reopenModalId }}" tabindex="-1" aria-labelledby="{{ $reopenModalId }}-label" aria-hidden="true"
     data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.roles.update', $role) }}" data-page-loading="Saving permissions…">
                @csrf
                @method('PUT')
                <input type="hidden" name="_reopen_modal" value="{{ $reopenModalId }}">
                <input type="hidden" name="name" value="{{ $role->name }}">
                <input type="hidden" name="label" value="{{ $role->label }}">

                <div class="modal-header">
                    <h5 class="modal-title" id="{{ $reopenModalId }}-label">
                        {{ __('adminlte.permissions') }} — {{ $role->label ?? $role->name }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @error('permissions')
                        <div class="text-danger small mb-2">{{ $message }}</div>
                    @enderror
                    @include('admin.roles.partials.permission-checkboxes', [
                        'checkedIds' => $role->permissions->pluck('id')->all(),
                        'idPrefix' => $reopenModalId.'-',
                        'reopenModalId' => $reopenModalId,
                    ])
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('adminlte.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1" aria-hidden="true"></i> {{ __('adminlte.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
