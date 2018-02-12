<?php 
namespace Admin\Controller;
use Think\Controller;
use Think\Upload;
use Think\Page;

class StudentController extends BaseController {
    public $StudentModel;
    //构造函数，判断是否登录
    public function _initialize() {
        if(!session('loginauth')) {
            $this->redirect('Public/login');
            exit();
        }
        $this->StudentModel = D('Student');
    }
// 1.学员列表模块
    /**
     * 学员列表的展示
     *
     * @return void
     * @author  wl
     * @date    july 25, 2016
     **/
    public function index () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Admin/Index/welcome'));
        }
        $school_id = $this->getLoginauth();
        // $student_list = D('Student')->getStudentList($school_id, $role_id);
        $student_list = D('Student')->getStudentList($school_id);
        $this->assign('role_id', $role_id);
        $this->assign('school_id', $school_id);
        $this->assign('count', $student_list['count']);
        $this->assign('page', $student_list['page']);
        $this->assign('student_list', $student_list['student_list']);
        $this->display(CONTROLLER_NAME.'/'.__FUNCTION__);
    }

    /**
     * 根据相关条件搜索学员列表
     *
     * @return  void
     * @author  wl
     * @date    july 25， 2016
     * @update  july 26,  2016
     **/
    public function searchStudentList () {
        $school_id = $this->getLoginauth();
        $param = I('param.');
        $s_keyword = trim((string)$param['s_keyword']);
        $search_type = trim((string)$param['search_type']);
        $status = trim((int)$param['status']);
        $elment = trim((string)$param['elment']);
        if ($elment == '搜索') {
            if ($s_keyword == '' && $search_type == '' && $status == '') {
                $this->redirect('Student/index');
            } else {
                $student_list   = D('Student')->searchStudent($param, $school_id);
                $this->assign('role_id', $role_id);
                $this->assign('school_id', $school_id);
                $this->assign('s_keyword', $s_keyword);
                $this->assign('status', $status);
                $this->assign('search_type', $search_type);
                $this->assign('count', $student_list['count']);
                $this->assign('page', $student_list['page']);
                $this->assign('student_list', $student_list['student_list']);
                $this->display('Student/index');
            }
        } elseif ($elment == 'Excel下载') {
            if ($param['begin_num'] < 0 || $param['end_num'] <= 0) {
                $param['begin_num'] = 1;
                $param['end_num'] = 2;
            }
            $num = intval(intval($param['end_num']) - intval($param['begin_num']) + 1);
            if ($num < 0 || $num > 9000) {
                $this->error('数据范围为1~9000');exit;
            }
            $student_list = D('Export')->getStudentListDownload($param, $school_id);
            $title = '学员信息';
            $result = $this->DownloadExcel($student_list, $title);
        }
    }

    /**
     * 设置学员的删除状态
     *
     * @return  void
     * @author  wl
     * @date    Nov 25, 2016 
     **/
    public function setStudentStatus () {
        if(IS_AJAX) {
            $id = I('post.id');
            $status = I('post.status');
            $phone = I('post.s_phone');
            if ($status == 0) {
                $checkstudentinfo = D('Student')->checkStudentInfo($phone);
                if (!empty($checkstudentinfo)) {
                    $data = array('code'=>105, 'msg'=>"该手机号的学员已经存在", 'data'=>$id);
                } else {
                    $res = D('Student')->setStudentStatus($id,$status);
                    if($res['i_status']) {
                        $data = array('code'=>200, 'msg'=>"设置成功", 'data'=>$id);
                    } else {
                        $data = array('code'=>102, 'msg'=>"设置失败", 'data'=>'');
                    }
                }
            } else {
                $res = D('Student')->setStudentStatus($id,$status);
                if($res['i_status']) {
                    $data = array('code'=>200, 'msg'=>"设置成功", 'data'=>$id);
                } else {
                    $data = array('code'=>102, 'msg'=>"设置失败", 'data'=>'');
                }

            }
        } else {
            $data = array('code'=>102, 'msg'=>"设置失败", 'data'=>'');    
        }
        action_log('set_student_status', 'user', $id, $this->getLoginUserId());
        $this->ajaxReturn($data, 'JSON');
    }

    /**
     * 删除学员
     *
     * @return 
     * @author Sun/wl
     * @update july 26, 2016
     **/
    public function delStudent() {
        if (IS_AJAX) {
            $role_id = $this->getRoleId();
            $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
            if (true !== $permission_check) {
                $data = array('code' => 400, 'msg' => 'Permission Denied!', 'data' => '');
            } else {
                $id = I('post.id');
                // 删除启用之前清空用户的其它关联数据
                // 报名驾校订单
                M('school_orders')->where(array('so_user_id' => $id))->save(array('so_order_status' => 101));
                // 预约学车订单
                M('study_orders')->where(array('l_user_id' => $id))->save(array('i_status' => 101));
                $del = D('User')->delStudent($id);
                if ($del) {
                    $data = array('code'=>'200', 'msg'=>'删除成功', 'data'=>$id);
                } else {
                    $data = array('code'=>'400', 'msg'=>'删除失败', 'data'=>$id);
                }
            }
            action_log('del_student', 'user/school_orders/study_orders', $id, $this->getLoginUserId());
            $this->ajaxReturn($data, 'JSON');
        } else {
            $this->error('Permission Denied!');
        }
    }
    /**
     * 学员列表---添加学员
     *
     * @return
     * @author  Sun/wl
     * @update  july 25, 2016 
     * @update  Nov 28, 2016 
     * @update  Nov 29, 2016 
     **/
    public function addStudent() {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Student/index'));
        }
        $school_id = $this->getLoginauth();
        if (IS_POST) {
            $post = I('post.'); 
            $phone = $post['user_phone'];
            $identity = $post['identity_id'];
            $post['lesson_id'] = $post['lesson_id'] != '' ? $post['lesson_id'] : '1';
            $post['license_id'] = $post['license_id'] != '' ? $post['license_id'] : '1';
            $lesson_id = $post['lesson_id'] != '' ? $post['lesson_id'] : '1';
            $license_id = $post['license_id'] != '' ? $post['license_id'] : '1';
            $lesson_name = D('Student')->getLessonNameById($lesson_id);
            $license_name = D('Student')->getLicenseNameById($license_id);
            $post['lesson_name'] = $lesson_name;
            $post['license_name'] = $license_name;
            // 1）User表中验证手机号码是否已经注册
            $phone_register = D('User')->isPhoneRegistered($phone);
            if ($phone_register == true) {
                $this->error('该手机号已注册！', U('Student/addStudent'));
            }

            // 2)UsersInfo表中验证身份证是否已经注册
            $identity_register = D('UsersInfo')->isIdentityRegistered($identity);
            if ($identity_register == true) {
                $this->error('该身份证已注册！', U('Student/addStudent'));
            }

            // 3)如果上面验证都通过，注册会员
            // if (!empty($_FILES)) {
            //     if ($_FILES['user_photo']['error'] == UPLOAD_ERR_OK) {
            //         $user_photo = $this->smallImgSingle('user_photo', 'student/user_photo/', 'photo_', '3145728', '../upload/');
            //         $post['user_photo'] = $user_photo;
            //     } else {
            //         $post['user_photo'] = '';
            //     }
            // } else {
            //     $post['user_photo'] = '';
            // }

            if ($post['real_name'] == '' && $post['user_phone'] == '' && $post['identity_id'] == '' && $post['age'] == '' ) {
                $this->error('请完善信息', U('Student/addStudent'));
            }
            $res = D('RegisterUser')->registerUser($post, $school_id);
            if ($res) {
                action_log('add_student', 'user/user_info', $res, $this->getLoginUserId());
                $this->success('添加成功');
            } else {
                action_log('add_student', 'user/user_info', $res, $this->getLoginUserId());
                $this->error('添加失败', U('Student/addStudent'));
            }
        } else {
            $province_list = D('Province')->getProvinceList();
            $licenseinfo = D('Coach')->getLicenseInfo();
            $lessoninfo = D('Coach')->getLessonInfo();
            $this->assign('licenseinfo', $licenseinfo);
            $this->assign('lessoninfo', $lessoninfo);
            $this->assign('province_list', $province_list);
            $this->assign('school_shifts', $school_shifts);
            $this->display(CONTROLLER_NAME.'/'.__FUNCTION__);
        }
    }
    /**
     * 学员列表---编辑学员
     *
     * @return  void
     * @author  wl
     * @date    july 26, 2016
     * @update  Nov 29, 2016 
     **/
    public function editStudent () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Student/index'));
        }
        $uid    = I('param.id');
        $student_list = D('Student')->getUserInfoById($uid);
        if (IS_POST) {
            $post = I('post.');
            if ($post['l_user_id'] != '') {
                $student_lists = D('Student')->getUserInfoById($post['l_user_id']);
            }
            // 修改user表中的信息
            $data['l_user_id']      = $post['l_user_id'];
            $data['s_real_name']    = $post['s_real_name'] == '' ? $student_lists['s_real_name'] : $post['s_real_name'];
            $data['s_username']     = $post['s_username'] == '' ? $student_lists['s_username'] : $post['s_username'];
            $data['s_phone']        = $post['user_phone'] != '' ? $post['user_phone'] : $student_lists['s_phone'];
            $data['updatetime']     = time();

            // 修改user_info中的信息
            if ($post['lesson_id'] != '') {
                $userInfo['lesson_id']  = $post['lesson_id'];
                $userInfo['lesson_name']  = D('Student')->getLessonNameById($userInfo['lesson_id']);
            } else {
                $userInfo['lesson_id']  = $student_lists['lesson_id'] != '' ? $student_lists['lesson_id'] : '1';
                $userInfo['lesson_name']  = $student_lists['lesson_name'] != '' ? $student_lists['lesson_name'] : '科目一';
            }
            if ($post['license_id'] != '') {
                $userInfo['license_id']  = $post['license_id'];
                $userInfo['license_name']  = D('Student')->getLicenseNameById($userInfo['license_id']);
            } else {
                $userInfo['license_id']  = $student_lists['license_id'] != '' ? $student_lists['license_id'] : '1';
                $userInfo['license_name']  = $student_lists['license_name'] != '' ? $student_lists['license_name'] : 'C1';
            }
            $userInfo['age']            = $post['age'] == '' ? $student_lists['age'] : $post['age'];
            $userInfo['identity_id']    = $post['identity_id'] == '' ? $student_lists['identity_id'] : $post['identity_id'];
            $userInfo['sex']            = $post['sex'] == '' ? $student_lists['sex'] : $post['sex'];
            $userInfo['learncar_status']= $post['learncar_status'] == '' ? $student_lists['learncar_status'] : $post['learncar_status'];
            $userInfo['photo_id']       = $post['photo_id'] == '' ? $student_lists['photo_id'] : $post['photo_id'];
            $userInfo['province_id']    = $post['province'] == '' ? $student_lists['province_id'] : $post['province'];
            $userInfo['city_id']        = $post['city'] == '' ? $student_lists['city_id'] : $post['city'];
            $userInfo['area_id']        = $post['area'] == '' ? $student_lists['area_id'] : $post['area'];
            $userInfo['address']        = $post['address'] == '' ? $student_lists['address'] : $post['address'];
            $userInfo['license_num']    = $post['license_num'] == '' ? $student_lists['license_num'] : $post['license_num'];
            $userInfo['updatetime']     = time();
            if (!empty($_FILES)) {
                if ($_FILES['user_photo']['error'] == UPLOAD_ERR_OK) {
                    $user_photo = $this->smallImgSingle('user_photo', 'student/user_photo/', 'photo_', '3145728', '../upload/');
                    $userInfo['user_photo'] = $user_photo;
                } else {
                    $userInfo['user_photo'] = $student_lists['user_photo'] ? $student_lists['user_photo'] : '';
                }
            } else {
                $userInfo['user_photo'] = $student_lists['user_photo'] ? $student_lists['user_photo'] : '';
            } 

            if ($data['s_username'] == '' && $data['s_real_name'] == '' && $data['s_phone'] == '' && $userInfo['identity_id'] == '' && $userInfo['age'] == '' ) {
                $this->error('请完善信息', U('Student/addStudent'));
            }

            $user = M('user');
            if($r = $user-> create($data)) {
                $re = $user->fetchSql(false)->where(array('l_user_id' => $post['l_user_id']))->save($r);
            } 

            $user_info = D('users_info');   
            if ($res = $user_info->create($userInfo)) {
                $result = $user_info->fetchSql(false)->where(array('user_id' => $post['l_user_id']))->save($res);
            }

            if ($re || $result) {
                action_log('edit_student', 'user/user_info', $post['l_user_id'], $this->getLoginUserId(), '编辑学员信息');
                $this->success('修改成功', U('Student/index'));
            } else {
                action_log('edit_student', 'user/user_info', $post['l_user_id'], $this->getLoginUserId(), '编辑学员信息失败');
                $this->success('保存成功', U('Student/editStudent'));
            }
        } else {
            $province_list = D('Province')->getProvinceList();
            $licenseinfo = D('Coach')->getLicenseInfo();
            $lessoninfo = D('Coach')->getLessonInfo();
            $this->assign('licenseinfo', $licenseinfo);
            $this->assign('lessoninfo', $lessoninfo);
            $this->assign('province_list', $province_list);
            $this->assign('student_list', $student_list);
            $this->display(CONTROLLER_NAME.'/'.__FUNCTION__);
        }
    }

    //检测手机号是否已注册
    public function checkPhone() {
        $phone = I('post.so_phone');
        //User表中检测手机号码是否已经注册
        $register = D('User')->isPhoneRegistered($phone);
        if ($register) {//返回true说明该号码已注册
            $data = array('code'=>400, 'msg'=>'亲，该手机号已注册','data'=>'');  
        } else {
            $data = array('code'=>200, 'msg'=>'√，手机号可以使用','data'=>'');
        }
        $this->ajaxReturn($data, 'JSON');
    }

    //检测身份证是否已注册
    public function checkIdentity() {
        $identity_id = I('post.identity_id');
        if ($identity_id == '') {
            $data = array('code'=>404, 'msg'=>'亲，请填写身份证','data'=>'');  
            $this->ajaxReturn($data, 'JSON');
        }
        //UserInfo表中检测身份证是否已经注册
        $register = D('UsersInfo')->isIdentityRegistered($identity_id);
        if ($register) {//返回true说明该身份证已注册
            $data = array('code'=>400, 'msg'=>'亲，该身份证已注册','data'=>'');  
        } else {
            $data = array('code'=>200, 'msg'=>'√，身份证可以使用','data'=>'');
        }
        $this->ajaxReturn($data, 'JSON');
    }

