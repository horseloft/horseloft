<?php
/**
 * Date: 2021/5/13 17:27
 * User: YHC
 * Desc:
 */

namespace Library\Core\Drawer;

final class Curl
{
    private $method;

    private $option = [];

    private $optionList = [];

    private $postData = [];

    public function __construct(string $method, string $url, array $postData = [])
    {
        if ($this->isSupportMethod($method) == false) {
            throw new \RuntimeException('Unsupported method:' . $method, HORSE_LOFT_CURL_ERROR_CODE);
        }

        if ($this->isSupportHttp($url) == false) {
            throw new \RuntimeException('Only supports HTTP and HTTPS', HORSE_LOFT_CURL_ERROR_CODE);
        }

        $this->option = [
            CURLOPT_URL => $url,
            CURLOPT_HEADER => false,
            CURLOPT_NOSIGNAL => true,
            CURLOPT_RETURNTRANSFER => true
        ];

        if (!empty($postData)) {
            $this->postData = $postData;
        }

        $this->method = $method;
    }

    /**
     *
     * @param array $header
     * @return $this
     */
    public function header(array $header)
    {
        if (!empty($header)) {
            $this->option[CURLOPT_HTTPHEADER] = $header;
        }
        return $this;
    }

    /**
     *
     * @param array $option
     * @return $this
     */
    public function option(array $option)
    {
        if (!empty($option)) {
            $this->optionList = $option;
        }

        return $this;
    }

    /**
     *
     * @param int $timeout
     * @return bool|string
     */
    public function exec(int $timeout = 30)
    {
        $this->option[CURLOPT_TIMEOUT] = $timeout;

        $handle = curl_init();
        if ($handle === false) {
            throw new \RuntimeException('curl initialization failed', HORSE_LOFT_CURL_ERROR_CODE);
        }

        if (!empty($this->postData)) {
            $this->option[CURLOPT_POSTFIELDS] = http_build_query($this->postData);
        }

        if (curl_setopt_array($handle, $this->option) == false) {
            throw new \RuntimeException('curl option not successfully', HORSE_LOFT_CURL_ERROR_CODE);
        }

        if (!empty($this->optionList) && curl_setopt_array($handle, $this->optionList) == false) {
            throw new \RuntimeException('curl option failed', HORSE_LOFT_CURL_ERROR_CODE);
        }

        $curlBack = curl_exec($handle);
        if (curl_errno($handle) > 0) {
            throw new \RuntimeException(curl_error($handle), HORSE_LOFT_CURL_ERROR_CODE);
        }

        //打印请求的header信息
        //print_r(curl_getinfo($handle, CURLINFO_HEADER_OUT));

        curl_close($handle);

        return $curlBack;
    }

    /**
     *
     * @param string $method
     * @return bool
     */
    private function isSupportMethod(string $method)
    {
        $method = strtoupper($method);

        if (in_array($method, ['GET', 'POST'])) {

            if ($method == 'POST') {
                $this->option = array_merge($this->option, [CURLOPT_POST => true, CURLOPT_CUSTOMREQUEST => 'POST']);
            } else {
                $this->option = array_merge($this->option, [CURLOPT_POST => false]);
            }

            return true;
        }
        return false;
    }

    /**
     *
     * @param string $url
     * @return bool
     */
    private function isSupportHttp(string $url)
    {
        if (empty($url)) {
            return false;
        }

        $headerIndex = strrpos($url, '://');
        if ($headerIndex === false) {
            return false;
        }

        $header = strtolower(substr($url, 0, $headerIndex));

        if ($header == 'https') {
            $this->option = array_merge($this->option, [CURLOPT_SSL_VERIFYPEER => false, CURLOPT_SSL_VERIFYHOST => false]);
        }

        if (!in_array($header, ['http', 'https'])) {
            return false;
        }

        return true;
    }
}
