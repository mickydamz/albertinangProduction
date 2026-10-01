<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome</title>
</head>
<body>
    <h1>Welcome to Laravel</h1>
   
        <a href="{{ route('login') }}">Login</a>
   
 
        <a href="{{ route('register') }}">Register</a>

    <p>hello </p>
    <p>hello</p>
</body>
</html>
