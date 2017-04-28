<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>multi auth</title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1, user-scalable=no' name='viewport'>
    
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <link href='https://fonts.googleapis.com/css?family=Nunito:400,300' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="{{url('/css/admin_panel.css')}}">
</head>
<body class="loginBody">
<div id="loginContainer" class="container">
    <div class="row">
        <div class="col-lg-4 col-md-4 col-md-offset-4 text-center">
            <h2>{{$loginTitle}}</h2>
            <div class="account-wall">
                <img class="profile-img" src="https://cdn.pixabay.com/photo/2016/08/08/09/17/avatar-1577909_960_720.png" alt="profile pic">
                <form class="form-signin" method="POST" action="{{url($form_action)}}">
                Username: miguel@cloudoki.pt
                <br>
                Password: cloudoki_pass
                {!! csrf_field() !!}

                    <input type="email" name="email" class="form-control" placeholder="Email" required autofocus>
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                    <div id="remember" class="checkbox">
                        <label>
                            <input type="checkbox" value="yes" name ="remember_me"> Remember me
                        </label>
                    </div>
                    <button class="btn btn-lg btn-primary btn-block" type="submit">
                        Entrar</button>
                    {{-- <label class="checkbox pull-left">
                        <input type="checkbox" value="remember-me">
                        Remember me
                    </label>
                    <a href="#" class="pull-right need-help">Need help? </a><span class="clearfix"></span> --}}
                </form>
            </div>
        </div>
    </div>
    @if(Session::has('flash_error'))
        <div class="alert alert-danger col-lg-4 col-md-4 col-md-offset-4 text-center">
            <b>{{Session::get('flash_error')}}</b>
        </div>
    @elseif(Session::has('flash_success'))
        <div class="alert alert-success col-lg-4 col-md-4 col-md-offset-4 text-center">
            <b>{{Session::get('flash_success')}}</b>
        </div>
    @endif
</div>
</body>
</html>