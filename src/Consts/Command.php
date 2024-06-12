<?php

/** 截取视频封面 **/
const CUT_COVER_IMAGE = '%s -y -i %s -ss %s -vframes 1 %s';

/** 获取视频帧率 **/
const GET_FRAME_RATE = '%s -v error -select_streams v:0 -show_entries stream=r_frame_rate -of default=noprint_wrappers=1:nokey=1 %s';

/** 获取视频时长 **/
const GET_VIDEO_DURATION = '%s -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 %s';

/** 裁切视频 **/
const CUT_VIDEO = '%s -i %s -ss %s -t %s -c copy -y %s';

/** 音频格式转换 **/
const CONVERT_MUSIC = '%s -i %s -vn -c:a libmp3lame -qscale:a 2 -y %s';

/** 视频格式转换 **/
const CONVERT_VIDEO = '%s -i %s -c:v libx264 -preset slow -crf 23 -c:a aac -b:a 128k -y %s';

/** 合并音频 **/
const CONCAT_MUSIC = '%s -i concat:\'%s\' -c copy -y %s';

/** 合并视频 **/
const CONCAT_VIDEO = '%s -safe 0 -f concat -i %s -c copy -y %s';

/** 获取视频流信息 **/
const VIDEO_STREAMS = '%s -v error -show_streams -print_format json %s';

/** 视频添加音频流信息 **/
const VIDEO_ADD_SOUND_STREAMS = '%s -i %s -f lavfi -i anullsrc=channel_layout=stereo:sample_rate=44100 -c:v copy -map 0:v:0 -map 1:a:0 -shortest %s';

/** 视频添加图片 **/
const VIDEO_ADD_IMAGE = "%s -i %s %s -i %s -filter_complex \"[0:v][1:v]overlay=x=%s:y=%s%s\" %s -y %s";

/** 视频添加音频 **/
const VIDEO_ADD_MUSIC = "%s -i %s -i %s -filter_complex \"[0:a]volume=1[0];[1:a]volume=1[1];[0][1]amix=inputs=2\" -y %s";

/** 视频叠加视频 **/
const VIDEO_ADD_VIDEO = '%s -i %s -i %s -filter_complex "[1:v]scale=%s:%s[1v];[0:v][1v]overlay=%s:(main_h-overlay_h):enable=\'between(t,0,%s)\'" -y %s';

/** 视频合并并添加转场特效 **/
const VIDEO_ADD_XFADE = '%s -i %s -i %s -filter_complex "[0:v][1:v]xfade=transition=%s:duration=0.6:offset=%s,format=yuv420p[v];[0:a][1:a]acrossfade=d=1[a]" -map "[v]" -map "[a]" -y %s';

/** 设置视频速率 **/
//const SET_VIDEO_RATE = '%s -i %s -filter_complex "setpts=%s*PTS" %s';

const SET_VIDEO_RATE = '%s -i %s -filter_complex "[0:v]setpts=%s*PTS[v];[0:a]atempo=%s[a]" -map "[v]" -map "[a]" -c:v libx264 -preset veryfast -crf 18 -c:a aac -b:a 128k -y %s';