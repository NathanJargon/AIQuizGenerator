<?php

namespace App\Services;

use Smalot\PdfParser\Parser;

class PdfTextExtractor
{
    public function extract(string $filePath): string
    {
        $parser = new Parser();
        $pdf = $parser->parseFile($filePath);

        return trim($pdf->getText());
    }
}