# FFmpeg开发助手 for PHP 
## ffmpeg-helper

### 安装
```
composer require liweishan/ffmpeg-helper 1.0
```

### 示例

(更新中......)

```php
    //配置命令路径(可选)
    FFmpegHelper::setConfig([
        'ffmpegPath' => '/opt/homebrew/bin/ffmpeg',
        'ffprobePath' => '/opt/homebrew/bin/ffprobe'
    ]);
    
    //------截取视频指定帧------
    $from = '/Users/lws/Desktop/111.mp4';
    $to = '/Users/lws/Desktop/111.png';
    $res = FFmpegHelper::getVideoCoverImage($from, $to, 1);
    var_dump($res);die;

```