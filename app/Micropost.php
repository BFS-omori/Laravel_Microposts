<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Micropost extends Model
{
    protected $fillable = ['content', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    //N:Nの記述
    public function favoriteUser()
    {
        return $this->belongsToMany(User::class, 'favorite', 'favorite_id', 'user_id')->withTimestamps();
    }
    //

}
