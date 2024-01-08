<?php

namespace Modules\FileManager\app\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateFileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string',
            'description' => 'nullable|string',
            'slug' => 'required|string',
            'ext' => 'required|string',
            'file' => 'required|string',
            'folder' => 'required|string',
            'domain' => 'required|string',
            'user_id' => 'nullable|integer|min:-2147483648|max:2147483647|exists:users,id',
            'folder_id' => 'required|integer|min:-2147483648|max:2147483647|exists:folders,id',
            'path' => 'required|string',
            'size' => 'nullable|integer|min:-2147483648|max:2147483647',
            'is_front' => 'required|integer|min:-2147483648|max:2147483647',
        ];
    }
}
