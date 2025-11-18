<?php

namespace App\Http\Controllers\Chef;

use App\Http\Controllers\Controller;
use App\Models\ChefLinkItem;
use App\Support\ImageUploadConstraints;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LinkItemController extends Controller
{
    /**
     * إضافة رابط جديد للصفحة.
     */
    public function store(Request $request): RedirectResponse
    {
        $page = $request->user()->ensureLinkPage();

        $data = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'subtitle' => ['nullable', 'string', 'max:160'],
            'url' => ['required', 'url', 'max:255'],
            'icon' => ['nullable', 'string', 'max:80'],
            'image' => array_merge(['nullable'], ImageUploadConstraints::rules()),
            'is_active' => ['nullable', 'boolean'],
        ], ImageUploadConstraints::messages('image', [
            'ar' => 'الصورة الرئيسية',
            'en' => 'primary image',
        ]), [
            'title' => 'عنوان الرابط',
            'subtitle' => 'الوصف المختصر',
            'url' => 'الرابط',
            'icon' => 'الأيقونة',
            'image' => 'الصورة الرئيسية',
        ]);

        $position = (int) $page->items()->max('position');

        $itemData = [
            'title' => $data['title'],
            'subtitle' => $data['subtitle'] ?? null,
            'url' => $data['url'],
            'icon' => $data['icon'] ?? null,
            'position' => $position + 1,
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($request->hasFile('image')) {
            $itemData['image_path'] = $request->file('image')->store('chef-links/items', 'public');
        }

        $page->items()->create($itemData);

        return back()->with('success', 'تم إضافة الرابط بنجاح.');
    }

    /**
     * تحديث رابط موجود.
     */
    public function update(Request $request, ChefLinkItem $item): RedirectResponse
    {
        $page = $request->user()->ensureLinkPage();

        if ($item->chef_link_page_id !== $page->id && !$request->user()->isAdmin()) {
            abort(403, 'لا تملك صلاحية تعديل هذا الرابط.');
        }

        $data = $request->validate([
            'item_id' => ['required', 'integer', 'in:' . $item->id],
            'title' => ['required', 'string', 'max:120'],
            'subtitle' => ['nullable', 'string', 'max:160'],
            'url' => ['required', 'url', 'max:255'],
            'icon' => ['nullable', 'string', 'max:80'],
            'image' => array_merge(['nullable'], ImageUploadConstraints::rules()),
            'remove_image' => ['nullable', 'boolean'],
            'position' => ['nullable', 'integer', 'min:1', 'max:200'],
            'is_active' => ['nullable', 'boolean'],
        ], ImageUploadConstraints::messages('image', [
            'ar' => 'الصورة الرئيسية',
            'en' => 'primary image',
        ]), [
            'title' => 'عنوان الرابط',
            'subtitle' => 'الوصف المختصر',
            'url' => 'الرابط',
            'icon' => 'الأيقونة',
            'image' => 'الصورة الرئيسية',
            'position' => 'الترتيب',
        ]);

        $item->title = $data['title'];
        $item->subtitle = $data['subtitle'] ?? null;
        $item->url = $data['url'];
        $item->icon = $data['icon'] ?? null;
        $item->is_active = $request->boolean('is_active');

        if ($request->boolean('remove_image') && $item->image_path) {
            Storage::disk('public')->delete($item->image_path);
            $item->image_path = null;
        }

        if ($request->hasFile('image')) {
            if ($item->image_path) {
                Storage::disk('public')->delete($item->image_path);
            }

            $item->image_path = $request->file('image')->store('chef-links/items', 'public');
        }

        if (isset($data['position'])) {
            $item->position = $data['position'];
        }

        $item->save();

        return back()->with('success', 'تم تحديث الرابط بنجاح.');
    }

    /**
     * حذف رابط من الصفحة.
     */
    public function destroy(Request $request, ChefLinkItem $item): RedirectResponse
    {
        $page = $request->user()->ensureLinkPage();

        if ($item->chef_link_page_id !== $page->id && !$request->user()->isAdmin()) {
            abort(403, 'لا تملك صلاحية حذف هذا الرابط.');
        }

        if ($item->image_path) {
            Storage::disk('public')->delete($item->image_path);
        }

        $item->delete();

        return back()->with('success', 'تم حذف الرابط.');
    }
}
