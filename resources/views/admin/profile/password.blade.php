@extends('adminlte::page')

@section('title', 'Change Password')

@section('content_header')
    <h3 class="mb-0 text-center">Change Password</h3>
@stop

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <x-adminlte-card icon="bi bi-key" title="Update password">
                <form method="POST" action="{{ route('admin.profile.password.update') }}">
                    @csrf
                    @method('PUT')

                    <x-adminlte-input name="current_password" type="password" label="Current password" required />
                    <x-adminlte-input name="password" type="password" label="New password" required />
                    <x-adminlte-input name="password_confirmation" type="password" label="Confirm password" required />

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1" aria-hidden="true"></i> Change password
                    </button>
                </form>
            </x-adminlte-card>
        </div>
    </div>
@stop
