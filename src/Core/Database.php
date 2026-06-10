<?php

final class Database
{
    private static ?PDO $connection = null;

    private function __construct()
    {
    }

    public static function connection(): PDO
    {
        if (self::$connection === null) {
            self::$connection = getConnection();
        }
        return self::$connection;
    }
}
