<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Page;
use Think\Crypt;

class BaseController extends Controller {

    /**
     * \Redis()对象
     **/
    public $redis = null;

    public function _initialize() {
        $this->redisInstance();
    }

    public function redisInstance() {
        $redisConfig = array(
            'host' => '127.0.0.1',
            'port' => '6379',
            'pass' => 'gdcheng',
            //'pass' => 'dalinux',
        );

        try {
            $this->redis = new \Redis();
            $this->redis->connect($redisConfig['host'], $redisConfig['port']);
            $this->redis->auth($redisConfig['pass']);
        } catch (\Exception $e) {
            // Err happens
        }
    }
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

    /**
     * 获取loginauth/驾校id
     *
     * @return void
     * @author
     **/
    public function getLoginauth() {
        $crypt = new \Think\Crypt();//实例化加密解密类
        $loginauth = session('loginauth');
        $loginauth_str = $crypt->decrypt($loginauth, CRYPT_KEY);//用Crypt类decrypt方法解密登录信息session
        $loginauth_arr = explode('\t', $loginauth_str);//以数组形式将解密后的用户登录信息赋给变量
        $school_id = intval($loginauth_arr[4]);//获取驾校id
        return $school_id;
    }

    /**
     * 获取loginauth/角色id
     *
     * @return void
     * @author
     **/
    public function getRoleId() {
        $crypt = new \Think\Crypt();//实例化加密解密类
        $loginauth = session('loginauth');
        $loginauth_str = $crypt->decrypt($loginauth, CRYPT_KEY);//用Crypt类decrypt方法解密登录信息session
        $loginauth_arr = explode('\t', $loginauth_str);//以数组形式将解密后的用户登录信息赋给变量
        $role_id = intval($loginauth_arr[3]);//获取驾校id
        return $role_id;
    }

    /**
     * 获取loginUserId/登陆者id
     *
     * @return void
     * @author
     **/
    public function getLoginUserId() {
        $crypt = new \Think\Crypt();//实例化加密解密类
        $loginauth = session('loginauth');
        $loginauth_str = $crypt->decrypt($loginauth, CRYPT_KEY);//用Crypt类decrypt方法解密登录信息session
        $loginauth_arr = explode('\t', $loginauth_str);//以数组形式将解密后的用户登录信息赋给变量
        $user_id = intval($loginauth_arr[1]);//获取驾校id
        return $user_id;
    }

    /**
     * 获取loginUserId/登陆者名称
     *
     * @return void
     * @author
     **/
    public function getLoginName() {
        $crypt = new \Think\Crypt();//实例化加密解密类
        $loginauth = session('loginauth');
        $loginauth_str = $crypt->decrypt($loginauth, CRYPT_KEY);//用Crypt类decrypt方法解密登录信息session
        $loginauth_arr = explode('\t', $loginauth_str);//以数组形式将解密后的用户登录信息赋给变量
        $login_name = $loginauth_arr[5];//获取驾校id
        return $login_name;
    }

    /**
     * 保存单个图片
     * @param integer $school_id 驾校id
     * @param array $files 图片信息数组
     * @return boolean
     * @author
     */
    public function uploadSingleImg($files_name, $savePath, $imgPrefix, $maxSize=2097152, $rootPath="../upload/", $exts=array('jpg', 'jpeg', 'png', 'gif')) {

        $list = array();
        $files = $_FILES[$files_name];
        if (!$files && !$savePath && !$imgPrefix) {
            return false;
        }
        $upload = new \Think\Upload();
        $upload->maxSize = $maxSize; //2M
        $upload->exts = $exts;
        $upload->rootPath = $rootPath;
        $upload->savePath = $savePath;
        $upload->subName = date('Ymd', time()); // Sub Directory
        $upload->saveName = array('uniqid', $imgPrefix);
        $upload->hash = false;
        //$info = $upload->upload();
        $info = $upload->uploadOne($files);

        if (!$info) {
            return $upload->getError();
        }
        //$writePath = $upload->rootPath . $info[$files_name]['savepath'] . $info[$files_name]['savename'];
        $writePath = $upload->rootPath . $info['savepath'] . $info['savename'];
        $list['info'] = $info;
        $list['path'] = $writePath;
        return $list;
    }

    /**
     * 保存缩略图片
     * @param integer $school_id 驾校id
     * @param array $files 图片信息数组
     * @return boolean
     * @author
     */
    public function smallImgSingle($files_name, $savePath, $imgPrefix, $maxSize=2097152, $rootPath="../upload/", $exts=array('jpg', 'jpeg', 'png', 'gif')) {

        $list = array();
        $files = $_FILES[$files_name];
        if (!$files && !$savePath && !$imgPrefix) {
            return false;
        }

        $upload = new \Think\Upload();
        $upload->maxSize = $maxSize; //2M
        $upload->exts = $exts;
        $upload->rootPath = $rootPath;
        $upload->savePath = $savePath;
        $upload->subName = date('Ymd', time()); // Sub Directory
        $upload->saveName = array('uniqid', $imgPrefix);
        $upload->hash = false;
        $z = $upload->upload();
        if(!$z){
            $upload->getError(); //获得上传附件产生的错误信息
        }else {
            //把已经上传好的图片制作缩略图Image.class.php
            $image = new \Think\Image();
            //open();打开图像资源，通过路径名找到图像
            foreach ($z as $key => $value) {
                $smallimgsingle = $upload->rootPath.$value['savepath']."small_".$value['savename'];
                $imgsingle = $upload->rootPath.$value['savepath'].$value['savename'];
                $image -> open($imgsingle);
                $image -> thumb(320,240);  //按照比例缩小
                $image -> save($smallimgsingle);
                $smallimg[] = $smallimgsingle;
            }
            return $smallimgsingle;
        }
    }

