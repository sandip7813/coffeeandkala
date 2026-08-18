@extends('adminlte::page')

@section('title', 'Restricted — Artisan Runner')

@section('content_header')
    <h1 class="m-0">Artisan Runner</h1>
    <p class="text-body-secondary mb-0">Password required to continue.</p>
@stop

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card border-warning"
                 data-artisan-gate
                 data-cancel-url="{{ route('admin.dashboard') }}"
                 data-title="Restricted page"
                 data-text="Artisan Runner can change this application. Enter your account password to continue."
                 data-confirm-button="Unlock"
                 data-cancel-button="Back to dashboard">
                <div class="card-body text-center py-5">
                    <i class="bi bi-shield-lock display-4 text-warning mb-3 d-block" aria-hidden="true"></i>
                    <h2 class="h4 mb-2">Restricted page</h2>
                    <p class="text-body-secondary mb-4">
                        Artisan Runner is locked. Confirm with your account password to open it.
                    </p>

                    <form method="POST" action="{{ route('admin.artisan.unlock') }}" id="artisan-gate-form">
                        @csrf
                        <div class="mb-3 text-start">
                            <label class="form-label" for="artisan-gate-password">Account password</label>
                            <div class="input-group">
                                <div class="input-group-text"><i class="bi bi-lock-fill"></i></div>
                                <input type="password"
                                       class="form-control"
                                       id="artisan-gate-password"
                                       name="password"
                                       autocomplete="current-password"
                                       required>
                                <button type="button" class="input-group-text js-password-toggle" tabindex="-1" aria-label="Show password">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-2 justify-content-center">
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-unlock me-1" aria-hidden="true"></i> Unlock
                            </button>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">Back to dashboard</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
