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

    /**
     * Display details for a single tool
     */
    public function show(Tool $tool): View
    {
        $relatedTools = Tool::active()
            ->where('id', '!=', $tool->id)
            ->when($tool->category, function ($query) use ($tool) {
                return $query->where('category', $tool->category);
            })
            ->ordered()
            ->limit(6)
            ->get();

        return view('tools.show', [
            'tool' => $tool,
            'relatedTools' => $relatedTools,
        ]);
    }
}
