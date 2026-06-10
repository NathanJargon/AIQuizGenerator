<?php

namespace App\Http\Controllers;

use App\Models\PdfUpload;
use App\Services\PdfTextExtractor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class PdfUploadController extends Controller
{
    public function index(): View
    {
        return view('pdf-uploads.index', [
            'uploads' => PdfUpload::latest()->get(),
        ]);
    }

    public function store(Request $request, PdfTextExtractor $pdfTextExtractor): RedirectResponse
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

        PdfUpload::create([
            'original_name' => $uploadedFile->getClientOriginalName(),
            'stored_name' => basename($storedPath),
            'file_path' => $storedPath,
            'file_size' => $uploadedFile->getSize(),
            'extracted_text' => $extractedText ?: null,
        ]);

        return redirect()
            ->route('pdf-uploads.index')
            ->with('success', 'PDF uploaded successfully and ready for quiz generation.');
    }
}