    /**
     * 保存缩略图的同时保留原图
     *
     * @return  void
     * @author  wl
     * @date    Dec 18,, 2016
     **/
    public function UplodTwoImg ($img, $thumb_img, $files_name, $savePath, $imgPrefix, $maxSize=2097152, $rootPath="../upload/", $exts=array('jpg', 'jpeg', 'png', 'gif')) {
        $list = array();
        $files = $_FILES[$files_name];
        if (!$files && !$savePath && !$imgPrefix) {
            return false;
        }

        $upload = new \Think\Upload();
        $upload->maxSize = $maxSize; //2M
        $upload->exts = $exts;
        $upload->rootPath = $rootPath;
        $upload->savePath = $savePath;
        $upload->subName = date('Ymd', time()); // Sub Directory
        $upload->saveName = array('uniqid', $imgPrefix);
        $upload->hash = false;
        $z = $upload->upload();
        if(!$z){
            $upload->getError(); //获得上传附件产生的错误信息
        } else {
            //把已经上传好的图片制作缩略图Image.class.php
            $image = new \Think\Image();
            //open();打开图像资源，通过路径名找到图像
            foreach ($z as $key => $value) {
                $smallimgsingle = $upload->rootPath.$value['savepath']."thumb".$value['savename'];
                $imgsingle = $upload->rootPath.$value['savepath'].$value['savename'];
                $image -> open($imgsingle);
                $image -> thumb(320,240);  //按照比例缩小
                $image -> save($smallimgsingle);
                $smallimg[] = $smallimgsingle;
            }

            // 获得缩略图
            $list[$img] = json_encode($smallimg, JSON_UNESCAPED_SLASHES);
            // 获得原图
            foreach ($z as $k => $v) {
                $orgimgsingle = $upload->rootPath.$v['savepath'].$v['savename'];
                $imgs[] =  $orgimgsingle;
            }

            $list[$thumb_img] = json_encode($imgs, JSON_UNESCAPED_SLASHES);
            return $list;
        }

    }

    /**
     * 多图片上传
     *
     * @return  void
     * @author  wl
     * @date    Dec 14, 2016
     **/
    public function uploadMore ($files_name, $savePath, $imgPrefix, $maxSize=2097152, $rootPath="../upload/", $exts=array('jpg', 'jpeg', 'png', 'gif')) {
        $list = array();
        $files = $_FILES[$files_name];
        if (!$files && !$savePath && !$imgPrefix) {
            return false;
        }

        $upload = new \Think\Upload();
        $upload->maxSize = $maxSize; //2M
        $upload->exts = $exts;
        $upload->rootPath = $rootPath;
        $upload->savePath = $savePath;
        $upload->subName = date('Ymd', time()); // Sub Directory
        $upload->saveName = array('uniqid', $imgPrefix);
        $upload->hash = false;
        $z = $upload->upload();
        if(!$z){
            $upload->getError(); //获得上传附件产生的错误信息
        }else {
            //把已经上传好的图片制作缩略图Image.class.php
            $image = new \Think\Image();
            //open();打开图像资源，通过路径名找到图像
            foreach ($z as $key => $value) {
                $smallimgsingle = $upload->rootPath.$value['savepath']."small_".$value['savename'];
                $imgsingle = $upload->rootPath.$value['savepath'].$value['savename'];
                $image -> open($imgsingle);
                $image -> thumb(320,240);  //按照比例缩小
                $image -> save($smallimgsingle);
                $smallimg[] = $smallimgsingle;
            }
            return $smallimg;
        }
    }

    /**
     * 检查操作是否已授权
     * @param string $controller_name 操作名称
     * @param int $role_id 角色id
     * @return boolean
     * @author gaodacheng
     * @date 2016-09-19
     *
     */
    public function is_authorized($controller_name = null, $role_id = null) {
        if (is_null($controller_name) || (string)$controller_name == '') {
            return 'controller name needed';
        }
        if (is_null($role_id) || intval($role_id) <= 0) {
            return 'role id needed to be an integer';
        }
        $module_id = D('Manager')->getModuleIdByController($controller_name);
        if ($module_id <= 0) {
            return 'module not exist';
        }
        $role_permission_module_list = D('Manager')->getPermissionModuleList($role_id);
        if (empty($role_permission_module_list)) {
            return 'permission module list is empty';
        }
        if (in_array($module_id, $role_permission_module_list)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 封装phpExcel
     *
     * @return  void
     * @author  wl
     * @date    Mar 05, 2017
     **/
    public function DownloadExcel( $fields = array(), $title) {
        if ( empty($fields) && empty($title) ) {
            return false;
        }

        vendor('Excel.Classes.PHPExcel'); // 引用PHPExcel扩展类
        $objPHPExcel = new \PHPExcel(); // 实例化PHPExcel类
        $date = date('Y-m-d');
        $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(15); // 设置单元格宽度
        $data_key = array_keys($fields);
        $keys_list = array_keys($fields[$data_key[0]]);
        foreach ($fields as $key => $value) {
            $num = $key + 1;
            $middle_obj = $objPHPExcel->setActiveSheetIndex(0);
            $start_column = 'A';
            foreach ($keys_list as $k => $v) {
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($start_column.$num, $value[$v]);
                $objPHPExcel->getActiveSheet()->getColumnDimension($start_column, true);
                $start_column++;
            }
        }

        $excelName = $date.$title.'.xls';
        $objPHPExcel->getActiveSheet()->setTitle($title);//设置sheet工作表名称
        $objPHPExcel->setActiveSheetIndex(0); // 设置表头
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        // header("Content-Disposition: attachment; filename=".urlencode($excelName));
        header("Content-Disposition: attachment; filename=".$excelName);
        header("Content-Transfer-Encoding: binary");
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }




}
