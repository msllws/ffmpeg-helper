<?php

namespace Lws;

class FFmpegHelper
{
    //ffmpeg命令路径
    public static $ffmpegPath;
    public static $ffprobePath;

    //配置信息
    public static function setConfig($config) {
        self::$ffmpegPath = $config['ffmpegPath'] ?? 'ffmpeg';
        self::$ffprobePath = $config['ffprobePath'] ?? 'ffprobe';
    }

    //截取视频封面(可指定第几帧 或 指定时间)
    public static function getCoverImage($from, $to, $frameNum = 1, $timeStamp = null)
    {
        //判断根据时间还是第几帧取
        if(empty($timeStamp)){
            if($frameNum == 1){
                $timeStamp = '00:00:00.000';
            }else{
                //帧率
                $frameRate = self::getFrameRate($from);
                //根据帧率 计算指定帧出现时间
                $timeStamp = self::getFrameTime($frameNum, $frameRate);
            }
        }
        $command = self::$ffmpegPath . " -y -i {$from} -ss {$timeStamp} -vframes 1 {$to}";
        exec($command, $output, $return_code);
        if($return_code === 0) return true;
        return false;
    }

    //获取视频帧率
    public static function getFrameRate($path)
    {
        $command = self::$ffprobePath . " -v error -select_streams v:0 -show_entries stream=r_frame_rate -of default=noprint_wrappers=1:nokey=1 {$path}";
        exec($command, $output, $return_code);
        if($return_code === 0){
            return (int)explode('/', $output[0])[0];
        }
        return 0;
    }

    //获取视频时长
    public static function getDurationSeconds($path)
    {
        $ffprobeOutput = shell_exec(self::$ffprobePath . " -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 {$path}");
        return floatval(trim($ffprobeOutput));
    }

    //根据帧率 计算指定帧出现时间(参数：帧数、帧率)
    public static function getFrameTime($frameNum, $frameRate) {
        if ($frameNum == 1) {
            return '00:00:00.000';
        } else {
            // 计算时间（秒）
            $timeInSeconds = ($frameNum - 1) / $frameRate;
            // 转换为毫秒并格式化
            $milliseconds = intval(round($timeInSeconds * 1000));
            $hours = floor($milliseconds / 3600000);
            $milliseconds %= 3600000;
            $minutes = floor($milliseconds / 60000);
            $milliseconds %= 60000;
            $seconds = floor($milliseconds / 1000);
            $milliseconds %= 1000;
            // 格式化为HH:MM:SS.mmm
            return str_pad($hours, 2, '0', STR_PAD_LEFT) . ':' . str_pad($minutes, 2, '0', STR_PAD_LEFT) . ':' . str_pad($seconds, 2, '0', STR_PAD_LEFT) . '.' . str_pad($milliseconds, 3, '0', STR_PAD_LEFT);
        }
    }
}