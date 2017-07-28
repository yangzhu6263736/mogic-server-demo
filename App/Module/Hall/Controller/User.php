<?php
/**
 * @name IndexController
 * @author yangzhu
 * @desc 默认控制器
 */
namespace Hall\Controller;

class User
{
    public static function test($request)
    {
        \Mogic\MLog::log("userController");
        // $userService = \Service\UserService::getInstance();
        // $userModel = new \Model\User();
        // $userModel->test($a, $b, $request, function () use ($request) {
        //     $request->done();
        // });
    }
}
