<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ $note }}</title>
</head>

<body>

    <p>Name: {{ $user->fullName }}</p>

    <p>Email address: {{ $user->email }}</p>

    <p>IP address: {{ $user->last_login_ip }}</p>

    <p>Login timestamp: {{ $user->last_login }}</p>

</body>

</html>
