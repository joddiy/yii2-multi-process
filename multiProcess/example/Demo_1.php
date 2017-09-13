<?php
/**
 * author:       joddiyzhang <joddiyzhang@gmail.com>
 * createTime:   2017/8/2 下午4:26
 */

namespace app\components\multiProcess\example;

use app\components\multiProcess\base\Worker;

class Demo_1 implements Worker
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
        $sql = <<<EOF
INSERT INTO test (data) VALUE ({$this->params});
EOF;
        \Yii::$app->getDb()->createCommand($sql)->execute();
    }
}