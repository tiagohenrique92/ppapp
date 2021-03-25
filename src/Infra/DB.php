<?php
namespace PPApp\Infra;

use Closure;
use Exception;
use Illuminate\Database\Connection;

class DB
{
    /**
     * $connection
     *
     * @var Connection
     */
    private static $connection = null;

    /**
     * beginTransaction
     *
     * @return void
     */
    public static function beginTransaction()
    {
        self::checkConnection();
        self::$connection->beginTransaction();
    }

    /**
     * checkConnection
     *
     * @return void
     */
    public static function checkConnection(): void
    {
        if (null === self::$connection) {
            throw new Exception("Connection not defined");
        }
    }

    /**
     * commit
     *
     * @return void
     */
    public static function commit(): void
    {
        self::checkConnection();
        self::$connection->commit();
    }

    /**
     * rollBack
     *
     * @return void
     */
    public static function rollBack(): void
    {
        self::checkConnection();
        self::$connection->rollBack();
    }

    /**
     * register
     *
     * @param Connection $connection
     * @return void
     */
    public static function setConnection(Connection $connection): void
    {
        if (null === self::$connection) {
            self::$connection = $connection;
        }
    }

    public static function transaction(Closure $callback, $attempts = 1)
    {
        self::checkConnection();
        return self::$connection->transaction($callback, $attempts);
    }
}
