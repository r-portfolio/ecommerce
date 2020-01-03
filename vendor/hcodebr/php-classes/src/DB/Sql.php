<?php

declare(strict_types=1);

namespace Hcode\DB;

class Sql
{
    public const HOSTNAME = '127.0.0.1';
    public const USERNAME = 'root';
    public const PASSWORD = '';
    public const DBNAME = 'db_ecommerce';

    private $conn;

    public function __construct()
    {
        $this->conn = new \PDO(
            'mysql:dbname='.self::DBNAME.';host='.self::HOSTNAME,
            self::USERNAME,
            self::PASSWORD
        );
    }

    public function query($rawQuery, $params = [])
    {
        $stmt = $this->conn->prepare($rawQuery);

        $this->setParams($stmt, $params);

        $stmt->execute();
    }

    public function select($rawQuery, $params = []): array
    {
        $stmt = $this->conn->prepare($rawQuery);

        $this->setParams($stmt, $params);

        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function setParams($statement, $parameters = [])
    {
        foreach ($parameters as $key => $value) {
            $this->bindParam($statement, $key, $value);
        }
    }

    private function bindParam($statement, $key, $value)
    {
        $statement->bindParam($key, $value);
    }
}