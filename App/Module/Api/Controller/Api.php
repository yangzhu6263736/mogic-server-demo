<?php
/**
 * @name Hall
 * @author yangzhu
 * @desc 默认控制器
 */
namespace Api\Controller;
        // use \test;

class Api
{

    
    public static function index($request)
    {
        $modulePath = APP_PATH.'Module';
        $trees = \Mogic\Utils::tree($modulePath);
        print_R($trees);
        // echo "Api\Controller\index";
        $request->response->trees = $trees;
        $request->done();
        // print_R($trees);
        // $a->tet($a11, function ($b11) {
        //     $b->test($b11, fucntion($c11){
        //         $c->test($c11, function ($d11) {

        //         });
        //     });
        // });
    }

    public static function queryModule($request)
    {
        $params = $request->params;
        $module = ucfirst($params['module']);
        $controller = ucfirst($params['controller']);
        
        $file = APP_PATH.'Module/'.$module.'/Controller/'.$controller.'.php';
        $route = $module.'\Controller\\'.$controller;
        $detail = \Mogic\Utils::getApiDetail($file, $route);
        $request->response->detail = $detail;
        $request->done();
    }

    public static function showTree($trees)
    {
        $res = "";
    }
}
