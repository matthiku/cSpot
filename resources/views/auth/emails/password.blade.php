Click here to reset your password: {{ Config::get('app.url') . '/password/reset', $token .'?email='.urlencode($user->getEmailForPasswordReset()) }}
