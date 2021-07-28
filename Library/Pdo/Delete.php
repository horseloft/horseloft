<?php
/**
 * Date: 2021/5/7 15:16
 * User: YHC
 * Desc: 必须调用execute()才会执行delete
 */

namespace Library\Pdo;

use Library\Core\Database\Factory\Origin;
use Library\Core\Database\Builder\ExecuteBuilder;
use Library\Core\Database\Builder\ConditionBuilder;

class Delete extends Origin
{
    use ConditionBuilder,ExecuteBuilder;

    protected $builder = true;

    /**
     * @var \PDO
     */
    protected $connect = null;

    /**
     * Insert constructor.
     * @param array $config
     * @param string $table
     */
    public function __construct(array $config, string $table, \PDO $connect = null)
    {
        $this->connect = $connect;
        parent::__construct($config, $table, self::DELETE);
    }
}