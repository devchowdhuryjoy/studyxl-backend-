<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <h2>Login</h2>
    @if($errors->any())
       @foreach ($errors->all() as $error)
           <li>{{$error}}</li>
       @endforeach
    @endif
    @if (Session::has('error'))
        <li>{{Session::get('error')}}</li>
    @endif
    @if (Session::has('success'))
        <li>{{Session::get('success')}}</li>
    @endif
    <form action="{{route('admin.login_submit')}}" method="post">
        @csrf
        <input type="text" name="email" placeholder="Eamil"><br>
        <input type="password" name="password" placeholder="Password"><br>
        <button type="submit">Login</button>
    </form>
    <a href="{{route('admin.forget_password')}}">Forget Password</a>
</body>
</html>