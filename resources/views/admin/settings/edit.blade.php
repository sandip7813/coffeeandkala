@extends('adminlte::page')

@section('title', 'Settings')

@section('content_header')
    <h3 class="mb-0 text-center">Settings</h3>
@stop

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <x-adminlte-card icon="bi bi-image" title="Brand logo" class="mb-4">
                <p class="text-body-secondary mb-4">
                    Choose which logo appears on the public site, admin panel, and transactional emails.
                </p>

                <form method="POST" action="{{ route('admin.settings.logo.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="brand-logo-options mb-4">
                        @foreach ($logos as $key => $logo)
                            <label class="brand-logo-option {{ $selectedLogo === $key ? 'is-selected' : '' }}">
                                <input
                                    type="radio"
                                    name="logo"
                                    value="{{ $key }}"
                                    class="form-check-input brand-logo-option__input"
                                    @checked(old('logo', $selectedLogo) === $key)
                                    required
                                >
                                <span class="brand-logo-option__preview">
                                    <img
                                        src="{{ asset($logo['path']) }}"
                                        alt="{{ $logo['label'] }}"
                                        width="{{ $logo['width'] }}"
                                        height="{{ $logo['height'] }}"
                                    >
                                </span>
                                <span class="brand-logo-option__meta">
                                    <span class="brand-logo-option__label">{{ $logo['label'] }}</span>
                                    <span class="brand-logo-option__description">{{ $logo['description'] }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>

                    @error('logo')
                        <div class="text-danger small mb-3">{{ $message }}</div>
                    @enderror

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1" aria-hidden="true"></i> Save logo
                    </button>
                </form>
            </x-adminlte-card>

            <x-adminlte-card icon="bi bi-share" title="Social media links">
                <p class="text-body-secondary mb-4">
                    Links shown in the public site footer. Leave a field blank to hide that network.
                </p>

                <form method="POST" action="{{ route('admin.settings.social.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="row g-3 mb-4">
                        @foreach ($socialNetworks as $key => $network)
                            <div class="col-12">
                                <label class="visually-hidden" for="social-{{ $key }}">{{ $network['label'] }}</label>
                                <div class="input-group @error('links.'.$key) has-validation @enderror">
                                    <span class="input-group-text social-link-icon" title="{{ $network['label'] }}">
                                        <i class="{{ $network['admin_icon'] }}" aria-hidden="true"></i>
                                    </span>
                                    <input
                                        type="url"
                                        name="links[{{ $key }}]"
                                        id="social-{{ $key }}"
                                        class="form-control @error('links.'.$key) is-invalid @enderror"
                                        value="{{ old('links.'.$key, $socialLinks[$key] ?? '') }}"
                                        placeholder="{{ $network['placeholder'] }}"
                                        maxlength="255"
                                        aria-label="{{ $network['label'] }} URL"
                                    >
                                    @error('links.'.$key)
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1" aria-hidden="true"></i> Save social links
                    </button>
                </form>
            </x-adminlte-card>
        </div>
    </div>
@stop

@push('css')
    <style>
        .brand-logo-options {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .75rem;
            max-width: 420px;
        }

        .brand-logo-option {
            display: flex;
            flex-direction: column;
            gap: .65rem;
            height: 100%;
            padding: .75rem;
            border: 1px solid var(--bs-border-color);
            border-radius: .5rem;
            cursor: pointer;
            background: var(--bs-body-bg);
            transition: border-color .15s ease, box-shadow .15s ease;
        }

        .brand-logo-option:hover,
        .brand-logo-option:focus-within,
        .brand-logo-option.is-selected {
            border-color: var(--bs-primary);
            box-shadow: 0 0 0 .2rem rgba(var(--bs-primary-rgb), .15);
        }

        .brand-logo-option__input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .brand-logo-option__preview {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100px;
            padding: .75rem;
            border-radius: .375rem;
            background: #5b3a29;
        }

        .brand-logo-option__preview img {
            max-width: 100%;
            max-height: 72px;
            width: auto;
            height: auto;
            object-fit: contain;
        }

        .brand-logo-option__meta {
            display: flex;
            flex-direction: column;
            gap: .15rem;
        }

        .brand-logo-option__label {
            font-weight: 600;
            font-size: .95rem;
        }

        .brand-logo-option__description {
            color: var(--bs-secondary-color);
            font-size: .8rem;
            line-height: 1.35;
        }

        .social-link-icon {
            min-width: 2.75rem;
            justify-content: center;
        }
    </style>
@endpush
