<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorFeedback extends Model
{
    protected $table = "doctors_feedback";
    use HasFactory;

    protected $fillable = [
        'user_id',
        'doctor_id',
        'message',
        'stars'
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
