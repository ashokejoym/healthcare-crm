<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientAudit extends Model
{
    protected $fillable = [
        'user_id', 'patient_id', 'action', 'old_values', 'new_values', 'ip_address'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

