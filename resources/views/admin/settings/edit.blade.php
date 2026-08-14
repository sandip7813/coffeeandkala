@extends('adminlte::page')

@section('title', 'Settings')

@section('content_header')
    <h1 class="m-0">Settings</h1>
    <p class="text-body-secondary mb-0">
        Manage the brand logo, social links, and contact details shown across the public site.
    </p>
@stop

@php
    // Every panel stays collapsed by default. One reopens automatically when its
    // form was just submitted — either it failed validation, or it saved successfully
    // (flagged via the `settings_section` session value set by the controller).
    $activeSection = match (true) {
        $errors->has('logo') => 'logo',
        $errors->has('links.*') => 'social',
        $errors->has('contact.*') => 'contact',
        default => session('settings_section'),
    };
@endphp

@section('content')
    {{-- Each panel toggles independently (no data-bs-parent) so more than one can stay open at once. --}}
    <div class="accordion settings-accordion" id="settings-accordion">
        <div class="accordion-item settings-card">
            <h2 class="accordion-header" id="settings-logo-heading">
                <button
                    class="accordion-button {{ $activeSection === 'logo' ? '' : 'collapsed' }}"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#settings-logo-collapse"
                    aria-expanded="{{ $activeSection === 'logo' ? 'true' : 'false' }}"
                    aria-controls="settings-logo-collapse"
                >
                    <span class="settings-accordion-icon"><i class="bi bi-image" aria-hidden="true"></i></span>
                    <span class="flex-grow-1">Brand logo</span>
                </button>
            </h2>
            <div id="settings-logo-collapse" class="accordion-collapse collapse {{ $activeSection === 'logo' ? 'show' : '' }}" aria-labelledby="settings-logo-heading">
                <div class="accordion-body">
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
                                        <span class="brand-logo-option__label">
                                            {{ $logo['label'] }}
                                            @if ($selectedLogo === $key)
                                                <i class="bi bi-check-circle-fill text-primary ms-1" title="Currently active"></i>
                                            @endif
                                        </span>
                                        <span class="brand-logo-option__description">{{ $logo['description'] }}</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>

                        @error('logo')
                            <div class="text-danger small mb-3">{{ $message }}</div>
                        @enderror

                        <div class="settings-card__actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1" aria-hidden="true"></i> Save logo
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="accordion-item settings-card">
            <h2 class="accordion-header" id="settings-social-heading">
                <button
                    class="accordion-button {{ $activeSection === 'social' ? '' : 'collapsed' }}"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#settings-social-collapse"
                    aria-expanded="{{ $activeSection === 'social' ? 'true' : 'false' }}"
                    aria-controls="settings-social-collapse"
                >
                    <span class="settings-accordion-icon"><i class="bi bi-share" aria-hidden="true"></i></span>
                    <span class="flex-grow-1">Social media links</span>
                    <span class="badge text-bg-light border settings-card__count me-2">
                        {{ count(array_filter($socialLinks)) }} / {{ count($socialNetworks) }} connected
                    </span>
                </button>
            </h2>
            <div id="settings-social-collapse" class="accordion-collapse collapse {{ $activeSection === 'social' ? 'show' : '' }}" aria-labelledby="settings-social-heading">
                <div class="accordion-body">
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
                                        <span class="input-group-text settings-field-icon" title="{{ $network['label'] }}">
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

                        <div class="settings-card__actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1" aria-hidden="true"></i> Save social links
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="accordion-item settings-card">
            <h2 class="accordion-header" id="settings-contact-heading">
                <button
                    class="accordion-button {{ $activeSection === 'contact' ? '' : 'collapsed' }}"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#settings-contact-collapse"
                    aria-expanded="{{ $activeSection === 'contact' ? 'true' : 'false' }}"
                    aria-controls="settings-contact-collapse"
                >
                    <span class="settings-accordion-icon"><i class="bi bi-telephone" aria-hidden="true"></i></span>
                    <span class="flex-grow-1">Contact information</span>
                    <span class="badge text-bg-light border settings-card__count me-2">
                        {{ count(array_filter($contactInfo)) }} / {{ count($contactFields) }} filled
                    </span>
                </button>
            </h2>
            <div id="settings-contact-collapse" class="accordion-collapse collapse {{ $activeSection === 'contact' ? 'show' : '' }}" aria-labelledby="settings-contact-heading">
                <div class="accordion-body">
                    <p class="text-body-secondary mb-4">
                        Shown in the public site footer. Leave a field blank to hide it.
                    </p>

                    <form method="POST" action="{{ route('admin.settings.contact.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-3 mb-4">
                            @foreach ($contactFields as $key => $field)
                                <div class="col-12">
                                    <label class="visually-hidden" for="contact-{{ $key }}">{{ $field['label'] }}</label>
                                    <div class="input-group @error('contact.'.$key) has-validation @enderror">
                                        <span class="input-group-text settings-field-icon" title="{{ $field['label'] }}">
                                            <i class="{{ $field['admin_icon'] }}" aria-hidden="true"></i>
                                        </span>
                                        <input
                                            type="{{ $field['type'] }}"
                                            name="contact[{{ $key }}]"
                                            id="contact-{{ $key }}"
                                            class="form-control @error('contact.'.$key) is-invalid @enderror"
                                            value="{{ old('contact.'.$key, $contactInfo[$key] ?? '') }}"
                                            placeholder="{{ $field['placeholder'] }}"
                                            maxlength="255"
                                            aria-label="{{ $field['label'] }}"
                                        >
                                        @error('contact.'.$key)
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        @if ($key === 'phone')
                                            <div class="form-text w-100">
                                                Digits only, with optional +, spaces, dashes, dots, or parentheses — e.g. {{ $field['placeholder'] }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="settings-card__actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1" aria-hidden="true"></i> Save contact information
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@push('css')
    <style>
        .settings-accordion {
            max-width: 900px;
        }

        .settings-accordion .accordion-item {
            border: 1px solid var(--bs-border-color);
            border-radius: .75rem !important;
            overflow: hidden;
            margin-bottom: 1rem;
            transition: box-shadow .15s ease, border-color .15s ease;
        }

        .settings-accordion .accordion-item:last-child {
            margin-bottom: 0;
        }

        .settings-accordion .accordion-item:has(.accordion-button:not(.collapsed)) {
            border-color: var(--bs-primary);
            box-shadow: 0 .5rem 1.25rem rgba(var(--bs-primary-rgb), .08);
        }

        .settings-accordion .accordion-button {
            padding: 1rem 1.25rem;
            font-size: 1rem;
            font-weight: 600;
            gap: .75rem;
        }

        .settings-accordion .accordion-button:not(.collapsed) {
            color: inherit;
            background-color: var(--bs-accordion-active-bg);
            box-shadow: none;
        }

        .settings-accordion .accordion-button:focus {
            box-shadow: 0 0 0 .2rem rgba(var(--bs-primary-rgb), .15);
            z-index: 1;
        }

        .settings-accordion-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2.25rem;
            height: 2.25rem;
            border-radius: 50%;
            background-color: rgba(var(--bs-primary-rgb), .1);
            color: var(--bs-primary);
            font-size: 1rem;
            flex-shrink: 0;
        }

        .settings-card__count {
            font-weight: 500;
            font-size: .75rem;
        }

        .settings-card__actions {
            margin-top: 1.25rem;
            padding-top: 1.25rem;
            border-top: 1px solid var(--bs-border-color);
        }

        .brand-logo-options {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .75rem;
            max-width: 480px;
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

        .settings-field-icon {
            min-width: 2.75rem;
            justify-content: center;
        }
    </style>
@endpush
