<?php
/**
 * Date: 2021/5/7 10:58
 * User: YHC
 * Desc: 必须调用execute()才会执行insert
 */

namespace Library\Pdo;

use Library\Core\Database\Factory\Origin;
use Library\Core\Database\Builder\CollectorBuilder;
use Library\Core\Database\Builder\ExecuteBuilder;

class Insert extends Origin
{
    use ExecuteBuilder,CollectorBuilder;

    protected $builder = false;

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
        parent::__construct($config, $table, self::INSERT);
    }
}
