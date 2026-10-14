<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone_no',
        'designation',
        'country',
        'city',
        'address',
        'price',
        'img',
    ];

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function feedbacks(): HasMany
    {
        return $this->hasMany(DoctorFeedback::class);
    }
}
