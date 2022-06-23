<?php


namespace App\Http\Controllers;


trait FileHelper
{

    private static $MASTER_PATH;
    private static $SLAVE_PATH;

    public function get_full_path($file_path)
    {
        $master_path = self::$MASTER_PATH . $file_path;
        $slave_path = self::$SLAVE_PATH . $file_path;
        if(is_dir($master_path)) {
            return $master_path;
        }
        elseif (is_dir($slave_path)) {
            return $slave_path;
        }
        return null;
    }
}
