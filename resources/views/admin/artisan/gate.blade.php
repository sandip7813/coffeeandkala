@extends('adminlte::page')

@section('title', 'Restricted — Artisan Runner')

@section('content_header')
    <div class="row">
        <div class="col-sm-8">
            <h1 class="m-0">Artisan Runner</h1>
            <p class="text-body-secondary mb-0">Password required to continue.</p>
        </div>
        <div class="col-sm-4">
            <ol class="breadcrumb float-sm-end">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Artisan</li>
            </ol>
        </div>
    </div>
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

                    @if ($errors->any())
                        <div class="alert alert-danger text-start" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.artisan.unlock') }}" id="artisan-gate-form">
                        @csrf
                        <div class="mb-3 text-start">
                            <label class="form-label" for="artisan-gate-password">Account password</label>
                            <input type="password"
                                   class="form-control"
                                   id="artisan-gate-password"
                                   name="password"
                                   autocomplete="current-password"
                                   required>
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
