@props(['element','sub_element'])


<div class="form-input {{ $element['class'] ?? '' }}"
     style="display: {{ isset($element['isHidden']) && $element['isHidden'] ? 'none' : 'inherit' }};
         padding-left: {{ ($sub_element ?? false) ? '10px' : '0' }}">
    <label for="{{ $element['title'] ?? '' }}"
           class="{{ ($element['required'] ?? true) ? 'required' : '' }}"
        {!! $element['html_extra'] ?? '' !!}>{{ $element['title'] ?? '' }}</label>
</div>

@isset($element['sub-form'])
    @foreach($element['sub-form'] as $sub)
        @php
            $isSub = true;
        @endphp
        <x-element :sub_element="$isSub" :component="$sub"/>
    @endforeach
@endisset
