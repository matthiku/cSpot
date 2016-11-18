<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Reminder to enter mssing items</title>
</head>

<body>
    <p>Dear {{ $recipient->first_name }},</p>

	<p>
        @if (isset($role) && $role!='not set')
        	You are <strong>{{ $role }}</strong> of this event and</p>
        @endif

        {{$user->first_name}} wants to remind you to enter the <strong>
        @if ( isset($role) )
            @if ($role=='leader')
                missing items (songs etc.)
            @elseif ($role=='teacher')
                last song
            @else
                missing items
            @endif
        @endif
        </strong>into the plan for<br>

        <h4><a href="{{ url('cspot/plans/'.$plan->id) }}">{{ $plan->type->name }} 
            on {{ $plan->date->formatLocalized('%A, %d %B %Y') }}</a>
            <small>(click to open)</small>
        </h4>
    <p>

    Best Regards,<br>    
    Your c-SPOT Admin
    </p>

</body>

</html>