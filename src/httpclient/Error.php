<?php
// +----------------------------------------------------------------------
// | HttpClient [Simple HTTP Client Library for PHP]
// +----------------------------------------------------------------------
// | PHP HTTP客户端
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: endness <endness@qq.com>
// +----------------------------------------------------------------------

namespace endness\httpclient;

/**
 * 业务请求逻辑错误封装类，主要用来解析API请求返回如下的内容：
 * <pre>
 *     {"error" : "detailed error message"}
 * </pre>
 */
class Error
{
    /**
     * 请求接口地址
     * @var string
     */
    protected $url;

    /**
     * 响应对象
     * @var Response
     */
    protected $response;

    /**
     * 架构函数
     * @access public
     * @param string $url 请求接口地址
     * @param Response $response 授权密钥
     */
    public function __construct($url, Response $response)
    {
        $this->url = $url;
        $this->response = $response;
    }

    /**
     * 获取响应状态码
     * @access public
     * @return int
     */
    public function code()
    {
        return $this->response->statusCode;
    }

    /**
     * 获取响应对象
     * @access public
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * 获取响应错误信息
     * @access public
     * @return string
     */
    public function message()
    {
        return $this->response->error;
    }
}
