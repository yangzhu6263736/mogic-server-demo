<?php
/**
 * @name IndexController
 * @author yangzhu
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
namespace Service;

class UserService extends \Mogic\BaseService
{
    protected $userMO;//protecetd 父子可访问 如果想用get方法外部访问必须设为protected
    public function __construct()
    {
        parent::__construct();
        $this->userMO = new \Model\UserMO();
        \Mogic\MLog::log("UserService", "__construct");
    }

    public function test($a, $b, $request, $callback)
    {
        \Mogic\MLog::log("Usermodel", $a, $b);
        $request->get('response')->set('fuck', "me");
        call_user_func($callback);
    }

    public function getUserByUserId($userId)
    {
        if (!$this->di['users'][$userId]) {
            return false;
        }
    }

    public function initUser($userId, $next)
    {
        $this->userMO->getOneUserInfo($userId, function ($err, $userInfo) use ($next) {
            $this->di['users'][$userInfo['userId']] = $userInfo;
            call_user_func($next, $err, $userInfo);
        });
    }
}
