<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="CoreUI Bootstrap 4 Admin Template">
    <meta name="author" content="Lukasz Holeczek">
    <meta name="keyword" content="CoreUI Bootstrap 4 Admin Template">
    <!-- <link rel="shortcut icon" href="assets/ico/favicon.png"> -->
    <title>贝壳书友会</title>
    <!-- Icons -->
    <link href="../static/css/font-awesome.min.css" rel="stylesheet">
    <link href="../static/css/simple-line-icons.css" rel="stylesheet">

    <!-- Main styles for this application -->
    <link href="{{ asset('static/css/style.css') }}" rel="stylesheet">
</head>
</head>
<body class="">
<div class="app flex-row align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card-group mb-0">
                    <div class="card p-2">
                        <form class="form-horizontal" role="form" method="POST" action="{{ route('login') }}">
                            {{ csrf_field() }}
                            <div class="card-block">
                                <h6 style="margin-bottom: 30px;">忘记密码</h6>
                                {{--@if (count($errors) > 0)--}}
                                    {{--<p class="text-danger">{{ $errors->first('username') }}</p>--}}
                                {{--@else--}}
                                    {{--<p class="text-muted">请登录您的账户</p>--}}
                                {{--@endif--}}
                                <div class="input-group mb-1">
                                    <span class="input-group-addon"><i class="icon-user"></i></span>
                                    <input id="username" type="text" class="form-control" name="username" value="{{ old('username') }}" required autofocus placeholder="手机号">
                                </div>
                                <div class="input-group mb-2">
                                    <span class="input-group-addon"><i class="icon-lock"></i></span>
                                    <input id="password" type="password" class="form-control" name="password" required placeholder="验证码">
                                </div>
                                <div class="input-group mb-2">
                                    <span class="input-group-addon"><i class="icon-lock"></i></span>
                                    <input id="password" type="password" class="form-control" name="password" required placeholder="新密码">
                                </div>
                                <div class="input-group mb-2">
                                    <span class="input-group-addon"><i class="icon-lock"></i></span>
                                    <input id="password" type="password" class="form-control" name="password" required placeholder="确认密码">
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <button type="submit" class="btn btn-primary px-2">提交</button>
                                    </div>
                                    <div class="col-6 text-right">
                                        <a href="{{ route('login') }}"><button type="button" class="btn btn-link px-0">去登录!</button></a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>