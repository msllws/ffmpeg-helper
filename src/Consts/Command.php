<?php

/** 截取视频封面 **/
const CUT_COVER_IMAGE = '%s -y -i %s -ss %s -vframes 1 %s';

/** 获取视频帧率 **/
const GET_FRAME_RATE = '%s -v error -select_streams v:0 -show_entries stream=r_frame_rate -of default=noprint_wrappers=1:nokey=1 %s';

/** 获取视频时长 **/
const GET_VIDEO_DURATION = '%s -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 %s';

/** 裁切视频 **/
const CUT_VIDEO = '%s -i %s -ss %s -t %s -c copy %s';

