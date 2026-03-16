<?php
// Remnider! php artisan make:model -mcr
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaveGame extends Model
{
    //Protected properties and methods can only be used by the class in which the property or method was defined and any classes that derive from it.
    //--> Any other code cannot use them.
    // https://www.w3schools.com/php/keyword_protected.asp#:~:text=Definition%20and%20Usage,other%20code%20cannot%20use%20them.

    protected $fillable = ['user_id', 'slot', 'chapter', 'game_variables', 'resolver_type'];
    protected $casts = ['game_variables' => 'array'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