// 2.学员回收站模块
    /**
     * 展示被删除的学员的列表
     *
     * @return  void
     * @author  wl
     * @update  Nov 30, 2016
     **/
    public function showDelStudentList() {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Admin/Index/welcome'));
        }
        $school_id = $this->getLoginauth();
        $student_list = D('Student')->getDelStudentList($school_id);
        if ($student_list) {
            $count          = $student_list['count'];
            $page           = $student_list['page'];    
            $student_list   = $student_list['student_list'];
        }
        $this->assign('role_id', $role_id);
        $this->assign('school_id', $school_id);
        $this->assign('count', $count);
        $this->assign('page', $page);
        $this->assign('student_list', $student_list);
        $this->display('Student/showDelStudentList');
    }

    /**
     * 恢复被删的学员
     *
     * @return  void
     * @author  wl
     * @date    july 26， 2016
     * @update  Nov 30, 2016
     **/
    public function recoverDelstudent () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Student/showDelStudentList'));
        }
        if (IS_AJAX) {
            $uid = I('post.id');
            $result = M('user')->where('l_user_id = :uid')
                ->bind(['uid' => $uid])
                ->save(array('i_status' => 0));
            if ($result) {
                $data = array('code' => 200, 'msg'=> '恢复学员成功', 'data' => $id);
            } else {
                $data = array('code' => 101, 'msg'=> '恢复学员失败', 'data' => $id);
            }
        } else {
            $data = array('code' => 102, 'msg'=> '恢复学员失败', 'data' => $id);
        }
        action_log('recover_del_student', 'user', $uid, $this->getLoginUserId());
        $this->ajaxReturn($data);
    }

    /**
     * 回收站中根据相关条件搜索学员列表
     *
     * @return  void
     * @author  wl
     * @date    july 26， 2016
     **/
    public function searchDelStudent () {
        // $role_id = $this->getRoleId();
        // $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.'index', $role_id);
        // if (true !== $permission_check) {
        //     $this->error('Permission Denied!');
        // }
        $school_id      = $this->getLoginauth();
        $param          = I('param.');
        $s_keyword      = trim((string)$param['s_keyword']);
        $search_type    = trim((string)$param['search_type']);
        if ($s_keyword == '' && $search_type == '') {
            $this->redirect('Student/showDelStudentList');
        }
        $student_list   = D('Student')->searchDelStudent($param, $school_id);
        $this->assign('school_id', $school_id);
        $this->assign('s_keyword', $s_keyword);
        $this->assign('search_type', $search_type);
        $this->assign('count', $student_list['count']);
        $this->assign('page', $student_list['page']);
        $this->assign('student_list', $student_list['student_list']);
        $this->display('Student/showDelStudentList');
    }

