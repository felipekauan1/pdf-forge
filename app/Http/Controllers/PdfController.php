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
        $file = $request->file('file');
        $path = $file->store('uploads', 'local');

        $task = PdfTask::create([
            'operation'         => $request->input('operation'),
            'status'            => 'processing',
            'original_filename' => $file->getClientOriginalName(),
            'result_path'       => $path,
            'error_message'     => null,
        ]);

        try {
            $resultPath = $this->pdfService->compress($path);

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
