<?php
/**
 * author:       joddiyzhang <joddiyzhang@qq.com>
 * createTime:   2017/8/2 下午4:18
 * fileName :    Worker.php
 */

namespace app\components\multiProcess\base;

/**
 * Interface Worker
 * @package app\components\multiProcess
 */
interface Worker
{
    /**
     * 执行方法
     * @return mixed
     */
    public function run();
}