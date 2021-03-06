<?php namespace Cafe\Foundation;

/**
 * Cafe PHP
 *
 * An agile development core based on PHP.
 *
 * @version  1.0.0
 * @link     https://github.com/biejun/CafePHP
 * @copyright Copyright (c) 2017-2018 Jun Bie
 * @license This content is released under the MIT License.
 */

/**
 * 系统应用层组件核心代码
 *
 * @package Cafe\Foundation\Component
 * @since 0.0.5 所有应用数据操作基于此类
 */

use Cafe\DataBase\DataManager;
use Cafe\Cache\Cache;

abstract class Component
{
    protected $bindings = [];

    public $session;

    public $cookie;

    public function __construct()
    {
        $this->session = new Session;
        $this->cookie = new Cookie;
        
        /* 数据库 */
        $this->bind('mysql', function () {
            static $_conf, $_db;
            if (is_null($_conf)) {
                $file = CONFIG . '/config.db.php';
                if (!file_exists($file)) {
                    throw new \Exception("数据库配置文件不存在！");
                }
                $_conf = include($file);
            }
            if (is_null($_db)) {
                $_db = new DataManager($_conf);
            }
            return $_db;
        });

        /* 数据缓存 */
        $this->bind('data', function () {
            return Cache::init();
        });
        /* 日志缓存 */
        $this->bind('log', function () {
            return Cache::init(['folder'=> STORAGE .'/logs']);
        });

        if (method_exists($this, '_initialize')) {
            $this->_initialize();
        }
    }

    public function bind($abstract, $concrete = null)
    {
        if (isset($this->bindings[$abstract])) {
            return false;
        }
        $this->bindings[$abstract] = $concrete;
    }

    public function getBind($abstract)
    {
        return $this->bindings[$abstract]();
    }

    /* 执行SQL语句的方法 */
    public function db()
    {
        return call_user_func_array(
            array(
          $this->getBind('mysql'), 'prepare'),
            func_get_args()
        );
    }

    /**
     *  执行应用组件中的方法
     *
     *  @param string $func 回调函数
     *  @param array $args 回调函数参数
     *  @return mixed
     *
    **/
    public function run($func, $args)
    {
        $reflection = new \ReflectionClass($this);
        $parentClass = $reflection->getParentClass();

        if ($parentClass) {
            $parentMethods = $parentClass->getMethods();
            // 过滤父类方法
            while ($it = current($parentMethods)) {
                if ($func === $it->getName()) {
                    return false;
                } else {
                    next($parentMethods);
                }
            }
            if ($reflection->hasMethod($func)) {
                $method = $reflection->getMethod($func);
                if ($method->isPublic()) {
                    $params = [];
                    foreach ($method->getParameters() as $arg) {
                        if ($args[$arg->name]) {
                            $params[$arg->name] = $args[$arg->name];
                        } else {
                            $params[$arg->name] = null;
                        }
                    }
                    return $method->invokeArgs($this, $params);
                }
            }
        }
        return false;
    }

    /* 载入应用组件 */
    public function load($component)
    {
        return self::instance($component);
    }

    /**
     *  实例化一个应用组件
     *
     *  @param string $component 组件名不区分大小写,调用子组件用@分隔，如"admin@api"
     *  @return instance
     */
    public static function instance($component)
    {
        if (empty($component)) {
            return false;
        }
        $parts = (strpos($component, '@')!==false) ? explode('@', $component) : [ucfirst($component)];
        $app = array_shift($parts);
        $appNameSpace = '\\App\\'.ucfirst($app).'\\Components\\';

        if (count($parts) > 0) {
            $className = $appNameSpace;
            foreach ($parts as $value) {
                $className .= ucfirst($value);
            }
        } else {
            $className = $appNameSpace.ucfirst($app);
        }
        return new $className();
    }

    /**
     * 复制当前组件到一个新变量中
     *
     * @param string $variable 变量名
     * @return void
     */
    public function to(&$variable)
    {
        return $variable = $this;
    }

    /**
     * 应用组件初始化回调
     *
     * @return void
     */
    public function _initialize()
    {
    }
}
