<?php

namespace App\Support;

use App\Enums\RoundType;
use App\Models\Admin;
use App\Models\User;

/**
 * Shared dropdown option lists for round scheduling/editing, kept separate
 * from CandidateOptions since these describe rounds, not candidates.
 */
class RoundOptions
{
    public static function types(): array
    {
        return RoundType::options();
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
        $admins = Admin::get(['id', 'name'])
            ->mapWithKeys(fn(Admin $admin) => ["admin:{$admin->id}" => "{$admin->name} (Admin)"]);

        $users = User::where('status', 'active')
            ->get(['id', 'name'])
            ->mapWithKeys(fn(User $user) => ["user:{$user->id}" => "{$user->name}"]);

        return $admins->merge($users)->all();
    }
}
