<?php

namespace App\Http\Controllers\Admin;

use App\Actions\StoreMediaFile;
use App\Actions\SyncArticleSections;
use App\Http\Requests\Admin\StoreFeatureArticleRequest;
use App\Http\Requests\Admin\UpdateFeatureArticleRequest;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;

class FeatureArticleController extends AbstractArticleController
{
    protected function type(): string
    {
        return Article::TYPE_FEATURE;
    }

    protected function categoryType(): string
    {
        return Category::TYPE_FEATURE;
    }

    public function store(StoreFeatureArticleRequest $request, StoreMediaFile $storeMediaFile, SyncArticleSections $syncArticleSections): RedirectResponse
    {
        return $this->storeArticle($request, $storeMediaFile, $syncArticleSections);
    }

    public function update(UpdateFeatureArticleRequest $request, Article $article, StoreMediaFile $storeMediaFile, SyncArticleSections $syncArticleSections): RedirectResponse
    {
        return $this->updateArticle($request, $article, $storeMediaFile, $syncArticleSections);
    }
}
