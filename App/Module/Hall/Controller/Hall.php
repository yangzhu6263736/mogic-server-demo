<?php
namespace Hall\Controller;

/**
 * @name Hall
 * @author yangzhu
 * @desc 游戏大厅控制器
 */
class Hall
{

    /**
     * 根据房号分发worker进程
     * @
     * @param $tes1 [int] 11111
     * @param $roomId [int] 游戏房间号(根据房间号分发请求到不同进程)
     * @param $tes2 [int] 2222
     * @param $tes3 [int] 333333
     * @return void
     */
    public static function dispatchWorker($request)
    {
        $params = $request['params'];
        $roomId = $params['roomId'];
        $request->client->bindDispatch($roomId);//dispatch后client会被释放 所以先向前端返回
        $request->done();
    }
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
        \Mogic\MLog::clog("red", "Hall\Controller\hall enter");
        \Mogic\MLog::clog(COLOR_RED, "Hall\Controller\hall enter");

        $params = $request->params;
        if (empty($params['session_id'])) {
            return $request->err(ERROR_USER_NOT_LOGIN);
        }

        $userService = \Service\UserService::getInstance();

        $funcs = array(
            function ($params, $next) use ($request) {
                $userService = \Service\UserService::getInstance();
                $session_id = $params['session_id'];
                \Mogic\MLog::log("async test:", 1);
                $userService->sso($session_id, function ($err, $session) use ($params, $next, $request) {
                    \Mogic\MLog::log("async test:", 2, $err, $session);
                    if ($err) {
                        return $next(ERROR_USER_NOT_LOGIN, "not login");
                    }
                    $request->client->bind($session->userId);//给当前请求客户端绑定用户 绑定后服务端才能主动向客户端推送消息
                    //如果未绑定用户可直接用client向前端派发事件
                    $request->client->emitClient('testClientEvent', array('fuck me testClientEvent'));
                    $request->client->push('testClientEvent', array('fuck me testClientEvent'));
                    //必须执行了绑定的才能调用
                    $client =  \Mogic\Client::getClientByUserId($session->userId);
                    $client->push('testClientEvent', array('fuck me testClientEvent2'));
                    \Mogic\MLog::clog("YELLOW", "bind");
                    \Mogic\Client::emitUser($session->userId, 'testClientEvent', array('fuck me testClientEvent3'));

                    $request->client->bindDispatch(1);//dispatch后client会被释放 所以先向前端返回
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
                    \Mogic\MLog::clog(COLOR_BLUE, "async test:", 4);
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

    public function test()
    {
        // $a->tet($a11, function ($b11) {
        //     $b->test($b11, fucntion($c11){
        //         $c->test($c11, function ($d11) {

        //         });
        //     });
        // });
    }
}
