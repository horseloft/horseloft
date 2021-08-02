<?php
/**
 * Date: 2021/5/7 15:10
 * User: YHC
 * Desc: 必须调用execute()才会执行update
 */

namespace Library\Pdo;

use Library\Core\Database\Factory\Origin;
use Library\Core\Database\Builder\ConditionBuilder;
use Library\Core\Database\Builder\ExecuteBuilder;

class Update extends Origin
{
    use ConditionBuilder,ExecuteBuilder;

    /**
     * @var \PDO
     */
    protected $connect = null;

    /**
     * Update constructor.
     * @param array $config
     * @param array $data
     * @param string $table
     */
    public function __construct(array $config, string $table, array $data, \PDO $connect = null)
    {
        $this->connect = $connect;
        parent::__construct($config, $table, self::UPDATE);
        $this->set($data);
    }

    /**
     * update set
     *
     * @param array $data
     */
    private function set(array $data)
    {
        if (empty($data)) {
            throw new \RuntimeException('Empty update', HORSE_LOFT_DATABASE_ERROR_CODE);
        }
        $arr = [];
        $str = ' set ';
        foreach ($data as $key => $value) {
            if (is_string($key)) {
                $str .= $this->packageColumn($key) . ' = ?,';
            } else {
                throw new \RuntimeException('Unsupported column:' . $key, HORSE_LOFT_DATABASE_ERROR_CODE);
            }
            if (is_string($value) || is_numeric($value) || is_null($value)) {
                $arr[] = $value;
            } else {
                throw new \RuntimeException('Unsupported column:' . $key, HORSE_LOFT_DATABASE_ERROR_CODE);
            }
        }

        $this->setSql = rtrim($str, ',');
        $this->setParam = $arr;
    }
}