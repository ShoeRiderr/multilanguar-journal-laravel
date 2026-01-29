<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        $category = $this->route('category');
        return $user && $category && $user->can('update', $category);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $category = $this->route('category');
        $currentId = optional($category)->id;

        return [
            'parent_id' => [
                'nullable',
                Rule::exists('categories', 'id')->where(function ($query) use ($currentId) {
                    if ($currentId) {
                        $query->where('id', '<>', $currentId);
                    }
                }),
            ],
            'translations' => ['required', 'array', 'min:1'],
            'translations.*.language_id' => ['required', 'exists:languages,id'],
            'translations.*.name' => ['required', 'string'],
            'translations.*.slug' => ['required', 'string', 'distinct', 'unique:category_translations,slug,' . $currentId . ',category_id'],
        ];
    }
}
