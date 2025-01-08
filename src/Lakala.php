<?php
// +----------------------------------------------------------------------
// | Lakala SDK [Lakala SDK for PHP]
// +----------------------------------------------------------------------
// | Lakala SDK
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: endness <endness@qq.com>
// +----------------------------------------------------------------------

namespace endness;

use endness\lakala\utils\Str;

/**
 * 拉卡拉SDK入口文件
 */
class Lakala
{
    /**
     * 创建的服务集合
     * @var static
     */
    protected static $services = [];

    /**
     * 静态创建服务
     * @access public
     * @param string $name 服务名称
     * @param array $options 配置参数
     * @return \endness\lakala\services\Base
     */
    public static function service($name, array $options = [])
    {
        // 如果服务名称为空
        if(empty($name)){
            throw new \InvalidArgumentException('Invalid service name.');
        }
        // 构造缓存键
        $key = md5($name . serialize($options));
        // 从缓存获取
        if (isset(static::$services[$key])){
            return static::$services[$key];
        }
        // 获取服务类名
        $serviceClass = __NAMESPACE__ . '\\lakala\\services\\' . Str::studly($name);
        // 如果服务类不存在
        if (!class_exists($serviceClass)){
            throw new \InvalidArgumentException(sprintf('Service "%s" not exists.', $name));
        }
        // 返回
        return static::$services[$key] = new $serviceClass($options);
    }
}