<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Page;
use Think\Crypt;

class BaseController extends Controller {

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
   * 获取用户组id/group_id
   *
   * @return void
   * @author wl
   * @date   August 15, 2016
   **/
  public function getGroupId() {
    $crypt = new \Think\Crypt();//实例化加密解密类
    $loginauth = session('loginauth');
    $loginauth_str = $crypt->decrypt($loginauth, CRYPT_KEY);//用Crypt类decrypt方法解密登录信息session
    $loginauth_arr = explode('\t', $loginauth_str);//以数组形式将解密后的用户登录信息赋给变量
    $group_id = $loginauth_arr[3];//获取驾校id 
    return $group_id;
  }

   /**
   * 获取登录者的id/id
   *
   * @return void
   * @author wl
   * @date   August 15, 2016
   **/
  public function getLoginUserId() {
    $crypt = new \Think\Crypt();//实例化加密解密类
    $loginauth = session('loginauth');
    $loginauth_str = $crypt->decrypt($loginauth, CRYPT_KEY);//用Crypt类decrypt方法解密登录信息session
    $loginauth_arr = explode('\t', $loginauth_str);//以数组形式将解密后的用户登录信息赋给变量
    $user_id = $loginauth_arr[1];//获取驾校id 
    return $user_id;
  }

  /**
   * 获取loginUserId/登陆者名称user_name
   *
   * @return void
   * @author wl
   * @date   August 15, 2016
   **/
  public function getLoginName() {
    $crypt = new \Think\Crypt();//实例化加密解密类
    $loginauth = session('loginauth');
    $loginauth_str = $crypt->decrypt($loginauth, CRYPT_KEY);//用Crypt类decrypt方法解密登录信息session
    $loginauth_arr = explode('\t', $loginauth_str);//以数组形式将解密后的用户登录信息赋给变量
    $login_name = $loginauth_arr[4];//获取驾校id 
    return $login_name;
  }

   /**
   * 保存文件
   * @param integer $school_id 驾校id
   * @param array $files 图片信息数组
   * @return boolean
   * @author 
   */
  public function upload($files_name, $savePath, $imgPrefix, $maxSize=10485760, $rootPath="../upload/", $exts=array()) {

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
      $info = $upload->upload();

      if (!$info) {
          return $upload->getError();
      }
      $writePath = $upload->rootPath . $info[$files_name]['savepath'] . $info[$files_name]['savename'];
      $list['info'] = $info;
      $list['path'] = $writePath;
      return $list;
  }

  
}
?>