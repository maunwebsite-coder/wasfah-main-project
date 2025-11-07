<?php

namespace App\Http\Requests\Admin;

use App\Services\HeroSlideImageService;
use Illuminate\Foundation\Http\FormRequest;

class HeroSlideRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'badge' => ['nullable', 'string', 'max:120'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'image_alt' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],

            'features' => ['nullable', 'array'],
            'features.*' => ['nullable', 'string', 'max:255'],

            'actions' => ['nullable', 'array'],
            'actions.*.label' => ['nullable', 'string', 'max:255'],
            'actions.*.url' => ['nullable', 'string', 'max:2048'],
            'actions.*.type' => ['nullable', 'in:primary,secondary,accent,ghost'],
            'actions.*.icon' => ['nullable', 'string', 'max:80'],
            'actions.*.behavior' => ['nullable', 'in:static,create_workshop,create_wasfah_link'],
            'actions.*.open_in_new_tab' => ['nullable', 'boolean'],

            'desktop_image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,bmp,svg,webp,webm', 'max:' . HeroSlideImageService::MAX_FILE_SIZE_KB],
            'mobile_image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,bmp,svg,webp,webm', 'max:' . HeroSlideImageService::MAX_FILE_SIZE_KB],
            'desktop_image_url' => ['nullable', 'url', 'max:2048'],
            'mobile_image_url' => ['nullable', 'url', 'max:2048'],
            'remove_desktop_image' => ['nullable', 'boolean'],
            'remove_mobile_image' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'badge' => 'الشارة',
            'title' => 'العنوان',
            'description' => 'الوصف',
            'desktop_image' => 'صورة سطح المكتب',
            'mobile_image' => 'صورة الجوال',
        ];
    }
}
