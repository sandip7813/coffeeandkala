<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FeatureController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\PoetryController;
use App\Models\Article;
use App\Models\Meta;
use App\Models\Poem;
use App\Support\Preview\ContentPreview;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Admin previews of Features/Journals articles and poems, rendered with the
 * real public page templates in a new tab — for anyone who can create,
 * edit or approve that content:
 *
 *  - savedArticle()/savedPoem(): the saved version, whatever its status
 *    (draft/pending/inactive included) — from the list pages;
 *  - articleForm()/poemForm(): whatever the create/edit form currently
 *    holds, without saving it — the form's Preview button submits here.
 *
 * Nothing is ever saved. Every preview carries a "Preview — not published"
 * bar and is kept out of search engines and caches.
 */
class PreviewController extends Controller
{
    public function savedArticle(Request $request, Article $article): Response
    {
        $type = $this->articleType($request, $article);
        $article = ContentPreview::savedArticle($article);

        return $this->respond(
            $this->renderArticle($type, $article, $article->meta),
            __('Saved version · :status', ['status' => __(ucfirst($article->status))]),
        );
    }

    public function articleForm(Request $request, ?Article $article = null): Response
    {
        $type = $this->articleType($request, $article);
        $data = $request->all();

        return $this->respond(
            $this->renderArticle($type, ContentPreview::articleFromForm($data, $type, $article), ContentPreview::metaFromForm($data, $article)),
            __('Unsaved changes — nothing has been saved yet'),
        );
    }

    public function savedPoem(Request $request, Poem $poem): Response
    {
        $this->authorizeFor($request, ['upload-poetry', 'edit-poetry', 'approve-poetry']);
        $poem = ContentPreview::savedPoem($poem);

        return $this->respond(
            app(PoetryController::class)->renderPreview($poem, $poem->meta),
            __('Saved version · :status', ['status' => __(ucfirst($poem->status))]),
        );
    }

    public function poemForm(Request $request, ?Poem $poem = null): Response
    {
        $this->authorizeFor($request, ['upload-poetry', 'edit-poetry', 'approve-poetry']);
        $data = $request->all();

        return $this->respond(
            app(PoetryController::class)->renderPreview(ContentPreview::poemFromForm($data, $poem), ContentPreview::metaFromForm($data, $poem)),
            __('Unsaved changes — nothing has been saved yet'),
        );
    }

    /**
     * The route's article type ('features'/'journals' — from its
     * admin.features.* / admin.journals.* name), after checking the viewer
     * may preview it and that $article (if any) is of that type.
     */
    private function articleType(Request $request, ?Article $article): string
    {
        $type = str_starts_with((string) $request->route()?->getName(), 'admin.features.')
            ? Article::TYPE_FEATURE
            : Article::TYPE_JOURNAL;

        $this->authorizeFor($request, ["create-{$type}", "edit-{$type}", "approve-{$type}"]);
        abort_if($article !== null && $article->type !== $type, 404);

        return $type;
    }

    /**
     * @param  list<string>  $permissions  any one of these is enough
     */
    private function authorizeFor(Request $request, array $permissions): void
    {
        $user = $request->user();

        abort_unless($user !== null && collect($permissions)->contains(fn (string $permission): bool => $user->can($permission)), 403);
    }

    private function renderArticle(string $type, Article $article, ?Meta $meta): View
    {
        $controller = $type === Article::TYPE_FEATURE ? app(FeatureController::class) : app(JournalController::class);

        return $controller->renderArticle($article, $meta);
    }

    private function respond(View $view, string $label): Response
    {
        return response($view->with('previewBar', $label))
            ->header('X-Robots-Tag', 'noindex, nofollow')
            ->header('Cache-Control', 'no-store, private');
    }
}
