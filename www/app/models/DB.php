<?php

namespace app\models;

use PDO;
use PDOException;

class DB
{
    protected static PDO $instance;
    public static function getInstance(): PDO
    {
        if (empty(self::$instance)) {

            try {
                self::$instance = new PDO("mysql:host=".$_ENV['DB_HOST'].";dbname=".$_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASS']);
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (PDOException $exception) {
                echo '<br>'.'Connection error: ' . $exception->getMessage();
            }
        }

        return self::$instance;
        
    }

}