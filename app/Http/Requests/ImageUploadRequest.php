<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Secure image upload validation request.
 *
 * Implements multiple layers of file upload security:
 * - MIME type validation (not just extension)
 * - File size limits
 * - Extension whitelist
 * - Blocks executable file types
 *
 * @see config/security.php for configurable limits
 */
class ImageUploadRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $maxSize = config('security.uploads.max_size', 10240);
        $allowedMimes = implode(',', config('security.uploads.allowed_image_mimes', [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
        ]));

        return [
            'image' => [
                'required',
                'file',
                'image',
                "max:{$maxSize}",
                "mimetypes:{$allowedMimes}",
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'image.mimetypes' => 'The file must be a valid image (JPEG, PNG, GIF, or WebP).',
            'image.max' => 'The image must not exceed ' . (config('security.uploads.max_size', 10240) / 1024) . 'MB.',
        ];
    }
}
