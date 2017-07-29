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
    private $memoName = MEMO_TABLE_USERS;
    private $memoTableConfig;
    public function __construct()
    {
        $configs = \Mogic\Config::getConfig("Memo");
        $this->memoTableConfig = $configs[$this->memoName];
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

    /**
     * 从共享内存中取出用户信息
     *
     * @param [type] $userId
     * @return void
     */
    public function getUserInfo($userId)
    {
        $userInfo = \Mogic\Memo::getInstance()->table($this->memoName)->GET($userId);
        return $userInfo;
    }

    public function getUserInfoAsync($userId, $next)
    {
        $sql = "SELECT * FROM `mog_user` where userId = '".$userId."'";
        \Mogic\MysqlPools::pool(MYSQL_GROUP_HALL)->query($sql, function ($db, $result) use ($userId, $next) {
            if ($result === false) {
                \Mogic\MLog::log($db->connect_errno, $db->connect_error);
                return \call_user_func($next, true, $db->connect_errno);
            }
            $userInfo = array();
            $userInfo['userId'] = $result[0]['userId'];
            $userInfo['userName'] = $result[0]['userName'];
            $userInfo['coin'] = 100;
            \Mogic\Memo::getInstance()->table(MEMO_TABLE_USERS)->SET($userId, $userInfo);
            \call_user_func($next, false, $userInfo);
        });
    }

    /**
     * 更新用户数据
     *
     * @param [type] $userId
     * @param [type] $data
     * @return void
     */
    public function update($userId, $data)
    {
        $userInfo = array();
        foreach ($this->memoTableConfig['columns'] as $key => $value) {
            if (isset($data['$key'])) {
                $userinfo[$key] = $value;
            }
        }
        \Mogic\Memo::getInstance()->table(MEMO_TABLE_USERS)->SET($userId, $userInfo);
        $this->updateMysql($userId, $data);
    }

    /**
     * 原子自增操作
     *
     * @param [type] $userId
     * @param [type] $key
     * @param integer $incrby
     * @return void
     */
    public function incr($userId, $key, $incrby = 1)
    {
        $value = \Mogic\Memo::getInstance()->table(MEMO_TABLE_USERS)->incr($userId, $key, $incrby);
        if (!$value) {
            return false;
        }
        $data[$key] = $value;
        $this->updateMysql($userId, $data);
    }

    /**
     * 原子递减操作
     *
     * @param [type] $userId
     * @param [type] $key
     * @param integer $decrby
     * @return void
     */
    public function decr($userId, $key, $decrby = 1)
    {
        $value = \Mogic\Memo::getInstance()->table(MEMO_TABLE_USERS)->decr($userId, $key, $decrby);
        if (!$value) {
            return false;
        }
        $data[$key] = $value;
        $this->updateMysql($userId, $data);
        return true;
    }

    public function updateMysql($userId, $data)
    {
        $sql = "UPDATE `mog_user` SET ";
        foreach ($data as $key => $value) {
            $sql .= " ".$key."=".$value.",";
        }
        $sql = str_replace(" ", ",", trim(str_replace($sql, ',', " "))).";";
        \Mogic\MysqlPools::pool(MYSQL_GROUP_HALL)->query($sql, function ($db, $result) use ($next) {
        });
    }
}
