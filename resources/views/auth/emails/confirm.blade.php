<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up Confirmation</title>
</head>
<body>
    <p>Dear {{ $user->getFullName() }},</p>
    <p>Thanks for signing up to c-SPOT!</p>
    <p>
        Before you can log in, we just need you to <a href='{{ url("register/confirm/{$user->token}") }}'>confirm your email address</a> real quick!
    </p>
    <p>Best Regards,<br>
    Your c-SPOT Admin
    </p>
</body>
</html>