<?php
/**
 * Date: 2021/5/7 13:08
 * User: YHC
 * Desc:
 */

namespace Library\Core\Database\Factory;

use Library\Core\Database\Handle\ConnectHandle;
use Library\Core\Database\Handle\StatementHandle;

class Origin
{
    use ConnectHandle,StatementHandle;

    /**
     * @var \PDO
     */
    protected $connect = null;

    //数据库连接配置
    protected $config = [];

    //操作的数据表名称
    protected $table = '';

    //查询字段 字符串
    protected $column = '*';

    //join语句
    protected $joinSql = '';

    //where查询语句
    protected $andSql = '';

    //or查询语句
    protected $orSql = '';

    //原生SQL语句
    protected $rawSql = '';

    //insert语句
    protected $insertSql = '';

    //set语句
    protected $setSql = '';

    //group by
    protected $groupSql = '';

    //having
    protected $havingSql = '';

    //order by
    protected $orderSql = '';

    //limit语句；格式：limit n,m;
    protected $limitSql = '';

    //where查询语句的参数
    protected $andParam = [];

    //where or 查询语句的参数
    protected $orParam = [];

    //set参数
    protected $setParam = [];

    //操作语句的头：select insert update delete
    protected $header = '';

    //查询计时时间戳
    protected $start = 0;

    protected const UPDATE = 'UPDATE';

    protected const SELECT = 'SELECT';

    protected const DELETE = 'DELETE';

    protected const INSERT = 'INSERT';

    /**
     * Mysql constructor.
     * @param array $config
     * @param string $table
     * @param string $type
     */
    public function __construct(array $config, string $table, string $type)
    {
        $this->config = $config;

        $this->table = $table;

        $this->header = $type;
    }

    /**
     * -----------------------------------------------------------
     * 设置数据表名称
     * -----------------------------------------------------------
     *
     * @param string $table
     * @return $this
     */
    public function table(string $table)
    {
        $this->table = $table;

        return $this;
    }

    /**
     * 带参数的SQL语句
     *
     * @return string
     */
    public function getCompleteQuery()
    {
        $param = $this->getParam();
        if (empty($param)) {
            return $this->getQuery();
        } else {
            $string = $this->getQuery();
            foreach ($param as $value) {
                $start = substr($string, 0, strpos($string, '?') + 1);
                $string = str_replace('?', "'" . $value . "'", $start) . substr($string, strpos($string, '?') + 1);
            }
            return $string;
        }
    }

    /**
     * SQL语句
     * @return string
     */
    public function getQuery()
    {
        switch($this->header) {
            case self::SELECT:
                $string = $this->header . ' ' . $this->column . ' from `' . $this->table . '`' . $this->joinSql;
                break;
            case self::UPDATE:
                $string = $this->header . ' `' . $this->table . '`' . $this->setSql;
                break;
            case self::DELETE:
                $string = $this->header . ' from `' . $this->table . '`';
                break;
            case self::INSERT:
                $string = $this->header . ' into `' . $this->table . '` ' .$this->insertSql;
                break;
            default:
                throw new \RuntimeException('Unsupported operation:' . $this->header, HORSE_LOFT_DATABASE_ERROR_CODE);
        }

        //非insert操作 会用到where、limit、group by等操作
        if ($this->header != self::INSERT) {
            //where条件
            if ($this->andSql != '') {
                $string .= ' where ' . $this->andSql;
            }

            //where or 条件
            if ($this->orSql != '') {
                //如果没有and条件仅有or条件 则将or条件转为and条件
                if ($this->andSql == '') {
                    $string .= ' where ' . ltrim($this->orSql, ' or ');
                } else {
                    $string .= $this->orSql;
                }
            }

            //where raw
            if ($this->rawSql != '') {
                if ($this->andSql == '' && $this->orSql == '') {
                    $string .= ' where ' . $this->rawSql;
                } else {
                    $string .= $this->rawSql;
                }
            }

            //group by
            if ($this->groupSql != '') {
                $string .= $this->groupSql;
            }

            //having
            if ($this->havingSql != '') {
                $string .= $this->havingSql;
            }

            //order by
            if ($this->orderSql != '') {
                $string .= $this->orderSql;
            }

            // limit
            $string .= $this->limitSql;
        }

        return $string;
    }

    /**
     * SQL语句的参数
     *
     * @return array
     */
    public function getParam()
    {
        return array_merge($this->setParam, $this->andParam, $this->orParam);
    }
}
