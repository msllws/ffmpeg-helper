<?php

//构造并执行命令
if (!function_exists('doExec')) {
    function doExec(string $command, ...$args): array
    {
        //构造命令
        if (count($args) > 0) {
            $command = sprintf($command, ...$args);
            $command = stripslashes($command);
        }
        echo 'FFmpegHelper==command=='.$command."\n";
        try {
            exec($command, $output, $return_code);
            return [
                'code' => $return_code,
                'data' => $output
            ];
        } catch (\Exception $e) {
            var_dump($e);
            return [
                'code' => 500,
                'data' => []
            ];
        }
    }
}

//根据帧率 计算指定帧出现时间(参数：帧数、帧率)
if (!function_exists('getFrameTime')) {
    function getFrameTime($frameNum, $frameRate): string
    {
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

//根据文件名 获取新的随机文件地址 保留原文件格式
if (!function_exists('getNewFile')) {
    function getNewFile(string $path): string
    {
        //获取原始文件的扩展名
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        //创建一个新的临时文件
        $to = tempnam(sys_get_temp_dir(), 'file_') . '.' . $ext;
        return $to;
    }
}

