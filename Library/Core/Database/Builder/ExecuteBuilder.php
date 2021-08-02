<?php
/**
 * Date: 2021/5/8 16:07
 * User: YHC
 * Desc:
 */

namespace Library\Core\Database\Builder;

trait ExecuteBuilder
{
    /**
     * --------------------------------------------------------
     * 执行删除操作
     * --------------------------------------------------------
     *
     * 成功返回受影响的行 失败返回false
     *
     * 如果未删除 则返回0
     *
     * 使用 === false 判断失败
     *
     * @return int
     */
    public function execute()
    {
        if (is_null($this->connect)) {
            $this->connect = $this->connect();
        }
        $statement = $this->statement($this->getQuery(), $this->getParam());

        if ($this->header == self::INSERT) {
            return $this->connect->lastInsertId();
        }
        return $statement->rowCount();
    }
}