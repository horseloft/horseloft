<?php
/**
 * Date: 2021/5/22 12:09
 * User: YHC
 * Desc:
 */

namespace Library\Core\Horseloft\Builder;

use Library\Core\Horseloft\Request;
use Library\Utils\Convert;

trait HttpRequestBuilder
{
    /**
     * 请求参数
     *
     * 不区分GET、POST；如果是raw格式，则仅支持：urlEncode和json
     *
     * @param \Swoole\Http\Request $request
     * @return array|mixed
     */
    private function requestParamHandle(\Swoole\Http\Request $request)
    {
        $responseParams = [];
        $rawRequest = @$request->rawContent();
        if (!empty($request->get)) {
            $responseParams = $request->get;
        }
        if (!empty($request->post)) {
            $responseParams = array_merge($responseParams, $request->post);
        }
        if (empty($responseParams) && !empty($rawRequest)) {
            $jsonData = json_decode($rawRequest, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $responseParams = $jsonData;
            } else {
                parse_str($rawRequest, $responseParams);
            }
        }
        $params = empty($responseParams) ? [] : $responseParams;

        //请求参数置于容器
        $this->container()->setParams($params);

        return $params;
    }

    /**
     * 路由处理 | 路由拦截器
     *
     * 请求的控制器的方法应为【静态方法】
     *
     * 将自动转换路由；路由对应的控制器首字转为母大写，方法的首字母转为小写；
     *
     * 请求必须有路由
     * 例：控制器：QuestionListController.php，方法：getUserName();
     * 那么路由应为：/questionList/getUserName
     *
     * 例：控制器：QuestionController.php，方法：test();
     * 那么路由为：/question/test
     *
     * 例：控制器：QuestionController.php，方法：test(array params); 参数为form表单或者json：name=lili&age=12
     * 那么路由为：/question/test
     *
     * @param \Swoole\Http\Request $request
     * @return array
     */
    private function getRequestRoute(\Swoole\Http\Request $request)
    {
        $uri = trim($request->server['request_uri'], '/');
        if (empty($uri)) {
            throw new \RuntimeException('Bad Request uri', HORSE_LOFT_BAD_REQUEST_CODE);
        }

        /*
         *
         * --------------------------------------------------------
         *          如果没有自定义路由 则使用默认路由
         * --------------------------------------------------------
         *
         */
        $interceptor = [];
        $routeConfig = $this->container()->getRouteConfig();
        if (empty($routeConfig)) {
            $pattern = '/^[a-zA-Z]+[0-9a-zA-Z]*$/';
            $uriPath = explode('/', $uri);
            $controller = ucfirst(trim($uriPath[0])) . 'Controller';
            $function = lcfirst(trim($uriPath[1]));
            if (preg_match($pattern, $controller) == false || preg_match($pattern, $function) == false) {
                throw new \RuntimeException('Request Not Found', HORSE_LOFT_REQUEST_NOT_FOUND_CODE);
            }
        } else {
            /*
             * --------------------------------------------------------
             *                  自定义路由
             * --------------------------------------------------------
             */
            if (!isset($routeConfig[$uri])) {
                throw new \RuntimeException('Request Not Found', HORSE_LOFT_REQUEST_NOT_FOUND_CODE);
            }
            //拦截器
            if (!empty($routeConfig[$uri]['interceptor'])) {
                $class = ucfirst($routeConfig[$uri]['interceptor']) . 'Interceptor';
                $namespace = rtrim(rtrim(HORSE_LOFT_CONTROLLER_NAMESPACE, '\\'), 'Controller') . 'Interceptor\\';
                $interceptor = [$namespace . $class, 'handle'];
            }
            $controller = $routeConfig[$uri]['controller'];
            $function = $routeConfig[$uri]['function'];
        }

        if (!is_callable([$this->container()->getNamespace() . $controller, $function])) {
            throw new \RuntimeException('Request Not Found', HORSE_LOFT_REQUEST_NOT_FOUND_CODE);
        }
        return [
            'interceptor' => $interceptor,
            'controller' => $controller,
            'function' => $function
        ];
    }

    /**
     * --------------------------------------------------------
     * 路由拦截器
     * --------------------------------------------------------
     *
     * 仅当返回值 === true 时，允许通过拦截器
     *
     * 如果拦截器返回值不全等于 true, 并且返回值中有 code/message/data 则替换默认的；如果没有 则将返回的信息赋值给data，
     *
     * @param $interceptor
     * @return array|bool
     */
    private function interceptorBuilder($interceptor)
    {
        if (empty($interceptor)) {
            return true;
        }
        // 拦截器回调方法不存在
        if (!is_callable($interceptor)) {
            throw new \RuntimeException('Request Not Allowed', HORSE_LOFT_REQUEST_NOT_ALLOWED_CODE);
        }

        // 拦截器返回值 === true 时，允许通过
        $callback = call_user_func($interceptor, new Request());
        if ($callback === true) {
            return true;
        }
        return [
            'code' => isset($callback['code']) ? $callback['code'] : HORSE_LOFT_REQUEST_NOT_ALLOWED_CODE,
            'data' => isset($callback['data']) ? $callback['data'] : $callback,
            'message' => isset($callback['message']) ? $callback['message'] : ''
        ];
    }

