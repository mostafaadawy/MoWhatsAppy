<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;
    protected $fillable = ['id']; // Add other fillable fields if needed

    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
