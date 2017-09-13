<?php
/**
 * author:       joddiyzhang <joddiyzhang@gmail.com>
 * createTime:   2017/8/2 下午4:48
 */

namespace app\components\multiProcess\base;

use Yii;

/**
 * Class DbConnection
 * @package app\components\multiProcess
 */
class DbConnection
{

    /**
     * 关闭所有 DB
     */
    public static function closeAll()
    {
        try {
            $components = \Yii::$app->getComponents();
            foreach ($components as $key => $item) {
                switch ($item['class']) {
                    case 'yii\db\Connection':
                        Yii::$app->$key->close();
                        break;
                    case 'app\components\xmredis\Connection':
                        Yii::$app->redis->disconnect();
                        break;
                }
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }


    /**
     * 打开所有 DB
     */
    public static function openAll()
    {
        try {
            $components = \Yii::$app->getComponents();
            foreach ($components as $key => $item) {
                switch ($item['class']) {
                    case 'yii\db\Connection':
                        Yii::$app->$key = Yii::createObject($item);
                        Yii::$app->$key->open();
                        break;
                    case 'app\components\xmredis\Connection':
                        Yii::$app->$key = Yii::createObject($item);
                        Yii::$app->redis->connect();
                        break;
                }
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}