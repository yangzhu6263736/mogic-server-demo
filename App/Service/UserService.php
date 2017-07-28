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

    /**
     * 执行用户登陆操作 异步
     *
     * @param [type] $userName
     * @param [type] $passwd
     * @param [type] $next
     * @return void
     */
    public function doLogin($userName, $passwd, $next)
    {
        $this->userMO->getUserByName($userName, function ($err, $userInfo) use ($next) {
            if ($err || empty($userInfo)) {
                return $next($err, 'no user');
            }
            \Mogic\MLog::log("hall login back", $err, $userInfo);
            $session = \Mogic\Session::start();
            // $request->response->userInfo = $userInfo;
            // print_R($userInfo);
            // $request->response->session_id = $session->session_id;
            // print_R($session);
            // echo $userInfo['userId'];
            // $session->set('userId', $userInfo['userId']);
            $session->userId = $userInfo['userId'];
            $session->userName = $userInfo['userName'];
            // $session->set('userId', $userInfo['userId']);
            // $session->set('userName', $userInfo['name']);
            $session->pull();//推送到远端
            $next(false, $session);
        });
    }

    /**
     * 单点登陆 异步
     *
     * @param [type] $session_id
     * @param [type] $next
     * @return void
     */
    public function sso($session_id, $next)
    {
        $session = \Mogic\Session::getSession($session_id);
        if (empty($session->userId)) {
            $session->fetch(function ($err, $session) use ($next) {
                call_user_func($next, $err, $session);
            });
        } else {
            call_user_func($next, false, $session);
        }
    }

    public function initUserInfo($userId, $next)
    {
        $userInfo = array(1, 2, 3);
        $next(false, $userInfo);
    }

    /**
     * 检测session 同步
     * 只有执行了login 和 sso之后的请求才能通过检测
     *
     * @param [type] $session_id
     * @return void
     */
    public function checkSession($session_id)
    {
        $session = \Mogic\Session::getInstance($session_id);
        return empty($session->userId) ? true : false;
    }

    public function bind($userId, $fd)
    {
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
