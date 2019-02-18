<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ Admin::title() }} @if($header) | {{ $header }}@endif</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    {!! Admin::css() !!}

    <script src="{{ Admin::jQuery() }}"></script>
    {!! Admin::headerJs() !!}
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body class="hold-transition {{config('admin.skin')}} {{join(' ', config('admin.layout'))}}">
<div class="wrapper">

    <div class="content-wrapper" id="pjax-container">
        <div id="app">
        @yield('content') 
        </div>
        {!! Admin::script() !!}
    </div>
    @include('admin::partials.footer')
    <span id="back-to-top" class="fa fa-upload" title="Back to top"></span></p>
</div>

<script>
    function LA() {}
    LA.token = "{{ csrf_token() }}";

    $(function () {
        $(window).scroll(function(){
            if ($(window).scrollTop() > 400){
                $("#back-to-top").fadeIn(300);
            }
            else
            {
                $("#back-to-top").fadeOut(300);
            }
        });
        $("#back-to-top").click(function(){
            if ($('html').scrollTop()) {
                $('html').animate({ scrollTop: 0 }, 300);
                return false;
            }
            $('body').animate({ scrollTop: 0 }, 300);
            return false;            
        });       
     });    
</script>

<!-- REQUIRED JS SCRIPTS -->
{!! Admin::js() !!}
</body>
</html>
