<div class="mb-3 {{ $fgroupClass }}">
    @isset($label)
        <label for="{{ $id }}" class="form-label">{{ $label }}</label>
    @endisset

    @php $isPassword = $type === 'password'; @endphp

    @if (isset($prepend) || $isPassword)
        <div class="input-group {{ $igroupSize ? 'input-group-'.$igroupSize : '' }}">
    @endif
    @isset($prepend)
            <span class="input-group-text">{{ $prepend }}</span>
    @endisset

    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $id }}"
        value="{{ $resolvedValue($attributes->get('value')) }}"
        @if ($hasError()) aria-invalid="true" aria-describedby="{{ $id }}-error" @endif
        {{ $attributes->except('value')->merge(['class' => 'form-control'.($hasError() ? ' is-invalid' : '')]) }}>

    @isset($append)
            <span class="input-group-text">{{ $append }}</span>
    @endisset
    @if ($isPassword)
            <button type="button" class="input-group-text js-password-toggle" tabindex="-1" aria-label="Show password">
                <i class="bi bi-eye"></i>
            </button>
    @endif
    @if (isset($prepend) || $isPassword)
        </div>
    @endif

    @if ($hasError())
        <div class="invalid-feedback d-block" id="{{ $id }}-error">{{ $errorMessage() }}</div>
    @endif

    {{ $slot ?? '' }}
</div>
