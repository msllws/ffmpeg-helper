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
     * 执行自定义命令
     * @param string $command 命令 参数用%s占位
     * @param array $args 可变数量参数
     * @return array
     */
    public static function exec($command, ...$args) : array
    {
        return doExec($command, ...$args);
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
        if (file_exists($to)) unlink($to);

        //判断根据时间还是第几帧取
        if (empty($timeStamp)) {
            if ($frameNum == 1) {
                $timeStamp = '00:00:00.000';
            } else {
                //帧率
                $frameRate = self::getVideoFrameRate($from);
                //根据帧率 计算指定帧出现时间
                $timeStamp = getFrameTime($frameNum, $frameRate);
            }
        }
        $res = doExec(CUT_COVER_IMAGE, self::$ffmpegPath, $from, $timeStamp, $to);
        return $res['code'] === 0;
    }

    /**
     * 获取视频帧率
     * @param string $path 视频路径
     * @return int
     */
    public static function getVideoFrameRate($path): int
    {
        $res = doExec(GET_FRAME_RATE, self::$ffprobePath, $path);
        if ($res['code'] === 0) {
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
            'size' => round((filesize($path) / 1024 / 1024), 2) //大小 单位：MB
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
    public static function cutVideo($from, $to, $startTime, $duration): bool
    {
        if (file_exists($to)) unlink($to);
        $res = doExec(CUT_VIDEO, self::$ffmpegPath, $from, $startTime, $duration, $to);
        return $res['code'] === 0;
    }

    /**
     * 音频格式转换
     * @param string $from 原音频文件
     * @param string $to 目标音频文件
     * @return bool
     */
    public static function convertMusic($from, $to): bool
    {
        if (file_exists($to)) unlink($to);
        $res = doExec(CONVERT_MUSIC, self::$ffmpegPath, $from, $to);
        return $res['code'] === 0;
    }

    /**
     * 视频格式转换
     * @param string $from 原视频文件
     * @param string $to 目标视频文件
     * @return bool
     */
    public static function convertVideo($from, $to): bool
    {
        if (file_exists($to)) unlink($to);
        $res = doExec(CONVERT_VIDEO, self::$ffmpegPath, $from, $to);
        return $res['code'] === 0;
    }

    /**
     * 合并音频
     *
     * @param array $paths 音频文件数组
     * @param string $to 目标文件路径
     * @return bool
     */
    public static function concatMusics($paths, $to): bool
    {
        if (file_exists($to)) unlink($to);
        $audioList = implode("|", array_map('escapeshellarg', $paths));
        $res = doExec(CONCAT_MUSIC, self::$ffmpegPath, $audioList, $to);
        return $res['code'] === 0;
    }

    /**
     * 合并视频
     * @param array $paths 视频文件数组
     * @param string $to 目标文件路径
     * @return bool
     */
    public static function concatVideos($paths, $to): bool
    {
        if (file_exists($to)) unlink($to);
        //创建视频列表临时文件
        $tempFilePath = tempnam(sys_get_temp_dir(), 'ffmpeg-helper_temp_list.txt');
        //打开临时文件并写入文件列表
        $videoListContent = '';
        foreach ($paths as $videoFile) {
            $videoListContent .= "file '$videoFile'\n";
        }
        if (file_put_contents($tempFilePath, $videoListContent) === false) {
            unlink($tempFilePath); // 清理临时文件
            echo "FFmpegHelper==Failed to write to temp file";
            return false;
        }
        $res = doExec(CONCAT_VIDEO, self::$ffmpegPath, $tempFilePath, $to)['code'];
        unlink($tempFilePath); // 清理临时文件
        return $res === 0;
    }

    /**
     * 校验视频流
     *（如果有音频流信息 直接返回原视频路径，如果没有音频流信息 添加音频流并返回新的文件路径）
     * @param $path
     * @return string
     */
    public static function checkVideoStreams($path): string
    {
        $streamInfo = doExec(VIDEO_STREAMS, self::$ffprobePath, $path);
        if($streamInfo['code'] === 0 && count($streamInfo['data'][0]['streams']) < 2){
            $streams = (json_decode(implode('', $streamInfo['data']), true))['streams'];
            if(count($streams) < 2){ //无音频
                $to = getNewFile($path);
                $res = doExec(VIDEO_ADD_SOUND_STREAMS, self::$ffmpegPath, $path, $to);
                if($res['code'] === 0){
                    return $to;
                }
            }
        }
        return $path;
    }

    /**
     * 视频添加图片
     * @param string $path 原视频地址
     * @param string $to 目标视频地址
     * @param string $img 图片地址
     * @param int $loop 是否循环播放
     * @param string $x 横坐标
     * @param string $y 纵坐标
     * @return bool
     */
    public static function videoAddImage($path, $to, $img, $loop, $x, $y) : bool
    {
        $loopA = ''; //图片循环
        $enable = ''; //图片持续时间
        if($loop == 1){
            $loopA = '-stream_loop -1';
        }else{
            $enable = ":enable='between(t,0,2)'";
        }
        $video_time = '-ss 00 -t ' . self::getMediaInfo($path)['duration']; //视频时间
        $res = doExec(VIDEO_ADD_IMAGE, self::$ffmpegPath, $path, $loopA, $img, $x, $y, $enable, $video_time, $to);
        return $res['code'] === 0;
    }

    /**
     * 视频添加音频
     * @param string $path 原视频地址
     * @param string $to 目标视频地址
     * @param string $music 音频地址
     * @return bool
     */
    public static function videoAddMusic($path, $to, $music) : bool
    {
        $res = doExec(VIDEO_ADD_MUSIC, self::$ffmpegPath, $path, $music, $to);
        return $res['code'] === 0;
    }
}

FFmpegHelper::setConfig([
    'ffmpegPath' => '/opt/homebrew/bin/ffmpeg',
    'ffprobePath' => '/opt/homebrew/bin/ffprobe'
]);
$from = '/Users/lws/Desktop/111.mp4';
$to = '/Users/lws/Desktop/333.png';
$img = '/Users/lws/Desktop/111.gif';
$music = '/Users/lws/Desktop/111.mp3';

$res = FFmpegHelper::exec('%s -y -i %s -ss %s -vframes 1 %s', '/opt/homebrew/bin/ffmpeg', $from, '00:00:00.000', $to);

$res = FFmpegHelper::exec('/opt/homebrew/bin/ffmpeg -y -i /Users/lws/Desktop/111.mp4 -ss 00:00:00.000 -vframes 1 /Users/lws/Desktop/333.png');
var_dump($res);
