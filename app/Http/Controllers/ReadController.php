<?php


namespace App\Http\Controllers;

use App\Meteo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ReadController extends Controller
{

    use FileHelper;

    const QLI = "qli";
    const BRS = "brs";
    const FD = "fd";
    const WAT = "wat";
    const CT2K = "ct2k";
    const METAR = "met";
    const FILE_EXTENSION_TEL = "tel";
    const FILE_EXTENSION_DAT = "dat";

    private static $READ_PATH; // The path, where the file should be read from.
    private static $READ_PATH2; // The path, where the file should be read from.
    private $metar; // Instance for Metar
    private $readDate; // example 0203, 0204, 0205 ... 0301, 0302 ..
    private static $move_error_path; // The path, where the problematic file should be moved.
    private $modelInstance;

    public function __construct()
    {
        self::$MASTER_PATH = env("MASTER_PATH");
        self::$SLAVE_PATH = env("SLAVE_PATH");
        self::$READ_PATH = env("FILE_READ_PATH");
        self::$READ_PATH2 = env("FILE_READ_PATH2");
        self::$move_error_path = env("MOVE_ERROR_PATH");
        $this->modelInstance = new Meteo();
    }

    public function __destruct()
    {
        // Destruct method is called twice, check if the model instance is not empty and save
        if(count($this->modelInstance->attributesToArray()))
        {
            $this->modelInstance->created_at = Carbon::now()->subMinute();
            $this->modelInstance->save();
        }
    }

    /**
     * Start reading the new file
     *
     * @return \Illuminate\Http\Response
     */
    public function start($count = 1)
    {
        $path = $this->get_full_path( self::$READ_PATH);
        if(is_null($path)) return;
        $files = scandir($path);
        $this->readDate = date('md H:i:s', strtotime('-1 minutes'));

        foreach ($files as $file) {
            if($file != "." && $file != "..") {
                $this->read($path.$file);
            }
        }

        $path2 = $this->get_full_path(self::$READ_PATH2);
        if(is_null($path2)) return;
        $files = scandir($path2);
        foreach ($files as $file) {
            if($file != "." && $file != "..") {
                $this->read($path2.$file);
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
        $explodedDate = explode(" ", $this->readDate);
        $date = $explodedDate[0];

        $expType = explode(env('DELIMITER'), $filePath);
        $type = end($expType);
        $explodedTime = explode(":", $explodedDate[1]);
        $currentHour = $explodedTime[0];
        $currentMinute = $explodedTime[1];

        // Read the file, which is created today
        if(self::QLI . $date . '.' . self::FILE_EXTENSION_DAT == $type) {
            $this->readQli($filePath, $currentHour, $currentMinute);
        } elseif (self::BRS . $date . '.' . self::FILE_EXTENSION_DAT == $type) {
            $this->readBrs($filePath, $currentHour, $currentMinute);
        } elseif(self::WAT . $date . '.' . self::FILE_EXTENSION_DAT == $type) {
            $this->readWat($filePath, $currentHour, $currentMinute);
        } elseif(self::FD . $date . '.' . self::FILE_EXTENSION_DAT == $type) {
            $this->readFd($filePath, $currentHour, $currentMinute);
        } elseif(self::CT2K . $date . '.' . self::FILE_EXTENSION_DAT == $type) {
            $this->readCt2k($filePath, $currentHour, $currentMinute);
        } elseif(self::METAR . $date . '.' . self::FILE_EXTENSION_TEL == $type) {
            $this->readMetar($filePath, $currentHour, $currentMinute);
        }

    }

    private function readQli($filePath, $currentHour, $currentMinute)
    {
        $myFile = fopen($filePath, "r");
        if($myFile) {
            try {
                while (($line = fgets($myFile)) !== false) {
                    $line = preg_replace('/\s\s+/', ' ', $line); // Remove the space if there is more than 1 h    h = h h
                    $explodedData = explode(" ", $line);
                    $date = $explodedData[1];
                    $explodedDate = explode(":", $date);
                    $readHour = $explodedDate[0];
                    $readMinute = $explodedDate[1];
                    if($currentHour == $readHour && $currentMinute == $readMinute) {
                        $this->modelInstance->temperature = str_replace(";", "", $explodedData[6]);
                        $this->modelInstance->wet = str_replace(";", "", $explodedData[8]);
                        $this->modelInstance->wind_speed_09 = explode("=", $explodedData[17])[1];
                        $this->modelInstance->wind_direction_09 = trim(preg_replace('/\s\s+/', ' ', explode("=", $explodedData[19])[1]));
                    }
                }
            } catch (\Exception $exception) {
                Log::info($exception);
            }
        }
        fclose($myFile);
    }

    private function readBrs($filePath, $currentHour, $currentMinute)
    {
        $myFile = fopen($filePath, "r");
        if($myFile) {
            try {
                while (($line = fgets($myFile)) !== false) {
                    $line = preg_replace('/\s\s+/', ' ', $line);
                    $explodedData = explode(" ", $line);
                    $date = $explodedData[1];
                    $explodedDate = explode(":", $date);
                    $readHour = $explodedDate[0];
                    $readMinute = $explodedDate[1];
                    if($currentHour == $readHour && $currentMinute == $readMinute) {
                        $this->modelInstance->bar = trim(preg_replace('/\s\s+/', ' ', $explodedData[4]));  // Remove last "\r\n"
                    }
                }
            } catch (\Exception $exception) {
                Log::info($exception);
            }
        }
        fclose($myFile);
    }

    private function readWat($filePath, $currentHour, $currentMinute)
    {
        $myFile = fopen($filePath, "r");
        if($myFile) {
            try {
                while (($line = fgets($myFile)) !== false) {
                    $line = preg_replace('/\s\s+/', ' ', $line);
                    $explodedData = explode(" ", $line);
                    $date = $explodedData[1];
                    $explodedDate = explode(":", $date);
                    $readHour = $explodedDate[0];
                    $readMinute = $explodedDate[1];
                    if($currentHour == $readHour && $currentMinute == $readMinute) {
                        $this->modelInstance->wind_speed_27 = $explodedData[5];
                        $this->modelInstance->wind_direction_27 = trim(preg_replace('/\s\s+/', ' ',  explode("=", $explodedData[8])[1]));  // Remove last "\r\n"
                    }
                }
            } catch (\Exception $exception) {
                Log::info($exception);
            }
        }
        fclose($myFile);
    }

    private function readFd($filePath, $currentHour, $currentMinute)
    {
        $myFile = fopen($filePath, "r");
        if($myFile) {
            try {
                $row = 1;
                while (($line = fgets($myFile)) !== false) {
                    $line = preg_replace('/\s\s+/', ' ', $line);
                    $explodedData = explode(" ", $line);
                    if($explodedData[0] == "") continue;
                    $date = $explodedData[1];
                    $explodedDate = explode(":", $date);
                    $readHour = $explodedDate[0];
                    $readMinute = $explodedDate[1];
                    if($currentHour == $readHour && $currentMinute == $readMinute) {
                        if($row == 1) {
                            if($explodedData[3] != "FD/85:") continue; // If the first line of the data is not the current one
                            $this->modelInstance->visibility_09 = $explodedData[5];
                            $row = 2;
                        } elseif ($row == 2) {
                            $this->modelInstance->visibility_mid = $explodedData[6];
                            $row = 3;
                        } elseif ($row == 3) {
                            $this->modelInstance->visibility_27 = $explodedData[6];
                            $row = 1;
                        }
                    }
                }
            } catch (\Exception $exception) {
                Log::info($exception);
            }
        }
        fclose($myFile);
    }

    private function readCt2k($filePath, $currentHour, $currentMinute)
    {
        $myFile = fopen($filePath, "r");
        if($myFile) {
            try {
                $next = false;
                while (($line = fgets($myFile)) !== false) {
                    $line = preg_replace('/\s\s+/', ' ', $line);
                    $explodedData = explode(" ", $line);
                    if($explodedData[0] != "") {
                        $date = $explodedData[1];
                        $explodedDate = explode(":", $date);
                        $readHour = $explodedDate[0];
                        $readMinute = $explodedDate[1];
                        if($currentHour == $readHour && $currentMinute == $readMinute) {
                            $next = true;
                        }
                    } else {
                        if($next) {
                            $this->modelInstance->cloud_height = ltrim($explodedData[3], '0');
                            $next = false;
                        }
                    }
                }
            } catch (\Exception $exception) {
                Log::info($exception);
            }
        }
        fclose($myFile);
    }

    private function readMetar($filePath, $currentHour, $currentMinute)
    {
        $myFile = fopen($filePath, "r");
        if($myFile) {
            try {
                $next = false;
                while (($line = fgets($myFile)) !== false) {
                    $explodedData = explode(" ", $line);
                    if($explodedData[0] != "") {
                        $date = $explodedData[1];
                        $explodedDate = explode(":", $date);
                        $readHour = $explodedDate[0];
                        $readMinute = $explodedDate[1];
                        if($currentHour == $readHour && $currentMinute >= $readMinute) {
                            if($currentMinute >= 30 && $readMinute < 30) continue;
                            $next = true;
                        }
                    } else {
                        if($next) {
                            $this->modelInstance->contact_coefficient = $explodedData[count($explodedData) - 2];
                            $first_with_slash = [];
                            foreach ($explodedData as $bin => $d) {
                                if(strpos($d, "/") && $bin != count($explodedData) - 2) {
                                    $this->modelInstance->start_point = explode("/", $d)[1];
                                }
                                if(strpos($d, "FEW") !== false || strpos($d, "NSC") !== false || strpos($d, "SCT") !== false || strpos($d, "BKN") !== false || strpos($d, "OVC") !== false) {
                                    $this->modelInstance->cloudy = $d;
                                }
                                if(strpos($d, "/")) {
                                    $first_with_slash []= $d;
                                }
                            }
                            if(count($first_with_slash) > 1) {
                                $this->modelInstance->cloudy = $this->modelInstance->cloudy . " " . $first_with_slash[0];
                            }
                            $next = false;
                        }
                    }
                }
            } catch (\Exception $exception) {
                Log::info($exception);
            }
        }
        fclose($myFile);
    }

}
