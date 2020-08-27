<?php

namespace App\Http\Controllers;

use App\Bi;
use App\Metar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class FileReadController extends Controller
{

    const READ_PATH = "/var/www/html/my/meteo/public/files/unread/"; // The path, where the file should be read from.
    const MOVE_PATH = "/var/www/html/my/meteo/public/files/read/"; // The path, where the file should be moved after successfully reading.
    const MOVE_ERROR_PATH = "/var/www/html/my/meteo/public/files/error/"; // The path, where the problematic file should be moved.
    const BI = "bi";

    private $metar; // Instance for Metar
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
            $this->manageMetarType($filePath);
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
                    $this->convertToNormalDate($explodedLine[0]);
                    $bi->col0 = Carbon::parse($explodedLine[0] . " " .$explodedLine[1]);
                    foreach ($explodedLine as $bin => $key) {
                        if($bin + 1 > 13) break;
                        $bi->{"col" . ($bin + 1)} = $explodedLine[$bin + 4] ?? "---";
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
        $myFile = fopen($filePath, "r");
        $error = false;
        if($myFile) {
            try {
                $odd = false; // Metar file types comes with 2 lines
                while (($line = fgets($myFile)) !== false) {
                    $explodedLine = explode(" ", $line);

                    if(!$odd) $this->metar = new Metar(); // Creating new Metar, when reading the first line

                    $this->removeUnusedCharsFromArray($explodedLine, ["", "|", '', ' ', " "]);
                    if(!$odd) {
                        // Reading first line
                        $this->convertToNormalDate($explodedLine[0]);
                        $this->metar->date = Carbon::parse($explodedLine[0] . " " .$explodedLine[1]);
                    } else {
                        $this->metar->value = implode(" ", $explodedLine);
                        $this->metar->save();
                    }
//                    } else {
//                        $number = 0;
//                        $this->metar->col1 = $explodedLine[$number]; $number ++;
//                        // This can be 'Metar' or 'Metar Cor'
//                        if($explodedLine[$number] == "COR") {
//                            $this->metar->col1 .= " " . $explodedLine[$number]; $number ++;
//                        }
//                        $this->metar->col2 = $explodedLine[$number]; $number ++;
//                        $this->metar->col3 = $explodedLine[$number]; $number ++;
//
//                        if($explodedLine[$number] == "AUTO" || $explodedLine[$number] == "NIL") {
//                            $this->metar->col4 = $explodedLine[$number]; $number ++;
//                        }
//
//                        $this->metar->col5 = $explodedLine[$number]; $number ++;
//
//                        if(!is_numeric($explodedLine[$number])) {
//                            // This is case, when this val can consist of 2 part
//                            $this->metar->col5 .= " " .$explodedLine[$number]; $number ++;
//                        }
//
//                        $this->metar->col6 = $explodedLine[$number]; $number ++;
//
//                        // This line comes with first letter 'R'.
//                        if($explodedLine[$number][0] != "R") {
//                            $this->metar->col6 .= " " .$explodedLine[$number]; $number ++;
//                        } else {
//                            $this->metar->col7 = $explodedLine[$number]; $number ++;
//                        }
//
//
//                        // regexp replaces all numbers. This line comes without numbers, so replacing numbers and checking if the length changes or not.
//                        if(strlen(preg_replace("/[^0-9]/", "", $explodedLine[$number] )) > 0) {
//                            $this->metar->col7 .= " " .$explodedLine[$number]; $number ++;
//                        }
//
//                        // col8
//
//                        $this->metar->col9 = $explodedLine[$number]; $number ++;
//
//                        // aaa333,
//                        if(preg_match('/\b[a-zA-Z]{3}\d{3}\b/', $explodedLine[$number]) ) {
//
//                        }
//
//                    }

                    $odd = !$odd;
                }
            } catch (\Exception $exception) {
                $error = true;
                Log::error($exception);  // Write in laravel.log in case of errors
            }
        }
        fclose($myFile);
        $this->moveFile($error, $filePath);
    }

    /**
     * Removing unwanted characters
     *
     * @param $error $filepath
     * @return void
     */
    private function removeUnusedCharsFromArray(&$array, $chars)
    {
        foreach ($array as $bin => $key) {
            if(in_array($key, $chars)) unset($array[$bin]);
        }
        $array = array_values($array); // Reindexing
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

    /**
     * Convert file date to normal sql date format
     *
     * @param $date
     * @return void (changes the instance)
     */
    private function convertToNormalDate(&$date)
    {
        $date = explode(".", $date);
        $date[count($date) - 1] = 20 . $date[count($date) - 1];
        $date = implode(".", $date);
    }

}
