@props(['element', 'sub_element'])
<div class="form-radio {{ $element['class'] ?? '' }}"
     style="display: {{ isset($element['isHidden']) && $element['isHidden'] ? 'none' : 'inherit' }};
         padding-left: {{ ($sub_element ?? false) ? '10px' : '0' }}">
    <div class="label-flex">
        <label for="{{ $element['title'] ?? '' }}"
               class="{{ ($element['required'] ?? true) ? 'required' : '' }}">{{ $element['title'] ?? '' }}</label>
    </div>
    <div class="form-radio-group">
        @if(isset($element['options']))
        @foreach($element['options'] as $opt)
        <div class="form-radio-item">
            <input {{ ($element['required'] ?? true) ? 'required' : '' }}
            type="radio" name="{{ $element['name'] ?? '' }}"
                   {!! $element['html_extra'] ?? '' !!}
            id="{{ $opt }}" checked>
            <label for="{{ $opt }}">{{ $opt }}</label>
            <span class="check"></span>
        </div>
        @endforeach
        @endif
    </div>
</div>

@isset($element['sub-form'])
    @foreach($element['sub-form'] as $sub)
        <x-element :sub_element="true" :component="$sub"/>
    @endforeach
@endisset
