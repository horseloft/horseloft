<?php
/**
 * Date: 2021/5/8 16:37
 * User: YHC
 * Desc:
 */

namespace Library\Core\Database\Builder;

trait ConditionBuilder
{
    /**
     * --------------------------------------------------------------------------
     * sql语句的where条件
     * --------------------------------------------------------------------------
     *
     * 支持一次查询中调用多次该方法，会自动拼接多次调用生成的where条件
     *
     * 格式：键 => 值
     * 例：
     *  ['name' => 'jack']                          //and name = 'jack'
     *  ['name' => 'eq' => 'jack']                  //and name = 'jack'
     *  ['name' => 'not_eq' => 'jack']              //and name != 'jack'
     *
     *  ['age' => ['gt' => 12]]                     //and age > 12
     *  ['age' => ['gte' => 12]]                    //and age >= 12
     *
     *  ['name' => ['like' => '%jack$']]            //and name like '%jack%'
     *
     *  ['age' => ['lt' => 12]]                     //and age < 12
     *  ['age' => ['lte' => 12]]                    //and age <= 12
     *
     *  ['age' => [between => [10, 20]]]            //and age between 10 and 20
     *  ['age' => [not_between => [10, 20]]]        //and age not between 10 and 20
     *
     *  ['home' => ['in' => ['china', 'us']]]       //and home in ('china', 'us')
     *  ['home' => ['not_in' => ['china', 'us']]]   //and home not in ('china', 'us')
     *
     * @param array $where
     * @return $this
     */
    public function where(array $where = [])
    {
        if (empty($where)) {
            $this->andParam = [];
            return $this;
        }

        $builder = $this->whereBuilder($where);

        if ($this->andSql == '') {
            $this->andSql = '(' . $builder['sql'] . ')';
        } else {
            $this->andSql = rtrim($this->andSql, ')') . ' AND ' . $builder['sql'] . ')';
        }

        $this->andParam = array_merge($this->andParam, $builder['param']);

        return $this;
    }

    /**
     * --------------------------------------------------------------------------
     * or 查询条件
     * --------------------------------------------------------------------------
     *
     * 如果生成器中没有使用where()方法 则whereOr()功能与where()方法一样
     *
     * 语法参考 where() 方法
     *
     * 生成的SQL语句：or (xxx = xxx and xxx = xxx and xxx like xxx)
     *
     * @param array $where
     * @return $this
     */
    public function whereOr(array $where = [])
    {
        if (empty($where)) {
            $this->orParam = [];
            return $this;
        }

        $builder = $this->whereBuilder($where);

        if ($this->orSql == '') {
            $this->orSql = ' OR (' . $builder['sql'] . ')';
        } else {
            $this->orSql = $this->orSql . ' OR (' . $builder['sql'] . ')';
        }

        $this->orParam = array_merge($this->orParam, $builder['param']);

        return $this;
    }

    /**
     * --------------------------------------------------------------------------
     *  原生的where SQL语句
     * --------------------------------------------------------------------------
     *
     * $str中无需写明where
     *
     * @param string $str
     * @return $this
     */
    public function whereRaw(string $str = '')
    {
        if ($str == '') {
            return $this;
        }

        $this->rawSql = ' ' . trim($str);

        return $this;
    }

    /**
     * --------------------------------------------------------------------------
     * group by 语句
     * --------------------------------------------------------------------------
     *
     * $string 会自动追加 group by
     *
     * 例: parent_id
     *
     * @param string $string
     * @return $this
     */
    public function group(string $string = '')
    {
        if (empty($string)) {
            $this->groupSql = '';
        } else {
            $this->groupSql = ' GROUP BY ' . $string;
        }
        return $this;
    }

    /**
     *
     * @param string $string
     * @return $this
     */
    public function having(string $string = '')
    {
        if (empty($string)) {
            $this->havingSql = '';
        } else {
            $this->havingSql = ' HAVING ' . $string;
        }
        return $this;
    }

    /**
     * --------------------------------------------------------------------------
     * order by 语句
     * --------------------------------------------------------------------------
     *
     * $string 会自动追加 order by
     *
     * 例：id desc,username asc
     *
     * @param string $string
     * @return $this
     */
    public function order(string $string = '')
    {
        if (empty($string)) {
            $this->orderSql = '';
        } else {
            $this->orderSql = ' ORDER BY ' . $string;
        }
        return $this;
    }

    /**
     * --------------------------------------------------------------------------
     * order by 语句
     * --------------------------------------------------------------------------
     *
     * $string 会自动追加 order by desc
     *
     * @param string $string
     * @return $this
     */
    public function orderDesc(string $string = '')
    {
        if (empty($string)) {
            $this->orderSql = '';
        } else {
            $this->orderSql = ' ORDER BY ' . $string . ' DESC';
        }
        return $this;
    }

