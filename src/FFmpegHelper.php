<?php

namespace Lws;

require_once __DIR__ . '/../vendor/autoload.php';

class FFmpegHelper
{
    //配置ffmpeg命令路径
    public static string $ffmpegPath;
    public static string $ffprobePath;

    //配置信息
    public static function setConfig($config): void
    {
        self::$ffmpegPath = $config['ffmpegPath'] ?? 'ffmpeg';
        self::$ffprobePath = $config['ffprobePath'] ?? 'ffprobe';
    }

    /**
     * 截取视频封面(指定第几帧 或 指定时间)
     * @param string $from 原视频路径
     * @param string $to 封面图片路径
     * @param int $frameNum 帧数
     * @param string $timeStamp 时间(传递则直接取时间点)
     * @return bool
     */
    public static function getVideoCoverImage($from, $to, $frameNum = 1, $timeStamp = null): bool
    {
        //判断根据时间还是第几帧取
        if(empty($timeStamp)){
            if($frameNum == 1){
                $timeStamp = '00:00:00.000';
            }else{
                //帧率
                $frameRate = self::getVideoFrameRate($from);
                //根据帧率 计算指定帧出现时间
                $timeStamp = getFrameTime($frameNum, $frameRate);
            }
        }
        return doExec(CUT_COVER_IMAGE, self::$ffmpegPath, $from, $timeStamp, $to)['code'] === 0;
    }

    /**
     * 获取视频帧率
     * @param string $path 视频路径
     * @return int
     */
    public static function getVideoFrameRate($path): int
    {
        $res = doExec(GET_FRAME_RATE, self::$ffprobePath, $path);
        if($res['code'] === 0){
            return (int)explode('/', $res['data'][0])[0];
        }
        return 0;
    }

    /**
     * 获取视频时长和大小
     * @param string $path 视频路径
     * @return array
     */
    public static function getMediaInfo($path): array
    {
        //获取时长
        $res = doExec(GET_VIDEO_DURATION, self::$ffprobePath, $path);
        $duration = $res['code'] === 0 ? floatval(trim($res['data'][0])) : 0;
        return [
            'duration' => $duration, //时长 单位：秒
            'size' => round((filesize($path) / 1024 /1024) , 2) //大小 单位：MB
        ];
    }

    /**
     * 裁切视频
     * @param string $from 原文件路径
     * @param string $to 目标文件路径
     * @param string $startTime 开始时间，格式HH:MM:SS
     * @param string $duration 截取时长，格式HH:MM:SS
     * @return bool
     */
    public static function cutVideo($from, $to, $startTime, $duration) {
        if(file_exists($to)){
            unlink($to);
        }
        return doExec(CUT_VIDEO, self::$ffmpegPath, $from, $startTime, $duration, $to)['code'] === 0;
    }

    /**
     * 音频格式转换
     * @param string $from 原音频文件
     * @param string $to 目标音频文件
     * @return bool
     */
    public static function convertMusic($from, $to) {
        return doExec(CONVERT_MUSIC, self::$ffmpegPath, $from, $to)['code'] === 0;
    }

    /**
     * 视频格式转换
     * @param string $from 原视频文件
     * @param string $to 目标视频文件
     * @return bool
     */
    public static function convertVideo($from, $to) {
        return doExec(CONVERT_VIDEO, self::$ffmpegPath, $from, $to)['code'] === 0;
    }

}

FFmpegHelper::setConfig([
    'ffmpegPath' => '/opt/homebrew/bin/ffmpeg',
    'ffprobePath' => '/opt/homebrew/bin/ffprobe'
]);
$from = '/Users/lws/Desktop/111.mp4';
$to = '/Users/lws/Desktop/111.avi';
$res = FFmpegHelper::convertVideo($from, $to);
var_dump($res);die;