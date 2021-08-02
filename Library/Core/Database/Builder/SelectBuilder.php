<?php
/**
 * Date: 2021/5/8 15:52
 * User: YHC
 * Desc: 必须调用first()/all()/count()才会执行select
 */

namespace Library\Core\Database\Builder;

trait SelectBuilder
{
    /**
     *
     * @param string $table
     * @param string $on
     * @return $this
     */
    public function join(string $table, string $on = '')
    {
        $this->setJoinSql('cross', $table, $on);
        return $this;
    }

    /**
     *
     * @param string $table
     * @param string $on
     * @return $this
     */
    public function leftJoin(string $table, string $on = '')
    {
        $this->setJoinSql('left', $table, $on);
        return $this;
    }

    /**
     *
     * @param string $table
     * @param string $on
     * @return $this
     */
    public function rightJoin(string $table, string $on = '')
    {
        $this->setJoinSql('right', $table, $on);
        return $this;
    }

    /**
     *
     * @param string $table
     * @param string $on
     * @return $this
     */
    public function innerJoin(string $table, string $on = '')
    {
        $this->setJoinSql('inner', $table, $on);
        return $this;
    }

    /**
     * 查询字段
     *
     * @param string $column
     * @return $this
     */
    public function column(string $column = '*')
    {
        if (empty($column)) {
            $this->column = '*';
        } else {
            $this->column = $this->packageSelectColumn($column);
        }
        return $this;
    }

    /**
     * 查询一条
     *
     * limit优先级 first() > page() > limit()
     *
     * @return array
     */
    public function first()
    {
        $this->limitSql = ' limit 1';
        return $this->fetch(false);
    }

    /**
     * 查询全部
     *
     * @return array
     */
    public function all()
    {
        return $this->fetch(true);
    }

    /**
     * count()统计查询
     *
     * @return int
     */
    public function count()
    {
        $this->limitSql = ' limit 1';

        $this->column = 'count(1) as num';

        $data = $this->fetch(false);

        if (empty($data)) {
            return 0;
        } else {
            return (int)$data['num'];
        }
    }

    /**
     * --------------------------------------------------------------------------
     * 一组分页数据
     * --------------------------------------------------------------------------
     *
     * limit优先级 first() > page() > limit()
     *
     * @param int $page
     * @param int $pageSize
     * @return $this
     */
    public function page(int $page = 1, int $pageSize = 20)
    {
        $start = 0;
        $offset = 1;
        if ($pageSize > 0) {
            $offset = $pageSize;
        }
        if ($page > 1) {
            $start = ($page - 1) * $offset;
        }
        $this->limitSql = ' limit ' . $start . ',' . $offset;

        return $this;
    }

    /**
     * 数据查询
     * @param bool $isFetchAll
     * @return array
     */
    private function fetch(bool $isFetchAll)
    {
        if (is_null($this->connect)) {
            $this->connect = $this->connect();
        }

        $statement = $this->statement($this->getQuery(), $this->getParam());

        return $this->fetchBuilder($statement, $isFetchAll);
    }

    /**
     *
     * @param string $type
     * @param string $table
     * @param string $on
     */
    private function setJoinSql(string $type, string $table, string $on)
    {
        $this->joinSql = ' ' . $type . ' join ' . $this->packageColumn($table) . ' ';
        if ($on != '') {
            $this->joinSql .= 'on ' . $on . ' ';
        }
    }
}