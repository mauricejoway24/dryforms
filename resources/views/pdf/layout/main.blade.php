<html>
<head>
    <title>App Name - @yield('title')</title>
    <link rel="stylesheet" type="text/css" href="{{ realpath('app/static/vendor/bootstrap.css') }}" media="all">
    <link rel="stylesheet" type="text/css" href="{{ realpath('app/static/vendor/font-awesome.css') }}" media="all">
    <link rel="stylesheet" type="text/css" href="{{ realpath('app/static/fonts/fontawesome-webfont.woff') }}" media="all">
    <link rel="stylesheet" type="text/css" href="{{ realpath('app/static/fonts/fontawesome-webfont.woff2') }}" media="all">
    <link rel="stylesheet" type="text/css" href="{{ realpath('app/static/fonts/fontawesome-webfont.ttf') }}" media="all">

    <style type="text/css">
        header {
            margin-top: 2000px;
        }
        .text-right {
            text-align: right;
        }
        .text-left {
            text-align: left;
        }
        .text-center {
            text-align: center;
        }
        td {
            font-size: 14px;
        }
        .border-input {
            border: 1px solid #ced4da;
            border-radius: 3px;
        }
        .border-bottom {
            border-bottom: 1px solid #ced4da;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>

<!-- Call Report Form -->
@isset($sections['call_report'])
    @include('pdf.call_report', $sections['call_report'])
@endisset

<div class="page-break"></div>

<!-- Work Authorization Form -->
@isset($sections['work_authorization'])
    @include('pdf.generic_form', $sections['work_authorization'])
@endisset

<div class="page-break"></div>

<!-- Anti Microbial Form -->
@isset($sections['anti_microbial'])
    @include('pdf.work_authorization', $sections['anti_microbial'])
@endisset

{{--@foreach($sections as $section)--}}

{{--<div class="container">--}}
    {{--@yield('content')--}}
{{--</div>--}}

{{--@endforeach--}}

</body>
</html>
