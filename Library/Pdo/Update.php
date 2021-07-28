<?php
/**
 * Date: 2021/5/7 15:10
 * User: YHC
 * Desc: 必须调用execute()才会执行update
 */

namespace Library\Pdo;

use Library\Core\Database\Factory\Origin;
use Library\Core\Database\Builder\ConditionBuilder;
use Library\Core\Database\Builder\SetBuilder;
use Library\Core\Database\Builder\ExecuteBuilder;

class Update extends Origin
{
    use SetBuilder,ConditionBuilder,ExecuteBuilder;

    protected $builder = false;

    /**
     * @var \PDO
     */
    protected $connect = null;

    /**
     * Update constructor.
     * @param array $config
     * @param string $table
     */
    public function __construct(array $config, string $table, \PDO $connect = null)
    {
        $this->connect = $connect;
        parent::__construct($config, $table, self::UPDATE);
    }
}