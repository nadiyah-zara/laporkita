<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
{
    return [
        'name'        => ['required', 'string', 'max:255'],
        'email'       => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($this->user()->id)],
        'phone'       => ['nullable', 'string', 'max:20'],
        'username'    => ['nullable', 'string', 'max:50', Rule::unique(User::class)->ignore($this->user()->id)],
        'bio'         => ['nullable', 'string', 'max:500'],
        'birth_date'  => ['nullable', 'date'],
        'nik'         => ['nullable', 'string', 'max:20'],
        'gender'      => ['nullable', 'string', 'in:laki-laki,perempuan'],
        'occupation'  => ['nullable', 'string', 'max:100'],
        'disability'  => ['nullable', 'string', 'max:20'],
        'city'        => ['nullable', 'string', 'max:100'],
        'address'     => ['nullable', 'string', 'max:500'],
        'postal_code' => ['nullable', 'string', 'max:10'],
        'website'     => ['nullable', 'url', 'max:255'],
        'avatar'      => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        'cover_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
    ];
}
}
