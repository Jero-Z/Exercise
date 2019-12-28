<?php

class Atomically
{
    public function __construct()
    {
        $redisHandle = new Redis();
        $redisHandle->connect('127.0.0.1', 6379, 60);
        for ($i = 1; $i <= 10; $i++) {
            $redisHandle->lPush('productList', $i);
        }
    }

    public function run()
    {
        for ($i = 1; $i <= 15; $i++) {
            $pid = pcntl_fork();
            if ($pid == 0) {
                $redis = new Redis();
                $redis->connect('127.0.0.1', 6379, 60);
                // child process
                $this->consumer($redis);
            }
        }
    }

    /**执行消费
     * @param $redis
     */
    public function consumer($redis)
    {
        $res = $redis->rPop('productList');
        if ($res) {
            echo "抢到了" . $res . "\n";
        } else {
            echo "没抢到了" . $res . "\n";
        }
    }
}

$handle = new Atomically();
$handle->run();