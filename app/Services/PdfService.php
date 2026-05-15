<?php

namespace App\Services;

use setasign\Fpdi\Fpdi;
use Spatie\PdfToImage\Pdf;
use Illuminate\Support\Facades\Storage;

class PdfService
{
    public function compress(string $filePath): string
    {
        // Caminho absoluto do arquivo original
        $fullPath = Storage::disk('local')->path($filePath);

        // Carrega o PDF original com FPDI
        $pdf = new Fpdi();
        $pageCount = $pdf->setSourceFile($fullPath);

        // Copia cada página para um novo PDF
        for ($i = 1; $i <= $pageCount; $i++) {
            $template = $pdf->importPage($i);
            $size = $pdf->getTemplateSize($template);

            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($template);
        }

        // Salva o arquivo comprimido
        $outputPath = 'processed/' . uniqid('compressed_') . '.pdf';
        $fullOutputPath = Storage::disk('local')->path($outputPath);

        // Garante que a pasta existe
        if (!file_exists(dirname($fullOutputPath))) {
            mkdir(dirname($fullOutputPath), 0755, true);
        }

        $pdf->Output($fullOutputPath, 'F');

        return $outputPath;
    }
}
