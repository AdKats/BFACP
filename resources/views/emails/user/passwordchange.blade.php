<h1>{{ trans('email.password_changed.subject') }}</h1>

<p>{{ trans('email.password_changed.greetings', ['username' => $username]) }},</p>

<p>{{ trans('email.password_changed.body', ['password' => $newPassword]) }}</p>
