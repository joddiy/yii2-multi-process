<?php
/**
 * author:      YangKe <yangke@xiaomi.com>
 * createTIme:  20150514 20:57
 * fileName :   ULog.php
 */

namespace app\components\multiProcess\base;
/**
 * Class ULog    封装一些日志输入输出的方法
 *
 * @package app\services\MultiProcess;
 * @author  YangKe <yangke@xiaomi.com>
 */
class ULog
{
    /**
     *  标准输出
     *
     * @static
     * @param string     $msg     日志描述
     * @param int        $pattern 模式
     *                            $pattern = 1:输出时间列，日志列
     *                            $pattern = 2:输出带的有tag列
     * @param string|int $tag     日志的标记
     * @param bool       $return  是直接输出到标准输出还是返回
     * @return string
     */
    public static function stdout($msg, $pattern = 2, $tag = 'LOG', $return = FALSE)
    {
        $tagLen = 7;

        if (strlen($tag) < $tagLen) {
            $tag .= ':' . str_repeat(' ', $tagLen - strlen($tag));
        } else {
            $tag .= ':';
        }
        $log      = '';
        $time     = date('Y-m-d H:i:s');
        $template = '';
        switch ($pattern) {
            case 1:
                $template = "[%1\$s] %3\$s\n";
                break;
            case 2:
                $template = "[%1\$s] %2\$s %3\$s\n";
                break;
            default:
                $template = "[%1\$s] %3\$s\n";
        }
        $log = sprintf($template, $time, $tag, $msg);
        if ($return) {
            return $log;
        } else {
            echo $log;
        }
    }
} 