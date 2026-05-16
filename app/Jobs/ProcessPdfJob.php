<?php

namespace App\Jobs;

use App\Models\PdfTask;
use App\Services\PdfService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessPdfJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $timeout = 120;

    public function __construct(
        public PdfTask $task,
        public array $paths,
    ) {}

    public function handle(PdfService $pdfService): void
    {
        $this->task->update(['status' => 'processing']);

        try {
            $resultPath = match($this->task->operation) {
                'compress'     => $pdfService->compress($this->paths[0]),
                'merge'        => $pdfService->merge($this->paths),
                'split'        => implode(',', $pdfService->split($this->paths[0])),
                'pdf_to_image' => implode(',', $pdfService->pdfToImage($this->paths[0])),
                'image_to_pdf' => $pdfService->imageToPdf($this->paths),
                default        => throw new \Exception("Operação não implementada."),
            };

            $this->task->update([
                'status'      => 'done',
                'result_path' => $resultPath,
            ]);

        } catch (\Exception $e) {
            $this->task->update([
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
            ]);
        }
    }
}
