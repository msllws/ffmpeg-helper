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
    
    //校验视频流 生成音轨（如果返回新的文件路径 用完记得删除）
    $from = '/Users/lws/Desktop/111.mp4';
    $res = FFmpegHelper::checkVideoStreams($from);
    var_dump($res);
    
    //视频添加图片
    $from = '/Users/lws/Desktop/111.mp4';
    $to = '/Users/lws/Desktop/222.mp4';
    $img = '/Users/lws/Desktop/111.gif';
    $res = FFmpegHelper::videoAddImage($from, $to, $img, 0, '220', '400');
    var_dump($res);
    
    //视频添加音频
    $from = '/Users/lws/Desktop/111.mp4';
    $to = '/Users/lws/Desktop/222.mp4';
    $music = '/Users/lws/Desktop/111.mp3';
    $res = FFmpegHelper::videoAddMusic($from, $to, $music);
    var_dump($res);


```

### 执行自定义命令

```php
    
    //方式一：命令(参数用%s占位) + 可变参数
    $ffmpegPath = '/opt/homebrew/bin/ffmpeg';
    $from = '/Users/lws/Desktop/111.mp4';
    $to = '/Users/lws/Desktop/111.png';
    $timeStamp = '00:00:00.000';
    $res = FFmpegHelper::exec('%s -y -i %s -ss %s -vframes 1 %s', $ffmpegPath, $from, $timeStamp, $to);
    var_dump($res);
    
    //方式二：纯命令
    $res = FFmpegHelper::exec('/opt/homebrew/bin/ffmpeg -y -i /Users/lws/Desktop/111.mp4 -ss 00:00:00.000 -vframes 1 /Users/lws/Desktop/333.png');
    var_dump($res);
    
    //返回数组格式
    ['code' => 0, 'data' => []]

```