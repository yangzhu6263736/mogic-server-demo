<?php
/**
 * @name Hall
 * @author yangzhu
 * @desc 默认控制器
 */
namespace Hall\Controller;

class Hall
{
    /**
     * 当玩家登陆过且第一次进入某进程时需要对sesssion做同步
     * 从远端redis中取回session数据 获取成功后才能开始进行业务操作
     * 比如登陆后第一次进入游戏房间（游戏进程并没有当前用户信息 需通过session_id获取信息）(否则每次进入一个新的进程都需要登陆)
     *
     * @param [type] $request
     * @return void
     */
    public static function enter($request)
    {
        \Mogic\MLog::log("enter");
        $userService = \Service\UserService::getInstance();
        $params = $request->params;
        print_R($params);
        $session_id = $params['session_id'];
        $userService->sso($session_id, function ($err, $session) use ($request) {
            if ($err) {
                return $request->done($err);
            }
            $userId = $session->userId;
        });
        $funcs = array(
            function ($params, $next) use ($request) {
                $userService = \Service\UserService::getInstance();
                $session_id = $params['session_id'];
                \Mogic\MLog::log("async test:", 1);
                $userService->sso($session_id, function ($err, $session) use ($params, $next, $request) {
                    \Mogic\MLog::log("async test:", 2, $err, $session);
                    var_dump($err);
                    if ($err) {
                        return $next($err, "not login");
                    }
                    // $userId = $session->userId;
                    $params['userId'] = $session->userId;
                    $next(false, $params);
                });
            },
            function ($params, $next) use ($request) {
                $userId = $params['userId'];
                $userService = \Service\UserService::getInstance();
                \Mogic\MLog::log("async test:", 3);
                $userService->initUserInfo($userId, function ($err, $userInfo) use ($params, $next, $request) {
                    \Mogic\MLog::log("async test:", 4);
                    if ($err) {
                        return $next($err, 'no user');
                    }
                    $request->response->userInfo = $userInfo;
                    $params['userInfo'] = $userInfo;
                    $next(false, $params);
                });
            },
            function ($err, $params) use ($request) {
                \Mogic\MLog::log("async test:", 5);
                if ($err) {
                    return $request->done($err);
                } else {
                    $request->done();
                }
            }
        );
        \Mogic\Utils::asyncCalls($funcs, $params);
    }
}
