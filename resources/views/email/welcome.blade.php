<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
</head>
<body>
    <h1>Welcome, {{$user->name}}</h1>
    <p>Thank you for registering your email is {{$user->email}}</p>
    <a href="{{$url}}">Enviar codigo al celular</a>
</body>
</html>