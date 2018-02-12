<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Page;
use Think\Upload;

/**
 * 教练模块
 * @author chenxi
 **/
class CoachController extends BaseController {

    public $ManagerModel;
    public function _initialize() {
        if (!session('loginauth')) {
            $this->redirect('Public/login');
            exit();
        }
        $this->ManagerModel = D('Coach');
    }
// 1.教练列表模块
    /**
     * 教练列表的展示
     *
     * @return 	void
     * @author 	wl
     * @date 	Sep 06, 2016
     **/
    public function index() {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Admin/Index/welcome'));
        }
        $school_id = $this->getLoginauth();
        // $coachlists = D('Coach')->getCoachList($school_id, $role_id);
        $coachlists = D('Coach')->getCoachList($school_id);
        $this->assign('role_id', $role_id);
        $this->assign('school_id', $school_id);
        $this->assign('page', $coachlists['page']);
        $this->assign('count', $coachlists['count']);
        $this->assign('coach_list', $coachlists['coach_list']);
        $this->display(CONTROLLER_NAME.'/'.__FUNCTION__);
    }

    /**
     * 教练列表的搜索
     *
     * @return 	void
     * @author 	wl
     * @date 	Sep 06, 2016
     **/
    public function searchCoach () {
        $school_id  = $this->getLoginauth();
        $role_id 	= $this->getRoleId();
        $param		= I('param.');
        $s_keyword  = trim((string)$param['s_keyword']);
        $search_star = trim((string)$param['search_star']);
        $search_type = trim((string)$param['search_type']);
        $status = trim((int)$param['status']);
        $certification_status = trim ((int)$param['certification_status']);
        if ($s_keyword == '' && $search_type == '' && $search_star == '' && $certification_status == '' && $status == '') {
            $this->redirect('Coach/index');
        } else {
            // $coachlists = D('Coach')->searchCoach($param, $school_id, $role_id);
            $coachlists = D('Coach')->searchCoach($param, $school_id);
            $this->assign('role_id', $role_id);
            $this->assign('school_id', $school_id);
            $this->assign('s_keyword', $s_keyword);
            $this->assign('status', $status);
            $this->assign('search_type', $search_type);
            $this->assign('search_star', $search_star);
            $this->assign('certification_status', $certification_status);
            $this->assign('page', $coachlists['page']);
            $this->assign('count', $coachlists['count']);
            $this->assign('coach_list', $coachlists['coach_list']);
            $this->display('Coach/index');
        }
    }
    /**
     * 设置教练是否在线状态
     *
     * @return  void
     * @author  wl
     * @date    Dec 21, 2016
     **/
    public function setCoachHotStatus () {
        if(IS_AJAX) {
            $id = I('post.id');
            $status = I('post.status');
            $res = D('Coach')->setCoachHotStatus($id, $status);
            if($res['is_hot']) {
                $data = array('code'=>200, 'msg'=>"设置成功", 'data'=>$res['id']);
            } else {
                $data = array('code'=>102, 'msg'=>"设置失败", 'data'=>'');
            }
        } else {
            $data = array('code'=>102, 'msg'=>"设置失败", 'data'=>'');
        }
        action_log('set_coach_hotstatus', 'coach', $id, $this->getLoginUserId());
        $this->ajaxReturn($data, 'JSON');
    }

    /**
     * 设置教练是否在线状态
     *
     * @return 	void
     * @author 	wl
     * @date 	Sep 06, 2016
     **/
    public function setCoachStatus () {
        if(IS_AJAX) {
            $id = I('post.id');
            $status = I('post.status');
            $res = D('Coach')->setCoachStatus($id, $status);
            if($res['order_receive_status']) {
                $data = array('code'=>200, 'msg'=>"设置成功", 'data'=>$res['id']);
            } else {
                $data = array('code'=>102, 'msg'=>"设置失败", 'data'=>'');
            }
        } else {
            $data = array('code'=>102, 'msg'=>"设置失败", 'data'=>'');
        }
        action_log('set_coach_status', 'coach', $id, $this->getLoginUserId());
        $this->ajaxReturn($data, 'JSON');
    }
    /**
     * 设置教练的删除状态
     *
     * @return  void
     * @author  wl
     * @date    Nov 28, 2016
     **/
    public function setDelCoachStatus () {
        if(IS_AJAX) {
            $id = I('post.id');
            $status = I('post.status');
            $phone = I('post.coach_phone');
            if ($status == 0) {
                $checkcoachinfo = D('Coach')->checkCoachInfo($phone);
                if (!empty($checkcoachinfo)) {
                    $data = array('code'=>105, 'msg'=>"该手机号的教练已经存在", 'data'=>$id);
                } else {
                    $res = D('Coach')->setDelCoachStatus($id,$status);
                    if($res['i_status']) {
                        $data = array('code'=>200, 'msg'=>"设置成功", 'data'=>$id);
                    } else {
                        $data = array('code'=>102, 'msg'=>"设置失败", 'data'=>'');
                    }
                }
            } else {
                $res = D('Coach')->setDelCoachStatus($id,$status);
                if($res['i_status']) {
                    $data = array('code'=>200, 'msg'=>"设置成功", 'data'=>$id);
                } else {
                    $data = array('code'=>102, 'msg'=>"设置失败", 'data'=>'');
                }

            }
        } else {
            $data = array('code'=>102, 'msg'=>"设置失败", 'data'=>'');
        }
        action_log('set_coachdel_status', 'coach/user', $id, $this->getLoginUserId());
        $this->ajaxReturn($data, 'JSON');
    }
    /**
     * 删除教练
     *
     * @return 	void
     * @author 	wl
     * @date 	Sep 06, 2016
     **/
    public function delCoach () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Coach/index'));
        }
        if(IS_AJAX) {
            $id = I('post.id');
            $res = D('Coach')->delCoach($id);
            if($res) {
                $data = array('code'=>200, 'msg'=>"删除成功", 'data'=>$list['id']);
            } else {
                $data = array('code'=>102, 'msg'=>"删除失败", 'data'=>'');
            }
        } else {
            $data = array('code'=>102, 'msg'=>"删除失败", 'data'=>'');
        }
        action_log('del_coach', 'coach', $id, $this->getLoginUserId());
        $this->ajaxReturn($data, 'JSON');
    }

    /**
     * 设置教练与学员之间的绑定状态
     *
     * @return  void
     * @author  wl
     * @date    Jan 04, 2017
     **/
    public function setCoachMustBind () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Coach/index'));
        }
        if (IS_AJAX) {
            $post = I('post.');
            $id = $post['id'];
            $status = $post['status'];
            $result = D('Coach')->setCoachMustBind($id, $status);
            if ($result) {
                action_log('set_coach_bind', 'coach', $id, $this->getLoginUserId());
                $data = array('code' => 200 , 'msg' => '设置成功' , 'data' => $result);
            } else {
                action_log('set_coach_bind', 'coach', $id, $this->getLoginUserId());
                $data = array('code' => 400, 'msg' => '设置失败', 'data' => 2);
            }

        } else {
            action_log('set_coach_bind', 'coach', $id, $this->getLoginUserId());
            $data = array('code' => 400, 'msg' => '设置失败', 'data' => 2);
        }
        $this->ajaxReturn($data, 'JSON');

    }


    /**
     * 添加教练
     *
     * @return 	void
     * @author 	wl
     * @date 	Sep 08, 2016
     **/
    public function addCoach () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Coach/index'));
        }
        $school_id = $this->getLoginauth();
        // 通过驾校id获得车辆信息
        $carlist = D('Cars')->getCarsByIds($school_id);
        // 获取驾校列表
        $school_list    = D('Manager')->getSchoolList();
        //获取省份列表
        $province_list = D('Province')->getProvinceList();
        // 获取牌照信息
        $licenseInfo = D('Coach')->getLicenseInfo();
        // 获取科目信息
        // $lessonInfo = D('Coach')->getLessonInfo();
        $lessonInfo = D('Coach')->productLesson();
        if (IS_POST) {
            $post = I('post.');
            if ($school_id == 0) {
                $data['s_school_name_id']   = $post['school_id'] != '' ? $post['school_id'] : 1;
                $sid                        = $post['school_id'];
            } else {
                $data['s_school_name_id']   = $school_id;
                $sid                        = $school_id;
            }
            if ($post['coach_star'] != '') {
                if ($post['coach_star'] >= 5) {
                    $data['i_coach_star'] = 5;

                } elseif ($post['coach_star'] <= 1) {
                    $data['i_coach_star'] = 1;

                } else {
                    $data['i_coach_star'] = $post['coach_star'];
                }
            } else {
                $data['i_coach_star'] = 1;
            }

            $data['s_coach_name']           = $post['coach_name'] != '' ? $post['coach_name'] : '' ;
            $data['s_coach_phone']          = $post['coach_phone'] != '' ? $post['coach_phone'] : '';
            $data['s_teach_age']            = $post['coach_age'] != '' ? $post['coach_age'] : '';
            $data['s_coach_sex']            = $post['coach_sex'];
            $data['s_coach_lesson_id']      = implode(',', $post['lesson_id']) ? implode(',', $post['lesson_id']) : 1;
            $data['s_coach_lisence_id']     = implode(',', $post['license_id']) ? implode(',', $post['license_id']) : 1;
            $data['s_coach_car_id']         = $post['coach_car'] != '' ? $post['coach_car'] : '';
            $data['province_id']            = $post['province'] != '' ? $post['province'] : 0;
            $data['city_id']                = $post['city'] != '' ? $post['city'] : 0;
            $data['area_id']                = $post['area'] != '' ? $post['area'] : 0;
            $data['s_coach_address']        = $post['address'] != '' ? $post['address'] : '';
            $data['average_license_time']   = $post['average_license_time'] != '' ? $post['average_license_time'] : '';
            $data['lesson2_pass_rate']      = $post['lesson2_pass_rate'] != '' ? $post['lesson2_pass_rate'] : '';
            $data['lesson3_pass_rate']      = $post['lesson3_pass_rate'] != '' ? $post['lesson3_pass_rate'] : '';
            $data['certification_status']   = 3; // 已认证状态
            if ( $post['coach_type'] == '') {
                $data['i_type'] = 1;
            } elseif ($post['coach_type'] == 0) {
                $data['i_type'] = 0;
            } else {
                $data['i_type'] = $post['coach_type'];
            }
            $data['order_receive_status']   = $post['is_online'] != '' ? $post['is_online'] : 1;
            $data['addtime']                = time();
            if (!empty($_FILES)) {
                if ($_FILES['license_img']['error'] == 0) {
                    $coach_imgurl = $this->uploadSingleImg('license_img', 'coach/'.$data['s_school_name_id'].'/', 'coachimg_','3145728','../upload/');
                    $data['s_coach_imgurl'] = $coach_imgurl['path'];
                } else {
                    $data['s_coach_imgurl'] = $post['oldimg'];
                }
            }
            if ($data['s_coach_address'] == '' && $data['i_type'] == '' && $data['s_coach_car_id'] == '' && $data['city_id'] == '' && $data['area_id'] == '' && $data['province_id'] == '' && $data['s_coach_name'] == '' && $data['s_coach_phone'] == '' && $data['s_coach_lesson_id'] == '' && $data['s_coach_lisence_id'] == '' && $data['s_school_name_id'] == '' ) {
                $this->error('请完善信息');
            }
            $check_coach_info = D('Coach')->checkCoachInfo(trim($data['s_coach_phone']));
            if (!empty($check_coach_info)) {
                $this->error('此教练已经存在', U('Coach/addCoach'));
            }
            $coachInfo = D('coach');
            $user = D('user');
            if ($res = $coachInfo->create($data)) {
                $lastId = $coachInfo->fetchSql(false)->add($res);
                if ($lastId) {
                    $datas['i_user_type'] = 1;
                    $datas['s_phone']  = $post['coach_phone'] ? $post['coach_phone'] : '';
                    $datas['s_password'] = md5('123456');
                    $datas['s_username'] = '嘻哈用户'.substr($post['coach_phone'], -4);
                    $datas['s_real_name'] = $post['coach_name'] ? $post['coach_name'] : '';
                    if ($resu = $user->create($datas)) {
                        $result = $user->fetchSql(false)->add($resu);
                        if ($result) {
                            $da['user_id'] = $result;
                            if ($r = $coachInfo->create($da)) {
                                $re = $coachInfo->where(array('l_coach_id' => $lastId))
                                    ->fetchSql(false)
                                    ->save($r);
                                if ($re) {
                                    action_log('add_coach', 'coach/user', $lastId, $this->getLoginUserId());
                                    $this->success('添加成功', U('Coach/index'));
                                } else {
                                    $this->error('添加失败', U('Coach/addCoach'));
                                }
                            } else {
                                $this->error('添加失败', U('Coach/addCoach'));
                            }
                        } else {
                            $this->error('添加失败', U('Coach/addCoach'));
                        }
                    } else {
                        $this->error('添加失败', U('Coach/addCoach'));
                    }
                } else {
                    $this->error('添加失败', U('Coach/addCoach'));
                }
            } else {
                $this->error('添加失败', U('Coach/addCoach'));
            }
        } else {
            $this->assign('school_id', $school_id);
            $this->assign('school_list', $school_list);
            $this->assign('licenseInfo', $licenseInfo);
            $this->assign('lessonInfo', $lessonInfo);
            $this->assign('province_list', $province_list);
            $this->assign('carlist', $carlist);
            $this->display(CONTROLLER_NAME.'/'.__FUNCTION__);
        }
    }

    /**
     * 编辑教练
     *
     * @return  void
     * @author  wl
     * @date    Sep 08, 2016
     **/
    public function editCoach () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Coach/index'));
        }
        $school_id  = $this->getLoginauth();
        $param      = I('param.');
        $id         = $param['id'];
        $sid        = $param['school_id'];
        $coachlist  = D('Coach')->getCoachInfoById($id);
        $coach_license = $coachlist['coach_license'];
        $coach_lesson = $coachlist['coach_lesson'];
        // 通过驾校id获得车辆信息
        $carlist    = D('Cars')->getCarsByIds($sid);
        // 获取驾校列表
        $school_list    = D('Manager')->getSchoolList();
        //获取省份列表
        $province_list  = D('Province')->getProvinceList();
        // 获取牌照信息
        $licenseInfo    = D('Coach')->getLicenseInfo();
        // 获取科目信息
        // 获得时间配置每天都不同
        $coach_time_config = D('Coach')->getChangeTimeConfig();
        $coach_time_config_date = $coach_time_config['date'];
        $coach_time_config_time = $coach_time_config['time'];
        if (IS_POST) {
            $post = I('post.');
            $coach_id = $post['l_coach_id'];
            $coachlist  = D('Coach')->getCoachInfoById($coach_id);
            if ($post['school_id'] == '') {
                $data['s_school_name_id'] = $coachlist['s_school_name_id'];
            } else {
                $data['s_school_name_id'] = $post['school_id'];
            }
            if ($post['coach_star'] != '') {
                if ($post['coach_star'] >= 5) {
                    $data['i_coach_star'] = 5;

                } elseif ($post['coach_star'] <= 1) {
                    $data['i_coach_star'] = 1;

                } else {
                    $data['i_coach_star'] = $post['coach_star'];
                }
            } else {
                $data['i_coach_star'] = $coachlist['i_coach_star'] != '' ? $coachlist['i_coach_star'] : 1;
            }
            $data['s_coach_name']           = $post['coach_name'] == '' ? $coachlist['s_coach_name'] : $post['coach_name'] ;
            $data['s_coach_phone']          = $post['coach_phone'] == '' ? $coachlist['s_coach_phone'] : $post['coach_phone'];
            $data['s_teach_age']            = $post['coach_age'] == '' ? $coachlist['s_teach_age'] : $post['coach_age'];
            $data['s_coach_sex']            = $post['coach_sex'] == '' ? $coachlist['s_coach_sex'] : $post['coach_sex'];
            $data['s_coach_lesson_id']      = implode(',', $post['lesson_id']) ? implode(',', $post['lesson_id']) : 1;
            $data['s_coach_lisence_id']     = implode(',', $post['license_id']) ? implode(',', $post['license_id']) : 1;
            $data['s_coach_car_id']         = $post['coach_car'] == '' ? $coachlist['s_coach_car_id'] : $post['coach_car'];
            $data['province_id']            = $post['province'] == '' ? $coachlist['province_id'] : $post['province'];
            $data['city_id']                = $post['city'] == '' ? $coachlist['city_id'] : $post['city'];
            $data['area_id']                = $post['area'] == '' ? $coachlist['area_id'] : $post['area'];
            $data['s_coach_address']        = $post['address'] == '' ? $coachlist['s_coach_address'] : $post['address'];
            $data['average_license_time']   = $post['average_license_time'] != '' ? $post['average_license_time'] : $coachlist['average_license_time'];
            $data['lesson2_pass_rate']      = $post['lesson2_pass_rate'] != '' ? $post['lesson2_pass_rate'] : $coachlist['lesson2_pass_rate'];
            $data['lesson3_pass_rate']      = $post['lesson3_pass_rate'] != '' ? $post['lesson3_pass_rate'] : $coachlist['lesson3_pass_rate'];
            if ($coachlist['i_type'] != '') {
                $data['i_type'] = $post['coach_type'] == '' ? $coachlist['i_type'] : $post['coach_type'];
            } else {
                $data['i_type'] = $post['coach_type'] == '' ? 1 : $post['coach_type'];
            }
            $data['order_receive_status']   = $post['is_online'] == '' ? $coachlist['order_receive_status'] : $post['is_online'];
            $data['updatetime']             = time();
            if (!empty($_FILES)) {
                if ($_FILES['license_img']['error'] == 0) {
                    $coach_imgurl = $this->uploadSingleImg('license_img', 'coach/'.$data['s_school_name_id'].'/', 'coachimg_','3145728','../upload/');
                    $data['s_coach_imgurl'] = $coach_imgurl['path'];
                } else {
                    $data['s_coach_imgurl'] = $coachlist['s_coach_imgurl'];
                }
            }
            if ($data['s_coach_address'] == '' && $data['i_type'] == '' && $data['s_coach_car_id'] == '' && $data['city_id'] == '' && $data['area_id'] == '' && $data['province_id'] == '' && $data['s_coach_name'] == '' && $data['s_coach_phone'] == '' && $data['s_coach_lesson_id'] == '' && $data['s_coach_lisence_id'] == '' && $data['s_school_name_id'] == '' ) {
                $this->error('请完善信息');
            }
            
            $coachInfo = D('coach');
            if ($res = $coachInfo->create($data)) {
                $result = $coachInfo->where(array('l_coach_id' => $coach_id))
                    ->fetchSql(false)
                    ->save($res);
                if ($result) {
                    action_log('edit_coach', 'coach', $coach_id, $this->getLoginUserId());
                    $this->success('修改成功');
                } else {
                    $this->success('保存成功');
                }
            } else {
                $this->error('修改失败', U('Coach/addCoach'));
            }
        } else {
            $this->assign('sid', $sid);
            $this->assign('school_id', $school_id);
            $this->assign('coachlist', $coachlist);
            $this->assign('school_list', $school_list);
            $this->assign('licenseInfo', $licenseInfo);
            $this->assign('coach_license', $coach_license);
            $this->assign('coach_lesson', $coach_lesson);
            $this->assign('province_list', $province_list);
            $this->assign('coach_time_config', $coach_time_config);
            $this->assign('coach_time_config_date', $coach_time_config_date);
            $this->assign('coach_time_config_time', $coach_time_config_time);
            $this->assign('carlist', $carlist);
            $this->display(CONTROLLER_NAME.'/'.__FUNCTION__);
        }
    }
    /**
     * 设置教练的排序
     *
     * @return  void
     * @author  wl
     * @date    Dec 09, 2016
     **/
    public function setCoachOrder () {
        if (!IS_AJAX || !IS_POST) {
            $msg = '参数错误';
            $data = '';
            $this->ajaxReturn(array('code' => 101, 'msg' => $msg, 'data' => $data));
        }

        $post = I('post.');
        $update_ok = D('Coach')->updateCoachOrder($post);
        if ($update_ok == 101 || $update_ok == 102 || $update_ok == 105) {
            $code = $update_ok;
            $msg = '参数错误';
            $data = $post['i_order'];

        } else if ($update_ok == 200) {
            $code = $update_ok;
            $msg = '更新成功';
            $data = $post['i_order'];

        } else if ($update_ok == 400) {
            $code = $update_ok;
            $msg = '更新失败';
            $data = $post['i_order'];

        }
        action_log('set_coach_order', 'coach', $post['id'], $this->getLoginUserId());
        $this->ajaxReturn(array('code' => $code, 'msg' => $msg, 'data' => $data), 'JSON');

    }

    /**
     *  设置教练是否支持优惠券的设置
     *
     * @return  void
     * @author  wl
     * @date    Dec 09, 2016
     **/
    public function setSupportStatus () {
        if (IS_AJAX) {
            $post = I('post.');
            $id = $post['id'];
            $status = $post['status'];
            $result = D('Coach')->setSupportStatus($id, $status);
            if ($result['res']) {
                $data = array('code' => 200, 'msg' => '设置成功', 'data' => $result['id']);
            } else {
                $data = array('code' => 400, 'msg' => '设置失败', 'data' => '');
            }
        } else {
            $data = array('code' => 400, 'msg' => '设置失败', 'data' => '');
        }
        action_log('set_coupon_support_status', 'coach', $id, $this->getLoginUserId());
        $this->ajaxReturn($data, 'JSON');
    }

    /**
     *  设置教练是否支持计时培训的设置
     *
     * @return  void
     * @author  wl
     * @date    Jan 10, 2017
     **/
    public function setTrainingSupport () {
        if (IS_AJAX) {
            $post = I('post.');
            $id = $post['id'];
            $status = $post['status'];
            $result = D('Coach')->setTrainingSupport($id, $status);
            if ($result['res']) {
                $data = array('code' => 200, 'msg' => '设置成功', 'data' => $result['id']);
            } else {
                $data = array('code' => 400, 'msg' => '设置失败', 'data' => '');
            }
        } else {
            $data = array('code' => 400, 'msg' => '设置失败', 'data' => '');
        }
        action_log('set_training_support_status', 'coach', $id, $this->getLoginUserId());
        $this->ajaxReturn($data, 'JSON');
    }

    /**
     * 教练的时间模板设置
     *
     * @return  void
     * @author  wl
     * @date    Jan 10, 2017
     **/
    public function setCoachTimeConfig () {
        // $role_id = $this->getRoleId();
        // $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        // if (true !== $permission_check) {
        //     $this->error('Permission Denied!', U('Coach/index'));
        // }
        $param = I('param.');
        $coach_id = $param['id'];
        $school_id = $param['school_id'];
        // $time_list = D('CoachTimeConfig')->getCoachTimeConfig();
        $time_list = D('CoachTimeConfig')->getCoachAmPmConfig($coach_id);
        $current_time = date('Y-m-d', time());
        // 获得时间配置每天都不同
        $coach_time_config = D('CoachTimeConfig')->getCoachTimeConfig($school_id, $coach_id);
        $date_list = $coach_time_config['date_time'];
        $am_time_list = $coach_time_config['am_list'];
        $pm_time_list = $coach_time_config['pm_list'];

        if (IS_POST) {
            $post = I('post.');
            $data['l_coach_id'] = $post['coach_id'] != '' ? $post['coach_id'] : '';
            $data['s_am_subject'] = $post['s_am_subject'] != '' ? $post['s_am_subject'] : '2';
            $data['s_pm_subject'] = $post['s_pm_subject'] != '' ? $post['s_pm_subject'] : '2';
            $data['s_am_time_list'] = isset($post['s_am_time_list']) ? implode(',', $post['s_am_time_list']) : implode('', $post['s_am_time_list']);
            $data['s_pm_time_list'] = isset($post['s_pm_time_list']) ? implode(',', $post['s_pm_time_list']) : implode('', $post['s_pm_time_list']);
            $result = D('CoachTimeConfig')->updateCoachTime($data);
            if ($result) {
                action_log('set_coach_time_config', 'coach', $data['l_coach_id'], $this->getLoginUserId());
                $this->success('设置成功', U('Coach/index'));
            } else {
                action_log('set_coach_time_config', 'coach', $data['l_coach_id'], $this->getLoginUserId());
                $this->success('保存成功', U('Coach/setCoachTimeConfig'));
            }
        } else {
            $this->assign('coach_id', $coach_id);
            $this->assign('school_id', $school_id);
            $this->assign('time_list', $time_list);
            $this->assign('date_list', $date_list);
            $this->assign('current_date', $current_time);
            $this->assign('am_time_list', $am_time_list);
            $this->assign('pm_time_list', $pm_time_list);
            $this->display('Coach/setCoachTimeConfig');
        }
    }

    /**
    * 教练的时间配置设置
    *
    * @return  void
    * @author  wl
    * @date    Jan 11, 2017
    **/
    public function setCoachFinalTime () {
        $post = I('post.');
        $time_config_id = array();
        $l_coach_id = $post['l_coach_id'] != '' ? $post['l_coach_id'] : 0;
        $time_config_id = isset($post['time_config_id']) ? $post['time_config_id'] : array();
        $license_no = isset($post['lisence_no']) ? $post['lisence_no'] : array();
        $subjects = isset($post['subjects']) ? $post['subjects'] : array();
        $single_price = isset($post['single_price']) ? $post['single_price'] : array();
        $time_config_ids = isset($post['time_config_ids']) ? $post['time_config_ids'] : array();
        $currentdate = $post['currentdate'] != '' ? $post['currentdate'] : time();

        // if (empty($time_config_id) || empty($license_no) || empty($subjects) || empty($single_price) || empty($time_config_ids)) {
        //     $data = array('code' => 3, 'msg' => '参数错误', 'data' => '');
        //     $this->ajaxReturn($data, 'JSON');
        // }

        $date_config_arr = explode('-', $currentdate);
        $data['year'] = $date_config_arr[0];
        $data['month'] = $date_config_arr[1];
        $data['day'] = $date_config_arr[2];
        $license_no_arr = array();
        $subjects_arr = array();
        $single_price_arr = array();
        $license_no_arr = array_combine($time_config_ids, $license_no);
        $subjects_arr = array_combine($time_config_ids, $subjects);
        $single_price_arr = array_combine($time_config_ids, $single_price);

        if (!empty($time_config_id)) {
            foreach ($time_config_id as $key => $value) {
                $time_money_config[$value] = $single_price_arr[$value];
                $time_lisence_config[$value] = $license_no_arr[$value];
                $time_lesson_config[$value] = $subjects_arr[$value];
            }
            foreach ($time_money_config as $price_index => $price_value) {
                if (!is_numeric($price_value)) {
                    $data = array('code' => 2, 'msg' => '参数错误', 'data' => '');
                    $this->ajaxReturn($data, 'JSON');
                }
            }

            $data['time_config_id'] = implode(',', $time_config_id);
            $data['time_config_money_id'] = json_encode($time_money_config);
            $data['time_lisence_config_id'] = json_encode($time_lisence_config);
            $data['time_lesson_config_id'] = D('CoachTimeConfig')->JSON($time_lesson_config);
        } else {
            $data['time_config_id'] = '';
            $data['time_config_money_id'] = '';
            $data['time_lisence_config_id'] = '';
            $data['time_lesson_config_id'] = '';
        }
        $data['coach_id'] = $l_coach_id;
        $data['current_time'] = strtotime($currentdate);

        $result = D('CoachTimeConfig')->setCoachTimeConfig($data);
        if ($result['data']) {
            $data = array('code' => 1, 'msg' => '更新成功', 'data' => $data);
        } else {
            $data = array('code' => 400, 'msg' => '更新失败', 'data' => '');
        }
        $this->ajaxReturn($data, 'JSON');
    }

    /**
     * 点击日期获取教练的时间配置
     *
     * @return  void
     * @author  wl
     * @date    Jan 13, 2017
     **/
    public function getCoachCurrentTime () {
        $post = I('post.');
        $coach_id = $post['id'];
        $school_id = $post['school_id'];
        $date = $post['date'] != '' ? $post['date'] : date('Y-m-d', time());
        $currentdate = D('CoachTimeConfig')->getCoachCurrentTime($coach_id, $date);
        // $currentdate = D('CoachTimeConfig')->getCoachCurrentFinalTime($school_id, $coach_id, $date);
        if (!empty($currentdate)) {
            $data = array('code' => '200', 'msg' => '获取成功', 'data' => $currentdate);
        } else {
            $data = array('code' => '200', 'msg' => '获取成功', 'data' => array());
        }
        $this->ajaxReturn($data, 'JSON');
    }

    /**
     * 预览教练信息
     *
     * @return  void
     * @author  wl
     * @date    Sep 12, 2016
     * @update  Dec 06, 2016
     **/
    public function showCoach () {
        // $role_id = $this->getRoleId();
        // $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        // if (true !== $permission_check) {
        //     $this->error('Permission Denied!');
        // }
        $param = I('param.');
        $coach_id = $param['id'];
        $school_id = $param['school_id'];
        $time = date('Y-m-d', time());
        // var_dump($coach_list);exit;
        // $study_star = D('Coach')->getStudyCommentInfo($coach_id);
        // $shifts_star = D('Coach')->getShiftsCommentInfo($coach_id, $school_id);
        // $studystar = $study_star['star_avg'];
        // $studystarcontent = $study_star['star_content'];
        // var_dump($shifts_star);exit;
        // var_dump($coachlist);exit;
        // $current_time_config = D('Coach')->getCoachCurrentTimeConfig($coach_id);
        $coach_lesson = $coachlist['coach_lesson'];
        $coach_license = $coachlist['coach_license'];
        $coach_list = D('Coach')->showCoachInfoById($coach_id);
        $coach_date_time = D('Coach')->getCoachDateTimeConfig($coach_id);
        $this->assign('time', $time);
        $this->assign('coach_id', $coach_id);
        $this->assign('school_id', $school_id);
        $this->assign('coachlist', $coach_list);
        $this->assign('coach_lesson', $coach_lesson);
        $this->assign('coach_license', $coach_license);
        $this->assign('coach_date_time', $coach_date_time);
        // $this->assign('studystar', $studystar);
        // $this->assign('studystarcontent', $studystarcontent);
        // $this->assign('current_time_config', $current_time_config);
        $this->display(CONTROLLER_NAME.'/'.__FUNCTION__);
    }

    /**
     * 获取教练主页
     *
     * @return  void
     * @author  wl
     * @date    Dec 06, 2016
     **/
    public function home () {
        if (IS_AJAX) {
            $coach_id = I('post.coach_id');
            $data = $this->getCoachHomeInfo($coach_id);
            $this->ajaxReturn($data, 'JSON');
        }
    }

    private function getCoachHomeInfo($coach_id) {
        $coach_base_info = D('Coach')->showCoachInfoById($coach_id);
        $appoint_list = D('Coach')->getAppointList($coach_id);
        $signup_list = D('Coach')->getSignUpList($coach_id);
        $comment_list = D('Coach')->getCommentList($coach_id);
        $data = [
            'code'=> 200,
            'msg' => '获取成功',
            'data' => [
                'coach_base_info'=> $coach_base_info,
                'appoint_list'=> $appoint_list,
                'signup_list'=> $signup_list,
                'comment_list'=> $comment_list
            ]
        ];
        return $data;
    }

    /**
     * 获取教练的资料
     *
     * @return  void
     * @author  wl
     * @date    Dec 07, 2016
     **/
    public function profile () {
        if (IS_AJAX) {
            $coach_id = I('post.coach_id');
            $data = $this->getCoachProfileInfo($coach_id);
            $this->ajaxReturn($data, 'JSON');
        }
    }
    private function getCoachProfileInfo ($coach_id) {
        $coach_profile = D('Coach')->showCoachInfoById($coach_id);
        $data = [
            'code'=> 200,
            'msg' => '获取成功',
            'data' => [
                'coach_profile'=> $coach_profile,
            ]
        ];
        return $data;
    }

    /**
     * 获取教练的资料
     *
     * @return  void
     * @author  wl
     * @date    Dec 07, 2016
     **/
    public function appoint () {
        if (IS_AJAX) {
            $date = I('post.date');
            if ($date == '') {
                $date = date('Y-m-d', time());
            }
            $coach_id = I('post.coach_id');
            $school_id = I('post.school_id');
            $data = $this->getCoachAppointInfo($coach_id, $school_id, $date);
            $this->ajaxReturn($data, 'JSON');
        }
    }

    private function getCoachAppointInfo ($coach_id, $school_id, $date) {
        $coach_time_config = D('Coach')->getCoachFinalTimeConfig($coach_id, $school_id, $date);
        $data = [
            'code'=> 200,
            'msg' => '获取成功',
            'data' => $coach_time_config,
        ];
        return $data;
    }

    /**
     * 获取教练的资料
     *
     * @return  void
     * @author  wl
     * @date    Dec 07, 2016
     **/
    public function signup () {
        if (IS_AJAX) {
            $coach_id = I('post.coach_id');
            $data = $this->getCoachSignUpInfo($coach_id);
            $this->ajaxReturn($data, 'JSON');
        }
    }
    private function getCoachSignUpInfo ($coach_id) {
        $coachsignupinfo = D('Coach')->getCoachSignUpInfo($coach_id);
        $coachsignuplist = D('Coach')->getCoachSignUpList($coach_id);
        $data = [
            'code'=> 200,
            'msg' => '获取成功',
            'data' => [
                'coach_signup'=> $coachsignupinfo,
                'coach_signup_list'=> $coachsignuplist,
            ]
        ];
        return $data;
    }



    /**
     * 删除上一天的时间
     *
     * @return  void
     * @author  wl
     * @date    Sep 09, 2016
     **/
    public function delpretime () {
        // $role_id = $this->getRoleId();
        // $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        // if (true !== $permission_check) {
        //     $this->error('Permission Denied!', U('Coach/editCoach'));
        // }
        $post = I('post.');
        $coach_id = I('post.id');
        $date = $post['day_config'];
        $res = D('Coach')->delPreTime($coach_id, $date);
        if($res) {
            $data = array('code' => 200, 'data' => $res);
        } else {
            $data = array('code' => 103, 'data' => $res);
        }
        $this->ajaxReturn($data, 'JSON');
    }

    /**
     * 删除此教练所有的的时间数据
     *
     * @return  void
     * @author  wl
     * @date    Sep 10, 2016
     **/
    public function delAllTime () {
        // $role_id = $this->getRoleId();
        // $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        // if (true !== $permission_check) {
        //     $this->error('Permission Denied!', U('Coach/editCoach'));
        // }
        $coach_id = I('post.id');
        $res = D('Coach')->delAllTime($coach_id);
        if($res) {
            $data = array('code' => 200, 'data' => $res);
        } else {
            $data = array('code' => 103, 'data' => $res);
        }
        $this->ajaxReturn($data, 'JSON');
    }

    /**
     * 保存时间段
     *
     * @return  void
     * @author  wl
     * @date    Sep 10, 2016
     **/
    public function saveTime () {
        // $role_id = $this->getRoleId();
        // $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        // if (true !== $permission_check) {
        //     $this->error('Permission Denied!', U('Coach/editCoach'));
        // }
        $data = array();
        $time_money_config = '';
        $lisence_no_post = '';
        $subjects_post = '';
        $post = I('post.');
        $coach_id = $post['coach_id'];
        $date_config = $post['date_config'];
        $time_money_config = $post['time_money_config'];
        $lisence_no_post = $post['lisence_no'];
        $subjects_post = $post['subjects'];
        if ($time_money_config != '') {
            $time_config = array_filter($post['time_money_config']);
            // 时间id和价格json
            $data['time_config_money_id'] = json_encode($time_config);
            $data['time_config_id'] = implode(',', array_keys($time_config));
        } else {
            $data['time_config_money_id'] = '';
            $data['time_config_id'] = '';
        }
        if ($lisence_no_post != '') {
            $lisence_no = array_filter($post['lisence_no']);
            // 时间id对应牌照json
            $data['time_lisence_config_id'] = json_encode($lisence_no);
        } else {
            $data['time_lisence_config_id'] = '';
        }

        if ($subjects_post != '') {
            $subjects = array_filter($post['subjects']);
            // 时间id对应的科目json
            $data['time_lesson_config_id'] = D('Coach')->JSON($subjects);
        } else {
            $data['time_lesson_config_id'] = '';
        }

        $data['coach_id'] = $coach_id;

        // 获取当前年份
        // $data['year'] = date('Y', time()); // 年
        $date_config_arr = explode('-', $date_config);
        $data['year'] = $date_config_arr[0]; // 年
        $data['month'] = $date_config_arr[1]; // 月
        $data['day'] = $date_config_arr[2]; // 日
        $data['current_time'] = strtotime($data['year'].'-'.$data['month'].'-'.$data['day']);// 当前时间（年月日）
        $result = D('Coach')->updateCoachTime($data);
        if ($result) {
            $res = array('code' => 200, 'data' => $result);
        } else {
            $res = array('code' => 103, 'data' => '');
        }
        $this->ajaxReturn($res, 'JSON');
    }


    /**
     * 通过school_id获得车辆表中的信息
     *
     * @return  void
     * @author  wl
     * @date    Sep 08, 2016
     **/
    public function getCarsByIds () {
        $school_id = $this->getLoginauth();
        if ($school_id == 0) {
            $sid = I('post.school_id');
            $carslist = D('Cars')->getCarsByIds($sid);
            if (is_array($carslist)) {
                $data = array('code' => 200, 'data' => $carslist);
            } else {
                $data = array('code' => 200, 'data' => array());
            }
            exit(json_encode($data));
        } else {
            $carslist = D('Cars')->getCarsByIds($school_id);
            if (is_array($carslist)) {
                $data = array('code' => 200, 'data' => $carslist);
            } else {
                $data = array('code' => 200, 'data' => array());
            }
            exit(json_encode($data));
        }
    }

