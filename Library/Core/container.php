<?php
/**
 * Date: 2021/5/18 13:21
 * User: YHC
 * Desc:
 */

namespace Library\Core;

trait container
{
    /**
     *
     * @return \Library\Core\Horseloft\Container
     */
    public function container()
    {
        return $GLOBALS[HORSE_LOFT_CONTAINER];
    }
}
