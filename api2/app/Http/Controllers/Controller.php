<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Str;
use Laravel\Lumen\Routing\Controller as BaseController;
use Log;

class Controller extends BaseController
{
    /**
     * @var
     */
    public $redis;

    public function __construct()
    {
        $this->redisInstance();
    }

    /**
     * 多维数组排序.
     *
     * @param mixed $arr
     * @param mixed $field
     * @param mixed $sort
     **/
    public function multiArraySort($arr, $field, $sort = 'SORT_ASC')
    {
        $sort = [
            'direction' => $sort,
            'field' => $field,
        ];

        // 多为数组根据某个字段排序
        $arrsort = [];
        foreach ($arr as $index => $row) {
            foreach ($row as $key => $value) {
                $arrsort[$key][$index] = $value;
            }
        }

        if ($sort['direction']) {
            array_multisort($arrsort[$sort['field']], constant($sort['direction']), $arr);
        }

        return $arr;
    }

    /**
     * 文件的路径.
     *
     * @param string $url
     */
    public function buildUrl($url)
    {
        // 空路径
        if (empty($url)) {
            return '';
        }

        // 将upload前辍去除
        if (Str::startsWith($url, '../upload/')) {
            $url = Str::replaceFirst('../upload/', '', $url);
        }
        if (Str::startsWith($url, 'upload/')) {
            $url = Str::replaceFirst('upload/', '', $url);
        }

        // 文件真实有效性
        if (file_exists(implode([app()->UPLOAD_PATH, $url]))) {
            return implode([env('APP_UPLOAD_PATH'), $url]);
        }
        if (file_exists(implode([app()->UPLOAD_PATH, '../admin/upload/', $url]))) {
            return implode([env('APP_UPLOAD_PATH'), '../admin/upload/', $url]);
        }
        if (file_exists(implode([app()->UPLOAD_PATH, '../sadmin/upload/', $url]))) {
            return implode([env('APP_UPLOAD_PATH'), '../sadmin/upload/', $url]);
        }

        return '';
    }

    // 兼容中英文版的json格式
    public function JSON($array)
    {
        $this->arrayRecursive($array, 'urlencode', true);
        $json = json_encode($array);

        return urldecode($json);
    }

    public function arrayRecursive(&$array, $function, $apply_to_keys_also = false)
    {
        static $recursive_counter = 0;
        if (++$recursive_counter > 1000) {
            die('possible deep recursion attack');
        }
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                arrayRecursive($array[$key], $function, $apply_to_keys_also);
            } else {
                $array[$key] = $function($value);
            }

