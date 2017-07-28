<?php
/**
 * @name Hall
 * @author yangzhu
 * @desc 默认控制器
 */
namespace Hall\Controller;

class Hall
{
    public static function test($request)
    {
        \Mogic\MLog::log("HallController");
        // $userService = \Service\UserService::getInstance();
        // $userModel = new \Model\User();
        // $userModel->test($a, $b, $request, function () use ($request) {
        //     $request->done();
        // });
    }

    public static function login($request)
    {
        \Mogic\MLog::log("HallController", "login");
        // $userInfo = \Mogic\Server::getInstance()->memtable->get(1);
        // print_R($userInfo);
        $params = $request->params;
        $userName = $params['user'];
        $userService = \Service\UserService::getInstance();
        $userService->get("userMO")->getUserByName($userName, function ($err, $userInfo) use ($request) {
            if ($err) {
                return $request->done($err, $userInfo);
            }
            \Mogic\MLog::log("hall login back", $err, $userInfo);
            $session = \Mogic\Session::start();
            $request->response->userInfo = $userInfo;
            $request->response->session_id = $session->session_id;
            $session->set('userId', $userInfo['userId']);
            $request->done();
        });
        // $userModel = new \Model\User();
        // $userModel->test($a, $b, $request, function () use ($request) {
        //     $request->done();
        // });
    }
}
