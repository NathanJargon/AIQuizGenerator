<?php

namespace Tests\Feature;

use App\Services\PdfTextExtractor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

class PdfUploadTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_pdf_upload_accepts_a_valid_pdf_and_stores_it_in_local_storage(): void
    {
        Storage::fake('local');

        $this->app->instance(PdfTextExtractor::class, new class extends PdfTextExtractor
        {
            public function extract(string $filePath): string
            {
                return 'Sample extracted text.';
            }
        });

        $response = $this->post(route('pdf-uploads.store'), [
            'pdf_file' => UploadedFile::fake()->create('sample.pdf', 120, 'application/pdf'),
        ]);

        $response->assertRedirect(route('pdf-uploads.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseCount('pdf_uploads', 1);

        $record = \App\Models\PdfUpload::query()->firstOrFail();

        Storage::disk('local')->assertExists($record->file_path);
        $this->assertStringStartsWith('pdfs/', $record->file_path);
        $this->assertSame('sample.pdf', $record->original_name);
        $this->assertSame('Sample extracted text.', $record->extracted_text);
    }

    public function test_non_pdf_files_are_rejected(): void
    {
        $response = $this->post(route('pdf-uploads.store'), [
            'pdf_file' => UploadedFile::fake()->create('module.docx', 120, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'),
        ]);

        $response->assertSessionHasErrors('pdf_file');

        $response = $this->post(route('pdf-uploads.store'), [
            'pdf_file' => UploadedFile::fake()->create('picture.jpg', 120, 'image/jpeg'),
        ]);

        $response->assertSessionHasErrors('pdf_file');
    }

    public function test_pdf_files_larger_than_100mb_are_rejected(): void
    {
        $response = $this->post(route('pdf-uploads.store'), [
            'pdf_file' => UploadedFile::fake()->create('large.pdf', 102401, 'application/pdf'),
        ]);

        $response->assertSessionHasErrors('pdf_file');
    }

    public function test_unreadable_pdf_returns_a_clear_error_message(): void
    {
        Storage::fake('local');

        $this->app->instance(PdfTextExtractor::class, new class extends PdfTextExtractor
        {
            public function extract(string $filePath): string
            {
                throw new \RuntimeException('PDF parse failed.');
            }
        });

        $response = $this->post(route('pdf-uploads.store'), [
            'pdf_file' => UploadedFile::fake()->create('broken.pdf', 120, 'application/pdf'),
        ]);

        $response->assertSessionHasErrors([
            'pdf_file' => 'The uploaded PDF could not be read. Please upload a valid, readable PDF file.',
        ]);

        $this->assertDatabaseCount('pdf_uploads', 0);
        $this->assertCount(0, Storage::disk('local')->allFiles('pdfs'));
    }
}