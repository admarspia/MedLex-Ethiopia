<?php
// activity logs

class Helper {

    public static function logger($type, $message) {
        $logfile = fopen(__DIR__ . "/../activities.log", "a+");

        fwrite($logfile, "# " . $type . " : " . $message . PHP_EOL);

        fclose($logfile);
    }


    public static function getLog() {
        $logfile = fopen(__DIR__ . "/../activities.log", "r");

        $file_contents = [];

        while (($line = fgets($logfile)) !== false) {
            $file_contents[] = $line;
        }

        fclose($logfile);

        return $file_contents;
    }

}