    /**
     * --------------------------------------------------------------------------
     * 请求参数是否一致|类型及个数
     * --------------------------------------------------------------------------
     * @param string $nameSpace
     * @param string $controller
     * @param string $function
     * @return array|mixed
     * @throws \ReflectionException
     */
    private function getCallArgs(string $controller, string $function)
    {
        $method = new \ReflectionMethod($this->container()->getNamespace() . $controller, $function);

        //调用的方法必须是静态方法
        if ($method->isStatic() == false) {
            throw new \RuntimeException('Request Not Found', HORSE_LOFT_REQUEST_NOT_FOUND_CODE);
        }

        //调用的方法没有参数
        if (count($method->getParameters()) == 0) {
            return [];
        }
        $params = $this->container()->getParams();

        $requestCount = count($params);
        $paramterNumber = $method->getNumberOfParameters();
        $paramterRequireNumber = $method->getNumberOfRequiredParameters();

        /*
         * --------------------------------------------------------------------------
         * 验证接受的参数类型
         * --------------------------------------------------------------------------
         *
         * 请求参数数量 >= 方法的参数数量，并且格式类型匹配，则直接请求
         * 或者
         * 如果请求参数数量 >= 必填参数数量，并且格式类型匹配，则直接请求
         *
         */
        $callArgs = $this->callArgs($method, $params);
        if (($requestCount >= $paramterNumber && !empty($callArgs)) || ($requestCount >= $paramterRequireNumber && !empty($callArgs))) {
            return $callArgs;
        }

        /*
         * --------------------------------------------------------------------------
         * 如果方法的参数数量为1，并且是数组格式或者无格式，或者混合类型格式，则把请求参数作为一个数组传递
         * --------------------------------------------------------------------------
         *
         */
        $firstParamType = $method->getParameters()[0]->getType();
        if (($paramterNumber == 1 || $paramterRequireNumber == 1) && is_null($firstParamType) || $firstParamType == 'array') {
            return [$params];
        }

        throw new \RuntimeException('Bad Request Param', HORSE_LOFT_BAD_REQUEST_CODE);
    }

    /**
     * --------------------------------------------------------------------------
     * 验证请求参数的类型，请求参数类型应与方法定义的参数类型一致
     * --------------------------------------------------------------------------
     *
     * @param \ReflectionMethod $method
     * @param array $params
     * @return array
     * @throws \ReflectionException
     */
    private function callArgs(\ReflectionMethod $method, array $params)
    {
        $result = [];
        $args = $method->getParameters();

        foreach ($args as $key => $value) {

            $argsName = $value->getName();
            $argsType = $value->getType();
            $paramValue = $this->getRequestParamValue($params, $argsName);

            if ($value->isDefaultValueAvailable()) { //如果有默认值

                if ($paramValue == 'horse_loft_null_value') {
                    $enableParam = $value->getDefaultValue();
                } else {
                    //如果方法参数：有类型、不是混合类型、参数类型不符合要求
                    if ($argsType != null && $argsType != 'mixed' && !$this->isVarType($argsType, $paramValue)) {
                        return [];
                    }
                    $enableParam = $paramValue;
                }

            } else { //如果没有默认值

                //请求参数存在、方法参数没有类型、方法参数是混合类型、请求参数格式=方法参数格式
                if ($paramValue != 'horse_loft_null_value'
                    && ($argsType == null || $argsType == 'mixed' || $this->isVarType($argsType, $paramValue))
                ) {
                    $enableParam = $paramValue;
                } else {
                    return [];
                }
            }

            $result[$value->getPosition()] = $enableParam;
        }
        return $result;
    }

    /**
     * --------------------------------------------------------------------------
     * 获取请求参数对应的方法的参数名称
     * --------------------------------------------------------------------------
     *
     * 如果请求参数是下划线分割的字符串，允许方法参数是对应的驼峰格式；
     * 例：user_name -> userName
     *
     * 如果不存在 返回一个标识符
     * 标识符 = 'horse_loft_null_value'
     *
     * @param array $params
     * @param string $argsName
     * @return false|mixed
     */
    private function getRequestParamValue(array $params, string $argsName)
    {
        if (isset($params[$argsName])) {
            return $params[$argsName];
        }

        //如果请求参数是下划线分割的字符串，允许方法参数是对应的驼峰格式；例子：user_name -> userName
        $string = Convert::humpToUnderline($argsName);
        if (isset($params[$string])) {
            return $params[$string];
        }

        //如果不存在 返回一个标识符
        return 'horse_loft_null_value';
    }

    /**
     * 验证$var的数据类型是否=$type
     * @param string $type
     * @param $var
     * @return bool
     */
    private function isVarType(string $type, $var)
    {
        switch ($type) {
            case 'string':
                //允许null
                if (in_array(gettype($var), ['unknown type', 'array', 'object', 'resource'])) {
                    return false;
                }
                break;
            case 'array':
                if (gettype($var) != 'array') {
                    return false;
                }
                break;
            case 'int':
                if (is_numeric($var) == false) {
                    return false;
                }
                break;
            case 'bool':
                if (gettype($var) != 'boolean' && $var !== 1 && $var !== 0) {
                    return false;
                }
                break;
            case 'float':
            case 'double':
                if (!is_numeric($var)) {
                    return false;
                }
                break;
            default:
                if ($type != gettype($var)) {
                    return false;
                }
                break;
        }
        return true;
    }
}