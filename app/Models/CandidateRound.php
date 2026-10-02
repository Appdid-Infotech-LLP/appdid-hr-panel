<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateRound extends Model
{
    protected $fillable = [
        'candidate_id',
        'type',
        'schedule_at',
        'mode',
        'meeting_link',
        'interviewer_type',
        'interviewer_id',
        'notes',
        'status',
        'calendar_event_id',
    ];

    protected $casts = [
        'schedule_at' => 'datetime',
    ];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    public function interviewer()
    {
        return $this->morphTo();
    }
}
