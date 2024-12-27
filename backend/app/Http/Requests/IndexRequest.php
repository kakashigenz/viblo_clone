<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

class IndexRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    #[OA\Schema(
        schema: "ListArticleRequest",
        properties: [
            new OA\Property(property: 'page', type: 'integer', example: 1, description: "Page number for pagination"),
            new OA\Property(property: 'size', type: 'integer', example: 10, description: "Number of items per page")
        ]
    )]
    public function rules(): array
    {
        return [
            'page' => 'integer|gt:0',
            'size' => 'integer|gt:0'
        ];
    }
}
