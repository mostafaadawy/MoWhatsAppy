<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Events\MessageNotificationEvent;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Notifications\MessageCreatedNotification;
class ProcessMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public $message;

    public function __construct(MessageNotificationEvent  $event)
    {
        $this->message = $event->message;
    }

    public function handle()
    {
        // Dispatch the event that will notify the user
        event(new MessageNotificationEvent($this->message));
    }
}
