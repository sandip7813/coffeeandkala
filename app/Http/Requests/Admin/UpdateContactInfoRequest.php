<?php

namespace App\Http\Requests\Admin;

use App\Support\ContactInfo;
use Illuminate\Foundation\Http\FormRequest;

class UpdateContactInfoRequest extends FormRequest
{
    /**
     * Only digits, an optional leading +, and spaces/dashes/dots/parentheses as separators —
     * rejects letters and other stray characters (e.g. "asads").
     */
    private const PHONE_CHARSET_REGEX = '/^\+?[0-9\s\-\.\(\)]+$/';

    public function authorize(): bool
    {
        return $this->user()?->can('manage-brand') === true;
    }

    protected function prepareForValidation(): void
    {
        $values = [];

        foreach (array_keys(ContactInfo::fields()) as $key) {
            $value = $this->input("contact.{$key}");
            $values[$key] = is_string($value) ? trim($value) : '';
        }

        $this->merge(['contact' => $values]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = ['contact' => ['required', 'array']];

        foreach (ContactInfo::fields() as $key => $field) {
            $rules["contact.{$key}"] = [
                'nullable',
                'string',
                'max:255',
                ...match ($field['type']) {
                    'email' => ['email'],
                    'tel' => ['regex:'.self::PHONE_CHARSET_REGEX, $this->phoneDigitCountRule()],
                    default => [],
                },
            ];
        }

        return $rules;
    }

    /**
     * A phone number, once separators are stripped, must hold 7–15 digits
     * (the range a real phone number falls within, per E.164).
     */
    private function phoneDigitCountRule(): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail): void {
            if (! is_string($value) || $value === '') {
                return;
            }

            $digitCount = strlen(preg_replace('/\D/', '', $value) ?? '');

            if ($digitCount < 7 || $digitCount > 15) {
                $fail('The :attribute must contain between 7 and 15 digits.');
            }
        };
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        $attributes = [];

        foreach (ContactInfo::fields() as $key => $field) {
            $attributes["contact.{$key}"] = $field['label'];
        }

        return $attributes;
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'contact.phone.regex' => 'The :attribute may only contain digits, spaces, and + - ( ) . as separators.',
        ];
    }
}
