<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

class UpdateQuoteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('manage-quotes') === true;
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
