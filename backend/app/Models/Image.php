<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function jsonSerialize(): mixed
    {
        $strings = explode('/', data_get($this, 'path'));
        $name = end($strings);
        return [
            'id' => data_get($this, 'id'),
            'url' => Storage::url(data_get($this, 'path')),
            'name' => $name,
            'created_at' => data_get($this, 'created_at'),
            'updated_at' => data_get($this, 'updated_at'),
        ];
    }
}
