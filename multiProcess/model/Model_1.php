<?php
/**
 * author:       joddiyzhang <joddiyzhang@gmail.com>
 * createTime:   2017/8/2 下午4:09
 */

namespace app\components\multiProcess\model;

use app\services\baseData\base\BaseLog;
use app\components\multiProcess\base\DbConnection;
use app\components\multiProcess\base\ULog;
use app\components\multiProcess\base\Worker;

/**
 * 模型1，只需要子进程工作，不限制最大进程数
 * $model = new Model_1();
 * for ($i = 0; $i < 100; $i++) {
 *     $model->run(new Demo_1($i));
 * }
 *
 */
class Model_1
{
    /**
     * 执行
     * @param Worker $worker
     */
    public function run($worker)
    {
        // 多进程操作
        $pid = pcntl_fork();
        if ($pid < 0) {
            die('Fork the process failed.');
        } else if ($pid == 0) {
            $c_pid = pcntl_fork();
            if ($c_pid < 0) {
                die('Fork the process failed.');
            } else if ($c_pid > 0) {
                // 二代父进程直接退出，使二代子进程成为孤儿进程，从而被 init 收养
                exit(0);
            } else {
                // 工作进程
                try {
                    ULog::stdout("fork a new worker");
                    DbConnection::openAll();
                    $worker->run();
                } catch (\Exception $e) {
                    BaseLog::stdout("Running process failed, reason:" . $e->getMessage());
                } finally {
                    // 关闭全部链接
                    DbConnection::closeAll();
                    exit(0);
                }
            }
        } else {
            //一代父进程回收二代父进程后继续运行
            pcntl_wait($status);
        }
    }

}