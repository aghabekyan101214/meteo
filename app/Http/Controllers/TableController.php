<?php

namespace App\Http\Controllers;

use App\Exports\MeteoTableExport;
use App\Meteo;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;


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

    public function export_to_excel(Request $request)
    {
        $sql = Meteo::orderBy("id", "DESC");
        $this->manageSearch($request, $sql);
        return Excel::download(new MeteoTableExport($sql->get()), 'meteo.xlsx');
    }

    private function manageSearch(Request $request, &$sql)
    {
        if(!is_null($request->from)) {
            $sql->whereDate("created_at", ">=", $request->from)->whereDate("created_at", "<=", $request->to);
        }
        $searchData = $request->all();
        unset($searchData['_token']);
        foreach ($searchData as $key => $val) {
            if($key == 'from' || $key == 'to' || $key == 'page') continue;
            $val = str_replace(' ', '', $val);
            if (strpos($val, '-')) {
                $splVal = explode('-', $val);
                if (count($splVal) == 2) {
                    $sql->whereBetween($key, [$splVal[0], $splVal[1]]);
                }
            } else {
                $sql->where($key, $val);
            }
        }
    }
}
