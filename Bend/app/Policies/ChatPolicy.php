<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Chat;

class ChatPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }
    public function delete(User $user, Chat $chat){
        return $user->id == $chat->ownership;

    }
}
