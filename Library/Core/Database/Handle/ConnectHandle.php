<?php
/**
 * Date: 2021/5/22 01:33
 * User: YHC
 * Desc:
 */

namespace Library\Core\Database\Handle;

trait ConnectHandle
{
    /**
     * -----------------------------------------------------------
     * 创建PDO连接
     * -----------------------------------------------------------
     *
     * $config = [
     *      'driver' => 'mysql',        //必填项 dblib/mysql
     *      'host' => '127.0.0.1',      //必填项 database host
     *      'port' => 3306,             //必填项 database port
     *      'username' => 'username',   //必填项 database username
     *      'password' => 'password',   //必填项 database password
     *      'database' => 'database'    //必填项 database name
     *      'charset' => 'utf8'         //选填项 database charset
     * ]
     *
     * @param array $config
     * @return \PDO
     */
    protected function connect(array $config = [])
    {
        if (empty($config)) {
            $config = $this->config;
        }
        $this->checkConfig($config);

        try {
            $connect = new \PDO($this->getDnsString($config), $config['username'], $config['password']);
        }catch (\PDOException $e) {
            if (HORSE_LOFT_PDO_ERROR_DETAIL) {
                $message = $e->getMessage();
            } else {
                $message = Builder::$warning;
            }
            throw new \RuntimeException($message, HORSE_LOFT_DATABASE_ERROR_CODE);
        }
        return $connect;
    }

    /**
     *
     * @param array $config
     */
    private function checkConfig(array $config)
    {
        if (empty($config['driver'])) {
            throw new \RuntimeException('empty database driver', HORSE_LOFT_DATABASE_ERROR_CODE);
        }

        if (empty($config['host'])) {
            throw new \RuntimeException('empty database host', HORSE_LOFT_DATABASE_ERROR_CODE);
        }

        if (empty($config['port'])) {
            throw new \RuntimeException('empty database port', HORSE_LOFT_DATABASE_ERROR_CODE);
        }

        if (empty($config['username'])) {
            throw new \RuntimeException('empty database username', HORSE_LOFT_DATABASE_ERROR_CODE);
        }

        if (empty($config['password'])) {
            throw new \RuntimeException('empty database password', HORSE_LOFT_DATABASE_ERROR_CODE);
        }

        if (empty($config['database'])) {
            throw new \RuntimeException('empty database name', HORSE_LOFT_DATABASE_ERROR_CODE);
        }
    }

    /**
     *
     * @param array $config
     * @return mixed|string
     */
    private function getCharset(array $config)
    {
        return isset($config['charset']) ? $config['charset'] : 'utf8';
    }

    /**
     *
     * @param array $config
     * @return string
     */
    private function getDnsString(array $config)
    {
        switch ($config['driver']) {
            case 'mysql':
                $dsn = $config['driver']
                    . ':host=' . $config['host']
                    . ';dbname=' . $config['database']
                    . ';port=' . $config['port']
                    . ';charset=' . $this->getCharset($config);
                break;
            case 'sqlserver':
                $dsn = 'dblib:host=' . $config['host'] . ':' . $config['port'] . ';dbname=' . $config['database'];
                break;
            default:
                throw new \RuntimeException('Unsupported driver:' . $config['driver'], HORSE_LOFT_DATABASE_ERROR_CODE);
        }
        return $dsn;
    }
}