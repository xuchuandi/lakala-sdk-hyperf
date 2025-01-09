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

class Header implements \ArrayAccess, \IteratorAggregate, \Countable
{
    /**
     * 请求头参数
     * @var array
     */
    protected $data = [];

    /**
     * 架构函数
     * @access public
     * @param array $data 请求头参数
     */
    public function __construct($data = [])
    {
        foreach ($data as $key => $values) {
            $normalizedKey = self::normalizeKey($key);
            $normalizedValues = [];
            foreach ($values as $value) {
                array_push($normalizedValues, self::normalizeValue($value));
            }
            $this->data[$normalizedKey] = $normalizedValues;
        }
        return $this;
    }

    /**
     * return origin headers, which is field name case-sensitive
     * @param string $raw
     * @return array
     */
    public static function parseRawText($raw)
    {
        $headers = [];
        $headerLines = explode("\r\n", $raw);
        foreach ($headerLines as $line) {
            $headerLine = trim($line);
            $kv = explode(':', $headerLine);
            if (count($kv) <= 1) {
                continue;
            }
            // for http2 [Pseudo-Header Fields](https://datatracker.ietf.org/doc/html/rfc7540#section-8.1.2.1)
            if ($kv[0] == "") {
                $fieldName = ":" . $kv[1];
            } else {
                $fieldName = $kv[0];
            }
            $fieldValue = trim(substr($headerLine, strlen($fieldName . ":")));
            if (isset($headers[$fieldName])) {
                array_push($headers[$fieldName], $fieldValue);
            } else {
                $headers[$fieldName] = [$fieldValue];
            }
        }
        return $headers;
    }

    /**
     * @param string $raw
     * @return Header
     */
    public static function fromRawText($raw)
    {
        return new Header(self::parseRawText($raw));
    }

    /**
     * @param string $key
     * @return string
     */
    public static function normalizeKey($key)
    {
        $key = trim($key);

        if (!self::isValidKeyName($key)) {
            return $key;
        }

        return ucwords(strtolower($key), '-');
    }

    /**
     * @param string|int $value
     * @return string|int
     */
    public static function normalizeValue($value)
    {
        if (is_numeric($value)) {
            return $value + 0;
        }
        return trim($value);
    }

    /**
     * @return array
     */
    public function getRawData()
    {
        return $this->data;
    }

    /**
     * @param $offset string
     * @return boolean
     */
    public function offsetExists($offset)
    {
        $key = self::normalizeKey($offset);
        return isset($this->data[$key]);
    }

    /**
     * @param $offset string
     * @return string|null
     */
    public function offsetGet($offset)
    {
        $key = self::normalizeKey($offset);
        if (isset($this->data[$key]) && count($this->data[$key])) {
            return $this->data[$key][0];
        } else {
            return null;
        }
    }

    /**
     * @param $offset string
     * @param $value string
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $key = self::normalizeKey($offset);
        if (isset($this->data[$key]) && count($this->data[$key]) > 0) {
            $this->data[$key][0] = self::normalizeValue($value);
        } else {
            $this->data[$key] = [self::normalizeValue($value)];
        }
    }

    /**
     * @return void
     */
    public function offsetUnset($offset)
    {
        $key = self::normalizeKey($offset);
        unset($this->data[$key]);
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        $arr = [];
        foreach ($this->data as $k => $v) {
            $arr[$k] = $v[0];
        }
        return new \ArrayIterator($arr);
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->data);
    }

    protected static $isTokenTable = [
        '!' => true,
        '#' => true,
        '$' => true,
        '%' => true,
        '&' => true,
        '\'' => true,
        '*' => true,
        '+' => true,
        '-' => true,
        '.' => true,
        '0' => true,
        '1' => true,
        '2' => true,
        '3' => true,
        '4' => true,
        '5' => true,
        '6' => true,
        '7' => true,
        '8' => true,
        '9' => true,
        'A' => true,
        'B' => true,
        'C' => true,
        'D' => true,
        'E' => true,
        'F' => true,
        'G' => true,
        'H' => true,
        'I' => true,
        'J' => true,
        'K' => true,
        'L' => true,
        'M' => true,
        'N' => true,
        'O' => true,
        'P' => true,
        'Q' => true,
        'R' => true,
        'S' => true,
        'T' => true,
        'U' => true,
        'W' => true,
        'V' => true,
        'X' => true,
        'Y' => true,
        'Z' => true,
        '^' => true,
        '_' => true,
        '`' => true,
        'a' => true,
        'b' => true,
        'c' => true,
        'd' => true,
        'e' => true,
        'f' => true,
        'g' => true,
        'h' => true,
        'i' => true,
        'j' => true,
        'k' => true,
        'l' => true,
        'm' => true,
        'n' => true,
        'o' => true,
        'p' => true,
        'q' => true,
        'r' => true,
        's' => true,
        't' => true,
        'u' => true,
        'v' => true,
        'w' => true,
        'x' => true,
        'y' => true,
        'z' => true,
        '|' => true,
        '~' => true,
    ];

    /**
     * @param string $str
     * @return boolean
     */
    protected static function isValidKeyName($str)
    {
        for ($i = 0; $i < strlen($str); $i += 1) {
            if (!isset(self::$isTokenTable[$str[$i]])) {
                return false;
            }
        }
        return true;
    }
}
