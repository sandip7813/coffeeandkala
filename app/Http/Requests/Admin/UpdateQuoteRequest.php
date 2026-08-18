<?php

namespace App\Http\Requests\Admin;

use App\Models\Quote;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

class UpdateQuoteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * A quote's own creator may always edit it; editing someone else's quote
     * requires the 'edit-quotes' permission.
     */
    public function authorize(): bool
    {
        /** @var Quote $quote */
        $quote = $this->route('quote');

        if ($quote->created_by === $this->user()?->id) {
            return true;
        }

        return $this->user()?->can('edit-quotes') === true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'text' => ['required', 'string', 'max:2000'],
            'dates' => ['nullable', 'array'],
            'dates.*' => [
                'date_format:Y-m-d',
                'after_or_equal:'.Carbon::today()->toDateString(),
                'before_or_equal:'.Carbon::today()->addDays(13)->toDateString(),
            ],
        ];
    }
}
