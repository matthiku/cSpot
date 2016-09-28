<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up Confirmation</title>
</head>
<body>
    <p>Dear {{ $user->fullName }},</p>
    <p>Thanks for signing up to c-SPOT!</p>
    <p>
        Before you can log in, we just need you to <a href='{{ url("register/confirm/{$user->token}") }}'>confirm your email address</a> real quick!
    </p>

    <p>Also, please remember, after you've logged in, you only have very basic access rights. Let the administrator know that you've signed up and you will get more access according to your role(s).</p>
    
    <p>Best Regards,<br>
    Your c-SPOT Admin
    </p>
</body>
</html>