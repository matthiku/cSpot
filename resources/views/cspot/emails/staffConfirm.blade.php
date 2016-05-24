<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>You've been selected!</title>
</head>

<body>
    <p>Dear {{ $user->first_name }},</p>

    <p>Please confirm that you are accepting your role as <big>{{ $team->role->name }}</big> for<br>

    <h3><a href="{{ url('cspot/plans/'.$plan->id) }}/team">{{ $plan->type->name }} 
        on {{ $plan->date->formatLocalized('%A, %d %B %Y') }}</a></h3>

    <p>Just go to the above link and click on the "Confirm" button for your role.</p>

    <p>You can also directly confirm this by clicking on 
        <a href="{{ url('cspot/plans/'.$plan->id.'/team/'.$team->id.'/confirm/'.$team->remember_token) }}">this link</a>.</p>

    <p>You can always reverse this at any time if you are unable to attend for any reason.</p>

    <p>Best Regards,<br>    
    Your c-SPOT Admin
    </p>

</body>

</html>