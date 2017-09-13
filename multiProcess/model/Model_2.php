<?php
/**
 * author:       joddiyzhang <joddiyzhang@gmail.com>
 * createTime:   2017/8/2 下午4:48
 */


namespace app\components\multiProcess\model;

use app\components\multiProcess\base\DbConnection;
use app\components\multiProcess\base\ULog;

/**
 * 模型2，规定进程数上限，超过时等待回收
 *
 * $model = new Model_2(10, 'app\services\MultiProcess\Demo\Demo_1');
 * for  * ($i = 0; $i < 100; $i++) {
 *      $model->push($i);
 * }
 * $model->run();
 * $model->waitStop();
 */
class Model_2
{

    /**
     * 同时最大进程数
     * @var
     */
    private $max;

    /**
     * work类空间地址
     * @var
     */
    private $class_path;

    /**
     * 当前进程数
     * @var int
     */
    private $cnt = 0;

    /**
     * 任务队列
     * @var array
     */
    public $task_list = array();

    /**
     * Model_2 constructor.
     * @param $max int 同时最大进程数
     * @param $class_path string work类空间地址
     */
    public function __construct($max, $class_path)
    {
        $this->max = $max;
        $this->class_path = $class_path;
    }

    /**
     * 等待最后的子进程退出
     */
    public function waitStop()
    {
        ULog::stdout("All tasks have been consumed, waiting for exit");
        pcntl_signal_dispatch();
        while ($this->cnt > 0) {
            pcntl_wait($status);
            $this->cnt--;
            ULog::stdout("One worker exited, there still are $this->cnt workers");
        }
        ULog::stdout("All workers exited");
    }

    /**
     * 向队列中塞入任务
     * @param $task
     */
    public function push($task)
    {
        $this->task_list[] = $task;
    }

    /**
     * 执行
     * @return int
     */
    public function run()
    {
        ULog::stdout("got " . count($this->task_list) . " tasks, now start consuming");
        pcntl_signal_dispatch();
        while (!empty($this->task_list)) {
            while (pcntl_wait($status, WNOHANG) > 0) {
                $this->cnt--;
            }
            // 如果当前进程数目超过最大进程数等待子进程退出
            if ($this->cnt >= $this->max) {
                usleep(10);
            } else {
                $task = array_shift($this->task_list);
                $this->cnt++;
                $pid = pcntl_fork();
                if ($pid < 0) {
                    die('Fork the process failed.');
                } else if ($pid == 0) {
                    // 工作进程
                    try {
                        ULog::stdout("fork a new worker for task: " . $task);
                        DbConnection::openAll();
                        $worker = new $this->class_path($task);
                        $worker->run();
                    } catch (\Exception $e) {
                        ULog::stdout("Running process failed, reason:" . $e->getMessage());
                    } finally {
                        // 关闭全部链接
                        DbConnection::closeAll();
                        exit(0);
                    }
                } else {
                    continue;
                }
            }
        }
    }
}