// 2.模板关联管理
    /**
     * 教练模板关联列表管理的展示
     *
     * @return  void
     * @author  wl
     * @date    Oct 19, 2016
     **/
    public function coachTempRelation () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Coach/index'));
        }
        $school_id = $this->getLoginauth();
        $temprelationlist = D('Coach')->getTempRelation($school_id);
        $this->assign('school_id', $school_id);
        $this->assign('page', $temprelationlist['page']);
        $this->assign('count', $temprelationlist['count']);
        $this->assign('temprelationlist', $temprelationlist['temprelationlist']);
        $this->display('Coach/coachTempRelation');
    }

    /**
     * 教练模板关联列表管理的展示
     *
     * @return  void
     * @author  wl
     * @date    Oct 20, 2016
     **/
    public function searchCoachTempRelation () {
        $school_id = $this->getLoginauth();
        $param = I('param.');
        $is_default = intval($param['is_default']);
        $is_online = intval($param['is_online']);
        $weekday = intval($param['weekday']);
        $temp_type = intval($param['temp_type']);
        $search_info = (string)$param['search_info'];
        $s_keyword = trim((string)$param['s_keyword']);
        if ($s_keyword == '' && $weekday == '' && $is_online == '' && $is_default == '' && $temp_type == '' && $search_info == '') {
            $this->redirect('Coach/coachTempRelation');
        } else {
            $temprelationlist = D('Coach')->searchTempRelation($param, $school_id);
            $this->assign('school_id', $school_id);
            $this->assign('weekday', $weekday);
            $this->assign('temp_type', $temp_type);
            $this->assign('search_info', $search_info);
            $this->assign('is_online', $is_online);
            $this->assign('s_keyword', $s_keyword);
            $this->assign('is_default', $is_default);
            $this->assign('page', $temprelationlist['page']);
            $this->assign('count', $temprelationlist['count']);
            $this->assign('temprelationlist', $temprelationlist['temprelationlist']);
            $this->display('Coach/coachTempRelation');
        }
    }

    /**
     * 通过owner_type获取模板角色的信息
     *
     * @return  void
     * @author  wl
     * @date    Oct 20, 2016
     **/
    public function getTempOwnerName () {
        $school_id = $this->getLoginauth();
        $type = I('post.temp_type');
        $rolelist = D('Coach')->getTempOwnerName($type, $school_id);
        if (is_array($rolelist)) {
            $data = array('code' => 200, 'data' => $rolelist);
        } else {
            $data = array('code' => 200, 'data' => array());
        }
        exit(json_encode($data));
    }

    /**
     * 教练模板关联列表管理的添加功能
     *
     * @return  void
     * @author  wl
     * @date    Oct 20, 2016
     **/
    public function addCoachTempRelation () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Coach/coachTempRelation'));
        }
        $licenselist = D('Coach')->getLicenseInfo();
        if (IS_POST) {
            $post = I('post.');
            $data['temp_name'] = $post['temp_name'] ? $post['temp_name'] : '';
            $data['temp_type'] = $post['temp_type'] ? $post['temp_type'] : 2;
            $data['temp_owner_id'] = $post['temp_owner_id'] ? $post['temp_owner_id'] : 1;
            $data['weekday'] = $post['weekday'] ? $post['weekday'] : '';
            $data['is_default'] = $post['is_default'] ? $post['is_default'] : 1;
            $data['is_online'] = $post['is_online'] ? $post['is_online'] : 1;
            $data['addtime'] = time();
            $data['price'] = intval($post['price']) > 0 ? intval($post['price']) : 0;
            $data['license_id'] = intval($post['license_id']);
            $data['lesson_id'] = intval($post['lesson_id']);

            if ($data['license_id'] != '') {
                $license_name = D('Coach')->getLicenseInfoById($data['license_id']);
                if (!empty($license_name)) {
                    $data['license_name'] = $license_name['license_name'];
                } else {
                    $data['license_name'] = '';
                }
            } else {
                $data['license_name'] = '';
            }

            switch ($data['lesson_id']) {
                case '1':
                    $data['lesson_name'] = '科目一';
                    break;
                case '2':
                    $data['lesson_name'] = '科目二';
                    break;
                case '3':
                    $data['lesson_name'] = '科目三';
                    break;
                case '4':
                    $data['lesson_name'] = '科目四';
                    break;
            }
            
            if ($data['temp_type'] != '' && $data['temp_owner_id'] != '') {
                $temp_owner_name = D('Coach')->getOwnerNameById( $data['temp_owner_id'], $data['temp_type'] );
                $data['temp_owner_name'] = $temp_owner_name;
            }

            if ($data['temp_name'] == '' && $data['temp_type'] == '' && $data['temp_owner_id'] == '' && $data['weekday'] == '') {
                $this->error('请完善信息', U('Coach/addCoachTempRelation'));
            }
            // check repetition
            $checkRepTemp = D('coach')->checkRepTemp($data['temp_type'], $data['temp_owner_id'], $data['temp_name']);
            if ($checkRepTemp == true) {
                $this->error('此用户的模板已经存在', U('Coach/addCoachTempRelation'));
            }
            $template_relation = D('template_relationship');
            if ($res = $template_relation->create($data)) {
                $result = $template_relation->add($res);
                if ($result) {
                    action_log('add_coach_temprelation', 'template_relationship', $result, $this->getLoginUserId());
                    $this->success('添加成功', U('Coach/coachTempRelation'));
                } else {
                    $this->error('添加失败', U('Coach/addCoachTempRelation'));
                }
            } else {
                $this->error('添加失败', U('Coach/addCoachTempRelation'));
            }
        } else {
            $this->assign('licenselist', $licenselist);
            $this->display('Coach/addCoachTempRelation');
        }
    }

    /**
     * 教练模板关联列表管理的编辑功能
     *
     * @return  void
     * @author  wl
     * @date    Oct 20, 2016
     **/
    public function editCoachTempRelation () {
         $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Coach/coachTempRelation'));
        }
        $id = I('param.id');
        $licenselist = D('Coach')->getLicenseInfo();
        $temprelationlist = D('Coach')->getTempRelationById($id);
        if (IS_POST) {
            $post = I('post.');
            $data['id'] = $post['id'];
            $data['temp_name'] = $post['temp_name'] == '' ? $temprelationlist['temp_name'] : $post['temp_name'];
            $data['temp_type'] = $post['temp_type'] == 0 ? $temprelationlist['temp_type'] : $post['temp_type'];
            $data['temp_owner_id'] = $post['temp_owner_id'] == 0 ? $temprelationlist['temp_owner_id'] : $post['temp_owner_id'];
            $data['weekday'] = $post['weekday'] == '' ? $temprelationlist['weekday'] : $post['weekday'];
            $data['is_default'] = $post['is_default'] == '' ? $temprelationlist['is_default'] : $post['is_default'];
            $data['is_online'] = $post['is_online'] == '' ? $temprelationlist['is_online'] : $post['is_online'];
            $data['updatetime'] = time();
            $data['price'] = intval($post['price']) > 0 ? intval($post['price']) : 0;
            $data['license_id'] = (intval($post['license_id']) != 0 || intval($post['license_id']) != '')? intval($post['license_id']) : $temprelationlist['license_id'];
            $data['lesson_id'] = (intval($post['lesson_id']) != 0 || intval($post['lesson_id']) != '') ? intval($post['lesson_id']) : $temprelationlist['lesson_id'];;

            if ($data['license_id'] != '') {
                $license_name = D('Coach')->getLicenseInfoById($data['license_id']);
                if (!empty($license_name)) {
                    $data['license_name'] = $license_name['license_name'];
                } else {
                    $data['license_name'] = $temprelationlist['license_name'];
                }
            } else {
                $data['license_name'] = $temprelationlist['license_name'];
            }

            switch ($data['lesson_id']) {
                case '1':
                    $data['lesson_name'] = '科目一';
                    break;
                case '2':
                    $data['lesson_name'] = '科目二';
                    break;
                case '3':
                    $data['lesson_name'] = '科目三';
                    break;
                case '4':
                    $data['lesson_name'] = '科目四';
                    break;
            }
            if ($data['temp_type'] != '' && $data['temp_owner_id'] != '') {
                $temp_owner_name = D('Coach')->getOwnerNameById( $data['temp_owner_id'], $data['temp_type'] );
                $data['temp_owner_name'] = $temp_owner_name;
            } else {
                $data['temp_owner_name'] = $temprelationlist['temp_owner_name'];
            }
            
            $template_relation = D('template_relationship');
            if ($res = $template_relation->create($data)) {
                $result = $template_relation->where(array('id' => $data['id']))->fetchSql(false)->save($res);
                if ($result) {
                    action_log('edit_coach_temprelation', 'template_relationship', $id, $this->getLoginUserId());
                    $this->success('编辑成功', U('Coach/coachTempRelation'));
                } else {
                    $this->error('未做任何修改', U('Coach/editCoachTempRelation'));
                }
            } else {
                $this->error('修改失败', U('Coach/editCoachTempRelation'));
            }
        } else {
            $this->assign('licenselist', $licenselist);
            $this->assign('temprelationlist', $temprelationlist);
            $this->display('Coach/editCoachTempRelation');
        }
    }


    /**
     * 删除模板
     *
     * @return  void
     * @author  wl
     * @date    Oct 20, 2016
     **/
    public function delCoachTempRelation () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Coach/coachTempRelation'));
        }
        if(IS_AJAX){
            $cid = I('post.id');
            $res = D('Coach')->delCoachTempRelation($cid);
            if($res){
                $data=array('code'=>200,'msg'=>"删除成功",'data'=>'');
            } else {
                $data=array('code'=>101,'msg'=>"删除失败",'data'=>'');
            }
        } else {
            $data=array('code'=>102,'msg'=>"删除失败",'data'=>'');
        }
        action_log('del_coach_temprelation', 'template_relationship', $cid, $this->getLoginUserId());
        $this->ajaxReturn($data,'JSON');
    }

    /**
     * 设置模板是否是默认的
     *
     * @return  void
     * @author  wl
     * @date    Oct 20, 2016
     **/
    public function setCoachDefault () {
        $id = I('post.id');
        $status = I('post.status');
        $list = D('Coach')->setCoachDefault($id, $status);
        if($list['res']) {
            $data = array('code'=>200, 'msg'=>"设置成功", 'data'=>$list['id']);
        } else {
            $data = array('code'=>102, 'msg'=>"设置失败", 'data'=>'');
        }
        action_log('set_coachtemprelation_default', 'template_relationship', $id, $this->getLoginUserId());
        $this->ajaxReturn($data, 'JSON');
    }

    /**
     * 设置是否在线的
     *
     * @return  void
     * @author  wl
     * @date    Oct 20, 2016
     **/
    public function setCoachOnline () {
        $id = I('post.id');
        $status = I('post.status');
        $list = D('Coach')->setCoachOnline($id, $status);
        if($list['res']) {
            $data = array('code'=>200, 'msg'=>"设置成功", 'data'=>$list['id']);
        } else {
            $data = array('code'=>102, 'msg'=>"设置失败", 'data'=>'');
        }
        action_log('set_coachtemprelation_online', 'template_relationship', $id, $this->getLoginUserId());
        $this->ajaxReturn($data, 'JSON');
    }

