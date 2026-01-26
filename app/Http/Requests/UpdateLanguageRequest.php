<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLanguageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        $language = $this->route('language');
        return $user && $language && $user->can('update', $language);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'unique:languages,code,' . $this->route('language')->id],
            'name' => ['required', 'string'],
            'native_name' => ['required', 'string'],
            'is_active' => ['boolean'],
            'is_default' => ['boolean'],
        ];
    }
}
