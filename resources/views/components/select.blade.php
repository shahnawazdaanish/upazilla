@props(['element', 'sub_element'])

<div class="form-select {{ $element['class'] ?? '' }}"
     style="display: {{ isset($element['isHidden']) && $element['isHidden'] ? 'none' : 'inherit' }};
         padding-left: {{ ($sub_element ?? false) ? '10px' : '0' }}">
    <div class="label-flex">
        <label for="meal_preference"
               class="{{ ($element['required'] ?? true) ? 'required' : '' }}">{{ $element['title'] ?? '' }}</label>
        {{--                                            <a href="#" class="form-link">Lunch detail</a>--}}
    </div>
    <div class="select-list">
        <select name="{{ $element['name'] ?? '' }}" id="{{ $element['name'] ?? '' }}"
                {!! $element['html_extra'] ?? '' !!}
                style="display: {{ isset($f['isHidden']) && $f['isHidden'] ? 'none' : '' }}"
                {{ ($element['required'] ?? true) ? 'required' : '' }}
                onchange="{{ $element['onchange'] ?? '' }}">
            <option value="">নির্বাচন</option>
            @if(isset($element['options']) && is_array($element['options']))
                @foreach($element['options'] as $key => $option)
                    <option value="{{ $key }}">{{ $option }}</option>
                @endforeach
            @endif
        </select>
    </div>
</div>

@isset($element['sub-form'])
    @foreach($element['sub-form'] as $sub)
        <x-element :sub_element="true" :component="$sub"/>
    @endforeach
@endisset
