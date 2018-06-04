<?php

namespace OCA\SensorLogger\Utils;

class ClassFinder
{

    /**
     * @param $dir
     * @param string $filter
     * @param array $results
     */
    public static function customClassLoader($dir,  $filter = '', $results = []) {
        $files = scandir($dir);

        foreach($files as $key => $value){
            $path = realpath($dir.DIRECTORY_SEPARATOR.$value);

            if(!is_dir($path)) {
                if(empty($filter) || preg_match($filter, $path)) $results[] = $path;
            } elseif($value != "." && $value != "..") {
                self::customClassLoader($path, $filter, $results);
            }
        }

        foreach ($results as $class) {
            require_once ($class);
        }
    }
}
