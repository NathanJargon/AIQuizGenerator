<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use JsonException;

class QuizService
{
    public function generate(string $extractedText, string $sourceTitle): array
    {
        if (blank(config('services.groq.key'))) {
            throw new QuizGenerationException('GROQ_API_KEY is not configured.');
        }

        try {
            $response = Http::baseUrl(config('services.groq.base_url'))
                ->withToken(config('services.groq.key'))
                ->acceptJson()
                ->timeout(120)
                ->post('/chat/completions', [
                    'model' => 'llama-3.3-70b-versatile',
                    'temperature' => 0.2,
                    'response_format' => ['type' => 'json_object'],
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You generate quiz JSON only. Return valid JSON only, with no markdown, code fences, or commentary.',
                        ],
                        [
                            'role' => 'user',
                            'content' => $this->buildPrompt($extractedText, $sourceTitle),
                        ],
                    ],
                ]);

        } catch (ConnectionException $exception) {
            throw new QuizGenerationException('Unable to reach Groq. Please try again.');
        }

        if (! $response->successful()) {
            throw new QuizGenerationException('Groq returned an unexpected response. Please try again.');
        }

        $content = data_get($response->json(), 'choices.0.message.content');

        $decoded = $this->decodeJson($content);

        $questions = $decoded['questions'] ?? null;

        logger()->info('Quiz debug', [
            'has_questions_key' => array_key_exists('questions', $decoded),
            'questions_type' => gettype($questions),
            'question_count' => is_array($questions) ? count($questions) : 'not array',
            'first_question' => is_array($questions) && count($questions) ? $questions[0] : null,
        ]);

        if (! is_array($questions)) {
            throw new QuizGenerationException('Groq returned malformed quiz data.');
        }

        if (count($questions) < 15) {
            throw new QuizGenerationException('Groq returned insufficient quiz data.');
        }

        $questions = array_slice($questions, 0, 15);

        $normalizedQuestions = [];

        foreach ($questions as $index => $question) {
            if (! is_array($question)) {
                throw new QuizGenerationException('Groq returned malformed quiz data. Please try again.');
            }

            foreach (['question', 'choices', 'correct_answer', 'explanation'] as $requiredField) {
                if (! array_key_exists($requiredField, $question)) {
                    throw new QuizGenerationException('Groq returned malformed quiz data. Please try again.');
                }
            }

            $choices = $question['choices'];

            if (! is_array($choices)) {
                throw new QuizGenerationException('Groq returned malformed quiz data. Please try again.');
            }

            foreach (['A', 'B', 'C', 'D'] as $choiceKey) {
                if (! array_key_exists($choiceKey, $choices)) {
                    throw new QuizGenerationException('Groq returned malformed quiz data. Please try again.');
                }
            }

            $correctAnswer = strtoupper((string) $question['correct_answer']);

            if (! in_array($correctAnswer, ['A', 'B', 'C', 'D'], true)) {
                throw new QuizGenerationException('Groq returned malformed quiz data. Please try again.');
            }

            $normalizedQuestions[] = [
                'position' => $index + 1,
                'question_text' => trim((string) $question['question']),
                'choice_a' => trim((string) $choices['A']),
                'choice_b' => trim((string) $choices['B']),
                'choice_c' => trim((string) $choices['C']),
                'choice_d' => trim((string) $choices['D']),
                'correct_answer' => $correctAnswer,
                'explanation' => trim((string) $question['explanation']),
            ];
        }

        return [
            'title' => trim((string) ($decoded['title'] ?? $sourceTitle)),
            'questions' => $normalizedQuestions,
        ];
    }

    private function buildPrompt(string $extractedText, string $sourceTitle): string
    {
        return implode("\n", [
            'Create exactly 15 multiple-choice questions from the extracted PDF text below.',
            'Return only valid JSON with this structure:',
            '{',
            '  "title": "string",',
            '  "questions": [',
            '    {',
            '      "question": "string",',
            '      "choices": { "A": "string", "B": "string", "C": "string", "D": "string" },',
            '      "correct_answer": "A|B|C|D",',
            '      "explanation": "string"',
            '    }',
            '  ]',
            '}',
            'The JSON must contain exactly 15 questions.',
            'The quiz title should be based on the source title: ' . $sourceTitle,
            'Extracted PDF text:',
            $extractedText,
        ]);
    }

    private function decodeJson(string $content): array
    {
        $cleanContent = trim($content);

        if (str_starts_with($cleanContent, '```')) {
            $cleanContent = preg_replace('/^```(?:json)?\s*/', '', $cleanContent) ?? $cleanContent;
            $cleanContent = preg_replace('/\s*```$/', '', $cleanContent) ?? $cleanContent;
        }

        try {
            return json_decode($cleanContent, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new QuizGenerationException('Groq returned malformed quiz data. Please try again.');
        }
    }
}