// 3.模板列表部分
    /**
     * 时间模板列表的列表展示功能
     *
     * @return  void
     * @author  wl
     * @date    Oct 20, 2016
     **/
    public function timeConfTemp () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Coach/index'));
        }
        $school_id = $this->getLoginauth();
        $timeConfTempList = D('Coach')->getTimeConfTemp($school_id);
        $this->assign('page', $timeConfTempList['page']);
        $this->assign('count', $timeConfTempList['count']);
        $this->assign('timeconftemplist', $timeConfTempList['timeconftemplist']);
        $this->display('Coach/timeConfTemp');
    }

    /**
     * 时间模板列表的列表的搜索功能
     *
     * @return  void
     * @author  wl
     * @date    Oct 21, 2016
     **/
    public function searchTimeConfTemp () {
        $param = I('param.');
        $s_keyword = trim((string)$param['s_keyword']);
        $search_info = trim((string)$param['search_info']);
        if ($s_keyword == '' && $search_info == '') {
            $this->redirect('Coach/timeConfTemp');
        }
        $school_id = $this->getLoginauth();
        $timeConfTempList = D('Coach')->searchTimeConfTemp($param, $school_id);
        $this->assign('s_keyword', $s_keyword);
        $this->assign('school_id', $school_id);
        $this->assign('search_info', $search_info);
        $this->assign('page', $timeConfTempList['page']);
        $this->assign('count', $timeConfTempList['count']);
        $this->assign('timeconftemplist', $timeConfTempList['timeconftemplist']);
        $this->display('Coach/timeConfTemp');
    }

    /**
     * 通过模板ID获取价格，科目和牌照
     * @param temp_id
     * @return void
     **/
    public function getTempInfo () {
        if (IS_AJAX) {
            $post = I('post.');
            $temp_id = $post['temp_id'];
            $temp_info = D('Coach')->getTempRelationById($temp_id);
            if ( ! empty($temp_info)) {
                $data = array('code' => 200, 'msg' => '获取成功', 'data' => $temp_info);
            } else {
                $data = array('code' => 400, 'msg' => '获取失败', 'data' => array());
            }
        } else {
            $data = array('code' => 400, 'msg' => '获取失败', 'data' => array());
        }

        $this->ajaxReturn($data, 'JSON');

    }


    /**
     * 时间模板列表的添加功能
     *
     * @return  void
     * @author  wl
     * @date    Oct 21, 2016
     **/
    public function addTimeConfTemp () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Coach/timeConfTemp'));
        }
        $school_id = $this->getLoginauth();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!');
        }
        $temprelationlist = D('Coach')->getTempName($school_id);
        $licenselist = D('Coach')->getLicenseInfo();
        if (IS_POST) {
            $post = I('post.');
            $data['temp_id']        = $post['temp_id'] ? $post['temp_id'] : '';
            $data['lesson_id']      = $post['lesson_id'] ? $post['lesson_id'] : 2;
            $data['license_id']     = $post['license_id'] ? $post['license_id'] : 2;
            $data['start_hour']     = $post['start_hour'] ? $post['start_hour'] : '';
            $data['end_hour']       = $post['end_hour'] ? $post['end_hour'] : '';
            $data['start_minute']   = $post['start_minute'] ? $post['start_minute'] : 00;
            $data['end_minute']     = $post['end_minute'] ? $post['end_minute'] : 00;
            if ($post['start_minute'] < 10) {
                $data['start_time']     = $post['start_hour'].':'.'0'.$post['start_minute'] ? $post['start_hour'].':'.'0'.$post['start_minute'] : 0;
            } else {
                $data['start_time']     = $post['start_hour'].':'.$post['start_minute'] ? $post['start_hour'].':'.$post['start_minute'] : 0;
            }

            if ($post['end_minute'] < 10) {
                $data['end_time']       = $post['end_hour'].':'.'0'.$post['end_minute'] ? $post['end_hour'].':'.'0'.$post['end_minute'] : 0;
            } else {
                $data['end_time']       = $post['end_hour'].':'.$post['end_minute'] ? $post['end_hour'].':'.$post['end_minute'] : 0;
            }
            
            if ($post['lesson_id'] == 2) {
                $data['lesson_name'] = '科目二';
            } else if ($post['lesson_id'] == 3) {
                $data['lesson_name'] = '科目三';
            }

            if ($data['license_id'] != '') {
                $license_name = D('Coach')->getLicenseInfoById($data['license_id']);
                if (!empty($license_name)) {
                    $data['license_name'] = $license_name['license_name'];
                } else {
                    $data['license_name'] = '';
                }
            } else {
                $data['license_name'] = '';
            }

            $data['lesson_time']    = $post['lesson_time'] ? $post['lesson_time'] : floor( ( strtotime($data['end_time'])- strtotime($data['start_time']))/3600 );
            $data['is_online']      = $post['is_online'] ? $post['is_online'] : 1;
            $data['price']          = $post['price'] ? $post['price'] : 130;
            $data['max_user_num']   = $post['max_user_num'] ? $post['max_user_num'] : 1;
            $data['addtime']        = time();
            if ($data['temp_id'] == '' && $data['lesson_id'] == '' 
                && $data['start_hour'] == '' && $data['end_hour'] == ''
                && $data['start_minute'] == '' && $data['end_minute'] == ''
                && $data['lesson_time'] == ''
            ) {
                $this->error('请完善信息', U('Coach/addTimeConfTemp'));
            }
            $timeConfTemp = D('time_config_template');
            if ($res = $timeConfTemp->create($data)) {
                $result = $timeConfTemp->fetchSql(false)->add($res);
                if ($result) {
                    action_log('add_timeconfigtemp', 'time_config_template', $result, $this->getLoginUserId());
                    $this->success('添加成功', U('Coach/timeConfTemp'));
                } else {
                    $this->error('添加失败', U('Coach/addTimeConfTemp'));
                }
            } else {
                $this->error('添加失败', U('Coach/addTimeConfTemp'));
            }
        } else {
            $this->assign('licenselist', $licenselist);
            $this->assign('temprelationlist', $temprelationlist);
            $this->display('Coach/addTimeConfTemp');
        }
    }

    /**
     * 时间模板列表的编辑功能
     *
     * @return  void
     * @author  wl
     * @date    Oct 21, 2016
     **/
    public function editTimeConfTemp () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Coach/timeConfTemp'));
        }
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!');
        }
        $id = I('param.id');
        $temprelationlist = D('Coach')->getTempName();
        $timeconftemplist = D('Coach')->getTimeConfTempById($id);
        $licenselist = D('Coach')->getLicenseInfo();
        if (IS_POST) {
            $post = I('post.');
            $data['temp_id']        = $post['temp_id'] == '' ? $timeconftemplist['temp_id'] : $post['temp_id'];
            $data['lesson_id']      = $post['lesson_id'] == '' ? $timeconftemplist['lesson_id'] : $post['lesson_id'];
            $data['license_id']     = $post['license_id'] == '' ? $timeconftemplist['license_id'] : $post['license_id'];
            $data['start_hour']     = $post['start_hour'] == '' ? $timeconftemplist['start_hour'] : $post['start_hour'];
            $data['end_hour']       = $post['end_hour'] == '' ? $timeconftemplist['end_hour'] : $post['end_hour'];
            $data['start_minute']   = $post['start_minute'] == '' ? $timeconftemplist['start_minute'] : $post['start_minute'];
            $data['end_minute']     = $post['end_minute'] == '' ? $timeconftemplist['end_minute'] : $post['end_minute'];
            if ($post['start_minute'] < 10) {
                $data['start_time']     = $post['start_hour'].':'.'0'.$post['start_minute'] ? $post['start_hour'].':'.'0'.$post['start_minute'] : 0;
            } else {
                $data['start_time']     = $post['start_hour'].':'.$post['start_minute'] ? $post['start_hour'].':'.$post['start_minute'] : 0;
            }

            if ($post['end_minute'] < 10) {
                $data['end_time']       = $post['end_hour'].':'.'0'.$post['end_minute'] ? $post['end_hour'].':'.'0'.$post['end_minute'] : 0;
            } else {
                $data['end_time']       = $post['end_hour'].':'.$post['end_minute'] ? $post['end_hour'].':'.$post['end_minute'] : 0;
            }

            if ($post['lesson_id'] == 2) {
                $data['lesson_name'] = '科目二';
            } else if ($post['lesson_id'] == 3) {
                $data['lesson_name'] = '科目三';
            }

            if ($data['license_id'] != '') {
                $license_name = D('Coach')->getLicenseInfoById($data['license_id']);
                if (!empty($license_name)) {
                    $data['license_name'] = $license_name['license_name'];
                } else {
                    $data['license_name'] = $timeconftemplist['license_name'];
                }
            } else {
                $data['license_name'] = $timeconftemplist['license_name'];
            }

            $data['lesson_time']    = $post['lesson_time'] ? $post['lesson_time'] : floor( ( strtotime($data['end_time'])- strtotime($data['start_time']))/3600 );
            $data['is_online']      = $post['is_online'] ? $post['is_online'] : 1;
            $data['price']          = $post['price'] == '' ? $timeconftemplist['price'] : $post['price'];
            $data['max_user_num']   = $post['max_user_num'] == '' ? $timeconftemplist['max_user_num'] : $post['max_user_num'];
            $data['updatetime']     = time();
            if ($data['temp_id'] == '' && $data['lesson_id'] == '' 
                && $data['start_hour'] == '' && $data['end_hour'] == ''
                && $data['start_minute'] == '' && $data['end_minute'] == ''
                && $data['lesson_time'] == ''
            ) {
                $this->error('请完善信息', U('Coach/addTimeConfTemp'));
            }
            $timeConfTemp = D('time_config_template');
            if ($res = $timeConfTemp->create($data)) {
                $result = $timeConfTemp->fetchSql(false)
                    ->where(array('id' => $id))
                    ->save($res);
                if ($result) {
                    action_log('edit_timeconfigtemp', 'time_config_template', $id, $this->getLoginUserId());
                    $this->success('编辑成功', U('Coach/timeConfTemp'));
                } else {
                    $this->error('编辑失败', U('Coach/editTimeConfTemp'));
                }
            } else {
                $this->error('编辑失败', U('Coach/editTimeConfTemp'));
            }
        } else {
            $this->assign('licenselist', $licenselist);
            $this->assign('temprelationlist', $temprelationlist);
            $this->assign('timeconftemplist', $timeconftemplist);
            $this->display('Coach/editTimeConfTemp');
        }
    }

    /**
     * 逻辑删除时间模板
     *
     * @return  void
     * @author  wl
     * @date    Oct 21, 2016
     **/
    public function delTimeConfTemp () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Coach/timeConfTemp'));
        }
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!');
        }
        if (IS_AJAX) {
            $id = I('post.id');
            $res = D('Coach')->delTimeConfTemp($id);
            if ($res) {
                $data = array('code' => 200, 'msg' => '删除成功' ,'data' => $id);
            } else {
                $data = array('code' => 400, 'msg' => '删除失败' ,'data' => '');
            }
        }
        action_log('del_timeconfigtemp', 'time_config_template', $id, $this->getLoginUserId());
        $this->ajaxReturn($data, 'JSON');
    }

    /**
     * 设置时间模板是否在线的状态
     *
     * @return  void
     * @author  wl
     * @date    Oct 21, 2016
     **/
    public function setTimeTempOnline () {
        $id = I('post.id');
        $status = I('post.status');
        $list = D('Coach')->setTimeTempOnline($id, $status);
        if($list['res']) {
            $data = array('code'=>200, 'msg'=>"设置成功", 'data'=>$list['id']);
        } else {
            $data = array('code'=>102, 'msg'=>"设置失败", 'data'=>'');
        }
        action_log('set_timeconfigtemp_online', 'time_config_template', $id, $this->getLoginUserId());
        $this->ajaxReturn($data, 'JSON');
    }

