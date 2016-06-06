<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ $subject }}</title>
</head>

<body>
    <p>Dear {{ $user->first_name }},</p>

    <p>You have received an internal message on c-SPOT:</p>

    <hr>
    <h4>Topic:</h4>
    <summary>{{ $messi->thread->subject }}</summary>

    <h4>Message:</h4>
    <details>{{ $messi->body }}</details>

    <hr>
    <p>Please do not reply to this message, use <a href="{{ url('messages').'/'.$messi->thread_id }}">this link</a> to reply!</p>

    <p>Best Regards,<br>    
    Your c-SPOT Admin
    </p>

    <small>You can always unsubscribe from these kind of notifications <a href="{{ url('admin/users').'/'.$user->id }}">here</a>.</small>

</body>

</html>