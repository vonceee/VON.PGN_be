<?php

namespace Database\Factories;

use App\Models\Chapter;
use App\Models\Lesson;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lesson>
 */
class LessonFactory extends Factory
{
    protected $model = Lesson::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(4, false);

        return [
            'chapter_id'     => Chapter::factory(),
            'title'          => $title,
            'slug'           => Str::slug($title),
            'content_blocks' => [
                [
                    'type'    => 'text',
                    'content' => fake()->paragraph(3),
                ],
            ],
            'order'          => fake()->numberBetween(1, 20),
        ];
    }
}
