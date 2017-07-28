<?php
/**
 * @name IndexController
 * @author yangzhu
 * @desc 默认控制器
 */
namespace Hall\Controller;

class User
{
 

    /**
     * 登陆操作
     *
     * @param [type] $request
     * @return void
     */
    public static function login($request)
    {
        \Mogic\MLog::log("HallController", "login");
        // $userInfo = \Mogic\Server::getInstance()->memtable->get(1);
        // print_R($userInfo);
        $params = $request->params;
        $userName = $params['user'];
        $passwd = $params['passwd'];
        $userService = \Service\UserService::getInstance();
        $userService->doLogin($userName, $passwd, function ($err, $session) use ($request) {
            if ($err) {
                \Mogic\MLog::log("userService doLogin back", $err, $session);
            }
            $request->response->session_id = $session->session_id;
            $request->done();
        });
    }
}
