<?php

namespace Tests\Feature;

use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_lists_the_authenticated_users_quizzes(): void
    {
        $user = User::factory()->create();
        $quiz = Quiz::create([
            'user_id' => $user->id,
            'title' => 'Algebra Basics',
            'source_filename' => 'algebra.pdf',
            'source_file_path' => 'pdfs/algebra.pdf',
            'source_text' => 'Extracted text',
        ]);

        QuizQuestion::factory()->count(15)->create(['quiz_id' => $quiz->id]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Algebra Basics')
            ->assertSee('15 questions');
    }

    public function test_a_quiz_page_shows_all_questions_with_answers(): void
    {
        $user = User::factory()->create();
        $quiz = Quiz::create([
            'user_id' => $user->id,
            'title' => 'Physics Quiz',
            'source_filename' => 'physics.pdf',
            'source_file_path' => 'pdfs/physics.pdf',
            'source_text' => 'Extracted text',
        ]);

        QuizQuestion::factory()->count(15)->create(['quiz_id' => $quiz->id]);

        $response = $this->actingAs($user)->get(route('quizzes.show', $quiz));

        $response->assertOk();
        $response->assertSee('Physics Quiz');
        $response->assertSee('Correct Answer:');
        $response->assertSee('Explanation:');
    }

    public function test_a_user_can_delete_their_quiz_and_questions(): void
    {
        $user = User::factory()->create();
        $quiz = Quiz::create([
            'user_id' => $user->id,
            'title' => 'History Quiz',
            'source_filename' => 'history.pdf',
            'source_file_path' => 'pdfs/history.pdf',
            'source_text' => 'Extracted text',
        ]);

        QuizQuestion::factory()->count(15)->create(['quiz_id' => $quiz->id]);

        $this->actingAs($user)
            ->delete(route('quizzes.destroy', $quiz))
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseMissing('quizzes', ['id' => $quiz->id]);
        $this->assertDatabaseCount('quiz_questions', 0);
    }

    public function test_a_user_cannot_view_another_users_quiz(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $quiz = Quiz::create([
            'user_id' => $owner->id,
            'title' => 'Owner Quiz',
            'source_filename' => 'owner.pdf',
            'source_file_path' => 'pdfs/owner.pdf',
            'source_text' => 'Extracted text',
        ]);

        QuizQuestion::factory()->count(15)->create(['quiz_id' => $quiz->id]);

        $this->actingAs($intruder)
            ->get(route('quizzes.show', $quiz))
            ->assertForbidden();
    }
}
