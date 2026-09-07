<?php

namespace App\Http\Requests\Admin;

use App\Models\Article;

class StoreJournalArticleRequest extends StoreArticleRequest
{
    public function type(): string
    {
        return Article::TYPE_JOURNAL;
    }
}
