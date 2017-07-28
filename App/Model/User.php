<?php
/**
 * @name IndexController
 * @author yangzhu
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
namespace Model;

class User
{

    public function __construct()
    {
        if (self::$instance) {
            throw new Exception("单例模式已存在, 不能重复创建。使用 xxxMO::getInstance()获取");
        }
    }

    public function test($a, $b, $request, $callback)
    {
        \Mogic\MLog::log("Usermodel", $a, $b);
        $request->get('response')->set('fuck', "me");
        call_user_func($callback);
    }
}
