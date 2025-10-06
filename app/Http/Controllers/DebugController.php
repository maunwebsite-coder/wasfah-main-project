<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DebugController extends Controller

{
    /**
     * Display the debug recipe page
     */

    public function debugRecipe()
    
    {
        return view('debug-recipe');
    }

    /**
     * Display the test recipe JavaScript page
     */
    public function testRecipeJs()
    {
        return view('test-recipe-js');
    }

    /**
     * Display the test API page
     */
    public function testApi()
    {
        return view('test-api');
    }

    /**
     * Display the test recipe debug page
     */
    public function testRecipeDebug()
    {
        return view('test-recipe-debug');
    }

    /**
     * Display the test recipe page
     */
    public function testRecipePage()
    {
        return view('test-recipe-page');
    }

    /**
     * Display the check script loading page
     */
    public function checkScriptLoading()
    {
        return view('check-script-loading');
    }

    /**
     * Display the check DOM elements page
     */
    public function checkDomElements()
    {
        return view('check-dom-elements');
    }

    /**
     * Display the check tools page
     */
    public function checkTools()
    {
        return view('check-tools');
    }

    /**
     * Display the debug index page
     */
    public function index()
    {
        return view('debug-index');
    }


}
