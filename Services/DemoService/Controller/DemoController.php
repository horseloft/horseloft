<?php
/**
 * Date: 2021/4/25 16:57
 * User: YHC
 * Desc: demo
 */

namespace Services\DemoService\Controller;

use Library\Utils\Helper;
use Library\Utils\Http;
use Services\DemoService\Models\People;
use Services\DemoService\Models\Teacher;

class DemoController
{
    /**
     * @return string
     */
    public static function index()
    {
        return 'Hello Horseloft';
    }

    /**
     * --------------------------------------------------------
     *  不带参数的请求 返回一个字符串 客户端接收到一个字符串
     * --------------------------------------------------------
     *
     * 例：http://localhost:8080/demo/empty
     *
     * @return string
     */
    public static function empty()
    {
        return 'empty';
    }

    /**
     * --------------------------------------------------------
     *  一个int参数 返回一个int值 客户端接收到一个int
     * --------------------------------------------------------
     *
     * 例：http://localhost:8080/demo/oneIntArgs?age=1
     *
     * @param int $age
     * @return int
     */
    public static function oneIntArgs(int $age)
    {
        return $age;
    }

    /**
     * --------------------------------------------------------
     *  一个int参数和一个字符串参数 返回一个数组 客户端收到一个json
     * --------------------------------------------------------
     *
     * 例：http://localhost:8080/demo/twoArgs?age=1&name=Tom
     *
     * @param int $age
     * @param string $name
     * @return array
     */
    public static function twoArgs(int $age, string $name)
    {
        return [
            'age' => $age,
            'name' => $name
        ];
    }

    /**
     * --------------------------------------------------------
     *  一个数组参数 返回一个数组 客户端收到一个json
     * --------------------------------------------------------
     *
     * 例：http://localhost:8080/demo/twoArgs?age=1&name=Tom
     *
     * @param array $param
     * @return array
     */
    public static function oneArrayArgs(array $param)
    {
        return $param;
    }

    /**
     * --------------------------------------------------------
     *  两个数组参数 返回一个数组 客户端收到一个json
     * --------------------------------------------------------
     *
     * 例：
     *  url: http://localhost:8080/demo/twoArrayArgs
     *  method: post
     *  header: 'Content-Type':'application/json'
     *  args: {"param":["user","age"],"args":[1,2,3]}
     *
     * @param array $param
     * @return array
     */
    public static function twoArrayArgs(array $param, array $args)
    {
        return [
            'param' => $param,
            'args' => $args
        ];
    }

    public static function pdo()
    {
        return People::pdo()->fetch('select * from people where id > ?', 0);
    }

    /**
     * --------------------------------------------------------
     *  获取数据表中的第一条数据 一维数组
     * --------------------------------------------------------
     *
     * @return array
     */
    public static function first()
    {
        //普通查询
        return Teacher::select()->first();

        //使用连接池查询
        //return People::select()->first();
    }

    /**
     * --------------------------------------------------------
     *  获取全部数据 二维数组
     * --------------------------------------------------------
     *
     * @return array
     */
    public static function all()
    {
        //普通查询 查询 id > 2 的数据
        $condition = [
            'id' => ['gt' => 0],
            'age' => ['or' => [1,2,3]]
        ];
        return Teacher::select()->where($condition)->getCompleteQuery();

        //使用连接池查询
        //return People::select()->where(['id' => ['gt' => 1]])->all();
    }

    /**
     *
     * @return false|string
     */
    public static function insert()
    {
        $data = [
            ['id' => 1,'username' => 'new_teacher_1', 'password' => 'password'],
            ['id' => 2,'username' => 'new_teacher_2', 'password' => 'password'],
            ['id' => 3,'username' => 'new_teacher_3', 'password' => 'password'],
            ['id' => 4,'username' => 'new_teacher_4', 'password' => 'password'],
        ];
        //普通操作
        return Teacher::insert()->collector($data)->execute();

        //使用连接池查询
//        return People::insert()->collector($data)->execute();
    }

    /**
     * --------------------------------------------------------
     *  事务
     * --------------------------------------------------------
     *
     * @return bool
     */
    public static function transaction()
    {
        //使用 People::transaction() 可以使用连接池开启事务
        //使用Teacher开启事务 默认操作的数据表名称为当前类设置的$table
        $table = Teacher::transaction();
        try {
            $table->begin();

            //删除一条teacher表的记录
            $table->delete()->where(['id' => 3])->execute();

            /*
             * 更新一条people表的记录
             * 使用table()方法切换了操作的数据表
             * 切换后如果再次此使用table()方法切换 则以后操作的数据表都是当前设置的数据表
             */
            $table->update(['username' => 'people'])->table('people')->where(['id' => 3])->execute();

            //查询一个people表记录
            $peopleData = $table->select()->first();
            print_r($peopleData);

            /*
             * 更新一条teacher表的记录
             * 在之前已经切换了数据表 如果要操作其他表 需要使用table()方法切换
             */
            $table->update(['username' => 'teacher'])->table('teacher')->where(['id' => 4])->execute();

            //插入一条数据到user表
            $table->insert(['username' => 'new_user', 'password' => 'password'])->table('user')->execute();

            $table->commit();

        } catch (\Exception $e){
            $table->rollBack();
            return false;
        }
        return true;
    }

    /**
     *
     * @return bool|string
     */
    public static function curl()
    {
        try {
            return Http::get('https://www.baidu.com/')->exec();
        } catch (\Exception $e){
            return  $e->getMessage();
        }
    }

    /**
     *
     * @return array|false|mixed
     */
    public static function config()
    {
        return Helper::config('database.dev.default.host');
    }

    /**
     *
     * @return bool
     */
    public static function crontabEcho(array $arr)
    {
        echo 'crontab ' . json_encode($arr) . PHP_EOL;
        return true;
    }
}
