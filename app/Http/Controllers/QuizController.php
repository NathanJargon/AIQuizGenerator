<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuizController extends Controller
{
    public function index(Request $request): View
    {
        return view('dashboard', [
            'quizzes' => Quiz::withCount('questions')
                ->latest()
                ->get(),
        ]);
    }

    public function show(Quiz $quiz): View
    {
        return view('quizzes.show', [
            'quiz' => $quiz->load('questions'),
        ]);
    }

    public function destroy(Quiz $quiz): RedirectResponse
    {
        $quiz->delete();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Quiz deleted successfully.');
    }

}