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

    public function split(string $filePath): array
    {
        $fullPath = Storage::disk('local')->path($filePath);
        $pageCount = $this->countPages($fullPath);

        if ($pageCount === 0) {
            throw new \Exception("Não foi possível determinar o número de páginas do PDF.");
        }

        $outputPaths = [];

        for ($i = 1; $i <= $pageCount; $i++) {
            $outputPath     = 'processed/' . uniqid("page_{$i}_") . '.pdf';
            $fullOutputPath = Storage::disk('local')->path($outputPath);

            if (!file_exists(dirname($fullOutputPath))) {
                mkdir(dirname($fullOutputPath), 0755, true);
            }

            $command = "gswin64c -dBATCH -dNOPAUSE -q -sDEVICE=pdfwrite -dFirstPage={$i} -dLastPage={$i} -sOutputFile=\"{$fullOutputPath}\" \"{$fullPath}\"";

            exec($command, $output, $returnCode);

            if ($returnCode !== 0) {
                throw new \Exception("Erro ao extrair página {$i}.");
            }

            $outputPaths[] = $outputPath;
        }

        return $outputPaths;
    }

    private function countPages(string $fullPath): int
    {
        $command = "gswin64c -dBATCH -dNOPAUSE -q -sDEVICE=nullpage \"{$fullPath}\" 2>&1";
        exec($command, $output, $returnCode);

        // Ghostscript imprime "Page N" para cada página processada
        $pageCount = 0;
        foreach ($output as $line) {
            if (preg_match('/Page\s+(\d+)/i', $line, $matches)) {
                $pageCount = max($pageCount, (int) $matches[1]);
            }
        }

        // Se não encontrou via output, tenta pelo número de arquivos gerados
        if ($pageCount === 0) {
            $tempOutput = Storage::disk('local')->path('processed/temp_count_%d.pdf');
            $countCommand = "gswin64c -dBATCH -dNOPAUSE -q -sDEVICE=pdfwrite -sOutputFile=\"{$tempOutput}\" \"{$fullPath}\" 2>&1";
            exec($countCommand, $countOut, $countReturn);

            // Conta e limpa os arquivos temporários
            $i = 1;
            while (file_exists(Storage::disk('local')->path("processed/temp_count_{$i}.pdf"))) {
                unlink(Storage::disk('local')->path("processed/temp_count_{$i}.pdf"));
                $pageCount = $i;
                $i++;
            }
        }

        return $pageCount;
    }
}
