<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\TuneStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TuneRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', Rule::enum(TuneStatus::class)],
            'links' => ['nullable', 'array'],
            'links.*' => ['string', 'max:255'],
            'steps' => ['nullable', 'array'],
            'steps.*' => ['string', 'max:255'],
            'tasks' => ['nullable', 'array'],
            'tasks.*' => ['array'],
            'tasks.*.description' => ['required', 'string', 'max:255'],
            'tasks.*.completed' => ['boolean'],
            'image' => ['nullable', 'image', 'max:5120'],
        ];
    }
}
