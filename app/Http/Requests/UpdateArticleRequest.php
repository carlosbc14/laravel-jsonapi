<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $article = $this->route('article');

        return [
            'data.id' => ['in:' . $article->getRouteKey()],
            'data.type' => ['in:articles'],
            'data.attributes.title' => ['sometimes', 'string', 'min:4', 'max:255'],
            'data.attributes.slug' => [
                'sometimes',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                'unique:' . \App\Models\Article::class . ',slug,' . $article->id,
            ],
            'data.attributes.content' => ['sometimes', 'string'],
            'data.relationships.category.data.id' => [
                'sometimes',
                'exists:' . \App\Models\Category::class . ',' . (new \App\Models\Category)->getRouteKeyName(),
            ],
        ];
    }
}
