<?php
/**
 * 考试题库
 * 获取文件类型
**/

function getMimeType( $filename ) {
    if ($filename[0] == '/') {
        $realpath = $filename;
    } else {
        $realpath = realpath( $filename );
    }

    if (!file_exists($realpath)) {
        return 'other/noexist';
    }

    if (  $realpath
            && function_exists( 'finfo_file' )
            && function_exists( 'finfo_open' ) 
            && defined( 'FILEINFO_MIME_TYPE' )
    ) {
        // Use the Fileinfo PECL extension (PHP 5.3+)
        return finfo_file( finfo_open( FILEINFO_MIME_TYPE ), $realpath);
    }

    if ( function_exists( 'mime_content_type' ) ) {
        // Deprecated in PHP 5.3
        return mime_content_type( $realpath );
    }

    if ( function_exists( 'getimagesize' ) ) {
        $r = getimagesize($realpath);
        if ( $r ) {
            return $r['mime'];
        }
    }

    $mime_type = array(
        'mp4' => 'video/mp4',
        'jpg' => 'image/jpg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'png' => 'image/png',
    );
    $extension = strtolower( pathinfo( $realpath, PATHINFO_EXTENSION ) );
    if ( isset( $mime_type[$extension] ) ) {
        return $mime_type[$extension];
    }

    return false;
}
?>
