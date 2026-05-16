<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadPdfRequest;
use App\Jobs\ProcessPdfJob;
use App\Models\PdfTask;
use Illuminate\Http\JsonResponse;

class PdfController extends Controller
{
    public function upload(UploadPdfRequest $request): JsonResponse
    {
        $operation = $request->input('operation');
        $files     = $request->file('files');

        $paths = [];
        foreach ($files as $file) {
            $paths[] = $file->store('uploads', 'local');
        }

        $task = PdfTask::create([
            'operation'         => $operation,
            'status'            => 'pending',
            'original_filename' => $files[0]->getClientOriginalName(),
            'result_path'       => $paths[0],
            'error_message'     => null,
        ]);

        ProcessPdfJob::dispatch($task, $paths);

        return response()->json([
            'message' => 'Arquivo recebido. Processando em background.',
            'task_id' => $task->id,
            'status'  => $task->status,
        ], 202);
    }

    public function status(PdfTask $task): JsonResponse
    {
        return response()->json([
            'task_id'     => $task->id,
            'operation'   => $task->operation,
            'status'      => $task->status,
            'result_path' => $task->result_path,
            'error'       => $task->error_message,
        ]);
    }
}
