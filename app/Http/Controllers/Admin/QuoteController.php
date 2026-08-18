<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Quotes\AssignQuoteToDate;
use App\Actions\Quotes\FindNextFreeScheduleDate;
use App\Actions\Quotes\SyncQuoteScheduleDates;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AssignQuoteDatesRequest;
use App\Http\Requests\Admin\StoreQuoteRequest;
use App\Http\Requests\Admin\UpdateQuoteRequest;
use App\Models\Quote;
use App\Models\QuoteSchedule;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class QuoteController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless(auth()->user()?->can('view-quotes'), 403);

        $filters = $request->only(['text', 'date_from', 'date_to']);

        $quotes = Quote::query()
            ->with('creator', 'upcomingSchedule')
            ->withCount('schedules')
            ->when(filled($filters['text'] ?? null), fn ($query) => $query->where('text', 'like', '%'.$filters['text'].'%'))
            ->when(filled($filters['date_from'] ?? null) || filled($filters['date_to'] ?? null), function ($query) use ($filters) {
                $query->whereHas('schedules', function ($query) use ($filters) {
                    if (filled($filters['date_from'] ?? null)) {
                        $query->whereDate('date', '>=', $filters['date_from']);
                    }

                    if (filled($filters['date_to'] ?? null)) {
                        $query->whereDate('date', '<=', $filters['date_to']);
                    }
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $hasActiveFilters = collect($filters)->filter(fn ($value) => filled($value))->isNotEmpty();
        $scheduleDates = $this->upcomingScheduleDates();

        return view('admin.quotes.index', compact('quotes', 'filters', 'hasActiveFilters', 'scheduleDates'));
    }

    public function create(): View
    {
        abort_unless(auth()->user()?->can('create-quotes'), 403);

        $scheduleDates = $this->upcomingScheduleDates();

        return view('admin.quotes.create', compact('scheduleDates'));
    }

    public function store(
        StoreQuoteRequest $request,
        AssignQuoteToDate $assignQuoteToDate,
        FindNextFreeScheduleDate $findNextFreeScheduleDate
    ): RedirectResponse {
        $data = $request->validated();

        $quote = Quote::create([
            'text' => $data['text'],
            'created_by' => $request->user()->id,
        ]);

        if (filled($data['dates'] ?? [])) {
            foreach ($data['dates'] as $date) {
                $assignQuoteToDate->handle($date, $quote, $request->user());
            }
        } else {
            // No date picked — auto-assign the next free date (falling past the usual
            // 14-day window if every one of those days already has a quote). Left as
            // auto-assigned so it still shows up as changeable, like any other auto pick.
            QuoteSchedule::create([
                'date' => $findNextFreeScheduleDate->handle()->toDateString(),
                'quote_id' => $quote->id,
                'assigned_by' => null,
                'is_auto_assigned' => true,
            ]);
        }

        return redirect()->route('admin.quotes.index')
            ->with('status', __('Quote created.'));
    }

    public function edit(Quote $quote): View
    {
        $this->authorizeManage($quote, 'edit-quotes');

        $scheduleDates = $this->upcomingScheduleDates();
        $selectedDates = $this->datesAssignedTo($quote, $scheduleDates);

        return view('admin.quotes.edit', compact('quote', 'scheduleDates', 'selectedDates'));
    }

    public function update(UpdateQuoteRequest $request, Quote $quote, SyncQuoteScheduleDates $syncQuoteScheduleDates): RedirectResponse
    {
        $data = $request->validated();

        $quote->update(['text' => $data['text']]);

        // The edit form's checkboxes are pre-checked to match the quote's current
        // assignments, so whatever comes back — including nothing — reflects the
        // admin's intended end state; unchecked dates are freed up again.
        $syncQuoteScheduleDates->handle($quote, $data['dates'] ?? [], $request->user());

        return redirect()->route('admin.quotes.index')
            ->with('status', __('Quote updated.'));
    }

    public function assignDates(AssignQuoteDatesRequest $request, Quote $quote, SyncQuoteScheduleDates $syncQuoteScheduleDates): RedirectResponse
    {
        $syncQuoteScheduleDates->handle($quote, $request->validated('dates'), $request->user());

        return redirect()->route('admin.quotes.index')
            ->with('status', __('Quote assigned to the selected date(s).'));
    }

    public function destroy(Quote $quote): RedirectResponse
    {
        $this->authorizeManage($quote, 'delete-quotes');

        $quote->delete();

        return redirect()->route('admin.quotes.index')
            ->with('status', __('Quote deleted.'));
    }

    /**
     * A quote's own creator may always manage it; managing someone else's
     * quote requires the given permission.
     */
    private function authorizeManage(Quote $quote, string $permission): void
    {
        if ($quote->created_by === auth()->id()) {
            return;
        }

        abort_unless(auth()->user()?->can($permission), 403);
    }

    /**
     * The next 14 days with whatever quote (if any) is currently assigned to each,
     * so admins can see what they'd be overriding when picking dates.
     *
     * @return Collection<int, array{date: string, label: string, assigned_quote_id: ?int, assigned_text: ?string}>
     */
    private function upcomingScheduleDates()
    {
        $window = [Carbon::today()->toDateString(), Carbon::today()->addDays(13)->toDateString()];

        $assigned = QuoteSchedule::query()
            ->with('quote')
            ->whereDate('date', '>=', $window[0])
            ->whereDate('date', '<=', $window[1])
            ->get()
            ->keyBy(fn (QuoteSchedule $schedule) => $schedule->date->toDateString());

        return collect(range(0, 13))->map(function (int $offset) use ($assigned) {
            $date = Carbon::today()->addDays($offset);
            $schedule = $assigned->get($date->toDateString());

            return [
                'date' => $date->toDateString(),
                'label' => $date->format('D, M j, Y'),
                'assigned_quote_id' => $schedule?->quote_id,
                'assigned_text' => $schedule?->quote?->text,
            ];
        });
    }

    /**
     * Which of the given schedule-date entries are currently assigned to this quote.
     *
     * @param  Collection<int, array{date: string, assigned_quote_id: ?int}>  $scheduleDates
     * @return array<int, string>
     */
    private function datesAssignedTo(Quote $quote, Collection $scheduleDates): array
    {
        return $scheduleDates
            ->where('assigned_quote_id', $quote->id)
            ->pluck('date')
            ->values()
            ->all();
    }
}
