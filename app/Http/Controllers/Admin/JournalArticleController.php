<?php

namespace App\Http\Controllers\Admin;

use App\Actions\StoreMediaFile;
use App\Actions\SyncArticleSections;
use App\Http\Requests\Admin\StoreJournalArticleRequest;
use App\Http\Requests\Admin\UpdateJournalArticleRequest;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;

class JournalArticleController extends AbstractArticleController
{
    protected function type(): string
    {
        return Article::TYPE_JOURNAL;
    }

    protected function categoryType(): string
    {
        return Category::TYPE_JOURNAL;
    }

    public function store(StoreJournalArticleRequest $request, StoreMediaFile $storeMediaFile, SyncArticleSections $syncArticleSections): RedirectResponse
    {
        return $this->storeArticle($request, $storeMediaFile, $syncArticleSections);
    }

    public function update(UpdateJournalArticleRequest $request, Article $article, StoreMediaFile $storeMediaFile, SyncArticleSections $syncArticleSections): RedirectResponse
    {
        return $this->updateArticle($request, $article, $storeMediaFile, $syncArticleSections);
    }
}
