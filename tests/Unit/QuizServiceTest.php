<?php

namespace Tests\Unit;

use App\Services\QuizGenerationException;
use App\Services\QuizService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class QuizServiceTest extends TestCase
{
    public function test_it_sends_the_expected_prompt_and_model_to_groq(): void
    {
        config(['services.groq.key' => 'test-key', 'services.groq.base_url' => 'https://api.groq.com/openai/v1']);

        Http::fake([
            'https://api.groq.com/openai/v1/chat/completions' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => json_encode([
                                'title' => 'Sample Quiz',
                                'questions' => array_map(function (int $position) {
                                    return [
                                        'question' => 'Question ' . $position,
                                        'choices' => ['A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D'],
                                        'correct_answer' => 'A',
                                        'explanation' => 'Explanation ' . $position,
                                    ];
                                }, range(1, 15)),
                            ]),
                        ],
                    ],
                ],
            ]),
        ]);

        $service = app(QuizService::class);
        $result = $service->generate('Extracted PDF text', 'module.pdf');

        $this->assertSame('Sample Quiz', $result['title']);
        $this->assertCount(15, $result['questions']);

        Http::assertSent(function ($request) {
            $body = $request->data();

            return $request->url() === 'https://api.groq.com/openai/v1/chat/completions'
                && $body['model'] === 'llama-3.3-70b-versatile'
                && str_contains($body['messages'][1]['content'], 'Extracted PDF text')
                && str_contains($body['messages'][1]['content'], 'module.pdf');
        });
    }

    public function test_it_throws_a_user_friendly_error_for_malformed_json(): void
    {
        config(['services.groq.key' => 'test-key', 'services.groq.base_url' => 'https://api.groq.com/openai/v1']);

        Http::fake([
            'https://api.groq.com/openai/v1/chat/completions' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => 'not json',
                        ],
                    ],
                ],
            ]),
        ]);

        $this->expectException(QuizGenerationException::class);
        $this->expectExceptionMessage('Groq returned malformed quiz data. Please try again.');

        app(QuizService::class)->generate('Extracted PDF text', 'module.pdf');
    }
}
