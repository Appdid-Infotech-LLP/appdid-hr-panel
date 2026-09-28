<?php

namespace App\Enums;

/**
 * The four recruitment round types a candidate moves through. The backing
 * value is the exact string already stored/displayed everywhere (round
 * type columns, badges, stage names) — kept as-is so nothing else needs
 * to change when this enum is introduced.
 */
enum RoundType: string
{
    case Hr = 'HR Round';
    case Task = 'Task Round';
    case Technical = 'Technical Round';
    case Final = 'Final Round';

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
