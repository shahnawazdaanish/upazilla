@props(['element','sub_element'])
<div class="form-input {{ $element['class'] ?? '' }}"
     style="width:180px; display: {{ isset($element['isHidden']) && $element['isHidden'] ? 'none' : 'inherit' }};
         padding-left: {{ ($sub_element ?? false) ? '10px' : '0' }}">
    <label for="{{ $element['title'] ?? '' }}"
           class="{{ ($element['required'] ?? true) ? 'required' : '' }}">{{ $element['title'] ?? '' }}</label>
    <input type="file" name="{{ $element['name'] ?? '' }}"
           {{ ($element['required'] ?? true) ? 'required' : '' }}
           id="{{ $element['name'] ?? '' }}"
           {!! $element['html_extra'] ?? '' !!}
           data-max-file-size="5MB"/>
</div>

@isset($element['sub-form'])
    @foreach($element['sub-form'] as $sub)
        <x-element :sub_element="true" :component="$sub"/>
    @endforeach
@endisset
