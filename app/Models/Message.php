<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'user_id',
        'party_id',
    ];

    public function party() {
        return $this->belongsTo(Party::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
