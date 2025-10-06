<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminAreaController extends Controller
{
    /**
     * عرض صفحة منطقة الإدمن الرئيسية
     */
    public function index()
    {
        return view('admin.admin-area');
    }
}

