<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $fillable = [
        'patient_id',
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'phone_number',
        'email',
        'address',
        'emergency_contact_name',
        'emergency_contact_phone',
        'insurance_details',
    ];

    protected $casts = [
        'insurance_details' => 'array',
    ];


    public function appointments()
{
    return $this->hasMany(Appointment::class);
}
public function audits()
{
    return $this->hasMany(PatientAudit::class);
}

}
