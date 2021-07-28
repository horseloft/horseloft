<?php
/**
 * Date: 2021/4/25 12:00
 * User: YHC
 * Desc: 全局常量
 */

//项目容器
define('HORSE_LOFT_CONTAINER', 'horseloftContainer');

//项目根目录
define('HORSE_LOFT_ROOT_PATH', dirname(dirname(__DIR__)));

//Config目录
define('HORSE_LOFT_CONFIG_PATH', HORSE_LOFT_ROOT_PATH . '/Config');

//Library目录
define('HORSE_LOFT_LIBRARY_PATH', HORSE_LOFT_ROOT_PATH . '/Library');

//Services目录
define('HORSE_LOFT_SERVICES_PATH', HORSE_LOFT_ROOT_PATH . '/Services');

//当前Service的目录
define('HORSE_LOFT_SERVICE_PATH', HORSE_LOFT_SERVICES_PATH . '/' . HORSE_LOFT_SERVICE);

//当前服务的命名空间 用于路由访问
define('HORSE_LOFT_CONTROLLER_NAMESPACE', 'Services\\' . HORSE_LOFT_SERVICE . '\Controller\\');

//当前服务的日志路径
define('HORSE_LOFT_SERVICE_LOG_PATH', HORSE_LOFT_SERVICES_PATH . '/' . HORSE_LOFT_SERVICE . '/Log');


/*
 * ---------------------------------------------------
 *  错误码
 * ---------------------------------------------------
 */

//success
define('HORSE_LOFT_SUCCESS_CODE', 10200);

//error
define('HORSE_LOFT_ERROR_CODE', 10500);

//数据库
define('HORSE_LOFT_DATABASE_ERROR_CODE', 10101);

//Redis
define('HORSE_LOFT_REDIS_ERROR_CODE', 10102);

//Curl
define('HORSE_LOFT_CURL_ERROR_CODE', 10103);

//other
define('HORSE_LOFT_OTHER_ERROR_CODE', 10104);


//Bad Request uri
define('HORSE_LOFT_BAD_REQUEST_CODE', 10400);

//Request Not Found
define('HORSE_LOFT_REQUEST_NOT_FOUND_CODE', 10404);

//Request Not Allowed
define('HORSE_LOFT_REQUEST_NOT_ALLOWED_CODE', 10405);
