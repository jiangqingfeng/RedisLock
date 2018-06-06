<?php

class Redis_lock
{
	//连接redis
    public static function getRedis()
    {
        $redis = new redis();
        $redis->connect('127.0.0.1', 6379, 0);
        $redis->auth('xxx');
        return $redis;
    }
	//锁名,过期时间
    public static function lock($key, $expire = 60)
    {
        if(!$key) {
            return false;
        }
        $redis = self::getRedis();
        do {
            if($acquired = ($redis->setnx("Lock:{$key}", time()))) { // 如果redis不存在，则成功
                $redis->expire($key, $expire);
                break;
            }

            usleep($expire);

        } while (true);

        return true;
    }

    //释放锁
    public static function release($key)
    {
        if(!$key) {
            return false;
        }
        $redis = self::getRedis();
        $redis->del("Lock:{$key}");
        $redis->close();
    }


}

/**
$redis = Redis_lock::getRedis();
Redis_lock::lock('lock');

$re = $redis->get('Sentiger');
$re--;
$redis->set('Sentiger', $re);
$re = $redis->get('Sentiger');
var_dump($re);
Redis_lock::release('lock');


**/
?>