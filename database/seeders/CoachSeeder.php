<?php

namespace Database\Seeders;

use App\Models\Coach;
use Illuminate\Database\Seeder;

class CoachSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $coaches = [
            [
                'id' => 'yves-rañola',
                'name' => 'Yves Rañola',
                'title' => 'IM',
                'short_info' => 'The best way to learn fast is to observe, listen, practice, and play!',
                'fide_rating' => 2368,
                'profile_picture' => 'assets/coaches/yves.webp',
                'is_academy_instructor' => true,
                'playing_experience' => [
                    'Peak FIDE rating (2467)',
                    'Multiple Times Philippine Representative in Asia & Europe Tournaments',
                    '1992 National Junior Chess Champion'
                ],
                'teaching_experience' => [
                    'FIDE Trainer (2007)',
                    'Singapore ASEAN Chess Academy Trainer (2006-2010)',
                    'Far Eastern University Head Coach (2011-2016)',
                    'Philippine Chess Team Head Coach (2011-2016)',
                    'Ateneo De Manila University Head Coach (2016-PRESENT)',
                    'Former Coach of GM John Paul Gomez, IM Paulo Bersamina, and IM Cyrus Low'
                ],
                'bio' => 'Greetings! I am Yves Ranola from the Philippines, an International Master and a Chess coach by profession.',
                'location' => 'Caloocan City, Philippines',
                'availability' => 'Accepting Students.',
                'teaching_methods' => [
                    'Learner - centered method',
                    'Interactive and reflective',
                    'High-quality resources and always up-to-date info',
                ],
                'coaching_type' => 'Online & Onsite',
                'social_media' => [
                    'facebook' => 'https://www.facebook.com/yves.ranola',
                    'lichess' => 'https://lichess.org/@/General_Vishy'
                ]
            ],
            [
                'id' => 'luffe-magdalaga',
                'name' => 'Luffe Magdalaga',
                'title' => 'NM',
                'short_info' => 'Dedicated chess coach focused on building strong tactical foundations and tournament readiness.',
                'fide_rating' => 2040,
                'profile_picture' => 'assets/coaches/luffe.webp',
                'is_academy_instructor' => true,
                'playing_experience' => [
                    'Former Varsity Player for the Far East University (FEU) Chess Team',
                    'Multiple-time medalist in the Philippine National Age Group Chess Championships',
                    'Active competitor in local and national open tournaments'
                ],
                'teaching_experience' => [
                    'FEU Chess Coach (2018-2022)',
                    '5+ years of 1-on-1 private coaching for kids and adult improvers',
                    'HeadCoach for Ateneo De Manila University Chess Team (2022-PRESENT)'
                ],
                'bio' => 'Kamusta! I am NM Luffe Magdalaga, a passionate chess player and coach from the Philippines. My goal is to help aspiring players reach their full potential, whether you are aiming to win local tournaments or simply want to improve your online rating. We will focus on practical opening lines, sharpening your tactical vision, and mastering essential endgame techniques. Let\'s work together to take your game to the next level!',
                'location' => 'Metro Manila, Philippines (Timezone: PHT / GMT+8)',
                'availability' => 'Available for weekday evenings and flexible weekend slots.',
                'teaching_methods' => [
                    'In-depth analysis of student\'s games',
                    'Customized tactical and positional drills',
                    'Building a practical opening repertoire',
                    'Tournament psychology and time management'
                ],
                'coaching_type' => 'Online & Onsite',
                'social_media' => [
                    'instagram' => 'https://instagram.com/luffe_chess',
                    'youtube' => 'https://youtube.com/@LuffeMagdalagaChess'
                ]
            ],
            [
                'id' => 'von-cedric-rañola',
                'name' => 'Von Cedric Rañola',
                'title' => '',
                'short_info' => 'Local Caloocan champion, Former National University Manila Varsity, National University Fairview Champion.',
                'fide_rating' => 2150,
                'profile_picture' => 'assets/coaches/von.webp',
                'is_academy_instructor' => true,
                'playing_experience' => [
                    'Local Caloocan Champion',
                    'Former National University Manila Varsity Player',
                    'National University Fairview Champion',
                    'Multiple-time local open tournament finalist'
                ],
                'teaching_experience' => [
                    'Private coaching for aspiring competitive players',
                    'Specialized trainer for tactical development'
                ],
                'bio' => 'Von Cedric is a proven competitor with a strong track record in university-level chess. His coaching style focuses on the practical application of tactics and building a resilient mental game for tournament play.',
                'location' => 'Caloocan City, Philippines',
                'availability' => 'Available for evening and weekend training.',
                'teaching_methods' => [
                    'Tactical vision training',
                    'Tournament preparation',
                    'Opening strategy for competitive play'
                ],
                'coaching_type' => 'Online & Onsite',
                'social_media' => [
                    'facebook' => 'https://facebook.com/voncedric.ranola'
                ]
            ]
        ];

        foreach ($coaches as $coachData) {
            Coach::updateOrCreate(['id' => $coachData['id']], $coachData);
        }
    }
}
