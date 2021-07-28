<?php
/**
 * Date: 2021/4/26 11:28
 * User: YHC
 * Desc:
 */

namespace Library\Core\Horseloft;

class Spanner
{
    /**
     * json
     * @param $data
     * @return string
     */
    public static function encode($data)
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }
    
    /**
     * 命令行输出
     * @param $data
     */
    public static function cliPrint($data)
    {
        if (HORSE_LOFT_COMMAND_OUTPUT === false) {
            return;
        }
        if (is_string($data)) {
            echo $data . PHP_EOL;
        } else {
            echo self::encode($data) . PHP_EOL;
        }
    }

    /**
     * 命令行输出异常信息
     *
     * @param $e
     * @return bool
     */
    public static function cliExceptionPrint($e)
    {
        $header = '=========================  SERVICE ERROR  =======================' . PHP_EOL;

        $message = 'message: ' . $e->getMessage() . PHP_EOL;

        $file = 'file: ' . $e->getFile() . ' (line:' . $e->getLine() . ')';

        self::cliPrint($header . $message . $file);
        return true;
    }

}
