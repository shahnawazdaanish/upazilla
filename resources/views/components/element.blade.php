@props(["component", "sub_element"])
@php
    $hasSub = isset($component) && isset($component['sub-form']);
@endphp
@if(isset($component['type']) && $component['type'] == 'select')
    <x-select :sub_element="isset($sub_element) ? $sub_element : false" :element="$component"/>
@endif

@if(isset($component['type']) && $component['type'] == 'text')
    <x-textbox :sub_element="isset($sub_element) ? $sub_element : false" :element="$component"/>
@endif
@if(isset($component['type']) && $component['type'] == 'radio')
    <x-radio :sub_element="isset($sub_element) ? $sub_element : false" :element="$component"/>
@endif
@if(isset($component['type']) && $component['type'] == 'photo')
    <x-file :sub_element="isset($sub_element) ? $sub_element : false" :element="$component"/>
@endif
@if(isset($component['type']) && $component['type'] == 'label')
    <x-label :sub_element="isset($sub_element) ? $sub_element : false" :element="$component"/>
@endif
