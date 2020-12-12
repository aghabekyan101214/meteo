<?php

namespace App\Http\Controllers;

use App\Bi;
use App\LastReadDate;
use App\Metar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class FileReadController extends Controller
{

    private static $READ_PATH; // The path, where the file should be read from.
    const MOVE_PATH = "/var/www/html/my/meteo/public/files/read/"; // The path, where the file should be moved after successfully reading.
    const BI = "bi1";
    const BRS = "brs";
    const METAR = "met";
    const FILE_EXTENSION_TEL = "tel";

    private $metar; // Instance for Metar
    private $readDate; // example 0203, 0204, 0205 ... 0301, 0302 ..
    private static $move_error_path; // The path, where the problematic file should be moved.

    public function __construct()
    {
        self::$READ_PATH = env("FILE_READ_PATH");
        self::$move_error_path = env("MOVE_ERROR_PATH");
    }

    /**
     * Start reading the new file
     *
     * @return \Illuminate\Http\Response
     */
    public static function start($count = 1)
    {
        $path = self::$READ_PATH;
        $files = scandir($path);
        $instance = new self();
        $instance->readDate = date('md',strtotime('0 days'));

        $lastReadDate = LastReadDate::first() ?? new LastReadDate();
        if($lastReadDate->day != $instance->readDate) {
            $lastReadDate->time_bi = -1;
            $lastReadDate->time_metar = -1;
            $lastReadDate->day = $instance->readDate;
            $lastReadDate->save();
        }
//        if($instance->checkDateToRead($instance->readDate)) return;
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
        $date = $this->readDate;
        $expType = explode("/", $filePath);
        $type = end($expType);
        // Read the file, which is created today
        if(self::BI . $date . '.' . self::FILE_EXTENSION_TEL == $type) {
            $this->manageBiType($filePath);
        } elseif(self::METAR . $date . '.' . self::FILE_EXTENSION_TEL == $type) {
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
                $count = 0;

                while (($line = fgets($myFile)) !== false) {

                    $lastReadDate = LastReadDate::first();

                    $metarLastData = $this->findMetar($filePath);

                    $col14 = $metarLastData[0];
                    $col15 = $metarLastData[1];
                    if(!is_null($lastReadDate) && $count <= $lastReadDate->time_bi) {
                        $count ++;
                        continue;
                    }
                    $explodedLine = explode(" ", $line);

                    $bi = new Bi();
                    $this->convertToNormalDate($explodedLine[0]);
                    $bi->col0 = Carbon::parse($explodedLine[0] . " " .$explodedLine[1]);
                    foreach ($explodedLine as $bin => $key) {
                        if($bin + 1 > 13) break;
                        $bi->{"col" . ($bin + 1)} = $explodedLine[$bin + 4] ?? "---";
                    }
                    $bi->col14 = $col14;
                    $bi->col15 = $col15;
                    $bi->save();

                    $lastReadDate->time_bi = $count;
                    $lastReadDate->save();

                    $count ++;
                }
            } catch (\Exception $exception) {
                $error = true;
                Log::info($exception);
            }
        }
        fclose($myFile);
        $this->moveFile($error, $filePath);
    }


    private function findMetar($filepath)
    {
        $newPath = explode('/', $filepath);
        unset($newPath[count($newPath) - 1]);
        $newPath = implode('/', $newPath);
        $newPath .= '/' . self::METAR . $this->readDate . '.' . self::FILE_EXTENSION_TEL;
        $f = fopen($newPath, "r");
        $line = '';
        $error = false;

        if($f) {
            try {
                $cursor = -1;

                fseek($f, $cursor, SEEK_END);
                $char = fgetc($f);

                /**
                 * Trim trailing newline chars of the file
                 */
                while ($char === "\n" || $char === "\r") {
                    fseek($f, $cursor--, SEEK_END);
                    $char = fgetc($f);
                }

                /**
                 * Read until the start of file or first newline char
                 */
                while ($char !== false && $char !== "\n" && $char !== "\r") {
                    /**
                     * Prepend the new char
                     */
                    $line = $char . $line;
                    fseek($f, $cursor--, SEEK_END);
                    $char = fgetc($f);
                }
                $line = explode(" ", $line);
                $this->removeUnusedCharsFromArray($line, ["", "|", '', ' ', " "]);
                $col14 = $line[count($line) - 3] ?? '---';
                $col15 = $this->checkForWeatherType($line);
                return array($col14, $col15);
            } catch (\Exception $e) {
                Log::info($e);
            }
        }
    }


    /**
     * Reading, saving METAR type of file
     *
     * @param $filePath
     * @return void
     */
    private function manageMetarType($filePath)
    {
        $myFile = fopen($filePath, "r");
        $error = false;
        if($myFile) {
            $count = 0;
            try {
                $odd = false; // Metar file types comes with 2 lines
                while (($line = fgets($myFile)) !== false) {
                    $lastReadDate = LastReadDate::first();
                    if(!is_null($lastReadDate) && $count <= $lastReadDate->time_metar) {
                        $count ++;
                        continue;
                    }
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

                    $odd = !$odd;
                    $lastReadDate->time_metar = $count;
                    $lastReadDate->save();
                    $count ++;
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
        if($error) copy($filepath, self::$move_error_path . $file);
//        else rename($filepath, self::MOVE_PATH . $file);
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

    /**
     * Check, if current date's file is read or not
     *
     * @param $date
     * @return bool
     */
    private function checkDateToRead($date)
    {
        $lastReadDate = LastReadDate::first()->date ?? 0;
        return $date == $lastReadDate;
    }


    /**
     * Check, if metar includes info about weather
     *
     * @param $arr
     * @return bool
     */
    private function checkForWeatherType($arr)
    {
        $types = ["DZ", 'RA', 'SN', 'SG', 'PL', 'DS', 'SS', 'FZDZ', 'FZRA', 'FZUP', 'FC', 'SHGR', 'SHGS', 'SHRA', 'SHSN', 'SHUP', 'TSGR', 'TSGS', 'TSRA', 'TSSN', 'TSUP', 'UP', 'FG', 'BR', 'SA', 'DU', 'HZ', 'FU', 'VA', 'SQ', 'PO', 'TS', 'BCFG', 'BLDU', 'BLSA', 'BLSN', 'DRDU', 'DRSA', 'DRSN', 'FZFG', 'MIFG', 'PRFG', '//', 'FG', 'PO', 'FC', 'DS', 'SS', 'TS', 'SH', 'BLSN', 'BLSA', 'BLDU', 'VA'];
        foreach ($arr as $a) {

            if(in_array($a, $types)) {
                return $a;
            } elseif(in_array("+$a", $types)) {
                return $a;
            } elseif(in_array("-$a", $types)) {
                return $a;
            } elseif(in_array("VC$a", $types)) {
                return $a;
            }
        }
        return '---';
    }

}
