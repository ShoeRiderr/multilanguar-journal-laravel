<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Category;

class StoreCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && $user->can('create', Category::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'parent_id' => ['nullable', 'exists:categories,id'],
            'translations' => ['required', 'array', 'min:1'],
            'translations.*.language_id' => ['required', 'exists:languages,id'],
            'translations.*.name' => ['required', 'string'],
            'translations.*.slug' => ['required', 'string', 'distinct', 'unique:category_translations,slug'],
        ];
    }
}
