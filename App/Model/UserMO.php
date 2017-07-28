<?php
/**
 * @name IndexController
 * @author yangzhu
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
namespace Model;

class UserMO
{
    public function __construct()
    {
    }

    public function test($a, $b, $request, $callback)
    {
        \Mogic\MLog::log("Usermodel", $a, $b);
        $request->get('response')->set('fuck', "me");
        call_user_func($callback);
    }

    public function getUserByName($userName, $next)
    {
        \Mogic\MLog::log('userMO:getUserByName', $userName);
        $sql = "SELECT * FROM `mog_user` where userName = '".$userName."'";
        \Mogic\MLog::log($sql);
        \Mogic\MysqlPools::pool(MYSQL_GROUP_HALL)->query($sql, function ($db, $result) use ($next) {
            if ($result === false) {
                \Mogic\MLog::log($db->connect_errno, $db->connect_error);
                return \call_user_func($next, true, $db->connect_errno);
            }
            \Mogic\MLog::log("db query back", $result);
            \call_user_func($next, false, $result[0]);
        });
    }
}