// 4.绑定教练列表部分
    /**
     * 绑定教练列表的展示
     *
     * @return  void
     * @author  wl
     * @date    Dec 15, 2016
     **/
    public function coachUserRelation () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Coach/index'));
        }
        $school_id = $this->getLoginauth();
        $coachuserlist = D('Coach')->getCoachUserRelationList($school_id);
        $this->assign('school_id', $school_id);
        $this->assign('page', $coachuserlist['page']);
        $this->assign('count', $coachuserlist['count']);
        $this->assign('coachuserlist', $coachuserlist['coachuserlist']);
        $this->display('Coach/coachUserRelation');
    }

    /**
     * 搜索教练与学员的绑定关系
     *
     * @return  void
     * @author  wl
     * @date    Dec 15, 2016
     **/
    public function searchCoachUserRelation () {
        $param = I('param.');
        $school_id = $this->getLoginauth();
        $s_keyword = trim((string)$param['s_keyword']);
        $bind_status = trim((string)$param['bind_status']);
        $search_info = trim((string)$param['search_info']);
        if ($s_keyword == '' && $search_info == '' && $bind_status == '') {
            $this->redirect('Coach/coachUserRelation');
        } else {
            $coachuserlist = D('Coach')->searchCoachUserRelation($param, $school_id);
            $this->assign('school_id', $school_id);
            $this->assign('s_keyword', $s_keyword);
            $this->assign('bind_status', $bind_status);
            $this->assign('search_info', $search_info);
            $this->assign('page', $coachuserlist['page']);
            $this->assign('count', $coachuserlist['count']);
            $this->assign('coachuserlist', $coachuserlist['coachuserlist']);
            $this->display('Coach/coachUserRelation');
        }
    }

    /**
     * 设置教练的绑定状态
     *
     * @return  void
     * @author  wl
     * @date    Dec 15, 2016
     **/
    public function setCoachBindStatus () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Coach/coachUserRelation'));
        }
        if (IS_AJAX) {
            $post = I('post.');
            $id = $post['id'];
            $status = $post['status'];
            $result = D('Coach')->updateCoachBindStatus($id, $status);
            if ($result) {
                action_log('set_coach_bindstatus', 'coach_user_relation', $id, $this->getLoginUserId());
                $data = array('code' => 200 , 'msg' => '设置成功' , 'data' => $result);
            } else {
                action_log('set_coach_bindstatus', 'coach_user_relation', $id, $this->getLoginUserId());
                $data = array('code' => 400, 'msg' => '设置失败', 'data' => 2);
            }

        } else {
            action_log('set_coach_bindstatus', 'coach_user_relation', $id, $this->getLoginUserId());
            $data = array('code' => 400, 'msg' => '设置失败', 'data' => 2);
        }
        $this->ajaxReturn($data, 'JSON');
    }

