<?php
/**
 * Date: 2021/5/8 15:33
 * User: YHC
 * Desc: 事务使用
 */

namespace Library\Pdo;

use Library\Core\Database\Builder\TransactionBuilder;
use Library\Core\Database\Handle\ConnectHandle;

class Transaction
{
    use TransactionBuilder,ConnectHandle;

    protected $config = [];

    protected $table = '';

    /**
     * @var \PDO
     */
    protected $connect = null;

    /**
     * Select constructor.
     * @param array $config
     * @param string $table
     */
    public function __construct(array $config, string $table, \PDO $connect = null)
    {
        $this->config = $config;

        $this->table = $table;

        $this->connect = $connect;
    }

    /**
     *
     * @param string $column
     * @return Select
     */
    public function select(string $column = '*')
    {
        $select = new Select($this->config, $this->table, $this->connect);

        $select->column($column);

        return $select;
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
    public function insert(array $data = [])
    {
        $insert = new Insert($this->config, $this->table, $this->connect);

        if (!empty($data)) {
            $insert->builder($data);
        }

        return $insert;
    }

    /**
     *
     * @param string $table
     * @return Delete
     */
    public function delete()
    {
        return new Delete($this->config, $this->table, $this->connect);
    }
}
