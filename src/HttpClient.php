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

namespace endness;

use endness\httpclient\Header;
use endness\httpclient\Request;
use endness\httpclient\Response;

class HttpClient
{
    /**
     * 发送GET请求
     * @access public
     * @param string $url 请求地址
     * @param array $headers 请求头参数
     * @return Response
     */
    public static function get($url, $headers = [])
    {
        $request = new Request('GET', $url, $headers);
        return self::sendRequest($request);
    }

    /**
     * 发送DELETE请求
     * @access public
     * @param string $url 请求地址
     * @param array $headers 请求头参数
     * @return Response
     */
    public static function delete($url, $headers = [])
    {
        $request = new Request('DELETE', $url, $headers);
        return self::sendRequest($request);
    }

    /**
     * 发送POST请求
     * @access public
     * @param string $url 请求地址
     * @param string|array $body 请求体
     * @param array $headers 请求头参数
     * @return Response
     */
    public static function post($url, $body, $headers = [])
    {
        $request = new Request('POST', $url, $headers, $body);
        return self::sendRequest($request);
    }

    /**
     * 发送PUT请求
     * @access public
     * @param string $url 请求地址
     * @param string|array $body 请求体
     * @param array $headers 请求头参数
     * @return Response
     */
    public static function put($url, $body, $headers = [])
    {
        $request = new Request('PUT', $url, $headers, $body);
        return self::sendRequest($request);
    }

    /**
     * 发送PATCH请求
     * @access public
     * @param string $url 请求地址
     * @param string|array $body 请求体
     * @param array $headers 请求头参数
     * @return Response
     */
    public static function patch($url, $body, $headers = [])
    {
        $request = new Request('PATCH', $url, $headers, $body);
        return self::sendRequest($request);
    }

    /**
     * 发送表单类型POST请求
     * @access public
     * @param string $url 请求地址
     * @param array $fields 字段
     * @param string $name 名称
     * @param string $fileName 上传文件名
     * @param string $fileBody 上传文件内容
     * @param string $mimeType MIME类型
     * @param array $headers 请求头参数
     * @return Response
     */
    public static function multipartPost(
        $url,
        $fields,
        $name,
        $fileName,
        $fileBody,
        $mimeType = null,
        $headers = []
    ) {
        $data = [];
        $mimeBoundary = md5(microtime());

        foreach ($fields as $key => $val) {
            array_push($data, '--' . $mimeBoundary);
            array_push($data, 'Content-Disposition: form-data; name="' . $key . '"');
            array_push($data, '');
            array_push($data, $val);
        }

        array_push($data, '--' . $mimeBoundary);
        $finalMimeType = empty($mimeType) ? 'application/octet-stream' : $mimeType;
        $finalFileName = self::escapeQuotes($fileName);
        array_push($data, 'Content-Disposition: form-data; name="' . $name . '"; filename="' . $finalFileName . '"');
        array_push($data, 'Content-Type: ' . $finalMimeType);
        array_push($data, '');
        array_push($data, $fileBody);

        array_push($data, '--' . $mimeBoundary . '--');
        array_push($data, '');

        $body = implode("\r\n", $data);
        // var_dump($data);exit;
        $contentType = 'multipart/form-data; boundary=' . $mimeBoundary;
        $headers['Content-Type'] = $contentType;
        $request = new Request('POST', $url, $headers, $body);
        return self::sendRequest($request);
    }

    /**
     * 获取UA
     * @access protected
     * @return string
     */
    protected static function userAgent()
    {
        // 系统信息
        $systemInfo = php_uname('s');
        // 设备信息
        $machineInfo = php_uname('m');
        // 构造环境信息
        $envInfo = '(' . $systemInfo . '/' . $machineInfo . ')';
        // PHP版本
        $phpVer = phpversion();
        // 构造UA
        $ua = $envInfo . ' PHP/' . $phpVer;
        // 返回
        return $ua;
    }

    /**
     * 发送请求
     * @access public
     * @param Request $request 名称
     * @return Response
     */
    public static function sendRequest(Request $request)
    {
        $t1 = microtime(true);
        $ch = curl_init();
        $options = array(
            CURLOPT_USERAGENT => self::userAgent(),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HEADER => true,
            CURLOPT_NOBODY => false,
            CURLOPT_CUSTOMREQUEST => $request->method,
            CURLOPT_URL => $request->url,
        );
        // Handle open_basedir & safe mode
        if (!ini_get('safe_mode') && !ini_get('open_basedir')) {
            $options[CURLOPT_FOLLOWLOCATION] = true;
        }
        if (!empty($request->headers)) {
            $headers = [];
            foreach ($request->headers as $key => $val) {
                array_push($headers, "$key: $val");
            }
            $options[CURLOPT_HTTPHEADER] = $headers;
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
        if (!empty($request->body)) {
            $options[CURLOPT_POSTFIELDS] = $request->body;
        }
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        $t2 = microtime(true);
        $duration = round($t2 - $t1, 3);
        $ret = curl_errno($ch);
        if ($ret !== 0) {
            $r = new Response(-1, $duration, [], null, curl_error($ch));
            curl_close($ch);
            return $r;
        }
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $headers = Header::parseRawText(substr($result, 0, $header_size));
        $body = substr($result, $header_size);
        curl_close($ch);
        return new Response($code, $duration, $headers, $body, null);
    }

    /**
     * 处理上传文件名
     * @access protected
     * @param string $str 上传的文件名字符串
     * @return string
     */
    protected static function escapeQuotes($str)
    {
        $find = ["\\", "\""];
        $replace = ["\\\\", "\\\""];
        return str_replace($find, $replace, $str);
    }
}
