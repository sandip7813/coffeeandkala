<?php

namespace App\Http\Requests\Admin;

use App\Models\Article;

class UpdateFeatureArticleRequest extends UpdateArticleRequest
{
    public function type(): string
    {
        return Article::TYPE_FEATURE;
    }
}
