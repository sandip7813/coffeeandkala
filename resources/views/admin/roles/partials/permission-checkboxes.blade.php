@php
    // Namespaced when this partial is reused more than once on the same page
    // (e.g. one modal per row in a table) so checkbox/label `id`s don't collide.
    $idPrefix = $idPrefix ?? '';

    // On a page with several instances of this partial (one modal per role),
    // a validation-error redirect's `old('permissions')` would otherwise leak
    // into every other role's modal too — only the one flagged as reopening
    // (via `_reopen_modal`, see initReopenModal in adminlte.js) should read it.
    // Single-instance pages (create/edit) don't pass `reopenModalId` at all,
    // so they keep reading `old()` unconditionally as before.
    $usesOld = ! isset($reopenModalId) || old('_reopen_modal') === $reopenModalId;
    $checkedIds = $usesOld ? old('permissions', $checkedIds ?? []) : ($checkedIds ?? []);
    $isSuperAdmin = $isSuperAdmin ?? false;

    $groupIcons = [
        'Dashboard' => 'bi bi-speedometer',
        'Users' => 'bi bi-people',
        'Categories' => 'bi bi-tags',
        'Quotes' => 'bi bi-chat-quote',
        'Roles & Permissions' => 'bi bi-shield-lock',
        'Settings' => 'bi bi-gear',
        'General' => 'bi bi-question-circle',
    ];
@endphp

@if ($isSuperAdmin)
    <div class="alert alert-info d-flex align-items-start gap-2 mb-0">
        <i class="bi bi-shield-check fs-5" aria-hidden="true"></i>
        <div>
            <strong>{{ __('Super Admin always has every permission.') }}</strong>
            <div class="small mb-0">{{ __("There's nothing to assign or remove here — new permissions are granted to this role automatically.") }}</div>
        </div>
    </div>
@elseif ($permissions->isEmpty())
    <p class="text-muted mb-0">{{ __('adminlte.no_permissions') }}</p>
@else
    <div class="row g-3 permission-groups">
        @foreach ($permissions as $group => $groupPermissions)
            @php $groupId = $idPrefix.'permission-group-'.\Illuminate\Support\Str::slug($group); @endphp
            <div class="col-md-6">
                <x-adminlte-card :icon="$groupIcons[$group] ?? 'bi bi-folder'" :title="$group" outline theme="secondary" bodyClass="py-2" class="h-100 mb-0 permission-group">
                    <x-slot name="tools">
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input js-permission-group-toggle" type="checkbox"
                                   id="{{ $groupId }}-toggle" aria-label="{{ __('Select all in :group', ['group' => $group]) }}">
                            <label class="form-check-label small text-body-secondary" for="{{ $groupId }}-toggle">{{ __('All') }}</label>
                        </div>
                    </x-slot>

                    @foreach ($groupPermissions as $permission)
                        <div class="form-check">
                            <input class="form-check-input js-permission-checkbox" type="checkbox" name="permissions[]"
                                   value="{{ $permission->id }}" id="{{ $idPrefix }}permission-{{ $permission->id }}"
                                   @checked(in_array($permission->id, $checkedIds))>
                            <label class="form-check-label" for="{{ $idPrefix }}permission-{{ $permission->id }}">
                                {{ $permission->label ?? $permission->name }}
                            </label>
                        </div>
                    @endforeach
                </x-adminlte-card>
            </div>
        @endforeach
    </div>
@endif
