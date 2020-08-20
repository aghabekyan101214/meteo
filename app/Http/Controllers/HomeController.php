<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{

    const VIEW_FOLDER = "main."; // Path to view folder
    const ROUTE = "/"; // Current route
    const TITLE = "Աղյուսակ Bi";

    /**
     * Show the application main page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }
}
