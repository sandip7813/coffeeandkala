@php
    $user = auth()->user();
    $name = $user->full_name ?: ($user->email ?? 'Guest');
@endphp
<li class="nav-item dropdown user-menu">
    <a href="#" class="nav-link dropdown-toggle d-flex align-items-center gap-2"
       data-bs-toggle="dropdown" aria-expanded="false" aria-label="{{ __('User menu') }}">
        @if ($user->profile_photo_thumbnail_url)
            <img src="{{ $user->profile_photo_thumbnail_url }}" alt="{{ $name }}"
                 class="rounded-circle" width="32" height="32" style="object-fit: cover;">
        @else
            <i class="bi bi-person-circle fs-4" aria-hidden="true"></i>
        @endif
        <span class="d-none d-md-inline">{{ $name }}</span>
    </a>
    <ul class="dropdown-menu dropdown-menu-end">
        <li class="dropdown-header text-body-secondary small">{{ $name }}</li>
        <li><hr class="dropdown-divider"></li>
        <li>
            <a class="dropdown-item" href="{{ route('admin.profile.edit') }}">
                <i class="bi bi-person me-2" aria-hidden="true"></i> Edit Profile
            </a>
        </li>
        <li>
            <a class="dropdown-item" href="{{ route('admin.profile.password.edit') }}">
                <i class="bi bi-key me-2" aria-hidden="true"></i> Change Password
            </a>
        </li>
        <li><hr class="dropdown-divider"></li>
        <li>
            <a class="dropdown-item text-danger" href="#"
               onclick="event.preventDefault(); document.getElementById('adminlte-logout-form').submit();">
                <i class="bi bi-box-arrow-right me-2" aria-hidden="true"></i> Logout
            </a>
            <form id="adminlte-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
        </li>
    </ul>
</li>
