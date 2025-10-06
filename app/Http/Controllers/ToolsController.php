<?php

namespace App\Http\Controllers;

use App\Models\Tool;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ToolsController extends Controller
{
    /**
     * Display the tools page
     */
    public function index(): View
    {
        $tools = Tool::active()->ordered()->get();
        $categories = $tools->groupBy('category');

        return view('tools', compact('tools', 'categories'));
    }
}
