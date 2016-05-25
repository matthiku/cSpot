<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>You've been selected!</title>
</head>

<body>
    <p>Dear {{ $user->first_name }},</p>

    <p>Please confirm that you are accepting your role as <strong>{{ $team->role->name }}</strong> for our
    
    <h3><a href="{{ url('cspot/plans/'.$plan->id) }}/team">{{ $plan->type->name }} 
        on {{ $plan->date->formatLocalized('%A, %d %B %Y') }}</a></h3>

    <p>Just go to the above link and click on the "Confirm" button for your role.</p>

    <p>You can also <strong>quickly confirm</strong> this by clicking on 
        <a href="{{ url('cspot/plans/'.$plan->id.'/team/'.$team->id.'/confirm/'.$team->remember_token) }}">this link</a>.</p>

    <p>If then for any reason you are unable to attend, please go to the first link above and click 'Decline'.</p>

    <p>Best Regards,<br>    
    Your c-SPOT Admin
    </p>

</body>

</html>