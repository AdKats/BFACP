<h1>{{ Lang::get('email.password_changed.subject') }}</h1>

<p>{{ Lang::get('email.password_changed.greetings', ['username' => $username]) }},</p>

<p>{{ Lang::get('email.password_changed.body', ['password' => $newPassword]) }}</p>
