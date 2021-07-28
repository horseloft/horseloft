<?php
/**
 * Date: 2021/5/22 01:55
 * User: YHC
 * Desc:
 */

namespace Library\Core\Database\Handle;

trait StatementHandle
{
    /**
     * SQL预处理 SQL参数绑定
     *
     * @param string $sql
     * @param array $param
     * @return false|\PDOStatement
     */
    protected function statement(string $sql, array $param = [])
    {
        try {
            //SQL预处理
            $stmt = $this->connect->prepare($sql);
            if ($stmt == false) {
                throw new \RuntimeException($stmt->errorInfo()[2]);
            }

            //绑定参数
            $inc = 1;
            foreach ($param as $value) {
                if ($stmt->bindValue($inc, $value) == false) {
                    throw new \RuntimeException($stmt->errorInfo()[2]);
                }
                $inc++;
            }

            //执行动作
            if ($stmt->execute() == false) {
                throw new \RuntimeException($stmt->errorInfo()[2]);
            }

            //返回 \PDOStatement
            return $stmt;

        } catch (\Exception $e) {
            if (HORSE_LOFT_PDO_ERROR_DETAIL) {
                $message = $e->getMessage();
            } else {
                $message = Builder::$warning;
            }
            throw new \RuntimeException($message, HORSE_LOFT_DATABASE_ERROR_CODE);
        }
    }

    /**
     * PDO fetch查询
     *
     * @param \PDOStatement $statement
     * @param bool $isFetchAll
     * @return array
     */
    protected function fetchBuilder(\PDOStatement $statement, bool $isFetchAll = true)
    {
        if ($isFetchAll) {
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
        } else {
            $result = $statement->fetch(\PDO::FETCH_ASSOC);
        }
        if ($result == false) {
            return [];
        }
        return $result;
    }
}