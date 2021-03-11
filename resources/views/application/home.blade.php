@extends('layouts.application_layout')

@section('content')
    <div class="signup-content">
        {{--<div class="signup-img">
            <img src="{{ asset('application/images/form-img.jpg') }}" alt="">
            <div class="signup-img-content">
                <h2>Register now </h2>
                <p>while seats are available !</p>
            </div>
        </div>--}}
        <div class="signup-form">
            <form method="POST" action="#" class="register-form" id="register-form">
                {!! csrf_field() !!}
                <div class="form-row">

                    <div class="form-groups" style="width: 50%; margin-left: 25%">
                        @if(isset($form) && is_array($form) && !is_null($form))
                            @foreach($form as $f)

                                @if(isset($f['type']) && $f['type'] == 'select')
                                    <div class="form-select {{ $f['class'] ?? '' }}"
                                         style="display: {{ isset($f['isHidden']) && $f['isHidden'] ? 'none' : '' }}">
                                        <div class="label-flex">
                                            <label for="meal_preference">{{ $f['title'] ?? '' }}</label>
{{--                                            <a href="#" class="form-link">Lunch detail</a>--}}
                                        </div>
                                        <div class="select-list">
                                            <select name="{{ $f['name'] ?? '' }}" id="{{ $f['name'] ?? '' }}"
                                                    style="display: {{ isset($f['isHidden']) && $f['isHidden'] ? 'none' : '' }}"
                                                    onchange="{{ $f['onchange'] ?? '' }}">
                                                <option value="">নির্বাচন</option>
                                                @if(isset($f['options']) && is_array($f['options']))
                                                    @foreach($f['options'] as $option)
                                                        <option value="{{ $option }}">{{ $option }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                @endif

                                @if(isset($f['type']) && $f['type'] == 'text')
                                    <div class="form-input {{ $f['class'] ?? '' }}"
                                         style="display: {{ isset($f['isHidden']) && $f['isHidden'] ? 'none' : '' }}">
                                        <label for="{{ $f['title'] ?? '' }}"
                                               class="required">{{ $f['title'] ?? '' }}</label>
                                        <input type="text" name="{{ $f['name'] ?? '' }}" id="{{ $f['name'] ?? '' }}"/>
                                    </div>
                                @endif

                            @endforeach
                        @endif
                    </div>
                </div>
                <div class="form-submit">
                    <input type="submit" value="Submit" class="submit" id="submit" name="submit"/>
                </div>
            </form>
        </div>
    </div>
@endsection



@section('footer')
    <script>
        // function showOption('.app_subject', ['বিবিধ' => '.others', 'ভাতা' => '.vata']){
        function showOption(parentClass, arrayOfLogics){
            var parent = $(parentClass);
            var parentVal = parent.find("select option:selected").val();

            if(Object.keys(arrayOfLogics).length > 0) {

                for (var key of Object.keys(arrayOfLogics)) {
                    if(key === parentVal) {
                        $(arrayOfLogics[key]).show();
                    } else {
                        $(arrayOfLogics[key]).hide();
                    }
                }
            }
        }
    </script>
@endsection
