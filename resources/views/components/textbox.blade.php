@props(['element','sub_element'])

<div class="form-input {{ $element['class'] ?? '' }}"
     style="display: {{ isset($element['isHidden']) && $element['isHidden'] ? 'none' : 'inherit' }};
         padding-left: {{ ($sub_element ?? false) ? '10px' : '0' }}">
    <label for="{{ $element['name'] ?? '' }}"
           class="{{ ($element['required'] ?? true) ? 'required' : '' }}">{{ $element['title'] ?? '' }}</label>
    <input type="text" name="{{ $element['name'] ?? '' }}"
           value="{{ $element['default'] ?? '' }}"
           id="{{ $element['name'] ?? '' }}" {{ ($element['required'] ?? true) ? 'required' : '' }}
         {!! $element['html_extra'] ?? '' !!}/>
</div>

@isset($element['sub-form'])
    @foreach($element['sub-form'] as $sub)
        <x-element :sub_element="true" :component="$sub"/>
    @endforeach
@endisset
