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

class Response
{
    /**
     * 响应状态码
     * @var int
     */
    public $statusCode;

    /**
     * 响应头参数
     * @var array
     */
    public $headers;

    /**
     * 响应头对象实例
     * @var Header
     */
    public $normalizedHeaders;

    /**
     * 响应体
     * @var string
     */
    public $body;

    /**
     * 错误信息
     * @var string
     */
    public $error;

    /**
     * 请求体json反序列化数据
     * @var mixed
     */
    protected $jsonData;

    /**
     * 请求时长
     * @var int
     */
    public $duration;

    /**
     * 状态码信息
     * @var array
     */
    protected static $statusTexts = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        226 => 'IM Used',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Reserved for WebDAV advanced collections expired proposal',
        426 => 'Upgrade required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates (Experimental)',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
    ];

    /**
     * 架构函数
     * @access public
     * @param int $code 状态码
     * @param double $duration 请求时长
     * @param array $headers 响应头部
     * @param string $body 响应内容
     * @param string $error 错误描述
     */
    public function __construct($code, $duration, $headers = [], $body = null, $error = null)
    {
        $this->statusCode = $code;
        $this->duration = $duration;
        $this->headers = [];
        $this->body = $body;
        $this->error = $error;
        $this->jsonData = null;

        if ($error !== null) {
            return;
        }

        foreach ($headers as $k => $vs) {
            if (is_array($vs)) {
                $this->headers[$k] = $vs[count($vs) - 1];
            } else {
                $this->headers[$k] = $vs;
            }
        }
        $this->normalizedHeaders = new Header($headers);

        // 如果返回null
        if ($body === null) {
            if ($code >= 400) {
                $this->error = self::$statusTexts[$code];
            }
            return;
        }

        // json反序列化
        try {
            // 反序列化成功
            $jsonData = self::bodyJsonDecode($body);
            // 如果状态码为错误
            if ($code >= 400) {
                // 记录错误信息
                $this->error = $body;
                // 存在error字段
                if (isset($jsonData['error'])) {
                    $this->error = $jsonData['error'];
                }
                // 存在message字段
                elseif (isset($jsonData['message'])) {
                    $this->error = $jsonData['message'];
                }
            }
            $this->jsonData = $jsonData;
        } catch (\InvalidArgumentException $e) {
            // 反序列化失败且响应头信息明确是json响应
            if (self::isJson($this->normalizedHeaders)) {
                $this->error = $body;
                if ($code >= 200 && $code < 300) {
                    $this->error = $e->getMessage();
                }
            }
        }
        return;
    }

    /**
     * 获取响应数据JSON反序列化数据
     * @access public
     * @return mixed
     */
    public function json()
    {
        return $this->jsonData;
    }

    /**
     * 获取响应头
     * @access public
     * @param bool $normalized 是否获取响应头对象实例
     * @return array
     */
    public function headers($normalized = false)
    {
        if ($normalized) {
            return $this->normalizedHeaders;
        }
        return $this->headers;
    }

    /**
     * 获取响应内容
     * @access public
     * @return string
     */
    public function body()
    {
        return $this->body;
    }

    /**
     * 响应内容JSON反序列化
     * @access public
     * @param string $body 响应内容字符串
     * @return array
     */
    public static function bodyJsonDecode($body)
    {
        if (empty($body)) {
            return null;
        }
        
        $jsonErrors = [
            JSON_ERROR_DEPTH => 'JSON_ERROR_DEPTH - Maximum stack depth exceeded',
            JSON_ERROR_STATE_MISMATCH => 'JSON_ERROR_STATE_MISMATCH - Underflow or the modes mismatch',
            JSON_ERROR_CTRL_CHAR => 'JSON_ERROR_CTRL_CHAR - Unexpected control character found',
            JSON_ERROR_SYNTAX => 'JSON_ERROR_SYNTAX - Syntax error, malformed JSON',
            JSON_ERROR_UTF8 => 'JSON_ERROR_UTF8 - Malformed UTF-8 characters, possibly incorrectly encoded'
        ];

        $data = json_decode($body, true, 512);

        if (JSON_ERROR_NONE !== json_last_error()) {
            $last = json_last_error();
            throw new \InvalidArgumentException('Unable to parse JSON data: ' . (isset($jsonErrors[$last]) ? $jsonErrors[$last] : 'Unknown error'));
        }

        return $data;
    }

    public function xVia()
    {
        $via = $this->normalizedHeaders['X-Via'];
        if ($via === null) {
            $via = $this->normalizedHeaders['X-Px'];
        }
        if ($via === null) {
            $via = $this->normalizedHeaders['Fw-Via'];
        }
        return $via;
    }

    public function xLog()
    {
        return $this->normalizedHeaders['X-Log'];
    }

    public function xReqId()
    {
        return $this->normalizedHeaders['X-Reqid'];
    }

    public function ok()
    {
        return $this->statusCode >= 200 && $this->statusCode < 300 && $this->error === null;
    }

    public function needRetry()
    {
        $code = $this->statusCode;
        if ($code < 0 || ($code / 100 === 5 and $code !== 579) || $code === 996) {
            return true;
        }
    }

    protected static function isJson($headers)
    {
        return isset($headers['Content-Type']) && strpos($headers['Content-Type'], 'application/json') === 0;
    }
}
