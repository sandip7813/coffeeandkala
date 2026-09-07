<?php

namespace App\Http\Requests\Admin;

use App\Models\Article;

class UpdateJournalArticleRequest extends UpdateArticleRequest
{
    public function type(): string
    {
        return Article::TYPE_JOURNAL;
    }
}
