<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    //


    public function user()
{
    return $this->belongsTo(User::class);
}

public function appointments()
{
    return $this->hasMany(Appointment::class);
}
}

