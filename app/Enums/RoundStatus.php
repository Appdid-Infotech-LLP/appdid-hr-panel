<?php

namespace App\Enums;

/**
 * The lifecycle a single scheduled round can be in. Note: `candidate_rounds`
 * doesn't have a `status` column yet — this enum is ready for when one is
 * added (cast it with 'status' => RoundStatus::class). Until then, the
 * candidate detail timeline derives round progress from the candidate's own
 * current_stage/status instead of a per-round status.
 */
enum RoundStatus: string
{
    case Pending = 'Pending';
    case Scheduled = 'Scheduled';
    case Completed = 'Completed';
    case Cancelled = 'Cancelled';
    case Rescheduled = 'Rescheduled';
    case NoShow = 'No Show';

    public function label(): string
    {
        return $this->value;
    }

    /**
     * [value => label] shape expected by <x-hr.select>/<x-hr.select2>.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        return array_column(self::cases(), 'value', 'value');
    }
}
