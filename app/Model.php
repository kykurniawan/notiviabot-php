<?php

if ($_SERVER['REQUEST_METHOD'] == 'GET' && realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME'])) {
    header('HTTP/1.0 403 Forbidden', TRUE, 403);
    exit();
}

require_once('Database.php');

class Model
{
    protected $database;

    public function __construct()
    {
        $this->database = Database::getConnection();
    }
}
