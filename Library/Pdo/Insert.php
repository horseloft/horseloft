<?php
/**
 * Date: 2021/5/7 10:58
 * User: YHC
 * Desc: 必须调用execute()才会执行insert
 */

namespace Library\Pdo;

use Library\Core\Database\Factory\Origin;
use Library\Core\Database\Builder\ExecuteBuilder;

class Insert extends Origin
{
    use ExecuteBuilder;

    /**
     * @var \PDO
     */
    protected $connect = null;

    /**
     * Insert constructor.
     * @param array $config
     * @param string $table
     */
    public function __construct(array $config, string $table, array $data, \PDO $connect = null)
    {
        $this->connect = $connect;
        parent::__construct($config, $table, self::INSERT);
        $this->analyze($data);
    }

    /**
     * 如果$data是二维数组 使用批量写入
     *
     * @param array $data
     */
    private function analyze(array $data)
    {
        if (empty($data)) {
            throw new \RuntimeException('data not allowed to be empty', HORSE_LOFT_DATABASE_ERROR_CODE);
        }
        if (is_numeric(array_keys($data)[0]) && is_array($data[array_keys($data)[0]])) {
            //如果数组的第一个下标是数字，判定参数是二维数组
            $this->collector($data);
        } else {
            $this->builder($data);
        }
    }

    /**
     * ------------------------------------------------------------
     *  写入操作
     * ------------------------------------------------------------
     *
     * $data 键 => 值; 键为数据库字段，值为字段的值
     *
     * @param array $data
     */
    private function builder(array $data)
    {
        $builder = $this->insert($data);

        $this->insertSql = $builder['sql'];

        $this->andParam = $builder['param'];
    }

    /**
     * --------------------------------------------------------------
     *  写入多条数据
     * --------------------------------------------------------------
     *
     * $data 是二维数组:
     * [
     *      [
     *          键 => 值 //键为数据库字段，值为字段的值
     *      ]
     * ]
     *
     * @param array $data
     */
    private function collector(array $data)
    {
        foreach ($data as $key => $value) {
            if (empty($value)) {
                throw new \RuntimeException(' data not allowed to be empty', HORSE_LOFT_DATABASE_ERROR_CODE);
            }

            if ($key == 0) {
                $isNeedColumn = true;
            } else {
                $isNeedColumn = false;
            }
            $builder = $this->insert($value, $isNeedColumn);

            if ($this->insertSql == '') {
                $this->insertSql = $builder['sql'];
            } else {
                $this->insertSql .= ',' . $builder['sql'];
            }
            $this->andParam = array_merge($this->andParam, $builder['param']);
        }
    }

    /**
     * insert
     *
     * @param array $insert
     * @return array
     */
    private function insert(array $insert, bool $isNeedColumn = true)
    {
        if ($isNeedColumn) {
            $columns = ' values (';
            $fields = ' (';
        } else {
            $columns = '(';
        }

        $params = [];
        foreach ($insert as $key => $value) {
            if (!is_string($key)) {
                throw new \RuntimeException('Unsupported column:' . $key, HORSE_LOFT_DATABASE_ERROR_CODE);
            }

            if (is_string($value) || is_numeric($value) || is_null($value)) {
                $columns .= '?,';
                $params[] = $value;
            } else {
                throw new \RuntimeException('Unsupported column:' . $key, HORSE_LOFT_DATABASE_ERROR_CODE);
            }

            if ($isNeedColumn) {
                $fields .= $this->packageColumn($key) . ',';
            }
        }

        if ($isNeedColumn) {
            $string = rtrim($fields, ',') . ')' . rtrim($columns, ',') . ')';
        } else {
            $string = rtrim($columns, ',') . ')';
        }

        return [
            'sql' => $string,
            'param' => $params
        ];
    }
}
