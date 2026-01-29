<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Page;

class StorePageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->can('create', Page::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'translations' => ['required', 'array', 'min:1'],
            'translations.*.language_id' => ['required', 'exists:languages,id'],
            'translations.*.title' => ['required', 'string'],
            'translations.*.slug' => ['required', 'string'],
            'translations.*.content_md' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
