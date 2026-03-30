<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CitizenRegistry extends Model
{
    use SoftDeletes;

    protected $table = 'citizen_registry';

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'birth_date',
        'birth_place',
        'register_number',
        'address',
        'profession_sector',
        'profession_title',
        'education_level',
        'years_experience',
        'skills',
        'current_status',
        'available_for_municipality',
        'cv_file_path',
        'cv_file_name',
        'portfolio_url',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'available_for_municipality' => 'boolean',
        'years_experience' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
