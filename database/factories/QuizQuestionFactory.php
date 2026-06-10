<?php

namespace Database\Factories;

use App\Models\Quiz;
use App\Models\QuizQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QuizQuestion>
 */
class QuizQuestionFactory extends Factory
{
    protected $model = QuizQuestion::class;

    public function definition(): array
    {
        return [
            'quiz_id' => Quiz::factory(),
            'position' => 1,
            'question_text' => $this->faker->sentence(),
            'choice_a' => $this->faker->sentence(3),
            'choice_b' => $this->faker->sentence(3),
            'choice_c' => $this->faker->sentence(3),
            'choice_d' => $this->faker->sentence(3),
            'correct_answer' => 'A',
            'explanation' => $this->faker->sentence(),
        ];
    }
}
