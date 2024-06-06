# FFmpeg开发助手 for PHP 
## ffmpeg-helper

### 安装
```
composer require liweishan/ffmpeg-helper
```

### 使用示例

```php
    //配置命令路径(可选)
    FFmpegHelper::setConfig([
        'ffmpegPath' => '/opt/homebrew/bin/ffmpeg',
        'ffprobePath' => '/opt/homebrew/bin/ffprobe'
    ]);
    
    //截取视频封面(指定帧或时间）
    $from = '/Users/lws/Desktop/111.mp4';
    $to = '/Users/lws/Desktop/111.png';
    $res = FFmpegHelper::getVideoCoverImage($from, $to, 1, null);
    var_dump($res);die;
    
    //获取视频帧率
    $path = '/Users/lws/Desktop/111.mp4';
    $res = FFmpegHelper::getVideoFrameRate($path):
    var_dump($res);die;
    
    //获取视频时长、大小
    $path = '/Users/lws/Desktop/111.mp4';
    $res = FFmpegHelper::getMediaInfo($path):
    var_dump($res);die;
    
    //视频裁切
    $from = '/Users/lws/Desktop/111.mp4';
    $to = '/Users/lws/Desktop/222.mp4';
    $res = FFmpegHelper::cutVideo($from, $to, '0.0.0.0', '0.0.0.15'):
    var_dump($res);die;
        
    //音频格式转换
    $from = '/Users/lws/Desktop/111.wav';
    $to = '/Users/lws/Desktop/111.mp3';
    $res = FFmpegHelper::convertMusic($from, $to);
    var_dump($res);die;
    
    //视频格式转换
    $from = '/Users/lws/Desktop/111.mp4';
    $to = '/Users/lws/Desktop/111.avi';
    $res = FFmpegHelper::convertVideo($from, $to);
    var_dump($res);die;
    
    //合并音频
    $paths = [
        '/Users/lws/Desktop/111.mp3',
        '/Users/lws/Desktop/222.mp3',
    ];
    $to = '/Users/lws/Desktop/333.mp3';
    $res = FFmpegHelper::concatMusics($paths,$to);
    var_dump($res);die;
    
    //合并视频
    $paths = [
        '/Users/lws/Desktop/111.mp4',
        '/Users/lws/Desktop/222.mp4',
    ];
    $to = '/Users/lws/Desktop/333.mp4';
    $res = FFmpegHelper::concatVideos($paths,$to);
    var_dump($res);die;

```