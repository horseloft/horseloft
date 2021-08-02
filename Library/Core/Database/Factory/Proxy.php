<?php
/**
 * Date: 2021/5/10 18:20
 * User: YHC
 * Desc:
 */

namespace Library\Core\Database\Factory;

use Library\Pdo\Delete;
use Library\Pdo\Insert;
use Library\Pdo\Select;
use Library\Pdo\Transaction;
use Library\Pdo\Update;
use Library\Pool\Table;

final class Proxy
{
    /**
     * @var \PDO
     */
    private $connect;

    private $config;

    private $table;

    private $key;

    /**
     * Proxy constructor.
     * @param string $key
     * @param string $table
     * @param \PDO $connect
     * @param $config
     */
    public function __construct(string $key, string $table, \PDO $connect, $config)
    {
        $this->key = $key;

        $this->table = $table;

        $this->config = $config;

        $this->connect = $connect;
    }

    public function __destruct()
    {
        Table::$pool[$this->key][] = $this->connect;
    }

    /**
     *
     * @param \PDO|null $connect
     * @return Select
     */
    public function select()
    {
        return new Select($this->config, $this->table, $this->connect);
    }

    /**
     *
     * @param array $data
     * @return Update
     */
    public function update(array $data)
    {
        return new Update($this->config, $this->table, $data, $this->connect);
    }

    /**
     *
     * @param array $data
     * @return Insert
     */
    public function insert(array $data)
    {
        return new Insert($this->config, $this->table, $data, $this->connect);
    }

    /**
     *
     * @return Delete
     */
    public function delete()
    {
        return new Delete($this->config, $this->table, $this->connect);
    }

    /**
     *
     * @return Transaction
     */
    public function transaction()
    {
        return new Transaction($this->config, $this->table, $this->connect);
    }

    /**
     *
     * @return PDO
     */
    public function pdo()
    {
        return new PDO($this->config, $this->connect);
    }
}