// 3.在线模拟模块
    /**
     * 学员管理模块中添加在线模拟列表
     *
     * @return  void
     * @author  wl
     * @update  Nov 30, 2016
     **/
    public function examRecords() {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Admin/Index/welcome'));
        }
        $school_id = $this->getLoginauth();
        $recordsList = $this->StudentModel->getExamRecords($school_id);
        $this->assign('school_id', $school_id);
        $this->assign('page', $recordsList['page']);
        $this->assign('count', $recordsList['count']);
        $this->assign('recordsList', $recordsList['list']);
        $this->display(CONTROLLER_NAME.'/'.__FUNCTION__);
    }

    /**
     * 通过学员姓名或者手机号码来搜索学员
     *
     * @return  void
     * @author  wl
     * @update  Nov 30, 2016
     **/
    public function searchUserRecords () {
        // $role_id = $this->getRoleId();
        // $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        // if (true !== $permission_check) {
        //     $this->error('Permission Denied!', U('Student/examRecords'));
        // }
        $school_id = $this->getLoginauth();
        $param = I('param.');
        $s_keyword = trim((string)$param['s_keyword']);
        if($s_keyword == '') {
            $this->redirect('Student/examRecords');
        } else {
            $records_list = $this->StudentModel->searchRecords($param, $school_id);
            $this->assign('s_keyword', $s_keyword);
            $this->assign('page', $records_list['page']);
            $this->assign('count', $records_list['count']);
            $this->assign('recordsList', $records_list['list']);
            $this->display('Student/examRecords');
        }
    }

