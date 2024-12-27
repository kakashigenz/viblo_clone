<?php

namespace App\Http\Requests;

use App\Models\Article;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;

class StoreUpdateArticleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    #[
        OA\Schema(
            schema: 'StoreUpdateArticleRequest',
            properties: [
                new OA\Property(property: 'title', type: 'string', example: "Why we should learn Python"),
                new OA\Property(property: 'content', type: 'string', example: "Because Python is fun!"),
                new OA\Property(
                    property: 'tags',
                    type: 'array',
                    items: new OA\Items(
                        type: 'string',
                    ),
                    example: ["python", "programming"]
                ),
                new OA\Property(property: 'status', type: 'integer', enum: [Article::VISIBLE, Article::DRAFT, Article::SPAM])
            ],
        )
    ]
    public function rules(): array
    {
        return [
            'title' => 'required|max:255',
            'content' => 'required',
            'tags' => 'required|array|between:1,5',
            'status' => ['required', Rule::in([Article::VISIBLE, Article::DRAFT, Article::SPAM])]
        ];
    }
}
