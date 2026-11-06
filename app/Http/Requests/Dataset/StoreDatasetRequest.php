<?php

namespace App\Http\Requests\Dataset;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDatasetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'file'      => ['required', 'file', 'extensions:csv', 'mimetypes:text/csv,text/plain,application/csv,application/vnd.ms-excel', 'max:512000'], // KB = 500 MB
            'delimiter' => ['nullable', Rule::in(['auto', 'comma', 'semicolon', 'tab', 'pipe'])],
            'header'    => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'file.extensions' => 'Please upload a .csv file.',
            'file.mimetypes'  => 'That file does not look like a CSV.',
            'file.max'        => 'The file is larger than 500 MB.',
            'file.uploaded'   => 'The upload failed. The file may be larger than the server allows.',
        ];
    }
}