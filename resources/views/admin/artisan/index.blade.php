@extends('adminlte::page')

@section('title', 'Artisan Runner')

@section('content_header')
    <div class="row">
        <div class="col-sm-8">
            <h1 class="m-0">Artisan Runner</h1>
            <p class="text-body-secondary mb-0">
                Run allowlisted <code>php artisan</code> commands from the browser when SSH is unavailable.
            </p>
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
    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-warning" role="alert">
            {{ session('error') }}
        </div>
    @endif

    @if ($result)
        <x-adminlte-card
            icon="bi bi-terminal"
            title="Last result"
            theme="{{ $result['succeeded'] ? 'success' : 'danger' }}"
            class="mb-3"
        >
            <p class="mb-2">
                <code>{{ $result['command'] }}</code>
                <span class="badge text-bg-{{ $result['succeeded'] ? 'success' : 'danger' }} ms-1">
                    exit {{ $result['exit_code'] }}
                </span>
            </p>
            <pre class="bg-dark text-light p-3 rounded mb-0 small" style="max-height: 24rem; overflow: auto; white-space: pre-wrap;">{{ $result['output'] }}</pre>
        </x-adminlte-card>
    @endif

    <div class="row g-3 mb-3">
        <div class="col-lg-8">
            <x-adminlte-card icon="bi bi-lightning-charge" title="Quick actions">
                <div class="accordion" id="artisan-preset-accordion">
                    @foreach ($groups as $group)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading-{{ $group['key'] }}">
                                <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#collapse-{{ $group['key'] }}"
                                        aria-expanded="{{ $loop->first ? 'true' : 'false' }}"
                                        aria-controls="collapse-{{ $group['key'] }}">
                                    {{ $group['label'] }}
                                    <span class="badge text-bg-secondary ms-2">{{ count($group['presets']) }}</span>
                                </button>
                            </h2>
                            <div id="collapse-{{ $group['key'] }}"
                                 class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                                 aria-labelledby="heading-{{ $group['key'] }}"
                                 data-bs-parent="#artisan-preset-accordion">
                                <div class="accordion-body">
                                    <div class="row g-2">
                                        @foreach ($group['presets'] as $preset)
                                            <div class="col-md-6">
                                                <form method="POST"
                                                      action="{{ route('admin.artisan.run') }}"
                                                      class="h-100"
                                                      data-confirm-artisan
                                                      data-confirm-title="Run this Artisan command?"
                                                      data-confirm-text="{{ $preset['description'] }} will run on this server."
                                                      data-confirm-button="Yes, run it"
                                                      data-cancel-button="Cancel"
                                                      data-loading-text="Running Artisan command…"
                                                      @if (! empty($preset['danger']))
                                                          data-confirm-danger="1"
                                                      @endif>
                                                    @csrf
                                                    <input type="hidden" name="preset" value="{{ $preset['key'] }}">
                                                    <input type="hidden" name="confirm" value="0" data-artisan-confirm>

                                                    <div class="border rounded p-3 h-100 d-flex flex-column">
                                                        <div class="fw-semibold mb-1">
                                                            {{ $preset['label'] }}
                                                            @if (! empty($preset['danger']))
                                                                <span class="badge text-bg-warning ms-1">Caution</span>
                                                            @endif
                                                        </div>
                                                        <div class="text-body-secondary small mb-3 flex-grow-1">
                                                            <code>{{ $preset['description'] }}</code>
                                                        </div>

                                                        @if (($preset['command'] ?? null) === 'db:seed')
                                                            <div class="mb-2">
                                                                <label class="form-label small mb-1" for="seeder-{{ $preset['key'] }}">Seeder class</label>
                                                                <select class="form-select form-select-sm" id="seeder-{{ $preset['key'] }}" name="seeder">
                                                                    <option value="">DatabaseSeeder (default)</option>
                                                                    @foreach ($seeders as $seeder)
                                                                        <option value="{{ $seeder }}" @selected(old('seeder') === $seeder)>{{ $seeder }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        @endif

                                                        <button type="submit"
                                                                class="btn btn-sm {{ ! empty($preset['danger']) ? 'btn-outline-danger' : 'btn-outline-primary' }} align-self-start">
                                                            <i class="bi bi-play-fill me-1" aria-hidden="true"></i> Run
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-adminlte-card>
        </div>

        <div class="col-lg-4">
            <x-adminlte-card icon="bi bi-keyboard" title="Custom command" class="mb-3">
                <form method="POST"
                      action="{{ route('admin.artisan.run') }}"
                      data-confirm-artisan
                      data-confirm-title="Run this Artisan command?"
                      data-confirm-text="Only allowlisted commands can run. Double-check the command before continuing."
                      data-confirm-button="Yes, run it"
                      data-cancel-button="Cancel"
                      data-loading-text="Running Artisan command…">
                    @csrf
                    <input type="hidden" name="confirm" value="0" data-artisan-confirm>

                    <div class="mb-3">
                        <label class="form-label" for="artisan-command">Command</label>
                        <input type="text" class="form-control font-monospace" id="artisan-command" name="command"
                               value="{{ old('command') }}"
                               placeholder="e.g. migrate:status or db:seed --class=AdminLteRbacSeeder"
                               autocomplete="off"
                               data-artisan-command>
                        <div class="form-text">
                            Prefixes like <code>php artisan</code> are optional. Shell operators are blocked.
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-play-fill me-1" aria-hidden="true"></i> Run command
                    </button>
                </form>
            </x-adminlte-card>

            <x-adminlte-card icon="bi bi-shield-check" title="Allowed commands" theme="secondary">
                <p class="small text-body-secondary mb-2">
                    Only these commands can run. Edit <code>config/artisan-runner.php</code> to change the list.
                </p>
                <div class="small" style="max-height: 16rem; overflow: auto;">
                    @foreach ($allowed as $command)
                        <code class="d-inline-block me-1 mb-1">{{ $command }}</code>
                    @endforeach
                </div>
            </x-adminlte-card>
        </div>
    </div>
@stop
