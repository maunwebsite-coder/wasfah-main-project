<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Recipe;

class PageController extends Controller
{
    /**
     * صفحة عن وصفة
     */
    public function about()
    {
        return view('pages.about');
    }
    
    /**
     * صفحة نصائح الخبز
     */
    public function bakingTips()
    {
        return view('pages.baking-tips');
    }
    
    /**
     * صفحة الإعلان
     */
    public function advertising()
    {
        return view('pages.advertising');
    }

    /**
     * صفحة الشراكات مع الشركات
     */
    public function partnership()
    {
        return view('pages.partnership');
    }
    
    /**
     * صفحة اتصل بنا
     */
    public function contact()
    {
        return view('pages.contact');
    }
    
}
