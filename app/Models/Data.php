<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Data extends Model
{
    use HasFactory;

    protected $fillable = [
        'surname',
        'address',
        'age',
        'steam_username',
        'user_id'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}