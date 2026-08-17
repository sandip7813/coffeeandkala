<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Quotes\EnsureQuoteScheduledForDate;
use App\Http\Controllers\Controller;
use App\Models\Quote;
use App\Models\QuoteSchedule;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class QuoteScheduleController extends Controller
{
    public function index(EnsureQuoteScheduledForDate $ensureQuoteScheduledForDate): View
    {
        $this->authorizeManage();

        $schedules = collect(range(0, 13))
            ->map(fn (int $offset) => $ensureQuoteScheduledForDate->handle(Carbon::today()->addDays($offset)));

        $quotes = Quote::orderBy('text')->get();

        return view('admin.quotes.schedule.index', compact('schedules', 'quotes'));
    }

    public function update(Request $request, string $date): RedirectResponse
    {
        $this->authorizeManage();

        $scheduleWindow = $this->scheduleWindow();

        $data = $request->merge(['date' => $date])->validate([
            'date' => ['required', 'date_format:Y-m-d', 'after_or_equal:'.$scheduleWindow[0], 'before_or_equal:'.$scheduleWindow[1]],
            'quote_id' => ['required', Rule::exists('quotes', 'id')],
        ]);

        $schedule = QuoteSchedule::query()->whereDate('date', $data['date'])->first() ?? new QuoteSchedule(['date' => $data['date']]);

        $schedule->fill([
            'quote_id' => $data['quote_id'],
            'assigned_by' => $request->user()->id,
            'is_auto_assigned' => false,
        ])->save();

        return redirect()->route('admin.quotes.schedule.index')
            ->with('status', __('Quote assigned for :date.', ['date' => $data['date']]));
    }

    private function authorizeManage(): void
    {
        abort_unless(auth()->user()?->can('manage-quotes'), 403);
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function scheduleWindow(): array
    {
        return [Carbon::today()->toDateString(), Carbon::today()->addDays(13)->toDateString()];
    }
}
