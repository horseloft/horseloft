<?php
/**
 * Date: 2021/5/8 15:49
 * User: YHC
 * Desc: 必须调用execute()才会执行update
 */

namespace Library\Core\Database\Builder;

trait SetBuilder
{
    /**
     * --------------------------------------------------------
     * set操作
     * --------------------------------------------------------
     *
     * $data是 键=>值 格式的一维数组；键对应数据表的字段名称，值即为字段值
     *
     * @param array $data
     * @return $this
     */
    public function set(array $data)
    {
        $this->update($data);

        $this->builder = true;

        return $this;
    }

    /**
     * update set
     *
     * @param array $data
     */
    private function update(array $data)
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