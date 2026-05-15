<?php

namespace App\Services;

use setasign\Fpdi\Fpdi;
use Spatie\PdfToImage\Pdf;
use Illuminate\Support\Facades\Storage;

class PdfService
{
    public function compress(string $filePath): string
    {
        $fullPath       = Storage::disk('local')->path($filePath);
        $outputPath     = 'processed/' . uniqid('compressed_') . '.pdf';
        $fullOutputPath = Storage::disk('local')->path($outputPath);

        if (!file_exists(dirname($fullOutputPath))) {
            mkdir(dirname($fullOutputPath), 0755, true);
        }

        $command = "gswin64c -dBATCH -dNOPAUSE -q -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS=/ebook -sOutputFile=\"{$fullOutputPath}\" \"{$fullPath}\"";

        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            throw new \Exception("Erro ao comprimir PDF com Ghostscript.");
        }

        return $outputPath;
    }
    
    public function merge(array $filePaths): string
    {
        $outputPath    = 'processed/' . uniqid('merged_') . '.pdf';
        $fullOutputPath = Storage::disk('local')->path($outputPath);

        if (!file_exists(dirname($fullOutputPath))) {
            mkdir(dirname($fullOutputPath), 0755, true);
        }

        // Monta os caminhos absolutos de cada arquivo
        $inputFiles = array_map(
            fn($path) => '"' . Storage::disk('local')->path($path) . '"',
            $filePaths
        );

        $inputString = implode(' ', $inputFiles);

        $command = "gswin64c -dBATCH -dNOPAUSE -q -sDEVICE=pdfwrite -sOutputFile=\"{$fullOutputPath}\" {$inputString}";

        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            throw new \Exception("Erro ao unir PDFs com Ghostscript.");
        }

        return $outputPath;
    }
}
