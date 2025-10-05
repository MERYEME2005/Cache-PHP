<?php
header("Content-Type: application/json");

class Cache {
    private static $data = [];

    public static function set($key, $value, $ttl) {
        self::$data[$key] = ["value" => $value, "expires" => time() + $ttl];
    }

    public static function get($key) {
        if (!isset(self::$data[$key])) return null;
        $item = self::$data[$key];
        if ($item["expires"] < time()) {
            unset(self::$data[$key]);
            return null;
        }
        return $item["value"];
    }

    public static function update($key, $value, $ttl) {
        if (!isset(self::$data[$key])) return false;
        self::$data[$key] = ["value" => $value, "expires" => time() + $ttl];
        return true;
    }

    public static function delete($key) {
        if (!isset(self::$data[$key])) return false;
        unset(self::$data[$key]);
        return true;
    }
}

$method = $_SERVER["REQUEST_METHOD"];
$body   = json_decode(file_get_contents("php://input"), true);

switch ($method) {
    case "POST":
        if (isset($body["key"], $body["value"], $body["ttl"])) {
            Cache::set($body["key"], $body["value"], $body["ttl"]);
            echo json_encode(["message" => "Key {$body['key']} set with value {$body['value']}"]);
        }
        break;

    case "GET":
        if (isset($_GET["key"])) {
            $value = Cache::get($_GET["key"]);
            echo $value !== null
                ? json_encode(["key" => $_GET["key"], "value" => $value])
                : json_encode(["error" => "Key not found or expired"]);
        }
        break;

    case "PUT":
        if (isset($body["key"], $body["value"], $body["ttl"])) {
            $ok = Cache::update($body["key"], $body["value"], $body["ttl"]);
            echo $ok
                ? json_encode(["message" => "Key {$body['key']} updated to {$body['value']}"])
                : json_encode(["error" => "Key not found"]);
        }
        break;

    case "DELETE":
        if (isset($_GET["key"])) {
            $ok = Cache::delete($_GET["key"]);
            echo $ok
                ? json_encode(["message" => "Key {$_GET['key']} deleted"])
                : json_encode(["error" => "Key not found"]);
        }
        break;

    default:
        echo json_encode(["error" => "Invalid HTTP method"]);
}
?>