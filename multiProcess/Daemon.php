<?php
/**
 * author:       joddiyzhang <joddiyzhang@qq.com>
 * createTime:   2017/8/2 下午3:42
 */

namespace app\components\multiProcess;
use app\components\multiProcess\base\ULog;
use app\components\multiProcess\base\Worker;

/**
 * Daemon 入口命令
 * Class Daemon
 */
class Daemon
{

    /**
     * 平滑重启
     * @param $pidFile
     * @param Worker $schedule
     */
    public function restart($pidFile, $schedule)
    {
        $this->stop($pidFile);
        while ($this->isAlive($pidFile)) {
            sleep(1);
        }
        $this->start($pidFile, $schedule);
    }


    /**
     * 发送信号通知进程结束
     * @param $pidFile
     */
    public function stop($pidFile)
    {
        if (file_exists($pidFile)) {
            $filePid = file_get_contents($pidFile);
            if (file_exists("/proc/$filePid")) {
                posix_kill($filePid, SIGHUP);
                ULog::stdout("Send stop signal successfully.");
                return;
            }
        }
        ULog::stdout("The Schedule is not alive.");
    }

    /**
     * 启动
     * @param $pidFile
     * @param Worker $schedule
     */
    private function start($pidFile, $schedule)
    {
        $shouldRun = true;
        // 注册信号处理函数，传入 $shouldRun 的地址
        pcntl_signal(SIGHUP, function ($signo) use (&$shouldRun) {
            $shouldRun = false;
        });
        if ($this->writePID($pidFile)) {
            // 收到信号后的下一轮退出
            while ($shouldRun) {
                // 检查信号
                pcntl_signal_dispatch();
                $schedule->run();
                sleep(1);
            }
            ULog::stdout("Schedule has exited.");
        }
    }


    /**
     * 向文件中写入当前进程 PID
     * @param $pidFile
     * @return bool
     */
    private function writePID($pidFile)
    {
        // 进程存活
        if ($this->isAlive($pidFile)) {
            return false;
        }
        // 否则加锁并写入最新进程号
        $fp = fopen($pidFile, 'w+');
        if (flock($fp, LOCK_EX | LOCK_NB)) {
            fwrite($fp, posix_getpid());
            fflush($fp);
            flock($fp, LOCK_UN);
        } else {
            ULog::stdout("Some other Daemon is running, return for it.");
            return false;
        }
        fclose($fp);
        ULog::stdout("The Schedule is dead, starting a new one.");
        return true;
    }

    /**
     * 进程是否存活
     * @param $pidFile
     * @return bool
     */
    private function isAlive($pidFile)
    {
        // 文件存在 且 进程存活
        if (file_exists($pidFile)) {
            $pid = file_get_contents($pidFile);
            if (!empty($pid) && file_exists("/proc/$pid")) {
                ULog::stdout("The Schedule is still alive, sleep.");
                return true;
            }
        }
        return false;
    }

}