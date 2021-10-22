<?php

namespace Application\Controller;

use Application\Models\CityModel;
use Application\Models\MoreModel;
use Application\Models\TeacherModel;
use Application\Models\UserModel;
use Horseloft\Database\Transaction;

/**
 * ------------------------------------------------
 * 控制器中的方法必须是静态方法
 * ------------------------------------------------
 *
 * Class DemoController
 * @package Application\Controller
 */
class DemoController
{
    public static function index()
    {
        return 'horseloft';
    }

    public static function where()
    {
        return [
            'mssql' => CityModel::select('cityID,cityName')->where(['cityID' => ['gt' => 1]])->first(),
            'first' => UserModel::select('id,username')->first(),
            'where-1' => UserModel::select('id,username')->where(['id' => 1])->all(),
            'where-2' => UserModel::select('id,username')->where(['id' => 1])->where(['id' => ['gt' => 0]])->all(),
            'or-1' => UserModel::select('id,username')->whereOr(['id' => 1])->all(),
            'or-2' => UserModel::select('id,username')->whereOr(['id' => 1])->whereOr(['id' => 2])->all(),
            'raw-1' => UserModel::select('id,username')->whereRaw('id = 2')->all(),
            'raw-2' => UserModel::select('id,username')->whereRaw('id = 2')->whereRaw('and id > 1')->all(),
            'where' => UserModel::select('id,username')->where(['id' => 2])->whereOr(['id' => 3])->whereRaw('or id = 4')->all(),
            'all' => UserModel::select('id')->all(),
        ];
    }

    public static function order()
    {
        return [
            'order-id' => UserModel::select('id')->order('id')->all(),
            'order-desc-id' => UserModel::select('id')->orderDesc('id')->all(),
            'order-asc-id' => UserModel::select('id')->orderAsc('id')->all(),
            'order-id-desc' => UserModel::select('id')->order('id desc')->all(),
        ];
    }

    public static function limit()
    {
        return [
            'limit-1' => UserModel::select('id')->limit(1)->all(),
            'limit-1-2' => UserModel::select('id')->limit(1, 2)->all(),
            'page-1-2' => UserModel::select('id')->page(1, 2)->all(),
        ];
    }

    public static function count()
    {
        return [
            'count' => UserModel::select()->count(),
            'count-where' => UserModel::select()->where(['id' => ['gt' => 1]])->count(),
        ];
    }

    public static function pdo()
    {
        return [
            'select-1' => TeacherModel::fetch('select * from teacher where id = ?', 1),
            'select-2' => TeacherModel::fetchAll('select * from teacher where id > ?', 1),
            'update-1' => TeacherModel::exec('update teacher set username = ? where id = 1', 'teacher_111')
        ];
    }

    public static function trans()
    {
        Transaction::begin('default');
        try {
            //insert
            $userInsertId = UserModel::insert(['username' => 'username_abc', 'password' => 'user_password'])->execute();

            //select
            $userSelectInsert = UserModel::select()->where(['id' => $userInsertId])->first();

            //update
            $userUpdateRow = UserModel::update(['username' => 'username_update'])->where(['id' => $userInsertId])->execute();

            //select
            $userSelectUpdate = UserModel::select()->where(['id' => $userInsertId])->first();

            //delete
            $userDeleteRow = UserModel::delete()->where(['id' => $userInsertId])->execute();

            //select
            $userSelectDelete = UserModel::select()->where(['id' => $userInsertId])->first();

            Transaction::commit();

            return [
                'user_insert_id' => $userInsertId,
                'user_update_row' => $userUpdateRow,
                'user_delete_row' => $userDeleteRow,
                'user_select_insert' => $userSelectInsert,
                'user_select_update' => $userSelectUpdate,
                'user_select_delete' => $userSelectDelete
            ];
        } catch (\Exception $e){

            Transaction::rollback();

            return $e->getMessage();
        }
    }

    /**
     * 多数据源
     *
     * @return array|string
     */
    public static function more()
    {
        Transaction::begin('more');
        try {

            //insert
            $userInsertId = MoreModel::insert(['username' => 'username_abc', 'password' => 'user_password'])->execute();

            //select
            $userSelectInsert = MoreModel::select()->where(['id' => $userInsertId])->first();

            //update
            $userUpdateRow = MoreModel::update(['username' => 'username_update'])->where(['id' => $userInsertId])->execute();

            //select
            $userSelectUpdate = MoreModel::select()->where(['id' => $userInsertId])->first();

            //delete
            $userDeleteRow = MoreModel::delete()->where(['id' => $userInsertId])->execute();

            //select
            $userSelectDelete = MoreModel::select()->where(['id' => $userInsertId])->first();

            Transaction::commit();
            return [
                'user_insert_id' => $userInsertId,
                'user_update_row' => $userUpdateRow,
                'user_delete_row' => $userDeleteRow,
                'user_select_insert' => $userSelectInsert,
                'user_select_update' => $userSelectUpdate,
                'user_select_delete' => $userSelectDelete
            ];
        } catch (\Exception $e){
            Transaction::rollback();
            return $e->getMessage();
        }
    }
}
