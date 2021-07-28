<?php
/**
 * Date: 2021/5/13 16:17
 * User: YHC
 * Desc: Curl
 */

namespace Library\Utils;

use Library\Core\Drawer\Curl;

class Http
{
    /**
     * -----------------------------------------------------------
     *  GET请求
     * -----------------------------------------------------------
     *
     * 需要调用exec()方法获取最终结果
     *
     * @param string $url
     * @return Curl
     */
    public static function get(string $url)
    {
        return new Curl('GET', $url);
    }

    /**
     * -----------------------------------------------------------
     * POST请求
     * -----------------------------------------------------------
     *
     * 需要调用exec()方法获取最终结果
     *
     * @param string $url
     * @param array $data
     * @return Curl
     */
    public static function post(string $url, array $data = [])
    {
        return new Curl('POST', $url, $data);
    }
}