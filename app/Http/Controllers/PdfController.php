<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadPdfRequest;
use App\Jobs\ProcessPdfJob;
use App\Models\PdfTask;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

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

    public function download(PdfTask $task): mixed
    {
        if ($task->status !== 'done') {
            return response()->json([
                'message' => 'O arquivo ainda não está pronto.',
                'status'  => $task->status,
            ], 422);
        }

        $paths = explode(',', $task->result_path);

        // Se tiver múltiplos arquivos, compacta em ZIP
        if (count($paths) > 1) {
            $zipName    = 'pdf_forge_' . $task->id . '_' . $task->operation . '.zip';
            $zipPath    = storage_path('app/private/processed/' . $zipName);

            $zip = new \ZipArchive();

            if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
                return response()->json(['message' => 'Erro ao criar arquivo ZIP.'], 500);
            }

            foreach ($paths as $path) {
                $fullPath = Storage::disk('local')->path(trim($path));
                if (file_exists($fullPath)) {
                    $zip->addFile($fullPath, basename($fullPath));
                }
            }

            $zip->close();

            return response()->download($zipPath)->deleteFileAfterSend(true);
        }

        // Arquivo único — download direto
        $path = Storage::disk('local')->path($task->result_path);

        if (!file_exists($path)) {
            return response()->json(['message' => 'Arquivo não encontrado.'], 404);
        }

        return response()->download($path);
    }
}
