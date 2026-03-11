@php
    $disabled = $disabled ?? false;
    $required = $required ?? false;
    $checked = $checked ?? false;
    $modal = $modal ?? false;
    $multiple = $multiple ?? false;
    $col_lg = $col_lg ?? 'col-lg-3';
    $col_md = $col_md ?? 'col-md-4';
    $col_sm = $col_sm ?? 'col-sm-6';
    $col = $col ?? 'col-12';
    $hasError = $hasError ?? false;
    $error = $error ?? null;
    $fill = $fill ?? true;
    $hasLabel = $hasLabel ?? true;
    $subtext = $subtext ?? null;
    $customClass = $customClass ?? '';
    $customClassField = $customClassField ?? '';
    $customProperty = $customProperty ?? '';
@endphp
<div
    class="content__field {{ $col_lg }} {{ $col_md }} {{ $col_sm }} {{ $col }} {{ $fill ? 'fill' : '' }} {{ $customClass }}">
    @if ($hasLabel)
        <label for="field__custom-{{ $id }}">
            <b style="font-size: 130%" >{{ $label }} @if ($subtext != null)
                    {!! $subtext !!}
                @endif
            </b>
            <b id="{{ $id }}" style="color:red; font-weight: 400"></b>
        </label>
    @endif
    @if ($type == 'select')
        @if (isset($selectType) && $selectType == 'normal')
            <div class="area__select">
        @endif
        <select name="{{ $name }}" id="field__custom-{{ $id }}" {{ $disabled ? 'disabled' : '' }}
            {{ $required ? 'required' : '' }}
            class="{{ $modal ? 'select2_popup' : 'select2_normal' }} {{ $customClassField }}" 
            {{ $multiple ? 'multiple="multiple"' : '' }} {{ $customProperty }}>
            <option value="" selected disabled>Seleccione una opción</option>
            @foreach ($options as $value_option => $text)
                <option {{ isset($value) && $value == $value_option ? 'selected' : '' }} value="{{ $value_option }}">
                    {{ $text }}</option>
            @endforeach
        </select>
        @if (isset($selectType) && $selectType == 'normal')
</div>
@endif
@elseif($type == 'checkbox' || $type == 'radio')
<div class="{{ $type }}__field">
    <input type="{{ $type }}" value="{{ isset($value) ? $value : '' }}" name="{{ $name }}"
        id="field__custom-{{ $id }}" {{ $disabled ? 'disabled' : '' }} {{ $required ? 'required' : '' }}
        {{ $checked ? 'checked' : '' }} {{ $customProperty }} class="{{ $customClassField }}">
    <span></span>
</div>
@elseif($type == 'switch')
<div class="{{ $type }}__field">
    <input type="checkbox" value="{{ isset($value) ? $value : '' }}" name="{{ $name }}"
        id="field__custom-{{ $id }}" {{ $disabled ? 'disabled' : '' }} {{ $required ? 'required' : '' }}
        {{ $checked ? 'checked' : '' }} {{ $customProperty }} class="{{ $customClassField }}">
    <span></span>
</div>
@elseif($type == 'textarea')
<div class="{{ $type }}__field">
    <textarea name="{{ $name }}" id="field__custom-{{ $id }}" {{ $disabled ? 'disabled' : '' }}
        {{ $required ? 'required' : '' }} rows="3" {{ $customProperty }} class="{{ $customClassField }}">{{ isset($value) ? $value : '' }}</textarea>
    <span></span>
</div>
@else
<input type="{{ $type }}" value="{{ isset($value) ? $value : '' }}" name="{{ $name }}"
    id="field__custom-{{ $id }}" {{ $disabled ? 'disabled' : '' }} {{ $required ? 'required' : '' }}
    {{ isset($min) ? 'min=' . $min : '' }} {{ isset($max) ? 'max=' . $max : '' }} {!! $customProperty !!}
    class="{{ $customClassField }}">
@endif
@if ($hasError)
    @if (isset($error))
        <p class="error__text">{{ $error }}</p>
    @endif
@endif
</div>