// 4.用户绑定的银行账户管理
    /**
     * 用户银行账户管理的列表展示功能
     *
     * @return  void
     * @author  wl
     * @date    Oct 27, 2016
     **/
    public function usersWalletAdmin () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Admin/Index/welcome'));
        }
        $userswalletlist = D('User')->getUserWalletList();
        $this->assign('page', $userswalletlist['page']);
        $this->assign('count', $userswalletlist['count']);
        $this->assign('userswalletlist', $userswalletlist['userswalletlist']);
        $this->display('Student/usersWalletAdmin');
    }

    /**
     * 用户银行账户管理的搜索
     *
     * @return  void
     * @author  wl
     * @date    Oct 27, 2016
     **/
    public function searchUsersWallet () {
        // $role_id = $this->getRoleId();
        // $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        // if (true !== $permission_check) {
        //     $this->error('Permission Denied!');
        // }
        $param = I('param.');
        $s_keyword = trim((string)$param['s_keyword']);
        $search_info = trim((string)$param['search_info']);
        if ($s_keyword == '' && $search_info == '') {
            $this->redirect('Student/usersWalletAdmin');
        } else {
            $userswalletlist = D('User')->searchUsersWallet($param);
            $this->assign('s_keyword', $s_keyword);
            $this->assign('search_info', $search_info);
            $this->assign('page', $userswalletlist['page']);
            $this->assign('count', $userswalletlist['count']);
            $this->assign('userswalletlist', $userswalletlist['userswalletlist']);
            $this->display('Student/usersWalletAdmin');
        }
    }

    /**
     * 用户银行账户管理的删除
     *
     * @return  void
     * @author  wl
     * @date    Oct 27, 2016
     **/
    public function delUsersWallet () {
        if(IS_AJAX){
            $role_id = $this->getRoleId();
            $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
            if (true !== $permission_check) {
                $this->error('Permission Denied!');
            } else {
                $id =I('post.id');
                $res = D('User')->delUsersWallet($id);
                if($res){
                    $data=array('code'=>200,'msg'=>"删除成功",'data'=>'');
                } else {
                    $data=array('code'=>101,'msg'=>"删除失败",'data'=>'');
                }
            }
            action_log('del_users_wallet', 'users_wallet', $id, $this->getLoginUserId());
            $this->ajaxReturn($data,'JSON');
        } else {
            $this->error('Permission Denied!');
        }
    }


    /**
     * 获取城市
     *
     * @return 
     * @author sun
     **/
    public function getCity() {
        $province_id = I('param.province_id');
        $city_list = D('City')->getCityList($province_id);
        // $html = "<option value=''>请选择市</option>";
        $html = "";
        foreach ($city_list as $key => $value) {
            $html .= "<option value='".$value['cityid']."'>".$value['city']."</option>";
        }
        echo $html;
    }

    /**
     * 获取区域
     *
     * @return 
     * @author sun
     **/
    public function getArea() {
        $city_id = I('param.city_id');
        // $this->ajaxReturn($city_id, 'JSON');
        $area_list = D('Area')->getAreaList($city_id);
        // $html = "<option value=''>请选择区域</option>";
        $html = "";
        foreach ($area_list as $key => $value) {
            $html .= "<option value='".$value['areaid']."'>".$value['area']."</option>";
        }
        echo $html;
    }

}
?>
