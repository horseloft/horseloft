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

    private $table;

    private $key;

    /**
     * Proxy constructor.
     * @param string $table
     */
    public function __construct(string $key, string $table, \PDO $connect)
    {
        $this->key = $key;

        $this->table = $table;

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
        return new Select([], $this->table, $this->connect);
    }

    /**
     *
     * @return Update
     */
    public function update()
    {
        return new Update([], $this->table, $this->connect);
    }

    /**
     *
     * @return Insert
     */
    public function insert()
    {
        return new Insert([], $this->table, $this->connect);
    }

    /**
     *
     * @return Delete
     */
    public function delete()
    {
        return new Delete([], $this->table, $this->connect);
    }

    /**
     *
     * @return Transaction
     */
    public function transaction()
    {
        return new Transaction([], $this->table, $this->connect);
    }

    /**
     *
     * @return PDO
     */
    public function pdo()
    {
        return new PDO([], $this->connect);
    }
}
