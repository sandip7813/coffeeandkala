@extends('adminlte::page')

@section('title', 'Edit Profile')

@section('content_header')
    <h3 class="mb-0 text-center">Edit Profile</h3>
@stop

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <x-adminlte-card icon="bi bi-person" title="Your details" class="mb-4">
                <form method="POST" action="{{ route('admin.profile.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <x-adminlte-input name="first_name" label="First name" :value="$user->first_name" required />
                        </div>
                        <div class="col-md-6">
                            <x-adminlte-input name="last_name" label="Last name" :value="$user->last_name" required />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <x-adminlte-input name="email" type="email" label="Email" :value="$user->email" disabled />
                        </div>
                        <div class="col-md-6">
                            <x-adminlte-input name="phone" type="tel" label="Phone number" :value="$user->phone"
                                              maxlength="10" inputmode="numeric" pattern="[0-9]{1,10}"
                                              placeholder="10 digits max" />
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1" aria-hidden="true"></i> Save changes
                    </button>
                </form>
            </x-adminlte-card>

            <x-adminlte-card icon="bi bi-image" title="Profile picture">
                <div class="d-flex align-items-center gap-3">
                    @if ($user->profile_photo_thumbnail_url)
                        <img src="{{ $user->profile_photo_thumbnail_url }}" alt="{{ $user->full_name }}"
                             class="rounded-circle" width="80" height="80" style="object-fit: cover;">
                    @else
                        <i class="bi bi-person-circle text-body-secondary" style="font-size: 80px; line-height: 1;" aria-hidden="true"></i>
                    @endif
                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#profilePhotoModal">
                        <i class="bi bi-upload me-1" aria-hidden="true"></i> Change picture
                    </button>
                </div>
            </x-adminlte-card>
        </div>
    </div>
@stop