    /**
     * --------------------------------------------------------------------------
     * order by 语句
     * --------------------------------------------------------------------------
     *
     * $string 会自动追加 order by asc
     *
     * @param string $string
     * @return $this
     */
    public function orderAsc(string $string = '')
    {
        if (empty($string)) {
            $this->orderSql = '';
        } else {
            $this->orderSql = ' ORDER BY ' . $string . ' ASC';
        }
        return $this;
    }

    /**
     * --------------------------------------------------------------------------
     * limit 语句
     * --------------------------------------------------------------------------
     *
     * $limit 应 >= 0 | 如果 $limit = null 则置空SQL语句中的limit条件
     * $offset 应 > 0 | 如果 $offset= null 则仅查询$limit条数据
     *
     * limit语句优先级 first() > page() > limit()
     *
     * @param int $limit
     * @param int $offset
     * @return $this
     */
    public function limit(int $limit = null, int $offset = null)
    {
        if ($limit === null) {
            $this->limitSql = '';
            return $this;
        }

        if ($limit < 0) {
            throw new \RuntimeException('limit args error', HORSE_LOFT_DATABASE_ERROR_CODE);
        }

        if ($offset !== null && $offset <= 0) {
            throw new \RuntimeException('limit offset error', HORSE_LOFT_DATABASE_ERROR_CODE);
        }

        $string = ' LIMIT ' . $limit;

        if ($offset !== null && $offset > 0) {
            $string .= ',' . $offset;
        }

        $this->limitSql = $string;

        return $this;
    }

    /**
     * where条件拼接
     * @param array $where
     * @return array
     */
    private function whereBuilder(array $where)
    {
        //前一步验证 empty $where
        $arr = [];
        $str = '';

        foreach ($where as $key => $value) {
            //矫正符号
            if (strpos($key, '.')) {
                $redress = '';
            } else {
                $redress = '`';
            }

            if (is_array($value)) {
                if (key($value) == 'string') {
                    if (!is_string(end($value))) {
                        throw new \RuntimeException('Unsupported column value', HORSE_LOFT_DATABASE_ERROR_CODE);
                    }
                    $str .= $redress . $key . $redress . ' ' . end($value) . ' and ';
                } else {
                    $convert = $this->convert($value);
                    $str .= $redress . $key . $redress . $convert['sign'] . ' and ';
                    if (is_array($convert['value'])) {
                        foreach ($convert['value'] as $k => $val) {
                            $arr[] = $val;
                        }
                    } else {
                        $arr[] = $convert['value'];
                    }
                }
            } else {
                $arr[] = $value;
                $str .= $redress . $key . $redress . ' = ? and ';
            }
        }
        return [
            'sql' => rtrim($str, 'and '),
            'param' => $arr
        ];
    }

    /**
     * 实体符号转SQL符号
     * @param array $condition
     * @return array
     */
    private function convert(array $condition)
    {
        $content = current($condition);
        if ($content === false) {
            throw new \RuntimeException('Unsupported query method', HORSE_LOFT_DATABASE_ERROR_CODE);
        }
        if (is_array($content) && count($content) == 0) {
            throw new \RuntimeException('Unsupported query method', HORSE_LOFT_DATABASE_ERROR_CODE);
        }

        switch (key($condition)) {
            case 'eq':
                $result = [
                    'sign' => ' = ?',
                    'value' => $content
                ];
                break;
            case 'not_eq':
                $result = [
                    'sign' => ' <> ?',
                    'value' => $content
                ];
                break;
            case 'gt':
                $result = [
                    'sign' => ' > ?',
                    'value' => $content
                ];
                break;
            case 'gte':
                $result = [
                    'sign' => ' >= ?',
                    'value' => $content
                ];
                break;
            case 'lt':
                $result = [
                    'sign' => ' < ?',
                    'value' => $content
                ];
                break;
            case 'lte':
                $result = [
                    'sign' => ' <= ?',
                    'value' => $content
                ];
                break;
            case 'like':
                $result = [
                    'sign' => ' like ?',
                    'value' => $content
                ];
                break;
            case 'between':
                $result = [
                    'sign' => ' between ? and ?',
                    'value' => [
                        current($condition['between']),
                        end($condition['between'])
                    ]
                ];
                break;
            case 'not_between':
                $result = [
                    'sign' => ' not between ? and ?',
                    'value' => [
                        current($condition['not_between']),
                        end($condition['not_between'])
                    ]
                ];
                break;
            case 'in':
                $str = ' in (';
                $inCount = count($condition['in']);
                for ($j = 0; $j < $inCount; $j++) {
                    $str .= '?,';
                }
                $result = [
                    'sign' => rtrim($str, ',') . ')',
                    'value' => $condition['in']
                ];
                break;
            case 'not_in':
                $str = ' not in (';
                $inCount = count($condition['not_in']);
                for ($j = 0; $j < $inCount; $j++) {
                    $str .= '?,';
                }
                $result = [
                    'sign' => rtrim($str, ',') . ')',
                    'value' => $condition['not_in']
                ];
                break;
            default:
                throw new \RuntimeException('Unsupported query method : ' . key($condition), HORSE_LOFT_DATABASE_ERROR_CODE);
        }
        return $result;
    }
}