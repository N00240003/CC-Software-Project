<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogBook extends Model
{
    protected $fillable = ['user_id', 'reflection_text', 'private'];

    protected $casts = ['private' => 'boolean'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
