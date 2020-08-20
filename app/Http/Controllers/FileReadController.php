<?php

namespace App\Http\Controllers;

use App\Bi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;

class FileReadController extends Controller
{

    const READ_PATH = "/var/www/html/my/meteo/public/files/unread/"; // The path, where the file should be read from.
    const MOVE_PATH = "/var/www/html/my/meteo/public/files/read/"; // The path, where the file should be moved after successfully reading.
    const MOVE_ERROR_PATH = "/var/www/html/my/meteo/public/files/error/"; // The path, where the problematic file should be moved.
    const BI = "bi";

    /**
     * Start reading the new file
     *
     * @return \Illuminate\Http\Response
     */
    public static function start($count = 1)
    {
        $path = self::READ_PATH;
        $files = scandir($path);
        $instance = new self();
        foreach ($files as $file) {
            if($file != "." && $file != "..") {
                $instance->read($path.$file);
            }
        }
    }


    /**
     * Reading file, knowing type
     *
     * @param $filePath
     * @return void
     */
    private function read($filePath)
    {
        $expType = explode("/", $filePath);
        $type = end($expType);
        if(strstr($type, self::BI)) {
            $this->manageBiType($filePath);
        } else {

        }

    }


    /**
     * Reading, saving BI type of file
     *
     * @param $filePath
     * @return void
     */
    private function manageBiType($filePath)
    {
        $myFile = fopen($filePath, "r");
        $error = false;
        if($myFile) {
            try {
                while (($line = fgets($myFile)) !== false) {
                    $explodedLine = explode(" ", $line);
                    $bi = new Bi();
                    foreach ($explodedLine as $bin => $key) {
                        if($bin + 1 > 17) break;
                        $bi->col0 = $explodedLine[0] . " " .$explodedLine[1];
                        $bi->{"col" . ($bin + 1)} = $explodedLine[$bin + 3] ?? "---";
                    }
                    $bi->save();
                }
            } catch (\Exception $exception) {
                $error = true;
            }
        }
        fclose($myFile);
        $this->moveFile($error, $filePath);
    }

    private function manageMetarType($filePath)
    {

    }


    /**
     * Move read file to another dir
     *
     * @param $error $filepath
     * @return void
     */
    private function moveFile($error, $filepath)
    {
        $explodedFile = explode("/", $filepath);
        $file = end($explodedFile);
        if($error) rename($filepath, self::MOVE_ERROR_PATH . $file);
        else rename($filepath, self::MOVE_PATH . $file);
    }
}
