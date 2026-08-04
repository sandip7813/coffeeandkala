<?php

namespace App\Http\Controllers\Admin;

use App\Actions\RunArtisanCommand;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RunArtisanCommandRequest;
use App\Http\Requests\Admin\UnlockArtisanRunnerRequest;
use App\Support\ArtisanCatalog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class ArtisanController extends Controller
{
    public function index(): View
    {
        $this->authorizeManage();

        if (! ArtisanCatalog::isUnlocked()) {
            return view('admin.artisan.gate');
        }

        return view('admin.artisan.index', [
            'groups' => ArtisanCatalog::groupedPresets(),
            'allowed' => ArtisanCatalog::allowedCommands(),
            'seeders' => ArtisanCatalog::seeders(),
            'result' => session('artisan_result'),
        ]);
    }

    public function unlock(UnlockArtisanRunnerRequest $request): RedirectResponse|JsonResponse
    {
        $this->authorizeManage();

        ArtisanCatalog::unlock();

        if ($request->expectsJson()) {
            return response()->json([
                'unlocked' => true,
                'redirect' => route('admin.artisan.index'),
            ]);
        }

        return redirect()
            ->route('admin.artisan.index')
            ->with('status', 'Artisan Runner unlocked for this session.');
    }

    public function store(RunArtisanCommandRequest $request, RunArtisanCommand $runner): RedirectResponse
    {
        $this->authorizeManage();

        abort_unless(ArtisanCatalog::isUnlocked(), 403);

        $invocation = $request->resolvedInvocation();

        abort_unless($invocation !== null, 422);

        [$command, $parameters] = $invocation;

        $result = $runner->handle($command, $parameters);

        return redirect()
            ->route('admin.artisan.index')
            ->with('artisan_result', $result)
            ->with(
                $result['succeeded'] ? 'status' : 'error',
                $result['succeeded']
                    ? 'Command finished successfully.'
                    : 'Command finished with errors (exit code '.$result['exit_code'].').',
            );
    }

    private function authorizeManage(): void
    {
        abort_unless(auth()->user()?->can('manage-artisan') === true, 403);
    }
}
