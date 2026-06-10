<?php

namespace App\Http\Controllers;

use App\Models\PdfUpload;
use App\Services\PdfTextExtractor;
use App\Services\QuizGenerationException;
use App\Services\QuizService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;
use App\Models\Quiz;

class PdfUploadController extends Controller
{
    public function index(): View
    {
        return view('dashboard', [
            'quizzes' => \App\Models\Quiz::withCount('questions')
                ->latest()
                ->get(),
        ]);
    }

    public function store(Request $request, PdfTextExtractor $pdfTextExtractor, QuizService $quizService): RedirectResponse
    {
        $validated = $request->validate([
            'pdf_file' => ['required', 'file', 'extensions:pdf', 'mimetypes:application/pdf,application/x-pdf', 'max:102400'],
        ]);

        $uploadedFile = $validated['pdf_file'];
        $storedName = uniqid('pdf_', true) . '.pdf';
        $storedPath = Storage::disk('local')->putFileAs('pdfs', $uploadedFile, $storedName);

        $fullStoredPath = Storage::disk('local')->path($storedPath);

        try {
            $extractedText = $pdfTextExtractor->extract($fullStoredPath);

            if ($extractedText === '') {
                throw ValidationException::withMessages([
                    'pdf_file' => 'The uploaded PDF could not be read. Please upload a valid, readable PDF file.',
                ]);
            }
        } catch (\Throwable $throwable) {

            Storage::disk('local')->delete($storedPath);

            if ($throwable instanceof ValidationException) {
                throw $throwable;
            }

            report($throwable);

            throw ValidationException::withMessages([
                'pdf_file' => 'The uploaded PDF could not be read. Please upload a valid, readable PDF file.',
            ]);
        }

        $sourceTitle = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);

        try {
            $generatedQuiz = $quizService->generate($extractedText, $sourceTitle);

        } catch (\Throwable $e) {

            Storage::disk('local')->delete($storedPath);

            return back()->withErrors([
                'pdf_file' => $e->getMessage(),
            ]);
        }

        $pdfUpload = PdfUpload::create([
            'original_name' => $uploadedFile->getClientOriginalName(),
            'stored_name' => basename($storedPath),
            'file_path' => $storedPath,
            'file_size' => $uploadedFile->getSize(),
            'extracted_text' => $extractedText ?: null,
        ]);
        
        $quiz = Quiz::create([
            'title' => $generatedQuiz['title'] ?: $sourceTitle,
            'source_filename' => $uploadedFile->getClientOriginalName(),
            'source_file_path' => $pdfUpload->file_path,
            'source_text' => $extractedText,
        ]);

        $quiz->questions()->createMany($generatedQuiz['questions']);

        return redirect()
            ->route('quizzes.show', $quiz)
            ->with('success', 'Quiz generated successfully.');
    }
}