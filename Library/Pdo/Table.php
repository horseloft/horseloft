<?php
/**
 * Date: 2021/5/6 18:32
 * User: YHC
 * Desc:
 *    1: 允许$config/$table使用空值，如果为空 则属性$table/$config需要被赋值
 *       如果继承了当前类，则允许在子类中声明静态属性$table和$config并赋值
 *       $table: 数据表名称
 *       $config：数据库连接配置
 *    2:
 *       允许的driver: mysql/dblib
 *       sqlserver使用dblib
 *    3:
 *       driver = dblib 仅支持pdo()方法
 *    4:
 *       事务功能 需使用 new Table();
 *       例: $table = new Table($config, 'customer'); 此时数据库配置信息不应为空，数据表名称可以为空
 *           $table->began(); 开启事务
 *           $table::select()->first(); 此时数据库配置信息应为空
 *           $table::update()->table('user')->set(['name' => 'Tom'])->execute(); 此时数据库配置应为空
 */

namespace Library\Pdo;

use Library\Core\Database\Handle\ConnectionHandle;
use Library\Core\Database\Factory\PDO;

class Table
{
    use ConnectionHandle;

    /*
     * ----------------------------------------------------------------------------
     * 数据表名称
     * ----------------------------------------------------------------------------
     *
     * 如果继承了当前类，则允许在子类中声明静态属性 $table 并赋值
     *
     */
    public static $table = '';

    /*
     * ----------------------------------------------------------------------------
     * 数据库配置|字符串或数组
     * ----------------------------------------------------------------------------
     *
     *【1】$connection是数组，那么必须是一维数组，并且内容为：
     * $connection = [
     *      'driver' => 'mysql',        //必填项 dblib/mysql
     *      'host' => '127.0.0.1',      //必填项 database host
     *      'port' => 3306,             //必填项 database port
     *      'username' => 'username',   //必填项 database username
     *      'password' => 'password',   //必填项 database password
     *      'database' => 'database'    //必填项 database name
     *      'charset' => 'utf8'         //选填项 database charset
     * ]
     *
     *【2】$connection是字符串
     * 例：$connection = 'mysql';
     *
     *    //必须在启动文件中声明并加载了database配置文件
     *    $horseLoft->setConfigure(['database']);
     *
     *    此时 /Config/ 或 /Services/xxxService/Config/ 目录下应该有 database.php 文件
     *
     *    database.php文件内容应有索引为 mysql 的配置
     *
     *    return [
     *      'mysql' => [
     *          'driver' => 'mysql',        //必填项 dblib/mysql
     *          'host' => '127.0.0.1',      //必填项 database host
     *          'port' => 3306,             //必填项 database port
     *          'username' => 'username',   //必填项 database username
     *          'password' => 'password',   //必填项 database password
     *          'database' => 'database'    //必填项 database name
     *          'charset' => 'utf8'         //选填项 database charset
     *      ]
     *    ];
     */
    public static $connection;

    /**
     *
     * @param array $config
     * @param string $table
     * @return Transaction
     */
    public static function transaction()
    {
        return new Transaction(self::connection(static::$connection), static::$table);
    }

    /**
     * ------------------------------------------------------------
     * select 操作
     * ------------------------------------------------------------
     *
     * $column 要查询的字段
     *
     * @param string $column
     * @return Select
     */
    public static function select(string $column = '*')
    {
       $select = new Select(self::connection(static::$connection), static::$table);

       $select->column($column);

       return $select;
    }

    /**
     * ------------------------------------------------------------
     * insert 操作
     * ------------------------------------------------------------
     *
     * $data 键 => 值; 键为数据库字段，值为字段的值
     *
     * @param array $data
     * @return Insert
     */
    public static function insert(array $data)
    {
        return new Insert(self::connection(static::$connection), static::$table, $data);
    }

    /**
     * ------------------------------------------------------------
     * update 操作
     * ------------------------------------------------------------
     *
     * $data是 键=>值 格式的一维数组；键对应数据表的字段名称，值即为字段值
     *
     * @param array $data
     * @return Update
     */
    public static function update(array $data)
    {
        return new Update(self::connection(static::$connection), static::$table, $data);
    }

    /**
     * ------------------------------------------------------------
     * delete 操作
     * ------------------------------------------------------------
     *
     * @return Delete
     */
    public static function delete()
    {
        return new Delete(self::connection(static::$connection), static::$table);
    }

    /**
     * 用于执行原生SQL语句
     *
     * $table 是兼容设置 使用默认值即可
     *
     * @param array $config
     * @param string $table
     * @return PDO
     */
    public static function pdo()
    {
        return new PDO(self::connection(static::$connection));
    }
}
