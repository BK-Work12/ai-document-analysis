<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentUploadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isClient();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $docTypes = \App\Models\DocumentRequirement::whereActive(true)->pluck('doc_type')->implode(',');

        return [
            'doc_type' => ['required', 'string', 'in:' . $docTypes],
            'file' => ['required', 'file', 'max:51200', 'mimes:pdf,doc,docx,jpg,jpeg,png'],
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'A document file is required.',
            'file.uploaded' => 'Upload failed - file size exceeds server limits (max 50MB). Try reducing file size or contact support.',
            'file.max' => 'The file may not be larger than 50MB.',
            'file.mimes' => 'The file must be a PDF, Word document, or image (JPG/PNG).',
            'doc_type.in' => 'Invalid document type.',
        ];
    }
}
