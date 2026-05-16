<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadPdfRequest;
use App\Models\PdfTask;
use App\Services\PdfService;
use Illuminate\Http\JsonResponse;

class PdfController extends Controller
{
    public function __construct(protected PdfService $pdfService)
    {
    }

    public function upload(UploadPdfRequest $request): JsonResponse
    {
        $operation = $request->input('operation');
        $files = $request->file('files');

        // Salva todos os arquivos e coleta os caminhos
        $paths = [];
        foreach ($files as $file) {
            $paths[] = $file->store('uploads', 'local');
        }

        $task = PdfTask::create([
            'operation'         => $operation,
            'status'            => 'processing',
            'original_filename' => $files[0]->getClientOriginalName(),
            'result_path'       => $paths[0],
            'error_message'     => null,
        ]);

        try {
            $resultPath = match($operation) {
                'compress' => $this->pdfService->compress($paths[0]),
                'merge'    => $this->pdfService->merge($paths),
                'split'    => implode(',', $this->pdfService->split($paths[0])),
                'pdf_to_image' => implode(',', $this->pdfService->pdfToImage($paths[0])),
                'image_to_pdf' => $this->pdfService->imageToPdf($paths),
                default    => throw new \Exception("Operação não implementada ainda."),
            };

            $task->update([
                'status'      => 'done',
                'result_path' => $resultPath,
            ]);

            return response()->json([
                'message'     => 'PDF processado com sucesso.',
                'task_id'     => $task->id,
                'status'      => $task->status,
                'result_path' => $resultPath,
            ], 200);

        } catch (\Exception $e) {
            $task->update([
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Erro ao processar o PDF.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
