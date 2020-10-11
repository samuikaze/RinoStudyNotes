<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title') | 超異域公主連結資料 API</title>
    <script src="{{ mix('js/app.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    @include('layouts.header')

    <div class="container-fluid mt-3">
        <div class="row">
            <div class="col-12">
                @yield('content')
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="d-flex justify-content-center">
            <div class="col-7 p-3">
                @include('layouts.footer')
            </div>
        </div>
    </div>
</body>
</html>