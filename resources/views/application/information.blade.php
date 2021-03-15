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
                        @foreach($forms as $formElem)
                            <div class="form-group">
                                @if(isset($formElem) && is_array($formElem) && !is_null($formElem))
                                    @foreach($formElem as $f)
                                        <x-element :component="$f"/>
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
            console.log(arrayOfLogics)
            var parent = $(parentClass);
            var parentVal = parent.find("select option:selected").val();

            if (Object.keys(arrayOfLogics).length > 0) {

                for (var key of Object.keys(arrayOfLogics)) {
                    if (key === parentVal) {
                        if(Array.isArray(arrayOfLogics[key])) {
                            for(var elem of arrayOfLogics[key]) {
                                $(elem).show();
                            }
                        } else {
                            $(arrayOfLogics[key]).show();
                        }
                    } else {
                        if(Array.isArray(arrayOfLogics[key])) {
                            for(var hideElem of arrayOfLogics[key]) {
                                $(hideElem).hide();
                            }
                        } else {
                            $(arrayOfLogics[key]).hide();
                        }
                    }
                }
            }
        }
        function calculateAge(from, to) {
            var today = new Date();
            var birthDate = new Date($(from).val());
            var age = today.getFullYear() - birthDate.getFullYear();
            var m = today.getMonth() - birthDate.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }

            return $(to).val(age);
        }

        $('#register-form').validate({
            ignore:":not(:visible)",
            rules: {
                @isset($form)
                    @foreach($form as $f)
                "{{ $f['name'] ?? '' }}": {{ $f['js_rules'] ?? "{}" }},
                         @isset($f['sub-form'])
                            @foreach($f['sub-form'] as $f1)
                "{{ $f1['name'] ?? '' }}": {{ $f1['js_rules'] ?? "{}" }},
                                @isset($fs['sub-form'])
                                    @foreach($f1['sub-form'] as $f2)
                "{{ $f2['name'] ?? '' }}": {{ $f2['js_rules'] ?? "{}" }},
                                    @endforeach
                                @endisset
                            @endforeach
                        @endisset
                    @endforeach
                @endisset
            },
            onfocusout: function (element) {
                $(element).valid();
            },
        });

        jQuery.extend(jQuery.validator.messages, {
            required: "",
            remote: "",
            email: "",
            url: "",
            date: "",
            dateISO: "",
            number: "",
            digits: "",
            creditcard: "",
            equalTo: ""
        });


        FilePond.setOptions({
            server: {
                url: '/filepond/api',
                process: '/process',
                revert: '/process',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }
        });
    </script>
@endsection
