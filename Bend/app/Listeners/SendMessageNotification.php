<?php

namespace App\Listeners;
use App\Notifications\MessageCreatedNotification;
use App\Events\MessageNotificationEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendMessageNotification
{use InteractsWithQueue;
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(MessageNotificationEvent $event)
    {
        // Notification logic here, including email and database notifications
        $event->message->user->notify(new MessageCreatedNotification($event->message));
    }
}
