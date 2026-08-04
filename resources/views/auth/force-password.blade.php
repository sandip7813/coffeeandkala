@extends('adminlte::page')

@section('title', 'Change password')

@section('content_header')
    <h3 class="mb-0 text-center">Change your password</h3>
@stop

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <x-adminlte-alert theme="info">
                You signed in with a one-time password. Please choose a new password to continue.
            </x-adminlte-alert>

            <x-adminlte-card icon="bi bi-key" title="Set a new password">
                <form method="POST" action="{{ route('password.force.update') }}">
                    @csrf
                    @method('PUT')

                    <x-adminlte-input name="current_password" type="password" label="One-time / current password" required />
                    <x-adminlte-input name="password" type="password" label="New password" required />
                    <x-adminlte-input name="password_confirmation" type="password" label="Confirm password" required />

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1" aria-hidden="true"></i> Save password
                    </button>
                </form>
            </x-adminlte-card>
        </div>
    </div>
@stop
