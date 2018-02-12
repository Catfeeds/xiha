<?php 
namespace Admin\Model;
use Think\Model;
use Think\Page;
/**
 * 车辆管理模型类
 *
 * @return 
 * @author 
 **/
class BaseModel extends Model {

	
/**
 * 分页方法
 *
 * @return string $show
 * @author Sun
 **/
	public function getPage($count, $p=10, $param=array()) {
		$Page = new Page($count, $p, $param);  
   		$Page->lastSuffix=false;
   		$Page->setConfig('first','首页');
		$Page->setConfig('prev','上一页'); 
		$Page->setConfig('next','下一页');
		$Page->setConfig('last','末页');
		$Page->setConfig('theme', ' %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% 共%TOTAL_PAGE%页');
   		$show = $Page->show();
   		return $show;
 	}

    // 文件路径
    public function buildUrl ($url) {
        if (empty($url)) {
            return '';
        }

        if (substr($url, 0, 10) == '../upload/') {
            $url = str_replace('../upload/', '', $url);
        }
        if (substr($url, 0, 7) == 'upload/') {
            $url = str_replace('upload/', '', $url);
        }

        //windows specific
        if ( 'WINNT' === PHP_OS) {
            // you are under windows os
            // path separator
            if (trim(file_exists(str_replace('/', '\\', C('UPLOAD_PATH').$url)))) {
                return C('HTTP_NHOST').$url;
            }
            if (trim(file_exists(str_replace('/', '\\', C('UPLOAD_PATH').'../admin/upload/'.$url)))) {
                return C('HTTP_NHOST').'../admin/upload/'.$url;
            }
            if (trim(file_exists(str_replace('/', '\\', C('UPLOAD_PATH').'../sadmin/upload/'.$url)))) {
                return C('HTTP_NHOST').'../sadmin/upload/'.$url;
            }

        } else {
            // other os

            if (trim(file_exists(C('UPLOAD_PATH').$url))) {
                return C('HTTP_NHOST').$url;
            }
            if (trim(file_exists(C('UPLOAD_PATH').'../admin/upload/'.$url))) {
                return C('HTTP_NHOST').'../admin/upload/'.$url;
            }
            if (trim(file_exists(C('UPLOAD_PATH').'../sadmin/upload/'.$url))) {
                return C('HTTP_NHOST').'../sadmin/upload/'.$url;
            }
        }

        return '';
    }
}