<?php
/**
 * Date: 2021/5/6 16:43
 * User: YHC
 * Desc: 必须调用first()/all()/count()才会执行select
 */

namespace Library\Pdo;

use Library\Core\Database\Factory\Origin;
use Library\Core\Database\Builder\ConditionBuilder;
use Library\Core\Database\Builder\SelectBuilder;

class Select extends Origin
{
    use ConditionBuilder,SelectBuilder;

    protected $builder = true;

    /**
     * @var \PDO
     */
    protected $connect = null;

    /**
     * Select constructor.
     * @param array $config
     * @param string $table
     */
    public function __construct(array $config, string $table, string $column, \PDO $connect = null)
    {
        $this->connect = $connect;

        parent::__construct($config, $table, self::SELECT);

        $this->column($column);
    }
}
