<?php

class Utils {

    public static function init() {
        self::config();
    }

    public static function config() {
        $ini = parse_ini_file("config.ini");
        foreach($ini as $k=>$v) {
            define($k,$v);
        }
    }
}

Utils::init();

