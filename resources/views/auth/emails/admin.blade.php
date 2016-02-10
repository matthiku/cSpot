<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $note }}</title>
</head>
<body>
    <p>Name: {{ $user->getFullName() }}</p>
    <p>Email address: {{ $user->email }}</p>
</body>
</html>