// 5.教练认证状态列表
    /**
     * 获取教练认证状态列表
     *
     * @return  void
     * @author  wl
     * @date    Jan 04, 2017
     **/
    public function coachCertification () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Coach/index'));
        }
        $school_id = $this->getLoginauth();
        $coachcertificationlist = D('CoachInfo')->getCoachCertificationList($school_id);
        $this->assign('school_id', $school_id);
        $this->assign('page', $coachcertificationlist['page']);
        $this->assign('count', $coachcertificationlist['count']);
        $this->assign('certification_status', 2);
        $this->assign('coachcertificationlist', $coachcertificationlist['coachcertificationlist']);
        $this->display('Coach/coachCertification');
    }

    /**
     * 获取教练认证状态列表
     *
     * @return  void
     * @author  wl
     * @date    Jan 04, 2017
     **/
    public function searchCoachCertification () {
        $school_id = $this->getLoginauth();
        $param = I('param.');
        $s_keyword = trim ((string)$param['s_keyword']);
        $search_info = trim ((string)$param['search_info']);
        $certification_status = trim ((int)$param['certification_status']);
        if ($s_keyword == '' && $search_info == '' && $certification_status == '') {
            $thsi->redirect('Coach/coachCertification');
        }
        $coachcertificationlist = D('CoachInfo')->searchCoachCertification($param, $school_id);
        $this->assign('school_id', $school_id);
        $this->assign('s_keyword', $s_keyword);
        $this->assign('search_info', $search_info);
        $this->assign('certification_status', $certification_status);
        $this->assign('page', $coachcertificationlist['page']);
        $this->assign('count', $coachcertificationlist['count']);
        $this->assign('coachcertificationlist', $coachcertificationlist['coachcertificationlist']);
        $this->display('Coach/coachCertification');
    }

    /**
     * 设置教练的认证状态
     *
     * @return  void
     * @author  wl
     * @date    Jan 05, 2017
     **/
    public function setCoachCertification () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Coach/coachCertification'));
        }
        if (IS_AJAX) {
            $post = I('post.');
            $id = $post['id'];
            $status = $post['status'];
            $result = D('CoachInfo')->updateCoachCertification($id, $status);
            if ($result) {
                action_log('set_coach_certification', 'coach', $id, $this->getLoginUserId());
                $data = array('code' => 200 , 'msg' => '设置成功' , 'data' => $result);
            } else {
                action_log('set_coach_certification', 'coach', $id, $this->getLoginUserId());
                $data = array('code' => 400, 'msg' => '设置失败', 'data' => 2);
            }

        } else {
            action_log('set_coach_certification', 'coach', $id, $this->getLoginUserId());
            $data = array('code' => 400, 'msg' => '设置失败', 'data' => 2);
        }
        $this->ajaxReturn($data, 'JSON');

    }


    /**
     * 教练证图片的展示
     *
     * @return  void
     * @author  wl
     * @date    Jan 05, 2017
     **/
    public function showLicenseImgurl () {
        $param = I('param.');
        $coach_id = $param['id'];
        $license_imgurl = $param['license_imgurl'];
        $this->assign('license_imgurl', $license_imgurl);
        $this->display('Coach/showLicenseImgurl');
    }

    /**
     * 教练身份证图片的展示
     *
     * @return  void
     * @author  wl
     * @date    Jan 05, 2017
     **/
    public function showIDcardImgurl () {
        $param = I('param.');
        $coach_id = $param['id'];
        $idcard_imgurl = $param['idcard_imgurl'];
        $this->assign('idcard_imgurl', $idcard_imgurl);
        $this->display('Coach/showIDcardImgurl');
    }

    /**
     * 教练形象的图片的展示
     *
     * @return  void
     * @author  wl
     * @date    Jan 05, 2017
     **/
    public function showPersonalImgurl () {
        $param = I('param.');
        $coach_id = $param['id'];
        $personal_imgurl = $param['personal_imgurl'];
        $this->assign('personal_imgurl', $personal_imgurl);
        $this->display('Coach/showPersonalImgurl');
    }

    /**
     * 教练车图片的展示
     *
     * @return  void
     * @author  wl
     * @date    Jan 05, 2017
     **/
    public function showCoachCarImgurl () {
        $param = I('param.');
        $coach_id = $param['id'];
        $car_imgurl = $param['car_imgurl'];
        $this->assign('car_imgurl', $car_imgurl);
        $this->display('Coach/showCoachCarImgurl');
    }




// 公共部分
    /**
     * 获取城市
     *
     * @return
     * @author sun
     **/
    public function getCity() {
        $province_id = I('param.province_id');
        $city_list = D('City')->getCityList($province_id);
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
        $area_list = D('Area')->getAreaList($city_id);
        $html = "";
        foreach ($area_list as $key => $value) {
            $html .= "<option value='".$value['areaid']."'>".$value['area']."</option>";
        }
        echo $html;
    }




}

?>
