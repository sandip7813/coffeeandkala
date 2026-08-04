@extends('adminlte::page')

@section('title', 'Edit Profile')

@section('content_header')
    <h3 class="mb-0 text-center">Edit Profile</h3>
@stop

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <x-adminlte-card icon="bi bi-person" title="Your details">
                <form method="POST" action="{{ route('admin.profile.update') }}">
                    @csrf
                    @method('PUT')

                    <x-adminlte-input name="first_name" label="First name" :value="$user->first_name" required />
                    <x-adminlte-input name="last_name" label="Last name" :value="$user->last_name" required />
                    <x-adminlte-input name="email" type="email" label="Email" :value="$user->email" disabled />
                    <x-adminlte-input name="phone" type="tel" label="Phone number" :value="$user->phone"
                                      maxlength="10" inputmode="numeric" pattern="[0-9]{1,10}"
                                      placeholder="10 digits max" />

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1" aria-hidden="true"></i> Save changes
                    </button>
                </form>
            </x-adminlte-card>
        </div>
    </div>
@stop
