<?php
/**
 * Date: 2021/5/8 18:06
 * User: YHC
 * Desc: 事务
 */

namespace Library\Core\Database\Builder;

trait TransactionBuilder
{
    /**
     * -----------------------------------------------------------
     * 事务开启
     * -----------------------------------------------------------
     *
     * 使用select/update/delete/insert后调用当前方法开启事务
     *
     * 事务过程中使用table()切换数据表名称
     *
     */
    public function begin()
    {
        if (is_null($this->connect)) {
            $this->connect = $this->connect();
        }

        if ($this->connect->beginTransaction() == false) {
            throw new \RuntimeException('Transaction begin false', HORSE_LOFT_DATABASE_ERROR_CODE);
        }
    }

    /**
     * -----------------------------------------------------------
     * 事务提交
     * -----------------------------------------------------------
     *
     */
    public function commit()
    {
        if ($this->connect->commit() == false) {
            throw new \RuntimeException('Transaction commit false', HORSE_LOFT_DATABASE_ERROR_CODE);
        }
    }

    /**
     * -----------------------------------------------------------
     * 事务回滚
     * -----------------------------------------------------------
     *
     */
    public function rollBack()
    {
        if ($this->connect->rollback() == false) {
            throw new \RuntimeException('Transaction rollback false', HORSE_LOFT_DATABASE_ERROR_CODE);
        }
    }
}
