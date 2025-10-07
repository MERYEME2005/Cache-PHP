<?php
class Cache {
    private static $file = "cache.json";
    private static $data = [];

    private static function load() {
        self::$data = file_exists(self::$file) ? json_decode(file_get_contents(self::$file), true) : [];
    }

    private static function save() {
        file_put_contents(self::$file, json_encode(self::$data));
    }

    public static function set($key, $value, $ttl) {
        self::load();
        self::$data[$key] = ["value"=>$value,"expires"=>time()+$ttl];
        self::save();
        return true;
    }

    public static function get($key) {
        self::load();
        if(!isset(self::$data[$key])) return null;
        if(self::$data[$key]["expires"] < time()){
            unset(self::$data[$key]);
            self::save();
            return null;
        }
        return self::$data[$key]["value"];
    }

    public static function update($key, $value, $ttl) {
        self::load();
        if(!isset(self::$data[$key])) return false;
        self::$data[$key] = ["value"=>$value,"expires"=>time()+$ttl];
        self::save();
        return true;
    }

    public static function delete($key) {
        self::load();
        if(!isset(self::$data[$key])) return false;
        unset(self::$data[$key]);
        self::save();
        return true;
    }
}
?>