<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Lang;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class RegisterInvitation extends Notification
{
    use Queueable;

    /**
     * @var array $payload
     */
    public $payload;

    /**
     * Create a new notification instance.
     *
     * @param array $payload
     */
    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('注册邀请')
            ->greeting('尊敬的用户您好！')
            ->line(Lang::get('您的好友 :inviter 邀请您注册成为 ECMS 会员。', ['inviter' => $this->payload['inviter']]))
            ->action('前往注册', $this->payload['url'])
            ->line('如果您对此并不知情，请忽略该邮件。顺祝商祺！');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
