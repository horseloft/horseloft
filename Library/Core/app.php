<?php
/**
 * Date: 2021/4/25 11:35
 * User: YHC
 * Desc: 启动项目
 */

/*
 * --------------------------------------------------------------------------
 * 全局常量
 * --------------------------------------------------------------------------
 *
 * 以下常量不可缺失：
 * 项目根目录：HORSE_LOFT_ROOT_PATH
 * Config目录：HORSE_LOFT_CONFIG_PATH
 * Library目录：HORSE_LOFT_LIBRARY_PATH
 * Service目录：HORSE_LOFT_SERVICES_PATH
 * 当前Service的目录：HORSE_LOFT_SERVICE_PATH
 * 当前服务的命名空间：HORSE_LOFT_CONTROLLER_NAMESPACE
 * 当前服务的日志路径：HORSE_LOFT_SERVICE_LOG_PATH
 *
 */
require_once __DIR__ .'/constant.php';




/*
 * --------------------------------------------------------------------------
 * 【定义当前运行环境】 $argv
 * --------------------------------------------------------------------------
 *
 * 用于定义当前环境的Service常量
 * APP_ENV：
 * 当前项目环境；参考值：dev|test|production|online
 *
 */
if (empty($argv) || empty($argv[1])) {
    die('启动命令错误；参考格式：php index.php dev' . PHP_EOL);
}
if (is_numeric($argv[1])) {
    die('启动命令错误；不能使用数字作为服务运行环境' . PHP_EOL);
}
define('APP_ENV', $argv[1]);




/*
 * --------------------------------------------------------------------------
 * 【可选项】定义当前运行环境的常量
 * --------------------------------------------------------------------------
 *
 * 文件名称与APP_ENV定义的环境一致
 * 例：Services/QuestionService/Config/questionService.production.php
 *
 */
$thisServiceConfig = HORSE_LOFT_SERVICE_PATH . '/Config/' . lcfirst(HORSE_LOFT_SERVICE) . '.' . APP_ENV . '.php';
if (is_file($thisServiceConfig)) {
    require_once $thisServiceConfig;
}




/*
 * --------------------------------------------------------------------------
 * 【可选项】加载通用配置文件
 * --------------------------------------------------------------------------
 *
 * 加载 HORSE_LOFT_CONFIG_PATH 目录下以Config.php结尾的配置文件中的常量或变量
 * 目录路径 Config/*Config.php
 *
 * 推荐：文件名以小写字母开头
 *
 */
if (is_dir(HORSE_LOFT_CONFIG_PATH)) {
    $baseConfigHandle = opendir(HORSE_LOFT_CONFIG_PATH);
    while (false !== $baseConfigFile = readdir($baseConfigHandle)) {
        if ($baseConfigFile == '.' || $baseConfigFile == '..') {
            continue;
        } else {
            $suffix = substr($baseConfigFile, -10);
            if ($suffix != false && strlen($baseConfigFile) > 10 && $suffix == 'Config.php') {
                require_once HORSE_LOFT_CONFIG_PATH . '/' . $baseConfigFile;
            }
        }
    }
    closedir($baseConfigHandle);
}




/*
 * --------------------------------------------------------------------------
 * 命名空间注册
 * --------------------------------------------------------------------------
 *
 *
 *
 */
$autoload = HORSE_LOFT_LIBRARY_PATH . '/vendor/autoload.php';
if (!is_file($autoload)) {
    die('文件 ' . $autoload . ' 不存在');
}
$loader = require_once HORSE_LOFT_LIBRARY_PATH . '/vendor/autoload.php';
$loader->addPsr4('Services' . '\\', HORSE_LOFT_SERVICES_PATH);
$loader->addPsr4('Library' . '\\', HORSE_LOFT_LIBRARY_PATH);




/*
 * --------------------------------------------------------------------------
 * 异常及命令行输出控制
 * --------------------------------------------------------------------------
 *
 * 以下常量不可缺失：
 * HORSE_LOFT_COMMAND_OUTPUT 将信息输出到命令行
 * HORSE_LOFT_PDO_ERROR_DETAIL pdo异常信息展示
 *
 */
if (APP_ENV == 'dev') {
    //开启命令行输出
    defined('HORSE_LOFT_COMMAND_OUTPUT') || define('HORSE_LOFT_COMMAND_OUTPUT', true);

    //开启数据库错误详情异常
    defined('HORSE_LOFT_PDO_ERROR_DETAIL') || define('HORSE_LOFT_PDO_ERROR_DETAIL', true);

    //定义异常输出
    error_reporting(-1);
} else {
    //关闭命令行输出
    defined('HORSE_LOFT_COMMAND_OUTPUT') || define('HORSE_LOFT_COMMAND_OUTPUT', false);

    //关闭数据库错误详情异常
    defined('HORSE_LOFT_PDO_ERROR_DETAIL') || define('HORSE_LOFT_PDO_ERROR_DETAIL', false);

    //关闭异常
    error_reporting(0);
}





/*
 * --------------------------------------------------------------------------
 * 数据容器
 * --------------------------------------------------------------------------
 *
 */
$horseloftContainer = new \Library\Core\Horseloft\Container();
