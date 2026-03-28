<?php

namespace Database\Factories;

use App\Models\Tournament;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tournament>
 */
class TournamentFactory extends Factory
{
    protected $model = Tournament::class;

    public function definition(): array
    {
        $name = fake()->sentence(4);
        $status = fake()->randomElement(['upcoming', 'ongoing', 'past']);
        $startDate = fake()->dateTimeBetween('-2 months', '+3 months');
        $endDate = (clone $startDate)->modify('+' . fake()->numberBetween(1, 7) . ' days');

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'banner_image' => fake()->randomElement([
                'https://images.unsplash.com/photo-1529699211952-734e80c4d42b?w=1200&h=400&fit=crop',
                'https://images.unsplash.com/photo-1580541631950-7282082b53ce?w=1200&h=400&fit=crop',
                'https://images.unsplash.com/photo-1606167668584-78701c57f13d?w=1200&h=400&fit=crop',
                'https://images.unsplash.com/photo-1560174038-51f4b4c22f1d?w=1200&h=400&fit=crop',
                'https://images.unsplash.com/photo-1611532736597-de2d4265fba3?w=1200&h=400&fit=crop',
            ]),
            'status' => $status,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'registration_deadline' => (clone $startDate)->modify('-5 days'),
            'location' => fake()->randomElement([
                'SMX Convention Center, Pasay City',
                'Grand Hyatt Manila, BGC',
                'Pasig City Sports Complex',
                'Novotel Manila Araneta City',
                'Ayala Museum, Makati',
            ]),
            'latitude' => fake()->latitude(14.4, 14.8),
            'longitude' => fake()->longitude(120.9, 121.1),
            'format' => fake()->randomElement(['Swiss System', 'Round Robin', 'Arena']),
            'time_control' => fake()->randomElement([
                '90 min + 30 sec increment',
                '60 min + 30 sec increment',
                '15 min + 10 sec increment',
                '3 min + 2 sec increment',
            ]),
            'entry_fee' => '₱' . fake()->numberBetween(300, 5000),
            'prize_pool' => '₱' . number_format(fake()->numberBetween(50000, 1000000)),
            'organizer' => fake()->company() . ' Chess Club',
            'contact_email' => fake()->safeEmail(),
            'description' => fake()->paragraph(3),
            'rounds' => fake()->numberBetween(5, 11),
            'current_participants' => fake()->numberBetween(10, 100),
            'max_participants' => fake()->randomElement([32, 64, 128, 256]),
            'eligibility' => [
                'Open to all nationalities',
                'Valid FIDE ID or NCFP membership required',
            ],
            'winner' => $status === 'past' ? fake()->name() : null,
            'standings' => $status === 'past' ? $this->generateStandings() : null,
        ];
    }

    private function generateStandings(): array
    {
        $standings = [];
        for ($i = 1; $i <= 5; $i++) {
            $standings[] = [
                'rank' => $i,
                'player' => fake()->name(),
                'score' => round(8 - ($i * 0.5) + fake()->randomFloat(1, 0, 0.5), 1),
            ];
        }
        return $standings;
    }

    public function upcoming(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'upcoming',
            'start_date' => fake()->dateTimeBetween('+1 week', '+3 months'),
            'end_date' => fake()->dateTimeBetween('+2 weeks', '+4 months'),
            'winner' => null,
            'standings' => null,
        ]);
    }

    public function ongoing(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'ongoing',
            'start_date' => fake()->dateTimeBetween('-1 week', 'now'),
            'end_date' => fake()->dateTimeBetween('+1 day', '+1 week'),
            'winner' => null,
            'standings' => null,
        ]);
    }

    public function past(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'past',
            'start_date' => fake()->dateTimeBetween('-3 months', '-1 month'),
            'end_date' => fake()->dateTimeBetween('-2 months', '-3 weeks'),
            'winner' => fake()->name(),
            'standings' => $this->generateStandings(),
        ]);
    }
}
