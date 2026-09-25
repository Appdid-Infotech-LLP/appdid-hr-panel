<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'date_of_birth',
        'gender',
        'location',
        'address',
        'highest_qualification',
        'college',
        'experience_years',
        'current_company',
        'current_designation',
        'current_salary',
        'expected_salary',
        'notice_period',
        'skills',
        'contacted_on',
        'tech_stack',
        'agreed_to_bond',
        'expected_joining_date',
        'reason_for_leaving',
        'linkedin_url',
        'portfolio_url',
        'resume_path',
        'status',
        'current_stage',
        'notes',
    ];

    protected $casts = [
        'skills' => 'array',
        'tech_stack' => 'array',
        'agreed_to_bond' => 'boolean',
        'date_of_birth' => 'date',
        'contacted_on' => 'date',
        'expected_joining_date' => 'date',
        'experience_years' => 'decimal:1',
    ];
}
