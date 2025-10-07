<?php
header("Content-Type: application/json");

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
        self::$data[$key] = ["value" => $value, "expires" => time() + $ttl];
        self::save();
        return true;
    }

    public static function get($key) {
        self::load();
        if (!isset(self::$data[$key])) return null;
        if (self::$data[$key]["expires"] < time()) {
            unset(self::$data[$key]);
            self::save();
            return null;
        }
        return self::$data[$key]["value"];
    }

    public static function update($key, $value, $ttl) {
        self::load();
        if (!isset(self::$data[$key])) return false;
        self::$data[$key] = ["value" => $value, "expires" => time() + $ttl];
        self::save();
        return true;
    }

    public static function delete($key) {
        self::load();
        if (!isset(self::$data[$key])) return false;
        unset(self::$data[$key]);
        self::save();
        return true;
    }
}

$method = $_SERVER["REQUEST_METHOD"];
$body = json_decode(file_get_contents("php://input"), true);

if ($method === "POST" && isset($body["key"], $body["value"], $body["ttl"])) {
    Cache::set($body["key"], $body["value"], $body["ttl"]);
    echo json_encode(["message" => "Key '{$body['key']}' set"]);
}

elseif ($method === "GET" && isset($_GET["key"])) {
    $value = Cache::get($_GET["key"]);
    echo $value !== null ? json_encode(["key" => $_GET["key"], "value" => $value])
                         : json_encode(["error" => "Key not found or expired"]);
}

elseif ($method === "PUT" && isset($body["key"], $body["value"], $body["ttl"])) {
    $ok = Cache::update($body["key"], $body["value"], $body["ttl"]);
    echo $ok ? json_encode(["message" => "Key '{$body['key']}' updated"])
             : json_encode(["error" => "Key not found"]);
}

elseif ($method === "DELETE" && isset($_GET["key"])) {
    $ok = Cache::delete($_GET["key"]);
    echo $ok ? json_encode(["message" => "Key '{$_GET['key']}' deleted"])
             : json_encode(["error" => "Key not found"]);
}

else {
    echo json_encode(["error" => "Invalid request"]);
}
?>
