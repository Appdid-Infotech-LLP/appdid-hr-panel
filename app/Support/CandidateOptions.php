<?php

namespace App\Support;

/**
 * Shared dropdown option lists for candidate forms and filters, so the
 * create form, edit form and listing filters all offer the same choices.
 * Not tied to the database — safe to keep even after real models exist,
 * or move into a config file / enum at that point if you prefer.
 */
class CandidateOptions
{
    public static function genders(): array
    {
        return [
            'Male' => 'Male',
            'Female' => 'Female',
            'Other' => 'Other',
            'Prefer not to say' => 'Prefer not to say',
        ];
    }

    public static function qualifications(): array
    {
        return [
            'High School' => 'High School',
            'Diploma' => 'Diploma',
            'B.Tech / B.E.' => 'B.Tech / B.E.',
            'B.Sc' => 'B.Sc',
            'B.Com' => 'B.Com',
            'BA' => 'BA',
            'BCA' => 'BCA',
            'B.Des' => 'B.Des',
            'MCA' => 'MCA',
            'M.Tech' => 'M.Tech',
            'MBA' => 'MBA',
            'M.Sc' => 'M.Sc',
            'PhD' => 'PhD',
            'Other' => 'Other',
        ];
    }

    public static function noticePeriods(): array
    {
        return [
            'Immediate' => 'Immediate',
            '15 days' => '15 days',
            '30 days' => '30 days',
            '45 days' => '45 days',
            '60 days' => '60 days',
            '90 days' => '90 days',
            'Other' => 'Other',
        ];
    }

    public static function locations(): array
    {
        return [
            'Bangalore' => 'Bangalore',
            'Mumbai' => 'Mumbai',
            'Pune' => 'Pune',
            'Delhi NCR' => 'Delhi NCR',
            'Hyderabad' => 'Hyderabad',
            'Chennai' => 'Chennai',
            'Remote' => 'Remote',
            'Other' => 'Other',
        ];
    }

    public static function skillSuggestions(): array
    {
        $skills = [
            'JavaScript', 'TypeScript', 'PHP', 'Laravel', 'Livewire', 'Vue.js', 'React',
            'Node.js', 'Python', 'Java', 'MySQL', 'PostgreSQL', 'Redis', 'Docker',
            'Kubernetes', 'AWS', 'CI/CD', 'REST APIs', 'Tailwind CSS', 'Figma',
            'Product Management', 'Recruitment', 'QA / Testing', 'System Design',
        ];

        return array_combine($skills, $skills);
    }

    public static function stages(): array
    {
        return [
            'New' => 'New',
            'HR Round' => 'HR Round',
            'Task Round' => 'Task Round',
            'Technical Round' => 'Technical Round',
            'Final Round' => 'Final Round',
            'Selected' => 'Selected',
            'Rejected' => 'Rejected',
        ];
    }

}
