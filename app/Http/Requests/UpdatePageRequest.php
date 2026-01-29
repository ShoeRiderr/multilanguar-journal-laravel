<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        $page = $this->route('page');
        return ($user && $page && $user->can('update', $page));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $page = $this->route('page');
        $currentId = optional($page)->id;

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
