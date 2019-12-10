<?php

namespace app;

use PDO;

class Db
{
    private static $dbh;

    protected static function getDbh(): PDO
    {
        if (static::$dbh) {
            return static::$dbh;
        }

        $dsn = 'mysql:dbname=' . DB_NAME . ';host=' . DB_HOST;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];
        static::$dbh = new PDO($dsn, DB_USER, DB_PASSWORD, $options);

        return static::$dbh;
    }

    public static function fetchAll(string $query, array $data = []): array
    {
        if (!$data) {
            return static::getDbh()->query($query)->fetchAll();
        }
        $stm = static::getDbh()->prepare($query);
        $stm->execute($data);
        return $stm->fetchAll();
    }

    public static function rowCount(string $query, array $data = []): int
    {
        if (!$data) {
            return static::getDbh()->query($query)->rowCount();
        }
        $stm = static::getDbh()->prepare($query);
        $stm->execute($data);

        return $stm->rowCount();
    }
}
