<?php

namespace App\Support;

/**
 * Shared dropdown option lists for round scheduling/editing, kept separate
 * from CandidateOptions since these describe rounds, not candidates.
 */
class RoundOptions
{
    public static function types(): array
    {
        return [
            'HR Round' => 'HR Round',
            'Task Round' => 'Task Round',
            'Technical Round' => 'Technical Round',
            'Final Round' => 'Final Round',
        ];
    }

    public static function statuses(): array
    {
        return [
            'Pending' => 'Pending',
            'Scheduled' => 'Scheduled',
            'Completed' => 'Completed',
            'Cancelled' => 'Cancelled',
            'Rescheduled' => 'Rescheduled',
            'No Show' => 'No Show',
        ];
    }

    public static function modes(): array
    {
        return [
            'Virtual' => 'Virtual',
            'In Person' => 'In Person',
        ];
    }

    public static function interviewers(): array
    {
        $names = ['Anita Desai', 'Karan Mehta', 'Vikram Singh'];

        return array_combine($names, $names);
    }
}
