@component('mail::message')
# 注册邀请

尊敬的用户您好！

您的好友 {{$payload['inviter']}} 邀请您注册 ECMS，如果您对此并不知情，请忽略该邮件。

@component('mail::button', ['url' => $payload['url']])
前往注册
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
