<?php

namespace App\Http\Controllers;

use App\Metar;
use Illuminate\Http\Request;

class MetarController extends Controller
{

    const VIEW_FOLDER = "metar."; // Path to view folder
    const ROUTE = "/metar"; // Current route
    const TITLE = "Աղյուսակ Metar"; // Title for resource

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sql = Metar::orderBy("id", "DESC");
        if(!is_null($request->search)) {
            $sql->where('value', 'like', '%' . $request->search . "%");
        }
        if(!is_null($request->from)) {
            $sql->whereDate("date", ">=", $request->from)->whereDate("date", "<=", $request->to);
        }
        $data = $sql->paginate(10);

        if(!count($data)) {
            $sql = Metar::orderBy("id", "DESC");
            if(!is_null($request->search)) {
                $string = explode(" ", $request->search);
                foreach ($string as $str) {
                    $sql->where('value', 'like', "%$str%");
                }
            }
            if(!is_null($request->from)) {
                $sql->whereDate("date", ">=", $request->from)->whereDate("date", "<=", $request->to);
            }
            $data = $sql->paginate(10);
        }
        $title = self::TITLE;
        return view(self::VIEW_FOLDER . "index", compact("data", 'title', 'request'));
    }
}
