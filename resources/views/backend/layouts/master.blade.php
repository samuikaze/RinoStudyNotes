<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title') | 後台 | 超異域公主連結資料 API</title>
    <script src="{{ mix('js/app.js') }}"></script>
    <script src="/js/backend.js"></script>
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>
    @include('backend.layouts.header')

    <div class="container-fluid mt-3">
        <div class="row">
            <div class="col-12">
                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>