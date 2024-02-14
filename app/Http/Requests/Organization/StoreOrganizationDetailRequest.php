<?php

namespace App\Http\Requests\Organization;
;

use Illuminate\Foundation\Http\FormRequest;


class StoreOrganizationDetailRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $rules = [
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'website' => 'nullable|url',
            'facebook' => 'nullable|url',
            'twitter' => 'nullable|url',
            'linkedin' => 'nullable|url',
            'instagram' => 'nullable|url',
            'youtube' => 'nullable|url',
            'pinterest' => 'nullable|url',
            'file_ids' => 'nullable|array',
            'file_ids.*' => 'required|integer|exists:files,id',
            'organization_id' => 'nullable|integer|exists:organizations,id',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ];

        $request = $this->all();
        if (isset($request['file_ids'])) {
            $rules['file_slug'] = 'required|string|max:255';
        }

        return $rules;
    }
}
