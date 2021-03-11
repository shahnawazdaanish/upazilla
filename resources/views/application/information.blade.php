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
            <form method="POST" action="#" class="register-form" id="register-form" enctype="multipart/form-data">
                {!! csrf_field() !!}
                <div class="form-row">


                    <?php
                    if (isset($form))
                        $forms = array_chunk($form, ceil(count($form) / 2))
                    ?>
                    @if(isset($forms) && is_array($forms))
                        @foreach($forms as $form)
                            <div class="form-group">
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
                                                <input type="text" name="{{ $f['name'] ?? '' }}"
                                                       id="{{ $f['name'] ?? '' }}"/>
                                            </div>
                                        @endif
                                        @if(isset($f['type']) && $f['type'] == 'radio')

                                            <div class="form-radio {{ $f['class'] ?? '' }}"
                                                 style="display: {{ isset($f['isHidden']) && $f['isHidden'] ? 'none' : '' }}">
                                                <div class="label-flex">
                                                    <label for="{{ $f['title'] ?? '' }}">{{ $f['title'] ?? '' }}</label>
                                                </div>
                                                <div class="form-radio-group">
                                                    @if(isset($f['options']))
                                                        @foreach($f['options'] as $opt)
                                                            <div class="form-radio-item">
                                                                <input type="radio" name="{{ $f['name'] ?? '' }}"
                                                                       id="{{ $opt }}" checked>
                                                                <label for="{{ $opt }}">{{ $opt }}</label>
                                                                <span class="check"></span>
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                        @if(isset($f['type']) && $f['type'] == 'photo')
                                            <div class="form-input {{ $f['class'] ?? '' }}"
                                                 style="width:180px; display: {{ isset($f['isHidden']) && $f['isHidden'] ? 'none' : '' }}">
                                                <label for="{{ $f['title'] ?? '' }}"
                                                       class="required">{{ $f['title'] ?? '' }}</label>
                                                <input type="file" name="{{ $f['name'] ?? '' }}"
                                                       id="{{ $f['name'] ?? '' }}"/>
                                            </div>
                                        @endif

                                    @endforeach
                                @endif
                            </div>
                        @endforeach
                    @endif
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
        function showOption(parentClass, arrayOfLogics) {
            var parent = $(parentClass);
            var parentVal = parent.find("select option:selected").val();

            if (Object.keys(arrayOfLogics).length > 0) {

                for (var key of Object.keys(arrayOfLogics)) {
                    if (key === parentVal) {
                        $(arrayOfLogics[key]).show();
                    } else {
                        $(arrayOfLogics[key]).hide();
                    }
                }
            }
        }
    </script>
@endsection
