<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UploadPdfRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'mimes:pdf', 'max:20480'],
            'operation' => ['required', 'string', 'in:merge,split,compress,pdf_to_image,image_to_pdf'],
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'Nenhum arquivo foi enviado.',
            'file.mimes' => 'O arquivo deve ser um PDF.',
            'file.max' => 'O arquivo não pode ultrapassar 20MB.',
            'operation.in' => 'Operação inválida.',
        ];
    }
}
