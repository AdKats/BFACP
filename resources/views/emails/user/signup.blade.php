<h1>{{ trans('confide::confide.email.account_confirmation.subject') }}</h1>

<p>{{ trans('confide::confide.email.account_confirmation.greetings', ['name' => array_get($user, 'username')]) }}
    ,</p>

<p>{{ trans('confide::confide.email.account_confirmation.body') }}</p>

{!! link_to_route('user.confirm',
    route('user.confirm', [array_get($user, 'confirmation_code')]),
    [array_get($user, 'confirmation_code')]) }}

<p>{{ trans('confide::confide.email.account_confirmation.farewell') }}</p>
