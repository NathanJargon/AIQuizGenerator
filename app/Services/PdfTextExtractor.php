<?php

namespace App\Services;

use Smalot\PdfParser\Parser;

class PdfTextExtractor
{
    public function extract(string $filePath): string
    {
        try {
            $parser = new Parser();
            $pdf = $parser->parseFile($filePath);

            return trim($pdf->getText());
        } catch (\Throwable $e) {
            dd([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'pdf_path' => $filePath,
                'exists' => file_exists($filePath),
                'filesize' => file_exists($filePath) ? filesize($filePath) : 0,
            ]);
        }
    }
}