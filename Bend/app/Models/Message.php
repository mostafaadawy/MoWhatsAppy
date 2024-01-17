<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    protected $fillable = [
        'chat_id',
        'content',
        'type'
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('status')->withTimestamps();
    }

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }
}
