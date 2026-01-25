<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\PostStatus;

class UpdatePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->authorize('update', $this->route('post'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => ['required', 'exists:posts,id'],
            'language_id' => ['required', 'exists:languages,id'],
            'title' => ['required', 'string'],
            'slug' => ['required', 'string'],
            'content_md' => ['required', 'string'],
            'status' => ['required', new Enum(PostStatus::class)],
            'published_at' => ['required', 'date'],
        ];
    }
}
