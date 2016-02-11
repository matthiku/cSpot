<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reminder to enter mssing items</title>
</head>
<body>
    <p>Dear {{ $user->first_name }},</p>

    <p>{{$recipient->first_name}} wants to remind you to enter the missing items (songs) into the plan for<br>

    <h3>{{ $plan->type->name }} 
    on {{ $plan->date->formatLocalized('%A, %d %B %Y') }}</h3>

    <p>Best Regards,<br>    
    Your ch-SPOT Admin
    </p>

</body>
</html>