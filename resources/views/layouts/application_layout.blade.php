<!doctype html>
<html lang="en">
<head>
    @include('layouts.header')
    @yield('header')
</head>
<body>

<div class="main">
    @include('layouts.nav')
    <div class="container">
        @if($errors->any())
            {!! implode('', $errors->all('<div class="alert">:message</div>')) !!}
        @endif
        @yield('content')
    </div>

</div>

@include('layouts.footer')
@yield('footer')
</body>
</html>
