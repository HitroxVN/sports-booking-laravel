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
            'name' => ['required', 'string', 'max:255'],
            // Số điện thoại Việt Nam: bỏ khoảng trắng/dấu gạch rồi kiểm tra đầu số 0/84
            'phone' => ['nullable', 'string', 'max:20', function (string $attribute, mixed $value, \Closure $fail) {
                $digits = preg_replace('/[\s.\-()]/', '', $value ?? '');

                if ($digits !== '' && ! preg_match('/^(0|\+?84)\d{9}$/', $digits)) {
                    $fail('Số điện thoại không hợp lệ. Vui lòng nhập số điện thoại Việt Nam (VD: 0912 345 678).');
                }
            }],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ];
    }

    /**
     * Thông báo lỗi bằng tiếng Việt cho các quy tắc trên.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'avatar.image' => 'Ảnh đại diện phải là tệp ảnh (JPG, PNG hoặc WebP).',
            'avatar.mimes' => 'Ảnh đại diện phải có định dạng JPG, PNG hoặc WebP.',
            'avatar.max' => 'Ảnh đại diện không được vượt quá 2MB.',
        ];
    }
}
