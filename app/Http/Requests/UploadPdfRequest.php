<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UploadPdfRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isMerge = $this->input('operation') === 'merge';

        return [
            'files'     => ['required', 'array', $isMerge ? 'min:2' : 'min:1'],
            'files.*'   => ['required', 'file', 'mimes:pdf', 'max:20480'],
            'operation' => ['required', 'string', 'in:merge,split,compress,pdf_to_image,image_to_pdf'],
        ];
    }

    public function messages(): array
    {
        return [
            'files.required'  => 'Nenhum arquivo foi enviado.',
            'files.min'       => 'Envie pelo menos 2 arquivos para unir.',
            'files.*.mimes'   => 'Todos os arquivos devem ser PDFs.',
            'files.*.max'     => 'Cada arquivo não pode ultrapassar 20MB.',
            'operation.in'    => 'Operação inválida.',
        ];
    }
}
