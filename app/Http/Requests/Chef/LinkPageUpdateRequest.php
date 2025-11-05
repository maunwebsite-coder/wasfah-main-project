<?php

namespace App\Http\Requests\Chef;

use Illuminate\Foundation\Http\FormRequest;

class LinkPageUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        return (bool) ($user && ($user->isChef() || $user->isAdmin()));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'headline' => ['nullable', 'string', 'max:120'],
            'subheadline' => ['nullable', 'string', 'max:160'],
            'bio' => ['nullable', 'string', 'max:600'],
            'cta_label' => ['nullable', 'string', 'max:80'],
            'cta_url' => ['nullable', 'url', 'max:255'],
            'accent_color' => ['nullable', 'regex:/^#?[0-9a-fA-F]{3,8}$/'],
            'is_published' => ['nullable', 'boolean'],
            'show_upcoming_workshop' => ['nullable', 'boolean'],
            'hero_image' => ['nullable', 'image', 'max:3072'],
            'remove_hero_image' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Custom attribute names.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'headline' => 'عنوان الصفحة',
            'subheadline' => 'العنوان الثانوي',
            'bio' => 'الوصف',
            'cta_label' => 'نص زر الدعوة',
            'cta_url' => 'رابط زر الدعوة',
            'accent_color' => 'لون التمييز',
            'hero_image' => 'صورة العرض',
            'show_upcoming_workshop' => 'عرض الورشة القادمة',
        ];
    }
}
