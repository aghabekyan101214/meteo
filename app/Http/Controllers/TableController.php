<?php

namespace App\Http\Controllers;

use App\Meteo;
use Illuminate\Http\Request;

class TableController extends Controller
{
    const VIEW_FOLDER = "meteo."; // Path to view folder
    const ROUTE = "/meteo"; // Current route
    const TITLE = "Աղյուսակ"; // Title for resource

    public function index(Request $request)
    {
        $sql = Meteo::orderBy("id", "DESC");
        $this->manageSearch($request, $sql);
        $data = $sql->paginate(10);
        return view(self::VIEW_FOLDER . "index", compact("data", 'request'));
    }

    private function manageSearch(Request $request, &$sql)
    {
        if(!is_null($request->from)) {
            $sql->whereDate("date", ">=", $request->from)->whereDate("date", "<=", $request->to);
        }
        $searchData = $request->all();
        foreach ($searchData as $key => $val) {
            if($key == 'from' || $key == 'to' || $key == 'page') continue;
            $sql->where($key, $val);
        }
    }
}
