<?php
/**
 * Date: 2021/5/8 13:28
 * User: YHC
 * Desc: 可以使用PDO原生语句
 */

namespace Library\Core\Database\Factory;

use Library\Core\Database\Handle\ConnectHandle;
use Library\Core\Database\Handle\StatementHandle;
use Library\Core\Database\Builder\TransactionBuilder;

class PDO
{
    use TransactionBuilder,StatementHandle,ConnectHandle;
    /**
     * @var \PDO
     */
    protected $connect = '';

    protected $config = [];

    public function __construct(array $config, \PDO $connect = null)
    {
        $this->config = $config;

        $this->connect = $connect;
    }

    /**
     * ----------------------------------------------------------------
     * PDO::query()
     * ----------------------------------------------------------------
     *
     * select
     *
     * 返回 FETCH_ASSOC 结果集
     *
     * @param string $sql
     * @return \PDOStatement
     */
    public function query(string $sql)
    {
        $sql = $this->getAndSet($sql);
        $stmt = $this->connect->query($sql, \PDO::FETCH_ASSOC);

        if ($stmt == false) {
            if (HORSE_LOFT_PDO_ERROR_DETAIL) {
                $message = $this->connect->errorInfo()[2];
            } else {
                $message = 'DATA SERVER ERROR';
            }
            throw new \RuntimeException($message, HORSE_LOFT_DATABASE_ERROR_CODE);
        }
        return $stmt;
    }

    /**
     * ----------------------------------------------------------------
     * 返回一条记录
     * ----------------------------------------------------------------
     *
     * @param string $sql
     * @param mixed ...$param
     * @return array
     */
    public function fetch(string $sql, ...$param)
    {
        $sql = $this->getAndSet($sql);
        $statement = $this->statement($sql, $param);

        return $this->fetchBuilder($statement, false);
    }

    /**
     * ----------------------------------------------------------------
     * 返回全部记录
     * ----------------------------------------------------------------
     *
     * @param string $sql
     * @param mixed ...$param
     * @return array
     */
    public function fetchAll(string $sql, ...$param)
    {
        $sql = $this->getAndSet($sql);
        $statement = $this->statement($sql, $param);

        return $this->fetchBuilder($statement);
    }

    /**
     * ----------------------------------------------------------------
     * PDO::exec()
     * ----------------------------------------------------------------
     *
     * insert|delete|update
     *
     * delete|update:返回受影响的行数
     * insert:返回最后一个写入的ID
     *
     * @param string $sql
     * @return int
     */
    public function exec(string $sql, ...$param)
    {
        $sql = $this->getAndSet($sql);

        $statement = $this->statement($sql, $param);

        if (strtolower(substr($sql, 0, 6)) == 'insert') {
            return (int)$this->connect->lastInsertId();
        }
        return $statement->rowCount();
    }

    /**
     * 最后插入行的ID或序列值
     *
     * @param PDO $query
     * @param null $name
     * @return mixed
     */
    public function lastInsertId(PDO $query, $name = null)
    {
        return $query->connect->lastInsertId($name);
    }

    /**
     *
     * @param string $sql
     * @return string
     */
    private function getAndSet(string $sql)
    {
        $sql = trim($sql);
        if (empty($sql)) {
            throw new \RuntimeException('Parameter cannot be empty', HORSE_LOFT_DATABASE_ERROR_CODE);
        }
        if (is_null($this->connect)) {
            $this->connect = $this->connect();
        }
        return $sql;
    }
}
