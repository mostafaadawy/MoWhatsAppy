<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Message;
class MessagePolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }
    public function update(User $user, Message $message)
    {
        // Add your authorization logic here
        // Example: Allow users to update their own messages
        return $user->id == $message->ownership;
    }
    public function delete_for_me(User $user, Message $message)
    {
        // Add your authorization logic here
        // Example: Allow users to update their own messages
        return $user->id !=null;
    }
    public function delete_for_all(User $user, Message $message)
    {
        // Add your authorization logic here
        // Example: Allow users to update their own messages
        return $user->id == $message->ownership;
    }
}
