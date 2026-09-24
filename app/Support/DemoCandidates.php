<?php

namespace App\Support;

/**
 * Temporary, hardcoded candidate data so the Candidates UI has something
 * consistent to render across the list, detail and edit pages before the
 * Candidate model/migration are wired up.
 *
 * TODO — YOUR IMPLEMENTATION (once the `candidates` migration is run):
 * Delete this class and replace its usages with real Eloquent calls, e.g.
 * `Candidate::query()->...->paginate()` in Index, `Candidate::findOrFail($id)`
 * in Show/Edit. A model factory (`database/factories/CandidateFactory.php`)
 * is the right replacement for `all()` in local seeding/testing.
 */
class DemoCandidates
{
    public static function all(): array
    {
        return [
            1 => [
                'id' => 1,
                'first_name' => 'Rahul', 'last_name' => 'Sharma',
                'email' => 'rahul.sharma@example.com', 'phone' => '+91 98765 43210', 'alternate_phone' => null,
                'date_of_birth' => '1996-04-12', 'gender' => 'Male',
                'location' => 'Bangalore', 'address' => 'HSR Layout, Bangalore, Karnataka',
                'highest_qualification' => 'B.Tech / B.E.', 'college' => 'RV College of Engineering',
                'experience_years' => 4.5, 'current_company' => 'TechNova Solutions', 'current_designation' => 'Senior Frontend Developer',
                'current_salary' => '14,00,000', 'expected_salary' => '20,00,000', 'notice_period' => '30 days',
                'skills' => ['Vue.js', 'JavaScript', 'Tailwind CSS', 'REST APIs'],
                'linkedin_url' => 'https://linkedin.com/in/rahulsharma', 'portfolio_url' => 'https://rahulsharma.dev',
                'resume_filename' => 'rahul-sharma-resume.pdf', 'resume_size_kb' => 245,
                'notes' => 'Strong frontend background, referred by Anita.',
                'status' => 'Active', 'stage' => 'Technical Round',
                'next_round' => 'Technical Round', 'next_round_date' => 'Sep 25, 2026', 'added_days_ago' => 6,
                'rounds' => [
                    ['type' => 'HR Round', 'date' => '2026-09-19', 'time' => '11:00 AM', 'mode' => 'Virtual', 'meeting_link' => 'https://meet.google.com/abc-defg-hij', 'interviewer' => 'Anita Desai', 'status' => 'Completed', 'notes' => 'Good communication, culture fit confirmed.'],
                    ['type' => 'Task Round', 'date' => '2026-09-22', 'time' => '10:00 AM', 'mode' => 'Virtual', 'meeting_link' => null, 'interviewer' => 'Karan Mehta', 'status' => 'Completed', 'notes' => 'Submitted assignment on time, clean code.'],
                    ['type' => 'Technical Round', 'date' => '2026-09-25', 'time' => '11:00 AM', 'mode' => 'Virtual', 'meeting_link' => 'https://meet.google.com/xyz-uvwx-rst', 'interviewer' => 'Anita Desai', 'status' => 'Scheduled', 'notes' => null],
                ],
            ],
            2 => [
                'id' => 2,
                'first_name' => 'Priya', 'last_name' => 'Patel',
                'email' => 'priya.patel@example.com', 'phone' => '+91 98123 45678', 'alternate_phone' => '+91 98123 00000',
                'date_of_birth' => '1998-08-02', 'gender' => 'Female',
                'location' => 'Mumbai', 'address' => 'Andheri East, Mumbai, Maharashtra',
                'highest_qualification' => 'MCA', 'college' => 'Mumbai University',
                'experience_years' => 2.0, 'current_company' => 'BrightWave IT', 'current_designation' => 'HR Executive',
                'current_salary' => '6,50,000', 'expected_salary' => '9,00,000', 'notice_period' => '15 days',
                'skills' => ['Recruitment', 'Onboarding', 'HRIS'],
                'linkedin_url' => 'https://linkedin.com/in/priyapatel', 'portfolio_url' => null,
                'resume_filename' => 'priya-patel-cv.pdf', 'resume_size_kb' => 180,
                'notes' => null,
                'status' => 'Active', 'stage' => 'HR Round',
                'next_round' => 'HR Round', 'next_round_date' => 'Sep 25, 2026', 'added_days_ago' => 3,
                'rounds' => [
                    ['type' => 'HR Round', 'date' => '2026-09-25', 'time' => '02:30 PM', 'mode' => 'In Person', 'meeting_link' => null, 'interviewer' => 'Karan Mehta', 'status' => 'Scheduled', 'notes' => null],
                ],
            ],
            3 => [
                'id' => 3,
                'first_name' => 'Amit', 'last_name' => 'Kumar',
                'email' => 'amit.kumar@example.com', 'phone' => '+91 90000 11122', 'alternate_phone' => null,
                'date_of_birth' => '1994-01-20', 'gender' => 'Male',
                'location' => 'Pune', 'address' => 'Baner, Pune, Maharashtra',
                'highest_qualification' => 'B.Tech / B.E.', 'college' => 'Pune Institute of Computer Technology',
                'experience_years' => 6.0, 'current_company' => 'DataSprint Labs', 'current_designation' => 'Backend Developer',
                'current_salary' => '16,00,000', 'expected_salary' => '22,00,000', 'notice_period' => '60 days',
                'skills' => ['Laravel', 'PHP', 'MySQL', 'Redis', 'Docker'],
                'linkedin_url' => 'https://linkedin.com/in/amitkumar', 'portfolio_url' => 'https://amitkumar.dev',
                'resume_filename' => 'amit-kumar-resume.pdf', 'resume_size_kb' => 310,
                'notes' => 'Strong Laravel background.',
                'status' => 'Active', 'stage' => 'Task Round',
                'next_round' => 'Task Round', 'next_round_date' => 'Sep 26, 2026', 'added_days_ago' => 10,
                'rounds' => [
                    ['type' => 'HR Round', 'date' => '2026-09-20', 'time' => '03:00 PM', 'mode' => 'Virtual', 'meeting_link' => 'https://meet.google.com/hr-amit', 'interviewer' => 'Anita Desai', 'status' => 'Completed', 'notes' => 'Notice period confirmed at 60 days.'],
                    ['type' => 'Task Round', 'date' => '2026-09-26', 'time' => '10:00 AM', 'mode' => 'Virtual', 'meeting_link' => null, 'interviewer' => 'Anita Desai', 'status' => 'Pending', 'notes' => null],
                ],
            ],
            4 => [
                'id' => 4,
                'first_name' => 'Sneha', 'last_name' => 'Rao',
                'email' => 'sneha.rao@example.com', 'phone' => '+91 97777 66655', 'alternate_phone' => null,
                'date_of_birth' => '1997-11-09', 'gender' => 'Female',
                'location' => 'Bangalore', 'address' => 'Koramangala, Bangalore, Karnataka',
                'highest_qualification' => 'B.Des', 'college' => 'National Institute of Design',
                'experience_years' => 3.5, 'current_company' => 'PixelCraft Studio', 'current_designation' => 'Frontend Developer',
                'current_salary' => '11,00,000', 'expected_salary' => '16,00,000', 'notice_period' => '30 days',
                'skills' => ['React', 'TypeScript', 'Figma', 'Tailwind CSS'],
                'linkedin_url' => 'https://linkedin.com/in/sneharao', 'portfolio_url' => 'https://sneharao.design',
                'resume_filename' => 'sneha-rao-resume.pdf', 'resume_size_kb' => 275,
                'notes' => 'Excellent design sense, strong React skills.',
                'status' => 'Active', 'stage' => 'Final Round',
                'next_round' => 'Final Round', 'next_round_date' => 'Sep 26, 2026', 'added_days_ago' => 14,
                'rounds' => [
                    ['type' => 'HR Round', 'date' => '2026-09-15', 'time' => '11:00 AM', 'mode' => 'Virtual', 'meeting_link' => null, 'interviewer' => 'Karan Mehta', 'status' => 'Completed', 'notes' => null],
                    ['type' => 'Task Round', 'date' => '2026-09-18', 'time' => '11:00 AM', 'mode' => 'Virtual', 'meeting_link' => null, 'interviewer' => 'Vikram Singh', 'status' => 'Completed', 'notes' => 'Pixel-perfect submission.'],
                    ['type' => 'Technical Round', 'date' => '2026-09-21', 'time' => '01:00 PM', 'mode' => 'In Person', 'meeting_link' => null, 'interviewer' => 'Vikram Singh', 'status' => 'Completed', 'notes' => 'Strong fundamentals.'],
                    ['type' => 'Final Round', 'date' => '2026-09-26', 'time' => '04:00 PM', 'mode' => 'In Person', 'meeting_link' => null, 'interviewer' => 'Vikram Singh', 'status' => 'Scheduled', 'notes' => null],
                ],
            ],
            5 => [
                'id' => 5,
                'first_name' => 'Farhan', 'last_name' => 'Ali',
                'email' => 'farhan.ali@example.com', 'phone' => '+91 96666 55544', 'alternate_phone' => null,
                'date_of_birth' => '1995-06-30', 'gender' => 'Male',
                'location' => 'Hyderabad', 'address' => 'Gachibowli, Hyderabad, Telangana',
                'highest_qualification' => 'M.Tech', 'college' => 'IIIT Hyderabad',
                'experience_years' => 5.0, 'current_company' => 'CloudEdge Systems', 'current_designation' => 'Backend Developer',
                'current_salary' => '18,00,000', 'expected_salary' => '25,00,000', 'notice_period' => '90 days',
                'skills' => ['Node.js', 'PostgreSQL', 'AWS', 'Microservices'],
                'linkedin_url' => 'https://linkedin.com/in/farhanali', 'portfolio_url' => null,
                'resume_filename' => 'farhan-ali-resume.pdf', 'resume_size_kb' => 290,
                'notes' => 'Long notice period — flag for early scheduling.',
                'status' => 'Active', 'stage' => 'Technical Round',
                'next_round' => 'Technical Round', 'next_round_date' => 'Sep 27, 2026', 'added_days_ago' => 8,
                'rounds' => [
                    ['type' => 'HR Round', 'date' => '2026-09-17', 'time' => '09:30 AM', 'mode' => 'Virtual', 'meeting_link' => null, 'interviewer' => 'Karan Mehta', 'status' => 'Completed', 'notes' => null],
                    ['type' => 'Task Round', 'date' => '2026-09-20', 'time' => '09:30 AM', 'mode' => 'Virtual', 'meeting_link' => null, 'interviewer' => 'Anita Desai', 'status' => 'Completed', 'notes' => null],
                    ['type' => 'Technical Round', 'date' => '2026-09-27', 'time' => '09:30 AM', 'mode' => 'Virtual', 'meeting_link' => 'https://meet.google.com/tech-farhan', 'interviewer' => 'Karan Mehta', 'status' => 'Rescheduled', 'notes' => 'Moved from Sep 24 at candidate\'s request.'],
                ],
            ],
            6 => [
                'id' => 6,
                'first_name' => 'Meera', 'last_name' => 'Nair',
                'email' => 'meera.nair@example.com', 'phone' => '+91 95555 44433', 'alternate_phone' => null,
                'date_of_birth' => '1999-02-14', 'gender' => 'Female',
                'location' => 'Remote', 'address' => 'Kochi, Kerala',
                'highest_qualification' => 'B.Des', 'college' => 'Srishti Institute',
                'experience_years' => 1.5, 'current_company' => 'Freelance', 'current_designation' => 'UI/UX Designer',
                'current_salary' => '5,00,000', 'expected_salary' => '8,00,000', 'notice_period' => 'Immediate',
                'skills' => ['Figma', 'User Research', 'Prototyping'],
                'linkedin_url' => 'https://linkedin.com/in/meeranair', 'portfolio_url' => 'https://meeranair.design',
                'resume_filename' => 'meera-nair-portfolio.pdf', 'resume_size_kb' => 512,
                'notes' => null,
                'status' => 'Active', 'stage' => 'New',
                'next_round' => null, 'next_round_date' => null, 'added_days_ago' => 1,
                'rounds' => [],
            ],
            7 => [
                'id' => 7,
                'first_name' => 'Vikram', 'last_name' => 'Singh',
                'email' => 'vikram.singh@example.com', 'phone' => '+91 94444 33322', 'alternate_phone' => null,
                'date_of_birth' => '1993-09-05', 'gender' => 'Male',
                'location' => 'Delhi NCR', 'address' => 'Gurgaon, Haryana',
                'highest_qualification' => 'B.Tech / B.E.', 'college' => 'Delhi Technological University',
                'experience_years' => 7.0, 'current_company' => 'QAFirst Technologies', 'current_designation' => 'QA Engineer',
                'current_salary' => '13,00,000', 'expected_salary' => '17,00,000', 'notice_period' => '30 days',
                'skills' => ['Selenium', 'Cypress', 'API Testing'],
                'linkedin_url' => 'https://linkedin.com/in/vikramsingh', 'portfolio_url' => null,
                'resume_filename' => 'vikram-singh-resume.pdf', 'resume_size_kb' => 198,
                'notes' => 'Did not meet the bar on automation depth.',
                'status' => 'Rejected', 'stage' => 'Task Round',
                'next_round' => null, 'next_round_date' => null, 'added_days_ago' => 16,
                'rounds' => [
                    ['type' => 'HR Round', 'date' => '2026-09-10', 'time' => '11:00 AM', 'mode' => 'Virtual', 'meeting_link' => null, 'interviewer' => 'Karan Mehta', 'status' => 'Completed', 'notes' => null],
                    ['type' => 'Task Round', 'date' => '2026-09-13', 'time' => '11:00 AM', 'mode' => 'Virtual', 'meeting_link' => null, 'interviewer' => 'Vikram Singh', 'status' => 'Completed', 'notes' => 'Below expectations on automation coverage.'],
                ],
            ],
            8 => [
                'id' => 8,
                'first_name' => 'Ananya', 'last_name' => 'Iyer',
                'email' => 'ananya.iyer@example.com', 'phone' => '+91 93333 22211', 'alternate_phone' => null,
                'date_of_birth' => '1992-12-25', 'gender' => 'Female',
                'location' => 'Bangalore', 'address' => 'Indiranagar, Bangalore, Karnataka',
                'highest_qualification' => 'MBA', 'college' => 'IIM Bangalore',
                'experience_years' => 8.0, 'current_company' => 'GrowthLoop Inc', 'current_designation' => 'Product Manager',
                'current_salary' => '28,00,000', 'expected_salary' => '35,00,000', 'notice_period' => '90 days',
                'skills' => ['Product Strategy', 'Roadmapping', 'Analytics'],
                'linkedin_url' => 'https://linkedin.com/in/ananyaiyer', 'portfolio_url' => null,
                'resume_filename' => 'ananya-iyer-resume.pdf', 'resume_size_kb' => 220,
                'notes' => null,
                'status' => 'Active', 'stage' => 'New',
                'next_round' => null, 'next_round_date' => null, 'added_days_ago' => 2,
                'rounds' => [],
            ],
            9 => [
                'id' => 9,
                'first_name' => 'Rohan', 'last_name' => 'Verma',
                'email' => 'rohan.verma@example.com', 'phone' => '+91 92222 11100', 'alternate_phone' => null,
                'date_of_birth' => '1996-03-18', 'gender' => 'Male',
                'location' => 'Pune', 'address' => 'Kothrud, Pune, Maharashtra',
                'highest_qualification' => 'B.Sc', 'college' => 'Fergusson College',
                'experience_years' => 4.0, 'current_company' => 'ByteForge', 'current_designation' => 'Full Stack Developer',
                'current_salary' => '12,00,000', 'expected_salary' => '18,00,000', 'notice_period' => '45 days',
                'skills' => ['Laravel', 'Vue.js', 'MySQL'],
                'linkedin_url' => null, 'portfolio_url' => 'https://rohanverma.dev',
                'resume_filename' => 'rohan-verma-resume.pdf', 'resume_size_kb' => 260,
                'notes' => null,
                'status' => 'Selected', 'stage' => 'Selected',
                'next_round' => null, 'next_round_date' => null, 'added_days_ago' => 25,
                'rounds' => [
                    ['type' => 'HR Round', 'date' => '2026-09-02', 'time' => '11:00 AM', 'mode' => 'Virtual', 'meeting_link' => null, 'interviewer' => 'Karan Mehta', 'status' => 'Completed', 'notes' => null],
                    ['type' => 'Task Round', 'date' => '2026-09-05', 'time' => '11:00 AM', 'mode' => 'Virtual', 'meeting_link' => null, 'interviewer' => 'Anita Desai', 'status' => 'Completed', 'notes' => null],
                    ['type' => 'Technical Round', 'date' => '2026-09-08', 'time' => '11:00 AM', 'mode' => 'In Person', 'meeting_link' => null, 'interviewer' => 'Anita Desai', 'status' => 'Completed', 'notes' => null],
                    ['type' => 'Final Round', 'date' => '2026-09-12', 'time' => '03:00 PM', 'mode' => 'In Person', 'meeting_link' => null, 'interviewer' => 'Vikram Singh', 'status' => 'Completed', 'notes' => 'Offer approved.'],
                ],
            ],
            10 => [
                'id' => 10,
                'first_name' => 'Kavya', 'last_name' => 'Reddy',
                'email' => 'kavya.reddy@example.com', 'phone' => '+91 91111 00099', 'alternate_phone' => null,
                'date_of_birth' => '1998-07-22', 'gender' => 'Female',
                'location' => 'Hyderabad', 'address' => 'Madhapur, Hyderabad, Telangana',
                'highest_qualification' => 'B.Tech / B.E.', 'college' => 'Osmania University',
                'experience_years' => 2.5, 'current_company' => 'Nimbus Cloud', 'current_designation' => 'DevOps Engineer',
                'current_salary' => '9,00,000', 'expected_salary' => '13,00,000', 'notice_period' => '30 days',
                'skills' => ['Docker', 'Kubernetes', 'CI/CD', 'AWS'],
                'linkedin_url' => 'https://linkedin.com/in/kavyareddy', 'portfolio_url' => null,
                'resume_filename' => 'kavya-reddy-resume.pdf', 'resume_size_kb' => 205,
                'notes' => null,
                'status' => 'Active', 'stage' => 'HR Round',
                'next_round' => 'HR Round', 'next_round_date' => 'Sep 28, 2026', 'added_days_ago' => 4,
                'rounds' => [
                    ['type' => 'HR Round', 'date' => '2026-09-28', 'time' => '10:00 AM', 'mode' => 'Virtual', 'meeting_link' => 'https://meet.google.com/hr-kavya', 'interviewer' => 'Anita Desai', 'status' => 'Scheduled', 'notes' => null],
                ],
            ],
            11 => [
                'id' => 11,
                'first_name' => 'Arjun', 'last_name' => 'Menon',
                'email' => 'arjun.menon@example.com', 'phone' => '+91 90999 88877', 'alternate_phone' => null,
                'date_of_birth' => '1991-05-11', 'gender' => 'Male',
                'location' => 'Bangalore', 'address' => 'Whitefield, Bangalore, Karnataka',
                'highest_qualification' => 'M.Tech', 'college' => 'IIT Madras',
                'experience_years' => 9.0, 'current_company' => 'CoreStack Systems', 'current_designation' => 'Engineering Lead',
                'current_salary' => '32,00,000', 'expected_salary' => '42,00,000', 'notice_period' => '90 days',
                'skills' => ['System Design', 'Java', 'Kafka', 'Team Leadership'],
                'linkedin_url' => 'https://linkedin.com/in/arjunmenon', 'portfolio_url' => null,
                'resume_filename' => 'arjun-menon-resume.pdf', 'resume_size_kb' => 340,
                'notes' => 'Overqualified for the role, keep warm for senior openings.',
                'status' => 'On Hold', 'stage' => 'HR Round',
                'next_round' => null, 'next_round_date' => null, 'added_days_ago' => 20,
                'rounds' => [
                    ['type' => 'HR Round', 'date' => '2026-09-05', 'time' => '04:00 PM', 'mode' => 'Virtual', 'meeting_link' => null, 'interviewer' => 'Karan Mehta', 'status' => 'Completed', 'notes' => 'Put on hold — budget mismatch for now.'],
                ],
            ],
            12 => [
                'id' => 12,
                'first_name' => 'Divya', 'last_name' => 'Joshi',
                'email' => 'divya.joshi@example.com', 'phone' => '+91 90888 77766', 'alternate_phone' => null,
                'date_of_birth' => '2000-01-30', 'gender' => 'Female',
                'location' => 'Mumbai', 'address' => 'Powai, Mumbai, Maharashtra',
                'highest_qualification' => 'B.Tech / B.E.', 'college' => 'VJTI Mumbai',
                'experience_years' => 0.6, 'current_company' => null, 'current_designation' => 'Fresher',
                'current_salary' => null, 'expected_salary' => '6,00,000', 'notice_period' => 'Immediate',
                'skills' => ['JavaScript', 'React', 'Git'],
                'linkedin_url' => 'https://linkedin.com/in/divyajoshi', 'portfolio_url' => 'https://divyajoshi.dev',
                'resume_filename' => 'divya-joshi-resume.pdf', 'resume_size_kb' => 150,
                'notes' => null,
                'status' => 'Active', 'stage' => 'New',
                'next_round' => null, 'next_round_date' => null, 'added_days_ago' => 0,
                'rounds' => [],
            ],
        ];
    }

    public static function find(int $id): ?array
    {
        return static::all()[$id] ?? null;
    }

    /**
     * Every round across every candidate, flattened into one list with the
     * owning candidate's id/name attached — used by the global Rounds list.
     */
    public static function allRoundsFlattened(): array
    {
        $rounds = [];

        foreach (static::all() as $candidate) {
            foreach ($candidate['rounds'] as $round) {
                $rounds[] = $round + [
                    'candidate_id' => $candidate['id'],
                    'candidate_name' => $candidate['first_name'].' '.$candidate['last_name'],
                ];
            }
        }

        return $rounds;
    }

    /**
     * A single round, identified by the candidate it belongs to and its
     * round type (candidates only ever have one round of each type).
     */
    public static function findRound(int $candidateId, string $roundType): ?array
    {
        $candidate = static::find($candidateId);

        if (! $candidate) {
            return null;
        }

        foreach ($candidate['rounds'] as $round) {
            if ($round['type'] === $roundType) {
                return $round + [
                    'candidate_id' => $candidate['id'],
                    'candidate_name' => $candidate['first_name'].' '.$candidate['last_name'],
                ];
            }
        }

        return null;
    }
}
