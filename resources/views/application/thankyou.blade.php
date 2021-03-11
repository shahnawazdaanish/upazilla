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
            {{--<div class="jumbotron text-center">
                <h1 class="display-3">ধন্যবাদ!</h1>
                <p class="lead"><strong>আপনার আবেদন গ্রহন করা হয়েছে</strong> পরবর্তি যাচাই নিমিত্তে নিম্নের সিরিয়াল নাম্বারটি সংরক্ষন করুন.</p>
                <hr>
                <p>
                    আপনার যাচাই নাম্বারঃ <strong>{{ uniqid("UPZ-") }}</strong>
                </p>
                <p class="lead">
                    <a class="btn btn-primary btn-sm" href="/" role="button">নতুন আবেদন করুন</a>
                </p>
            </div>--}}

            <div class="thankyou-page">
                <div class="_header">
{{--                    <div class="logo">--}}
{{--                        <img src="https://codexcourier.com/images/banner-logo.png" alt="">--}}
{{--                    </div>--}}
                    <h1>ধন্যবাদ!</h1>
                </div>
                <div class="_body">
                    <div class="_box">
                        <h2>
                            <strong>আপনার আবেদন গ্রহন করা হয়েছে</strong>, পরবর্তি যাচাই নিমিত্তে নিম্নের সিরিয়াল নাম্বারটি সংরক্ষন করুন..
                        </h2>
                        <h3>
                            আপনার যাচাই নাম্বারঃ <strong>{{ uniqid("UPZ-") }}</strong>
                        </h3>
                    </div>
                </div>
                <div class="_footer">
                    <a class="btn" href="/">নতুন আবেদন করুন</a>
                </div>
            </div>
        </div>
    </div>
@endsection
