<?php

namespace App\Http\Controllers;

use App\Bi;
use Illuminate\Http\Request;

class BiController extends Controller
{

    const VIEW_FOLDER = "bi."; // Path to view folder
    const ROUTE = "/"; // Current route
    const TITLE = "Աղյուսակ Bi"; // Title for resource

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Bi::orderBy("id", "DESC")->paginate(10);
        $title = self::TITLE;
        return view(self::VIEW_FOLDER . "index", compact("data", 'title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Bi  $bi
     * @return \Illuminate\Http\Response
     */
    public function show(Bi $bi)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Bi  $bi
     * @return \Illuminate\Http\Response
     */
    public function edit(Bi $bi)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Bi  $bi
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Bi $bi)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Bi  $bi
     * @return \Illuminate\Http\Response
     */
    public function destroy(Bi $bi)
    {
        //
    }
}
