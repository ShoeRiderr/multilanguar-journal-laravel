<?php

namespace App\Http\Requests;

use App\PostStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StorePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function getRedirectUrl()
    {
        return route('admin.posts.index', [
            'locale' => app()->getLocale(),
        ], false);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'language_id' => ['required', 'exists:languages,id'],
            'title' => ['required', 'string'],
            'slug' => ['required', 'string'],
            'content_md' => ['required', 'string'],
            'status' => ['required', new Enum(PostStatus::class)],
            'published_at' => ['required', 'date'],
        ];
    }
}
