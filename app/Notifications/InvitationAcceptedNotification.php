<?php

namespace App\Notifications;

use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvitationAcceptedNotification extends Notification
{
    use Queueable;

    public function __construct(public Invitation $invitation)
    {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Invitation Accepted — ' . $this->invitation->company->name)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line($this->invitation->email . ' has accepted the invitation to join ' . $this->invitation->company->name . ' as ' . $this->invitation->role . '.')
            ->action('View Users', url('/users'))
            ->line('Thank you for using SaaS Platform.');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'message'      => $this->invitation->email . ' accepted the invitation to ' . $this->invitation->company->name,
            'action_url'   => '/users',
            'action_label' => 'View Users',
        ];
    }
}