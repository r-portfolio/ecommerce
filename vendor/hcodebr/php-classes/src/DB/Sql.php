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

    /**
     * __construct.
     *
     * @return void
     */
    public function __construct()
    {
        $this->conn = new \PDO(
            'mysql:dbname='.self::DBNAME.';host='.self::HOSTNAME,
            self::USERNAME,
            self::PASSWORD
        );
    }

    // Só executa alguma coisa no banco

    /**
     * query.
     *
     * @param mixed $rawQuery
     * @param mixed $params
     *
     * @return void
     */
    public function query($rawQuery, $params = [])
    {
        $stmt = $this->conn->prepare($rawQuery);

        $this->setParams($stmt, $params);

        $stmt->execute();
    }

    // O select executa e nos trás uma resposta com  fetchAll

    /**
     * select.
     *
     * @param mixed $rawQuery
     * @param mixed $params
     *
     * @return array
     */
    public function select($rawQuery, $params = []): array
    {
        $stmt = $this->conn->prepare($rawQuery);

        $this->setParams($stmt, $params);

        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * setParams.
     *
     * @param mixed $statement
     * @param mixed $parameters
     *
     * @return void
     */
    private function setParams($statement, $parameters = [])
    {
        foreach ($parameters as $key => $value) {
            $this->bindParam($statement, $key, $value);
        }
    }

    /**
     * bindParam.
     *
     * @param mixed $statement
     * @param mixed $key
     * @param mixed $value
     *
     * @return void
     */
    private function bindParam($statement, $key, $value)
    {
        $statement->bindParam($key, $value);
    }
}