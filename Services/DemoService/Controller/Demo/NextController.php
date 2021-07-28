<?php
/**
 * Date: 2021/5/17 16:49
 * User: YHC
 * Desc:
 */

namespace Services\DemoService\Controller\Demo;

class NextController
{
    public static function next()
    {
        return 'next demo';
    }

    /**
     * 异常捕捉
     */
    public static function exception()
    {
        throw new \RuntimeException('exception....');
    }
}