<?php
/**
 * Date: 2021/5/8 16:31
 * User: YHC
 * Desc:
 */

namespace Library\Core\Database\Builder;

trait CollectorBuilder
{
    /**
     * ------------------------------------------------------------
     *  写入操作
     * ------------------------------------------------------------
     *
     * $data 键 => 值; 键为数据库字段，值为字段的值
     *
     * @param array $data
     * @return $this
     */
    public function builder(array $data)
    {
        if (empty($data)) {
            throw new \RuntimeException('DATA SERVER ERROR builder() not allowed to be empty', HORSE_LOFT_DATABASE_ERROR_CODE);
        }
        $builder = $this->insert($data);

        $this->insertSql = $builder['sql'];

        $this->andParam = $builder['param'];

        $this->builder = true;

        return $this;
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
     * @return $this
     */
    public function collector(array $data)
    {
        if (empty($data)) {
            throw new \RuntimeException('collector() not allowed to be empty', HORSE_LOFT_DATABASE_ERROR_CODE);
        }

        foreach ($data as $key => $value) {
            if (empty($value)) {
                throw new \RuntimeException('collector() data not allowed to be empty', HORSE_LOFT_DATABASE_ERROR_CODE);
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

        $this->builder = true;

        return $this;
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