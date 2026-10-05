<?php

namespace App\Support;

use App\Enums\RoundType;
use App\Models\CandidateRound;

/**
 * A round's effective status is "Completed"/"Cancelled" once set explicitly
 * on the row (candidate_rounds.status), but for a round that hasn't reached
 * that point yet, it's derived from the candidate's own current_stage. Used
 * by both the Rounds list and the Calendar page, so the same round always
 * shows the same status everywhere.
 */
class RoundStatusResolver
{
    /**
     * SQL CASE expression + bindings computing the same thing as effective(),
     * for use in a query (filtering/sorting/selecting) instead of per-row PHP.
     *
     * @return array{string, list<string>}
     */
    public static function sqlExpression(): array
    {
        $roundTypes = array_map(fn (RoundType $type): string => $type->value, RoundType::cases());
        $cases = [
            'WHEN candidate_rounds.status IS NOT NULL THEN candidate_rounds.status',
            'WHEN candidates.current_stage = ? THEN ?',
            'WHEN candidates.current_stage = ? THEN ?',
        ];
        $bindings = ['Rejected', 'Cancelled', 'Selected', 'Completed'];

        foreach ($roundTypes as $stageIndex => $stage) {
            $cases[] = 'WHEN candidates.current_stage = ? AND candidate_rounds.type = ? THEN ?';
            array_push($bindings, $stage, $stage, 'Scheduled');

            foreach (array_slice($roundTypes, 0, $stageIndex) as $completedRoundType) {
                $cases[] = 'WHEN candidates.current_stage = ? AND candidate_rounds.type = ? THEN ?';
                array_push($bindings, $stage, $completedRoundType, 'Completed');
            }
        }

        return ['CASE '.implode(' ', $cases).' ELSE ? END', [...$bindings, 'Pending']];
    }

    public static function effective(CandidateRound $round): string
    {
        if ($round->status !== null) {
            return $round->status;
        }

        $candidate = $round->candidate;

        if ($candidate->current_stage === 'Rejected') {
            return 'Cancelled';
        }

        if ($candidate->current_stage === 'Selected') {
            return 'Completed';
        }

        $roundTypes = array_map(fn (RoundType $type): string => $type->value, RoundType::cases());
        $currentStageIndex = array_search($candidate->current_stage, $roundTypes, true);
        $roundIndex = array_search($round->type, $roundTypes, true);

        if ($currentStageIndex === false || $roundIndex === false) {
            return 'Pending';
        }

        return match (true) {
            $roundIndex < $currentStageIndex => 'Completed',
            $roundIndex === $currentStageIndex => 'Scheduled',
            default => 'Pending',
        };
    }

    /**
     * The stage a candidate should default into once a round of this type
     * is marked Completed — the next step in HR Round → Task Round →
     * Technical Round → Final Round. Null for Final Round (and for any
     * value that isn't a round type at all): Selected/Rejected is always
     * an explicit, confirmed HR decision, never automatic.
     */
    public static function stageAfterCompleting(string $roundType): ?string
    {
        $roundTypes = array_map(fn (RoundType $type): string => $type->value, RoundType::cases());
        $index = array_search($roundType, $roundTypes, true);

        if ($index === false || ! array_key_exists($index + 1, $roundTypes)) {
            return null;
        }

        return $roundTypes[$index + 1];
    }
}
