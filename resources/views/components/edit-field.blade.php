@props(['name', 'label', 'value' => '', 'type' => 'text', 'options' => [], 'wide' => false, 'help' => null])
@aware(['id' => null])
@php
    $fieldId = $id.'-'.$name;
    $editing = old('_edit_modal') === $id;
    $value = $type === 'password' ? '' : ($editing ? old($name, $value) : $value);
    $invalid = $editing && $errors->has($name);
    $describedBy = trim(($help ? $fieldId.'-help ' : '').($invalid ? $fieldId.'-error' : ''));
    $controlAttributes = $attributes->merge(['id' => $fieldId, 'name' => $name, 'aria-invalid' => $invalid ? 'true' : 'false']);
    if ($describedBy) $controlAttributes = $controlAttributes->merge(['aria-describedby' => $describedBy]);
@endphp
<div class="form-group edit-field {{ $wide ? 'field-wide' : '' }}">
    @if($type === 'checkbox')
        <input type="hidden" name="{{ $name }}" value="0">
        <label for="{{ $fieldId }}" class="checkbox-label">
            <input type="checkbox" value="1" {{ $controlAttributes }} @checked($value)> {{ $label }}
        </label>
    @else
        <label for="{{ $fieldId }}">{{ $label }}</label>
        @if($type === 'select')
            <select {{ $controlAttributes->class(['form-select']) }}>
                @foreach($options as $key => $text)
                    <option value="{{ $key }}" @selected((string) $value === (string) $key)>{{ $text }}</option>
                @endforeach
            </select>
        @elseif($type === 'textarea')
            <textarea {{ $controlAttributes->class(['form-control']) }} rows="3">{{ $value }}</textarea>
        @else
            <input type="{{ $type }}" value="{{ $value }}" {{ $controlAttributes->class(['form-control']) }}>
        @endif
    @endif
    @if($help)<div id="{{ $fieldId }}-help" class="help">{{ $help }}</div>@endif
    @if($invalid)<div id="{{ $fieldId }}-error" class="field-error">{{ $errors->first($name) }}</div>@endif
</div>
