<?php

namespace App\Services;

use Smalot\PdfParser\Parser;

class PdfAttendanceParser
{
    /**
     * Extract raw text from a given PDF file path.
     *
     * @param string $path
     * @return string
     */
    public function extractText(string $path): string
    {
        $parser = new Parser();
        $pdf = $parser->parseFile($path);
        
        return $pdf->getText();
    }
}
