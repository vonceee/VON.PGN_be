<?php

namespace Database\Seeders;

use App\Models\Tournament;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TournamentSeeder extends Seeder
{
    public function run(): void
    {
        $tournaments = [
            [
                'name' => 'Manila Open Chess Championship 2026',
                'status' => 'upcoming',
                'start_date' => '2026-04-15',
                'end_date' => '2026-04-20',
                'registration_deadline' => '2026-04-10',
                'location' => 'SMX Convention Center, Pasay City, Philippines',
                'latitude' => 14.5349,
                'longitude' => 120.9846,
                'format' => 'Swiss System, 9 Rounds',
                'time_control' => '90 min + 30 sec increment',
                'entry_fee' => '₱2,500',
                'prize_pool' => '₱500,000',
                'organizer' => 'National Chess Federation of the Philippines',
                'contact_email' => 'tournaments@ncfph.org',
                'description' => 'The Manila Open Chess Championship returns for its 12th edition! Open to players of all nationalities and ratings. This FIDE-rated event brings together the best players in Southeast Asia for an exciting week of competitive chess.',
                'rounds' => 9,
                'current_participants' => 87,
                'max_participants' => 128,
                'eligibility' => [
                    'Bonafide NCFP member',
                    'FIDE standard rating 2099 and below',
                    'Must present valid FIDE ID upon registration',
                ],
                'categories' => [
                    'open' => [
                        'eligibility' => ['Bonafide NCFP member', 'FIDE standard rating 2099 and under'],
                        'prizes' => [
                            'champion' => '₱50,000 + trophy & certificate',
                            '2nd_place' => '₱30,000 + medal & certificate',
                            '3rd_place' => '₱20,000 + medal & certificate',
                            '4th_place' => '₱10,000 + medal & certificate',
                            '5th_place' => '₱7,000 + medal & certificate',
                            '6th_to_10th' => '₱4,000 + medal & certificate',
                            '11th_to_15th' => '₱2,000 + certificate',
                        ],
                        'specialAwards' => [
                            'Top Lady' => ['1st' => '₱5,000 + medal', '2nd' => '₱3,000 + medal', '3rd' => '₱2,000 + medal'],
                            'Top Senior' => ['1st' => '₱3,000 + medal', '2nd' => '₱2,000 + medal', '3rd' => '₱1,500 + medal'],
                            'Top Junior' => ['1st' => '₱3,000 + medal', '2nd' => '₱2,000 + medal', '3rd' => '₱1,500 + medal'],
                            'Top Youth' => '₱3,000 + medal & certificate',
                            'Top Kiddie' => '₱3,000 + medal & certificate',
                        ],
                    ],
                    'under_14' => [
                        'eligibility' => ['Bonafide NCFP member', 'Born 2012 and later'],
                        'prizes' => [
                            'champion' => '₱20,000 + medal & certificate',
                            '2nd_place' => '₱12,000 + medal & certificate',
                            '3rd_place' => '₱8,000 + medal & certificate',
                            '4th_place' => '₱5,000 + medal & certificate',
                            '5th_place' => '₱3,000 + medal & certificate',
                            '6th_to_10th' => '₱2,000 + medal & certificate',
                        ],
                        'specialAwards' => [
                            'Top Girl' => ['1st' => '₱3,000 + medal', '2nd' => '₱2,000 + medal', '3rd' => '₱1,500 + medal'],
                            'Top 8 Years Old & Under' => '₱2,000 + medal & certificate',
                            'Top 10 Years Old & Under' => '₱2,000 + medal & certificate',
                            'Top 12 Years Old & Under' => '₱2,000 + medal & certificate',
                        ],
                    ],
                ],
                'schedule' => [
                    'day_1' => [
                        'date' => '2026-04-15',
                        'events' => [
                            ['name' => 'Registration & Check-in', 'time' => '7:00 AM – 8:30 AM'],
                            ['name' => 'Opening Ceremony', 'time' => '8:30 AM – 9:00 AM'],
                            ['name' => 'Round 1', 'time' => '9:00 AM – 1:00 PM'],
                            ['name' => 'Round 2', 'time' => '2:00 PM – 6:00 PM'],
                        ],
                    ],
                    'day_2' => [
                        'date' => '2026-04-16',
                        'events' => [
                            ['name' => 'Round 3', 'time' => '9:00 AM – 1:00 PM'],
                            ['name' => 'Round 4', 'time' => '2:00 PM – 6:00 PM'],
                        ],
                    ],
                    'day_3' => [
                        'date' => '2026-04-17',
                        'events' => [
                            ['name' => 'Round 5', 'time' => '9:00 AM – 1:00 PM'],
                            ['name' => 'Round 6', 'time' => '2:00 PM – 6:00 PM'],
                        ],
                    ],
                    'day_4' => [
                        'date' => '2026-04-18',
                        'events' => [
                            ['name' => 'Round 7', 'time' => '9:00 AM – 1:00 PM'],
                            ['name' => 'Round 8', 'time' => '2:00 PM – 6:00 PM'],
                        ],
                    ],
                    'day_5' => [
                        'date' => '2026-04-19',
                        'events' => [
                            ['name' => 'Round 9', 'time' => '9:00 AM – 1:00 PM'],
                            ['name' => 'Closing & Awarding Ceremony', 'time' => '3:00 PM – 5:00 PM'],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'ASEAN Youth Chess Championship 2026',
                'status' => 'ongoing',
                'start_date' => '2026-03-20',
                'end_date' => '2026-03-30',
                'registration_deadline' => '2026-03-15',
                'location' => 'Grand Hyatt Manila, BGC, Philippines',
                'latitude' => 14.5515,
                'longitude' => 121.0506,
                'format' => 'Round Robin, 7 Rounds',
                'time_control' => '60 min + 30 sec increment',
                'entry_fee' => '₱1,500',
                'prize_pool' => '₱200,000',
                'organizer' => 'ASEAN Chess Federation',
                'contact_email' => 'info@aseanchess.org',
                'description' => 'The premier youth chess event in Southeast Asia. Players aged 8-18 from all ASEAN member nations compete for regional glory and the chance to represent their countries at the World Youth Chess Championship.',
                'rounds' => 7,
                'current_participants' => 64,
                'max_participants' => 64,
                'eligibility' => [
                    'Age 8-18 years old',
                    'Citizen of an ASEAN member state',
                    'Valid FIDE ID',
                ],
            ],
            [
                'name' => 'Pasig Rapid Chess Open 2026',
                'status' => 'upcoming',
                'start_date' => '2026-05-03',
                'end_date' => '2026-05-03',
                'registration_deadline' => '2026-05-01',
                'location' => 'Pasig City Sports Complex, Pasig, Philippines',
                'latitude' => 14.5764,
                'longitude' => 121.0851,
                'format' => 'Swiss System, 7 Rounds',
                'time_control' => '15 min + 10 sec increment',
                'entry_fee' => '₱500',
                'prize_pool' => '₱80,000',
                'organizer' => 'Pasig Chess Club',
                'contact_email' => 'pasigchess@gmail.com',
                'description' => 'A one-day rapid chess tournament perfect for players who want a quick competitive experience. All ages and ratings welcome. Trophies for the top 10 finishers.',
                'rounds' => 7,
                'current_participants' => 42,
                'max_participants' => 100,
                'eligibility' => [
                    'Open to all nationalities and rating levels',
                    'Valid FIDE ID or NCFP membership',
                ],
                'schedule' => [
                    'day_1' => [
                        'date' => '2026-05-03',
                        'events' => [
                            ['name' => 'Registration & Check-in', 'time' => '7:30 AM – 8:30 AM'],
                            ['name' => 'Opening Ceremony', 'time' => '8:30 AM – 9:00 AM'],
                            ['name' => 'Round 1', 'time' => '9:00 AM – 10:00 AM'],
                            ['name' => 'Round 2', 'time' => '10:10 AM – 11:10 AM'],
                            ['name' => 'Round 3', 'time' => '11:20 AM – 12:20 PM'],
                            ['name' => 'Lunch Break', 'time' => '12:20 PM – 1:20 PM'],
                            ['name' => 'Round 4', 'time' => '1:20 PM – 2:20 PM'],
                            ['name' => 'Round 5', 'time' => '2:30 PM – 3:30 PM'],
                            ['name' => 'Round 6', 'time' => '3:40 PM – 4:40 PM'],
                            ['name' => 'Round 7', 'time' => '4:50 PM – 5:50 PM'],
                            ['name' => 'Awarding Ceremony', 'time' => '6:15 PM'],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Quezon City Invitational Masters 2025',
                'status' => 'past',
                'start_date' => '2025-11-10',
                'end_date' => '2025-11-17',
                'registration_deadline' => '2025-10-25',
                'location' => 'Novotel Manila Araneta City, Quezon City, Philippines',
                'latitude' => 14.6186,
                'longitude' => 121.0510,
                'format' => 'Round Robin, 10 Rounds',
                'time_control' => '90 min + 30 sec increment',
                'entry_fee' => 'Invitational (No Entry Fee)',
                'prize_pool' => '₱300,000',
                'organizer' => 'Quezon City Chess Association',
                'contact_email' => 'qcchess2025@gmail.com',
                'description' => 'An elite invitational tournament featuring 10 of the strongest players in the Philippines. This prestigious event showcases the highest level of chess talent in the country.',
                'rounds' => 10,
                'current_participants' => 10,
                'max_participants' => 10,
                'winner' => 'GM John Paul Gomez',
                'standings' => [
                    ['rank' => 1, 'player' => 'GM John Paul Gomez', 'score' => 7.5],
                    ['rank' => 2, 'player' => 'IM Paulo Bersamina', 'score' => 7.0],
                    ['rank' => 3, 'player' => 'GM Mark Paragua', 'score' => 6.5],
                    ['rank' => 4, 'player' => 'IM Daniel Quizon', 'score' => 6.0],
                    ['rank' => 5, 'player' => 'IM Jan Emmanuel Garcia', 'score' => 5.5],
                ],
            ],
            [
                'name' => 'Makati Weekend Blitz Series 2025',
                'status' => 'past',
                'start_date' => '2025-12-06',
                'end_date' => '2025-12-07',
                'registration_deadline' => '2025-12-04',
                'location' => 'Ayala Museum Function Hall, Makati, Philippines',
                'latitude' => 14.5544,
                'longitude' => 121.0205,
                'format' => 'Swiss System, 9 Rounds',
                'time_control' => '3 min + 2 sec increment',
                'entry_fee' => '₱300',
                'prize_pool' => '₱50,000',
                'organizer' => 'Makati Blitz Chess Club',
                'contact_email' => 'makatiblitz@outlook.com',
                'description' => 'A thrilling weekend blitz chess event in the heart of Makati business district. Fast-paced games, exciting finishes, and a vibrant chess community atmosphere.',
                'rounds' => 9,
                'current_participants' => 48,
                'max_participants' => 48,
                'winner' => 'IM Cyrus Low',
                'standings' => [
                    ['rank' => 1, 'player' => 'IM Cyrus Low', 'score' => 8.0],
                    ['rank' => 2, 'player' => 'FM Roel Abelgas', 'score' => 7.5],
                    ['rank' => 3, 'player' => 'NM Luffe Magdalaga', 'score' => 7.0],
                    ['rank' => 4, 'player' => 'John Dave Lavandero', 'score' => 6.5],
                    ['rank' => 5, 'player' => 'Jerome Villanueva', 'score' => 6.0],
                ],
            ],
        ];

        foreach ($tournaments as $data) {
            Tournament::updateOrCreate(
                ['slug' => Str::slug($data['name'])],
                array_merge($data, ['slug' => Str::slug($data['name'])])
            );
        }
    }
}