            if ($apply_to_keys_also && is_string($key)) {
                $new_key = $function($key);
                if ($new_key !== $key) {
                    $array[$new_key] = $array[$key];
                    unset($array[$key]);
                }
            }
        }
        --$recursive_counter;
    }

    // 生成唯一码
    public function guid($opt = true)
    {
        //  Set to true/false as your default way to do this.
        if (function_exists('com_create_guid')) {
            if ($opt) {
                return com_create_guid();
            }

            return trim(com_create_guid(), '{}');
        }
        mt_srand((float) microtime() * 10000);    // optional for php 4.2.0 and up.
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45);    // "-"
            $left_curly = $opt ? chr(123) : '';     //  "{"
            $right_curly = $opt ? chr(125) : '';    //  "}"
            $uuid = $left_curly
                .substr($charid, 0, 8).$hyphen
                .substr($charid, 8, 4).$hyphen
                .substr($charid, 12, 4).$hyphen
                .substr($charid, 16, 4).$hyphen
                .substr($charid, 20, 12)
                .$right_curly;

        return $uuid;
    }

    /**
     * 获取随机位数数字.
     *
     * @param int $len 长度
     *
     * @return string
     **/
    public function randNumber($len = 6)
    {
        $chars = str_repeat('0123456789', 10);
        $chars = str_shuffle($chars);
        $str = substr($chars, 0, $len);

        return $str;
    }

    /**
     * 上传文件.
     *
     * @param string $url
     * @param mixed  $fieldname
     * @param mixed  $rootPath
     * @param mixed  $dir
     * @param mixed  $prefix
     * @param mixed  $exts
     */
    public function uploadFile($fieldname, $rootPath, $dir, $prefix, $exts = ['png', 'jpg', 'jpeg'])
    {
        if (!$fieldname || !$dir) {
            return false;
        }

        if ($this->request->hasFile($fieldname)) {
            // 判断图片类型
            $field_name = $this->request->file($fieldname);
            $upload_file_ext = $field_name->getClientMimeType();
            $upload_file_ext_arr = explode('/', $upload_file_ext);
            if (!in_array($upload_file_ext_arr[1], $exts, true)) {
                return false;
            }

            // 创建文件路径
            $path = app()->UPLOAD_PATH;
            $url = $path.$dir;
            if (is_dir($url)) {
                $img_url = $url;
            } else {
                if (mkdir($url, 0777, true)) {
                    $img_url = $url;
                }
            }

            try {
                $result = $this->request->file($fieldname)->move($img_url, $prefix.'.'.$upload_file_ext_arr[1]);
                $field_value = $rootPath.$dir.'/'.$prefix.'.'.$upload_file_ext_arr[1];

                return $field_value;
            } catch (Exception $e) {
                Log::Info('File:'.$e->getFile().'Line:'.$e->getLine().',Error:'.$e->getMessage());
            }
        } else {
            return false;
        }
    }

    // 多文件上传
    public function uploadFiles($fieldname, $prefix, $dir)
    {
        if (!$fieldname && !$prefix && !$dir) {
            return false;
        }
        $nowtime = time();
        $result = [];
        if ($fieldname) {
            $files_name = $fieldname['name'];
            if (is_array($files_name)) {
                if ($files_name) {
                    foreach ($files_name as $files_index => $files_value) {
                        if ($fieldname['error'][$files_index] === UPLOAD_ERR_OK) {
                            $tmp_name = $fieldname['tmp_name'][$files_index];
                            $prefix_name = substr($tmp_name, -6, 6);
                            $name = $prefix.$nowtime.$prefix_name; // 文件名前缀
                            // 判断图片类型
                            $exts_type = $fieldname['type'][$files_index];
                            $exts_type_arr = explode('/', $exts_type);
                            $exts_name = $name.'.'.$exts_type_arr[1];
                            $exts = ['jpg', 'png', 'jpeg', 'gif'];
                            if (!in_array($exts_type_arr[1], $exts, true)) {
                                return false;
                            }

                            // 创建文件路径
                            $path = app()->UPLOAD_PATH;
                            $url = $path.$dir;
                            if (is_dir($url)) {
                                $img_url = $url;
                            } else {
                                if (mkdir($url, 0777, true)) {
                                    $img_url = $url;
                                }
                            }
                            $save_ok = move_uploaded_file($tmp_name, $img_url.$exts_name);
                            if (true === $save_ok) {
                                $result[] = 'upload/'.$dir.$exts_name;
                            }
                        }
                    }
                }
            } else {
                if (UPLOAD_ERR_OK === $fieldname['error']) {
                    $tmp_name = $fieldname['tmp_name'];
                    $prefix_name = substr($tmp_name, -6, 6);
                    $name = $prefix.$nowtime.$prefix_name; // 文件名前缀
                    // 判断图片类型
                    $exts_type = $fieldname['type'];
                    $exts_type_arr = explode('/', $exts_type);
                    $exts_name = $name.'.'.$exts_type_arr[1];
                    $exts = ['jpg', 'png', 'jpeg', 'gif'];
                    if (!in_array($exts_type_arr[1], $exts, true)) {
                        return false;
                    }

                    // 创建文件路径
                    $path = app()->UPLOAD_PATH;
                    $url = $path.$dir;
                    if (is_dir($url)) {
                        $img_url = $url;
                    } else {
                        if (mkdir($url, 0777, true)) {
                            $img_url = $url;
                        }
                    }
                    $save_ok = move_uploaded_file($tmp_name, $img_url.$exts_name);
                    if (true === $save_ok) {
                        $result[] = 'upload/'.$dir.$exts_name;
                    }
                }
            }

            return $result;
        }
    }

    private function redisInstance()
    {
        $redisConfig = [
            'host' => '127.0.0.1',
            'port' => 6379,
            'pass' => 'dalinux',
        ];

        try {
            $this->redis = new \Redis();
            $this->redis->connect($redisConfig['host'], $redisConfig['port']);
            $this->redis->auth($redisConfig['pass']);
        } catch (Exception $e) {
            Log::Info($e->getMessage().':'.$e->getLine());
        }
    }
}
