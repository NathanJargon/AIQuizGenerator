<?php

namespace Database\Factories;

use App\Models\Quiz;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Quiz>
 */
class QuizFactory extends Factory
{
    protected $model = Quiz::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => $this->faker->sentence(4),
            'source_filename' => $this->faker->word() . '.pdf',
            'source_file_path' => 'pdfs/' . $this->faker->uuid() . '.pdf',
            'source_text' => $this->faker->paragraph(),
        ];
    }
}
