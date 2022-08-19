<?php

if ($_SERVER['REQUEST_METHOD'] == 'GET' && realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME'])) {
    header('HTTP/1.0 403 Forbidden', TRUE, 403);
    exit();
}

require_once('constants.php');

class Database
{
    private static ?\PDO $pdo = null;

    private function __construct()
    {
    }

    public static function getConnection(): \PDO
    {
        if (self::$pdo == null) {
            self::$pdo = new \PDO("mysql:host=" . DBHOST . ";dbname=" . DBNAME, DBUSER, DBPASS);
        }

        return self::$pdo;
    }
}
