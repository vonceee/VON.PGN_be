<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    protected $model = Course::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->randomElement([
            'Chess Fundamentals',
            'Opening Principles',
            'Tactical Patterns',
            'Endgame Mastery',
            'Middlegame Strategy',
            'Pawn Structure',
            'Piece Coordination',
            'Attack & Defense',
        ]);

        return [
            'title'       => $title,
            'slug'        => Str::slug($title),
            'description' => fake()->paragraph(2),
        ];
    }
}
