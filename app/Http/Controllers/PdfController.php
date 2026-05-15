<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadPdfRequest;
use App\Models\PdfTask;
use Illuminate\Http\JsonResponse;

class PdfController extends Controller
{
    public function upload(UploadPdfRequest $request): JsonResponse
    {
        $file = $request->file('file');

        $path = $file->store('uploads', 'local');

        $task = PdfTask::create([
            'operation'         => $request->input('operation'),
            'status'            => 'pending',
            'original_filename' => $file->getClientOriginalName(),
            'result_path'       => $path,
            'error_message'     => null,
        ]);

        return response()->json([
            'message' => 'Arquivo recebido com sucesso.',
            'task_id' => $task->id,
            'status'  => $task->status,
        ], 201);
    }
}
