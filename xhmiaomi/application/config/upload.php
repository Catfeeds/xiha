<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 *-------------------------------------------------------------------------
 * 文件上传路径，绝对路径
 *-------------------------------------------------------------------------
 */
$config['upload_path'] = realpath(BASEPATH.'../../upload').DIRECTORY_SEPARATOR;

//	修改过的路径 2017年06月28日16:07:31 by 夏玉峰
// $config['upload_path'] = realpath(BASEPATH. DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . "upload") . DIRECTORY_SEPARATOR;

/**
 *-------------------------------------------------------------------------
 * 是否建子目录
 *-------------------------------------------------------------------------
 */
$config['enable_sub_dir'] = TRUE;

/**
 *-------------------------------------------------------------------------
 * 子目录格式
 *-------------------------------------------------------------------------
 *
 * eg.: field/YYYYMMDD/
 *
 */
$config['sub_dir_format'] = '%s'.DIRECTORY_SEPARATOR.'%s'.DIRECTORY_SEPARATOR;

/**
 *-------------------------------------------------------------------------
 * 文件的相对路径
 *-------------------------------------------------------------------------
 */
// $config['http_host'] = "http://60.173.247.68:50003/php/upload/";
$config['http_host'] = "http://w.xihaxueche.com:8001/service/upload/";
$config['allowed_types']        = '*';
$config['max_size']             = 102400; // 100MB
$config['max_width']            = 3840;
$config['max_height']           = 2160;
$config['min_width']            = 100;
$config['min_height']           = 100;
$config['encrypt_name']         = TRUE;
