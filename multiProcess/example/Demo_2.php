<?php
/**
 * author:       joddiyzhang <joddiyzhang@gmail.com>
 * createTime:   2017/8/2 下午4:26
 */

namespace app\components\multiProcess\example;

use app\components\multiProcess\base\Worker;

class Demo_2 implements Worker
{
    private $params = '';

    public function __construct($params)
    {
        $this->params = $params;
    }

    public function run()
    {
        sleep(rand(0, 1));
        var_dump($this->params);
        $key = 'test_demo_3' . $this->params;
        \Yii::$app->redis->set($key, '1111');
        \Yii::$app->redis->expire($key, 300);
        echo \Yii::$app->redis->get($key);
    }
}