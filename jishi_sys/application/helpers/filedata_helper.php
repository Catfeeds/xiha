<?php
/**
 * filedata
 *
 * file_web_path 计算资源文件的web访问路径
 * file_local_path 计算资源文件的本地路径
 */
if ( ! function_exists('file_web_path'))
{
    /**
     * @param $path
     */
    function file_web_path($path, $prefix = '')
    {
        if ('' === $prefix)
        {
            $s = sprintf('%s%s', base_url(), $path);
        }
        else
        {
            $s = sprintf('%s%s/%s', base_url(), $prefix, $path);
        }
        return $s;
    }
}
