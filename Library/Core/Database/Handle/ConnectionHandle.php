<?php
/**
 * Date: 2021/5/22 02:16
 * User: YHC
 * Desc:
 */

namespace Library\Core\Database\Handle;

use Library\Utils\Helper;

trait ConnectionHandle
{
    /**
     *获取数据库连接的配置信息
     *
     * @param $connection
     * @return array|false|mixed
     */
    private static function connection($connection)
    {
        if (empty($connection)) {
            throw new \RuntimeException('Unsupported connection', HORSE_LOFT_DATABASE_ERROR_CODE);
        }
        if (is_array($connection)) {
            return $connection;
        }

        if (!is_string($connection)) {
            throw new \RuntimeException('Unsupported connection', HORSE_LOFT_DATABASE_ERROR_CODE);
        }

        $connection = Helper::config('database.'. APP_ENV . '.' . $connection);
        if (empty($connection)) {
            throw new \RuntimeException('missing connection ' . $connection, HORSE_LOFT_DATABASE_ERROR_CODE);
        }
        return $connection;
    }
}