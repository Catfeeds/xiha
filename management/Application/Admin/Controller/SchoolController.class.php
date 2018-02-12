<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Page;
use Think\Upload;

/**
 * @author Gao
 * 驾校管理模块
 * * 1. 班制管理
 * * 2. 报名点管理
 * * 3. 轮播图管理
 *
 */

class SchoolController extends BaseController {
    //构造函数，判断是否登录
    public function _initialize() {
        if(!session('loginauth')) {
            $this->redirect('Public/login');
            exit();
        }
    }
// 1.驾校列表
    //展示驾校信息
    public function show() {
        $id = I('param.id');    //自动验证传递的参数（驾校id），并赋值
        $school = M('school');  //实例化操作数据表school
        $condition['l_school_id'] = $id;    //将驾校id作为条件
        $schoolinfo = $school->where($condition)->find();       //在数据库中查找驾校信息
        $this->assign('schoolinfo',$schoolinfo);    //对模板变量赋值   
        $this->display(CONTROLLER_NAME.'/'.__FUNCTION__);   
    }
    
    /**
     * 获得驾校或代理商的信息
     *
     * @return  void
     * @author  wl
     * @date    july 27, 2016
     * @update  july 28, 2016
     **/
    public function index () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Admin/Index/welcome'));
        }
        //获取省份列表
        $province_list = D('Province')->getProvinceList();
        $this->assign('province_list', $province_list);
        $school_id = $this->getLoginauth();
        $school_info = D('School')->getSchoolInfo();
        $this->assign('school_id', $school_id);
        $this->assign('page', $school_info['page']);
        $this->assign('count', $school_info['count']);
        $this->assign('school_info', $school_info['school_info']);
        $this->display(CONTROLLER_NAME.'/'.__FUNCTION__);

    }
    /**
     * 根据相关条件搜索代理驾校
     *
     * @return  void
     * @author  wl
     * @date    july 28, 2016
     **/
    public function searchSchool () {
        $school_id = $this->getLoginauth();
        $param = I('param.');
        $dwxz = trim((int)$param['dwxz']);
        $is_show = trim((int)$param['is_show']);
        $is_hot = trim((int)$param['is_hot']);
        $support_coupon = trim((int)$param['support_coupon']);
        $province = trim((int)$param['province']);
        $city = trim((int)$param['city']);
        $area = trim((int)$param['area']);
        $s_keyword = trim((string)$param['s_keyword']);
        $search_info = trim((string)$param['search_info']);
        
        if ($s_keyword == '' && $dwxz == '' 
            && $is_show == '' && $is_hot == '' 
            && $support_coupon == '' && $search_info == ''
            && $province == '' && $city == '' && $area == '') 
        {
            $this->redirect('School/index');
        } else {
            $province_name = D('Province')->getProvinceName($province);
            $city_name = D('City')->getCityName($city);
            $area_name = D('Area')->getAreaName($area);
            $school_list  = D('School')->searchSchool($param);
            //获取省份列表
            $province_list = D('Province')->getProvinceList();
            $this->assign('province_list', $province_list);
            $this->assign('dwxz', $dwxz);
            $this->assign('province', $province);
            $this->assign('province_name', $province_name);
            $this->assign('city', $city);
            $this->assign('city_name', $city_name);
            $this->assign('area', $area);
            $this->assign('area_name', $area_name);
            $this->assign('is_show', $is_show);
            $this->assign('is_hot', $is_hot);
            $this->assign('support_coupon', $support_coupon);
            $this->assign('s_keyword', $s_keyword);
            $this->assign('search_info', $search_info);
            $this->assign('page', $school_list['page']);
            $this->assign('count', $school_list['count']);
            $this->assign('school_info', $school_list['school_info']);
            $this->display('School/index');            
        }
    }

    /**
     * 添加驾校
     *
     * @return void
     * @author wl
     * @date   july 29, 2016
     **/
    public function addSchool() {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('School/index'));
        }
        if (IS_POST) {
            $post = I('post.');
            $data['s_school_name']  = trim($post['school_name']) != '' ? trim($post['school_name']) : '';
            $data['s_frdb']         = trim($post['corporate']) != '' ? trim($post['corporate']) : '' ;
            $data['s_frdb_mobile']  = trim($post['corporate_mobile']) != '' ? trim($post['corporate_mobile']) : '';
            $data['s_frdb_tel']     = trim($post['corporate_tel']) != '' ? trim($post['corporate_tel']) : '';
            $data['s_zzjgdm']       = trim($post['institution']) != '' ? trim($post['institution']) : '';
            $data['dc_base_je']     = trim($post['base_charge']) != '' ? trim($post['base_charge']) : 0;
            $data['dc_bili']        = trim($post['proportion']) != '' ? trim($post['proportion']) : 0;
            $data['s_yh_name']      = trim($post['bankname']) != '' ? trim($post['bankname']) : '';
            $data['s_yh_zhanghao']  = trim($post['bankcard']) != '' ? trim($post['bankcard']) : '';
            $data['s_yh_huming']    = trim($post['bank_account']) != '' ? trim($post['bank_account']) : '';
            $data['i_dwxz']         = trim($post['nature']) != '' ? trim($post['nature']) : 1;
            $data['brand']          = trim($post['brand']) != '' ? trim($post['brand']) : 1;
            $data['s_location_x']   = trim($post['location_x']) != '' ? trim($post['location_x']) : 0;
            $data['s_location_y']   = trim($post['location_y']) != '' ? trim($post['location_y']) : 0;
            $data['province_id']    = $post['province'] != '' ? $post['province'] : '';
            $data['city_id']        = $post['city'] != '' ? $post['city'] : '';
            $data['area_id']        = $post['area'] != '' ? $post['area'] : '';
            $data['s_address']      = trim($post['address']) != '' ? trim($post['address']) : '';
            $data['s_shuoming']     = trim($post['school_intro']) != '' ? trim($post['school_intro']) : '';
            $data['shifts_intro']   = trim($post['shifts_intro']) != '' ? trim($post['shifts_intro']) : '';
            $data['is_show']        = $post['show'] ? $post['show'] : 2;
            $data['addtime']        = time();
            
            if (!($data['s_school_name']&&$data['s_frdb']&& $data['s_frdb_mobile']&& $data['i_dwxz']&& $data['brand']&& $data['s_location_x']&&$data['s_location_y']&& $data['province_id']&& $data['city_id']&& $data['area_id']&& $data['s_address']&& $data['s_shuoming'])) {
                $this->error('请完善信息', U('School/addSchool'));
            }
            $check_school = D('School')->checkSchool($data['s_school_name']);
            if ($check_school == true) {
                $this->error('该驾校已经存在', U('School/addSchool'));
            }
            $school = D('school');
            if ($res = $school->create($data)) {
                $result = $school->add($res);
                if ($_FILES['license_img']['error'] == UPLOAD_ERR_OK) {
                    $s_yyzz = $this->uploadSingleImg('license_img', 'school/license/'.$result.'/', 'default_photo','2097152','../upload/'); 
                    $update_data['s_yyzz'] = $s_yyzz['path'];
                } else {
                    $update_data['s_yyzz'] = '';
                }

                if ($_FILES['s_thumb']['error'] == UPLOAD_ERR_OK) {
                    $s_thumb = $this->uploadSingleImg('s_thumb', 'school/thumb/'.$result.'/', 'thumb','2097152','../upload/');  
                    $update_data['s_thumb'] = $s_thumb['path'];
                } else {
                    $update_data['s_thumb'] = '';
                }
                if ($r = $school->create($update_data)) {
                    $re = $school->where(array('l_school_id' => $result))->save($r);
                }
                if ($result) {
                    action_log('add_school', 'school', $result, $this->getLoginUserId());
                    $this->success('添加成功', U('School/index'));
                } else {
                    action_log('add_school', 'school', $result, $this->getLoginUserId());
                    $this->error('添加失败', U('School/addSchool'));

                }
            }

        } else {
            //获取省份列表
            $province_list = D('Province')->getProvinceList();
            $this->assign('province_list', $province_list);
            $this->display(CONTROLLER_NAME.'/'.__FUNCTION__);           
        }
    }

    /**
     * 编辑驾校
     *
     * @return void
     * @author wl
     * @date   july 29, 2016
     * @update August 05, 2016
     **/
    public function editSchool() {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('School/index'));
        }
        $school_id    = $this->getLoginauth();
        $sid = I('param.sid');
        $school_info = D('School')->getSchoolInfoById($sid); 
        if(IS_POST) {
            $post = I('post.');
            $data['l_school_id']  = trim($post['sid']) == '' ? trim($school_info['sid']) : trim($post['sid']);
            $data['s_school_name']  = trim($post['school_name']) == '' ? trim($school_info['s_school_name']) : trim($post['school_name']);
            $data['s_frdb']         = trim($post['corporate']) == '' ? trim($school_info['s_frdb']) : trim($post['corporate']);
            $data['s_frdb_mobile']  = trim($post['corporate_mobile']) == '' ? trim($school_info['s_frdb_mobile']) : trim($post['corporate_mobile']);
            $data['s_frdb_tel']     = trim($post['corporate_tel']) == '' ? trim($school_info['s_frdb_tel']) : trim($post['corporate_tel']);
            $data['s_zzjgdm']       = trim($post['institution']) == '' ? trim($school_info['s_zzjgdm']) : trim($post['institution']);
            $data['dc_base_je']     = trim($post['base_charge']) == '' ? trim($school_info['dc_base_je']) : trim($post['base_charge']);
            $data['dc_bili']        = trim($post['proportion']) == '' ? trim($school_info['dc_bili']) : trim($post['proportion']);
            $data['s_yh_name']      = trim($post['bankname']) == '' ? trim($school_info['s_yh_name']) : trim($post['bankname']);
            $data['s_yh_zhanghao']  = trim($post['bankcard']) == '' ? trim($school_info['s_yh_zhanghao']) : trim($post['bankcard']);
            $data['s_yh_huming']    = trim($post['bank_account']) == '' ? trim($school_info['s_yh_huming']) : trim($post['bank_account']);
            $data['i_dwxz']         = $post['nature'] == '' ? $school_info['i_dwxz'] : $post['nature'];
            $data['brand']          = $post['brand'] == '' ? $school_info['brand'] : $post['brand'];
            $data['s_location_x']   = trim($post['location_x']) == '' ? trim($school_info['s_location_x']) : trim($post['location_x']);
            $data['s_location_y']   = trim($post['location_y']) == '' ? trim($school_info['s_location_y']) : trim($post['location_y']);
            $data['province_id']    = $post['province'] == '' ? $school_info['province_id'] : $post['province'];
            $data['city_id']        = $post['city'] == '' ? $school_info['city_id'] : $post['city'];
            $data['area_id']        = $post['area'] == '' ? $school_info['area_id'] : $post['area'];
            $data['s_address']      = trim($post['address']) == '' ? trim($school_info['s_address']) : trim($post['address']);
            $data['s_shuoming']     = trim($post['school_intro']) == '' ? trim($school_info['s_shuoming']) : trim($post['school_intro']);
            $data['shifts_intro']   = trim($post['shifts_intro']) == '' ? trim($school_info['shifts_intro']) : trim($post['shifts_intro']);
            $data['is_show']        = $post['show'] == '' ? $school_info['is_show'] : $post['show'];
            $data['addtime']        = time();
            
            if ($_FILES['license_img']['error'] == UPLOAD_ERR_OK) {
                $s_yyzz = $this->uploadSingleImg('license_img', 'school/license/'.$data['l_school_id'].'/', 'default_photo','2097152','../upload/');  
                $data['s_yyzz'] = $s_yyzz['path'];
            } else {
                $data['s_yyzz'] = isset($school_info['s_yyzz']) ? $school_info['s_yyzz'] : '';
            }

            if ($_FILES['s_thumb']['error'] == UPLOAD_ERR_OK) {
                $s_thumb = $this->uploadSingleImg('s_thumb', 'school/thumb/'.$data['l_school_id'].'/', 'thumb','2097152','../upload/');  
                $data['s_thumb'] = $s_thumb['path'];
            } else {
                $data['s_thumb'] = isset($school_info['s_thumb']) ? $school_info['s_thumb'] : '';
            }
            
            if (!($data['s_school_name']&&$data['s_frdb']&& $data['s_frdb_mobile']&& $data['i_dwxz']&& $data['brand']&& $data['s_location_x']&&$data['s_location_y']&& $data['province_id']&& $data['s_address']&& $data['s_shuoming'])) {
                $this->error('请完善信息', U('School/addSchool'));
            }
            $school = M('school');
            if ($res = $school->create($data)) {
                $result = $school->fetchSql(false)->where(array('l_school_id' => $sid))->save($res);
                if ($result) {
                    action_log('edit_school', 'school', $sid, $this->getLoginUserId());
                    $this->success('编辑成功', U('School/index'));
                } else {
                    action_log('edit_school', 'school', $sid, $this->getLoginUserId());
                    $this->error('未做任何修改', U('School/editSchool'));
                }
            } else {
                action_log('edit_school', 'school', $sid, $this->getLoginUserId());
                $this->error('未做任何修改', U('School/editSchool'));
            }
        } else {
            $province_list = D('Province')->getProvinceList();
            $this->assign('province_list', $province_list);
            $this->assign('school_info',$school_info);
            $this->display(CONTROLLER_NAME.'/'.__FUNCTION__);       
        }                      
    }

    /**
     * 设置驾校的排序状态
     * @return  void
     * @author  wl
     * @date    2017-4-27
     **/
    public function setSchoolOrder () {
        if (!IS_AJAX || !IS_POST) {
            $msg = '参数错误';
            $data = '';
            $this->ajaxReturn(array('code' => 101, 'msg' => $msg, 'data' => $data), 'JSON');
        }

        $post = I('post.');
        $update_ok = D('School')->setSchoolOrder($post);
        if ($update_ok == 101 || $update_ok == 102 || $update_ok == 105) {
            $code = $update_ok;
            $msg = '参数错误';
            $data = '';

        } elseif ($update_ok == 200) {
            $code = $update_ok;
            $msg = '更新成功';
            $data = '';

        } elseif ($update_ok == 400) {
            $code = $update_ok;
            $msg = '更新失败';
            $data = '';

        }
        action_log('set_school_order', 'school', $post['id'], $this->getLoginUserId());
        $this->ajaxReturn(array('code' => $code, 'msg' => $msg, 'data' => $data), 'JSON');

    }

    /**
     * 逻辑上删除驾校
     *
     * @return void
     * @author wl
     * @date   july 29, 2016
     **/
    public function changeShow(){
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.'editSchool', $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('School/index'));
        }
        if (IS_AJAX) {
            $id = I('post.id');
            $result = D('School')->changeShowSchool($id);
            if ($result) {
                $data = array('code' => 200, 'data' => $id);
            } else {
                $data = array('code' => 101, 'data' => '删除失败');
            }
        } else {
            $data = array('code' => 101, 'data' => '删除失败');
        }
        action_log('del_school', 'school', $id, $this->getLoginUserId());
        $this->ajaxReturn($data, 'JSON');
    }

    /**
     * 设置驾校的热门状态
     *
     * @return  void
     * @author  wl
     * @date    2017-05-04
     **/
    public function setHotStatus () {
        if (IS_AJAX) {
            $sid = I('post.id');
            $status = I('post.status');
            $result = D('School')->setHotStatus($sid, $status);
            if ($result['is_hot']) {
                $data = array('code'=>200, 'msg'=>"设置成功", 'data'=>$result['id']);
            } else {
                $data = array('code'=>102, 'msg'=>"设置失败", 'data'=>'');
            }
        } else {
            $data = array('code'=>102, 'msg'=>"设置失败", 'data'=>'');
        }
        action_log('set_school_hot_status', 'school', $sid, $this->getLoginUserId());
        $this->ajaxReturn($data, 'JSON');
    }

     /**
     * 修改驾校的券支持状态
     *
     * @return  void
     * @author  wl
     * @date    2017-05-08
     **/
    public function setCouponStatus () {
        if (IS_AJAX) {
            $sid = I('post.id');
            $status = I('post.status');
            $result = D('School')->setCouponStatus($sid, $status);
            if ($result['support_coupon']) {
                $data = array('code'=>200, 'msg'=>"设置成功", 'data'=>$result['id']);
            } else {
                $data = array('code'=>102, 'msg'=>"设置失败", 'data'=>'');
            }
        } else {
            $data = array('code'=>102, 'msg'=>"设置失败", 'data'=>'');
        }
        action_log('set_school_coupon_status', 'school', $sid, $this->getLoginUserId());
        $this->ajaxReturn($data, 'JSON');
    }

    /**
     * 设置驾校是否展示的状态
     *
     * @return  void
     * @author  wl
     * @date    August 04, 2016
     **/
    public function setstatus () {
        if (IS_AJAX) {
            $sid = I('post.id');
            $status = I('post.status');
            $result = D('School')->setSchoolShow($sid, $status);
            if ($result['is_show']) {
                $data = array('code'=>200, 'msg'=>"设置成功", 'data'=>$result['id']);
            } else {
                $data = array('code'=>102, 'msg'=>"设置失败", 'data'=>'');
            }
        } else {
            $data = array('code'=>102, 'msg'=>"设置失败", 'data'=>'');
        }
        action_log('set_school_status', 'school', $sid, $this->getLoginUserId());
        $this->ajaxReturn($data, 'JSON');
    }

// 2.驾校场地管理模块
    /**
     * 驾校的场地管理
     *
     * @return  void
     * @author  wl
     * @date    August 08, 2016
     **/
    public function siteAdmin () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Admin/Index/welcome'));
        }
        $school_id = $this->getLoginauth();
        $site_lists = D('School')->getSchoolSiteInfo($school_id);
        $this->assign('school_id', $school_id);
        $this->assign('page', $site_lists['page']);
        $this->assign('count', $site_lists['count']);
        $this->assign('site_list', $site_lists['site_list']);
        $this->display('School/siteAdmin');
    }
    /**
     * 搜索驾校场地
     *
     * @return  void
     * @author  wl
     * @date    August 08, 2016
     **/
    public function searchSchoolSite () {
        $school_id = $this->getLoginauth();
        $param = I('param.');
        $s_keyword = trim((string)$param['s_keyword']);
        $site_status = trim((int)$param['site_status']);
        if ($s_keyword == '' && $site_status == '') {
            $this->redirect('School/siteAdmin');
        }
        $site_lists = D('School')->searchSchoolSite($param, $school_id);
        $this->assign('school_id', $school_id);
        $this->assign('s_keyword', $s_keyword);
        $this->assign('site_status', $site_status);
        $this->assign('page', $site_lists['page']);
        $this->assign('count', $site_lists['count']);
        $this->assign('site_list', $site_lists['site_list']);
        $this->display('School/siteAdmin');
    }

    /**
     * 添加驾校场地
     *
     * @return  void
     * @author  wl
     * @date    August 08, 2016
     **/
    public function addSchoolSite () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('School/siteAdmin'));
        }
        $school_id = $this->getLoginauth();
        if (IS_POST) {
            $post = I('post.');
            if ($school_id == 0) {
                $data['school_id']  = $post['school_id'];
                $school_id = $post['school_id'];
            } else {
                $data['school_id']  = $school_id;
            }
            $data['site_name']      = $post['site_name'] ? $post['site_name'] : ''; 
            $data['site_status']    = $post['show'] ? $post['show'] : 1; 
            $data['province_id']    = $post['province'] ? $post['province'] : 0; 
            $data['city_id']        = $post['city'] ? $post['city'] : 0; 
            $data['area_id']        = $post['area'] ? $post['area'] : 0; 
            $data['address']        = $post['address'] ? $post['address'] : ''; 
            $data['site_desc']      = $post['site_intro'] ? $post['site_intro'] : ''; 
            $data['add_time']        = time();
            if (!empty($_FILES)) {
                if ($_FILES['imgurl']['error'] == UPLOAD_ERR_OK) {
                    $imgurl = $this->uploadSingleImg('imgurl', 'site/imgurl/'.$data['school_id'].'/', 'imgurl_','3145728','../upload/');  
                    if (! is_array($imgurl)) {
                        $this->error($imgurl, U('School/addSchoolSite'));
                    }
                    $data['imgurl'] = $imgurl['path'];
                } else {
                    $data['imgurl'] = '';
                } 

                if ($_FILES['point_texturl']['error'] == UPLOAD_ERR_OK) {
                    $texturl = $this->uploadSingleImg('point_texturl', 'site/textUrl1/'.$data['school_id'].'/', 'txturl1_','3145728','../upload/',array('txt'));  
                    if (! is_array($texturl)) {
                        $this->error($texturl, U('School/addSchoolSite'));
                    }
                    $data['point_text_url1'] = $texturl['path'];
                } else {
                    $data['point_text_url1'] = '';
                } 

                if ($_FILES['point_texturl2']['error'] == UPLOAD_ERR_OK) {
                    $texturl2 = $this->uploadSingleImg('point_texturl2', 'site/textUrl2/'.$data['school_id'].'/', 'txturl2_','104857600','../upload/',array('doc', 'txt'));  
                    if (! is_array($texturl2)) {
                        $this->error($texturl2, U('School/addSchoolSite'));
                    }
                    $data['point_text_url2'] = $texturl2['path'];
                } else {
                    $data['point_text_url2'] = '';
                } 

                if ($_FILES['resource_url']['error'] == UPLOAD_ERR_OK) {
                    $model_url = $this->uploadSingleImg('resource_url', 'site/model/'.$data['school_id'].'/', 'model_resource_url_', '104857600', '../upload/', array('zip', 'rar') );  
                    if (! is_array($model_url)) {
                        $this->error($model_url, U('School/addSchoolSite'));
                    }
                    $data['model_resource_url'] = $model_url['path'];
                } else {
                    $data['model_resource_url'] = $site_lists['model_resource_url'] ? $site_lists['model_resource_url'] :'';
                } 
            } 

            if (!($data['school_id'] && $data['site_name'] && $data['province_id'] && $data['city_id'] && $data['area_id'] && $data['address'] )) {
                $this->error('请完善信息',U('School/addSchoolSite'));
            }

            $checkSchoolSite = D('School')->checkSchoolSite($data['school_id'], $data['site_name']);
            if ($checkSchoolSite == true) {
                $this->error('该场地已经存在', U('School/addSchoolSite'));
            }

            $schoolSite = D('site');
            if ($res = $schoolSite->create($data)) {
                $result = $schoolSite->add($res);
                if ($result) {
                    action_log('add_schoolsite', 'site', $result, $this->getLoginUserId());
                    $this->success('添加成功！', U('School/siteAdmin'));
                } else {
                    action_log('add_schoolsite', 'site', $result, $this->getLoginUserId());
                    $this->error('添加失败！', U('School/addSchoolSite'));
                }
            } else {
                action_log('add_schoolsite', 'site', $result, $this->getLoginUserId());
                $this->error('添加失败！', U('School/addSchoolSite'));
            }
        } else {
            $school_list = D('Manager')->getSchoolList();
            $province_list = D('Province')->getProvinceList();
            $this->assign('school_id', $school_id);
            $this->assign('province_list', $province_list);
            $this->assign('school_list', $school_list);
            $this->display('School/addSchoolSite');
        }
    }

    /**
     * 编辑驾校场地
     *
     * @return  void
     * @author  wl
     * @date    August 08, 2016
     * @update  August 09, 2016
     * @update  Oct 08, 2016
     **/
    public function editSchoolSite () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('School/siteAdmin'));
        }
        $param = I('param.');
        $sid = $param['id'];
        $school_id = $param['school_id'];
        $site_lists = D('School')->getSiteInfoById($sid);
        if (IS_POST) {
            $post = I('post.');
            $data['id']             = $post['id'] == '' ? $site_lists['id'] : $post['id'];
            $data['school_id']      = $post['school_id'] == '' ? $school_id : $post['school_id'];
            $data['site_name']      = $post['site_name'] == '' ? $site_lists['id'] : $post['site_name']; 
            $data['site_status']    = $post['show'] == '' ? $site_lists['site_status'] : $post['show']; 
            $data['province_id']    = $post['province'] == '' ? $site_lists['province_id'] : $post['province']; 
            $data['city_id']        = $post['city'] == '' ? $site_lists['city_id'] : $post['city']; 
            $data['area_id']        = $post['area'] == '' ? $site_lists['area_id'] : $post['area']; 
            $data['address']        = $post['address'] == '' ? $site_lists['address'] : $post['address']; 
            $data['site_desc']      = $post['site_intro'] == '' ? $site_lists['site_desc'] : $post['site_intro']; 
            $data['add_time']       = time();
            if (!empty($_FILES)) {
                if ($_FILES['imgurl']['error'] == UPLOAD_ERR_OK) {
                    $imgurl = $this->uploadSingleImg('imgurl', 'site/imgurl/'.$data['school_id'].'/', 'imgurl_','3145728','../upload/');  
                    $data['imgurl'] = $imgurl['path'];
                } else {
                    $data['imgurl'] = isset($site_lists['imgurl']) ? $site_lists['imgurl'] : '';
                } 

                if ($_FILES['point_texturl']['error'] == UPLOAD_ERR_OK) {
                    $texturl = $this->uploadSingleImg('point_texturl', 'site/textUrl1/'.$data['school_id'].'/', 'txturl1_','3145728','../upload/',array('txt'));  
                    if (! is_array($texturl)) {
                        $this->error($texturl, U('School/editSchoolSite'));
                    }
                    $data['point_text_url1'] = $texturl['path'];
                } else {
                    $data['point_text_url1'] = isset($site_lists['point_text_url1']) ? $site_lists['point_text_url1'] : '';
                } 

                if ($_FILES['point_texturl2']['error'] == UPLOAD_ERR_OK) {
                    $texturl2 = $this->uploadSingleImg('point_texturl2', 'site/textUrl2/'.$data['school_id'].'/', 'txturl2_','1UPLOAD_ERR_OK4857600','../upload/',array('doc', 'txt'));  
                    if (! is_array($texturl2)) {
                        $this->error($texturl2, U('School/editSchoolSite'));
                    }
                    $data['point_text_url2'] = $texturl2['path'];
                } else {
                    $data['point_text_url2'] = isset($site_lists['point_text_url2']) ? $site_lists['point_text_url2'] : '';
                } 

                if ($_FILES['resource_url']['error'] == UPLOAD_ERR_OK) {
                    $model_url = $this->uploadSingleImg('resource_url', 'site/model/'.$data['school_id'].'/', 'model_resource_url_', '104857600', '../upload/', array('zip', 'rar') );  
                    if (! is_array($model_url)) {
                        $this->error($model_url, U('School/editSchoolSite'));
                    }
                    $data['model_resource_url'] = $model_url['path'];
                } else {
                    $data['model_resource_url'] = isset($site_lists['model_resource_url']) ? $site_lists['model_resource_url'] : '';
                } 
            } 

            if (!($data['school_id'] && $data['site_name'] && $data['province_id'] && $data['city_id'] && $data['area_id'] && $data['address'] )) {
                $this->error('请完善信息',U('School/editSchoolSite'));
            }
            $schoolSite = D('site');
            if ($res = $schoolSite->create($data)) {
                $result = $schoolSite->where(array('id' => $sid))->fetchSql(false)->save($res);
                if ($result) {
                    action_log('edit_schoolsite', 'site', $sid, $this->getLoginUserId());
                    $this->success('修改成功！', U('School/siteAdmin'));
                } else {
                    action_log('edit_schoolsite', 'site', $sid, $this->getLoginUserId());
                    $this->error('修改失败！', U('School/editSchoolSite'));
                }
            } else {
                action_log('edit_schoolsite', 'site', $sid, $this->getLoginUserId());
                $this->error('修改失败！', U('School/editSchoolSite'));
            }
        } else {
            $school_list = D('Manager')->getSchoolList();
            $province_list = D('Province')->getProvinceList();
            $this->assign('school_id', $school_id);
            $this->assign('province_list', $province_list);
            $this->assign('school_list', $school_list);
            $this->assign('school_site', $site_lists);
            $this->display('School/editSchoolSite');
        }
    }


    /**
     * 删除驾校场地
     *
     * @return  void
     * @author  wl
     * @date    August 08, 2016
     **/
    public function delSchoolSite () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('School/siteAdmin'));
        }
        if (IS_AJAX) {
            $sid = I('param.id');
            $result = D('School')->delSchoolSite($sid);
            if ($result) {
                $data= array('code' => 200, 'msg' =>'删除成功', 'data' => $sid);
            } else {
                $data= array('code' => 103, 'msg' =>'删除失败', 'data' => '');
            }
        } else {
            $data= array('code' => 103, 'msg' =>'删除失败', 'data' => '');
        }
        action_log('del_schoolsite', 'site', $sid, $this->getLoginUserId());
        $this->ajaxReturn($data, 'JSON');
    }

    /**
     * 设置驾校场地开放的状态
     *
     * @return  void
     * @author  wl
     * @date    August 08, 2016
     **/
    public function setSiteStatus () {
        if (IS_AJAX) {
            $sid = I('post.id');
            $status = I('post.status');
            $result = D('School')->setSiteStatus($sid, $status); 
            if ($result['site_status']) {
                $data = array('code' => 200, 'msg'=> '设置成功','data' => $result['id']);
            } else {
                $data = array('code' => 103, 'msg'=> '设置失败','data' => '');
            }
        } else {
            $data = array('code' => 103, 'msg'=> '设置失败','data' => '');
        }
        action_log('set_site_status', 'site', $sid, $this->getLoginUserId());
        $this->ajaxReturn($data, 'JSON');
    }

// 3.驾校班制模块
    /**
     * 驾校班制列表的展示
     *
     * @return  void
     * @author  wl
     * @date    August 02, 2016
     **/
    public function schoolShiftsAdmin () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Admin/Index/welcome'));
        }
        $school_id = $this->getLoginauth();
        $shifts_list = D('School')->getSchoolShifts($school_id);
        $this->assign('school_id', $school_id);
        $this->assign('page', $shifts_list['page']);
        $this->assign('count', $shifts_list['count']);
        $this->assign('shifts_list', $shifts_list['school_shifts']);
        $this->display('School/schoolShiftsAdmin');
    }

    /**
     * 驾校班制列表的搜索功能
     *
     * @return  void
     * @author  wl
     * @date    Nov 02, 2016
     **/
    public function searchSchoolShifts () {
        $school_id = $this->getLoginauth();
        $param = I('param.');
        $deleted = trim((int)$param['deleted']);
        $is_promote = trim((int)$param['is_promote']);
        $is_package = trim((int)$param['is_package']);
        $s_keyword = trim((string)$param['s_keyword']);
        $search_info = trim((string)$param['search_info']);
        if ($s_keyword == '' && $is_promote == '' && $search_info == '' && $deleted == '' && $is_package == '') {
            $this->redirect('School/schoolShiftsAdmin');
        } else {
            $shifts_list = D('School')->searchSchoolShifts($param, $school_id);
            $this->assign('deleted', $deleted);
            $this->assign('s_keyword', $s_keyword);
            $this->assign('is_promote', $is_promote);
            $this->assign('is_package', $is_package);
            $this->assign('search_info', $search_info);
            $this->assign('school_id', $school_id);
            $this->assign('page', $shifts_list['page']);
            $this->assign('count', $shifts_list['count']);
            $this->assign('shifts_list', $shifts_list['school_shifts']);
            $this->display('School/schoolShiftsAdmin');
        }
    }

    /**
     * 通过coach_id获得优惠券兑换码
     *
     * @return  void
     * @author  wl
     * @date    August 18, 2016 
     **/
    public function getCoupCodeById () {
        $cid = I('post.school_id');
        if ($cid == '') {
            $sid = 0;
        } else {
            $sid = $cid;
        }
        $schoolcouponlist = D('School')->getCoupCodeById($sid);
        if (is_array($schoolcouponlist)) {
            $data = array('code' => 200, 'data' => $schoolcouponlist);
        } else {
            $data = array('code' => 200, 'data' => array());
        }
        exit(json_encode($data));
    }
    /**
     * 通过驾校id获取教练信息
     *
     * @return  void
     * @author  wl
     * @date    Dec 12, 2016
     **/
    public function getCoachNameBySchoolId () {
        if (IS_AJAX) {
            $school_id = I('param.school_id');
            $coach_list = D('School')->getCoachNameBySchoolId($school_id);
            if (!empty($coach_list)) {
                $data = array('code' => 200, 'data' => $coach_list);
            } else {
                $data = array('code' => 200, 'data' => array());
            }
        } else {
            $data = array('code' => 200, 'data' => array());
        }
        $this->ajaxReturn($data, 'JSON');
    }

    /**
     * 通过教练id，获取标签信息
     *
     * @return  void
     * @author  wl
     * @date    Dec 12, 2016
     **/
    public function getCoachTag () {
        if (IS_AJAX) {
            $coach_id = I('param.coach_id');
            if ($coach_id != '') {
                $tag_list = D('School')->getCoachTag();
                if (!empty($tag_list)) {
                    $data = array('code' => 200, 'data' => $tag_list);
                } else {
                    $data = array('code' => 200, 'data' => array());
                }
            }
        } else {
            $data = array('code' => 200, 'data' => array());
        }
        $this->ajaxReturn($data, 'JSON');

    }

    /**
     * 获取教练设置的优惠券兑换码
     *
     * @return  void
     * @author  wl
     * @date    Dec 12, 2016
     **/
    public function getCoachCoupon () {
        if (IS_AJAX) {
            $coach_id = I('param.coach_id');
            $coupon_list = D('School')->getCoachCoupon($coach_id);
            if (!empty($coupon_list)) {
                $data = array('code' => 200, 'data' => $coupon_list);
            } else {
                $data = array('code' => 200, 'data' => array());
            }
        } else {
            $data = array('code' => 200, 'data' => array());
        }
        $this->ajaxReturn($data, 'JSON');

    }

    /**
     * 新增班制
     *
     * @return void
     * @author  wl
     * @date    August 02, 2016
     * @update  Nov 02, 2016
     **/
    public function addSchoolShifts () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('School/schoolShiftsAdmin'));
        }
        $school_id = $this->getLoginauth();
        $systemtaglist = D('School')->getSystemTagList();
        $coponlist = D('School')->getCoupCodeById($school_id);
        $licenselist = D('Coach')->getLicenseInfo();
        $school_list = D('Manager')->getSchoolList();
        $coach_list = D('School')->getCoachNameBySchoolId($school_id);
        if (IS_POST) {
            $post = I('post.');
            if ($school_id == 0) {
                if ($post['school_id'] == '') {
                    $data['sh_school_id']   = 0;
                } else {
                    $data['sh_school_id']   = $post['school_id'];
                }
            } else {
                $data['sh_school_id'] = $school_id;
            }
            $data['coach_id']           = $post['coach_id'] == '' ? NULL : $post['coach_id'];
            $data['sh_title']           = $post['sh_title'] ? $post['sh_title'] : '';
            $data['coupon_id']          = $post['coupon_id'] ? $post['coupon_id'] : '';
            $data['sh_tag_id']          = $post['sh_tag_id'] ? $post['sh_tag_id'] : '';
            $data['sh_license_id']      = $post['sh_license_id'] ? $post['sh_license_id'] : 1;
            $data['sh_type']            = $post['sh_type'] ? $post['sh_type'] : 1;
            $data['sh_original_money']  = abs($post['sh_original_money']) ? abs($post['sh_original_money']) : 300;
            $data['sh_money']           = abs($post['sh_money']) ? abs($post['sh_money']) : '';
            $data['order']              = abs($post['order']) ? abs($post['order']) : 0;
            $data['is_promote']         = $post['is_promote'] ? $post['is_promote'] : 1;
            $data['sh_description_1']   = $post['description_1'] ? $post['description_1'] : '';
            $data['sh_description_2']   = $post['description_2'] ? $post['description_2'] : '';
            $data['sh_info']            = $post['shifts_info'] ? $post['shifts_info'] : '';
            $data['is_package']         = $post['is_package'] != '' ? $post['is_package'] : 2;
            $data['deleted']            = 1;
            $data['addtime']            = time();
           
            if ($data['sh_tag_id'] != '') {
                $tag_name = D('School')->getSystemTagById($data['sh_tag_id']);
                if (!empty($tag_name)) {
                    $data['sh_tag'] = $tag_name['tag_name'];
                } else {
                    $data['sh_tag'] = '';
                }
            } else {
                $data['sh_tag'] = '';
            }

            if ($data['is_package'] == 1 
                && ( $data['coach_id'] != '' 
                    && $data['coach_id'] != NULL 
                    && $data['coach_id'] != 0 ) 
                ) 
            {
                $this->error('学车套餐仅归属于驾校', U('School/addSchoolShifts'));
            }

            if ($data['sh_license_id'] != '') {
                $license_name = D('Coach')->getLicenseInfoById($data['sh_license_id']);
                if (!empty($license_name)) {
                    $data['sh_license_name'] = $license_name['license_name'];
                } else {
                    $data['sh_license_name'] = '';
                }
            } else {
                $data['sh_license_name'] = '';
            }
            if ( $data['sh_license_id'] == '' && $data['sh_type'] == '' && $data['sh_original_money'] == '' && $data['sh_money'] == '') {
                $this->error('请完善信息', U('School/addSchoolShifts'));
            }

            $schoolShifts = D('school_shifts');
            if ($re = $schoolShifts->create($data)) {
                $result = $schoolShifts->add($re);

                if ($_FILES['sh_imgurl']['error'] == UPLOAD_ERR_OK) {
                    $sh_imgurl = $this->uploadSingleImg('sh_imgurl', 'schoolshifts/'.$data['sh_school_id'].'/'.$result.'/', 'imgurl','2097152','../upload/');  
                    $update_data['sh_imgurl'] = $sh_imgurl['path'];
                } else {
                    $update_data['sh_imgurl'] = '';
                }

                if ($res = $schoolShifts->create($update_data)) {
                    $result = $schoolShifts->where(array('id' => $result))->save($res);
                }

                if ($result) {
                    action_log('add_schoolshifts', 'school_shifts', $result, $this->getLoginUserId());
                    $this->success('添加成功', U('School/schoolShiftsAdmin'));
                } else {
                    action_log('add_schoolshifts', 'school_shifts', $result, $this->getLoginUserId());
                    $this->error('添加失败', U('School/addSchoolShifts'));
                }
            } else {
                action_log('add_schoolshifts', 'school_shifts', $result, $this->getLoginUserId());
                $this->error('添加失败', U('School/addSchoolShifts'));
            }

        } else {
            $this->assign('school_id', $school_id);
            $this->assign('coach_list', $coach_list);
            $this->assign('school_list', $school_list);
            $this->assign('coponlist', $coponlist);
            $this->assign('systemtaglist', $systemtaglist);
            $this->assign('licenselist', $licenselist);
            $this->display(CONTROLLER_NAME.'/'.__FUNCTION__);
        }    
    }
    
    /**
     * 编辑班制
     *
     * @return  void
     * @author  wl
     * @date    August 01, 2016
     * @update  Nov 02, 2016
     **/
    public function editSchoolShifts () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('School/schoolShiftsAdmin'));
        }
        $param  = I('param.');
        $sid = $param['school_id'];
        $shfits_id = $param['id'];
        $licenselist = D('Coach')->getLicenseInfo();
        $school_list = D('Manager')->getSchoolList();
        $school_list = D('Manager')->getSchoolList();
        $coponlist = D('School')->getCoupCodeById($sid);
        $coach_list = D('School')->getCoachNameBySchoolId($sid);
        $school_shifts = D('School')->getSchoolShiftsById($shfits_id);
        $systemtaglist = D('School')->getSystemTagList();
        if ($school_shifts['coach_id'] != 0 && $school_shifts['coach_id'] != null) {
            $systemtaglist = D('School')->getCoachTag();
        }
        if (IS_POST) {
            $post = I('post.');
            $data['id']                 = $post['id'] == '' ? $school_shifts['id'] : $post['id'];
            $data['coach_id']           = $post['coach_id'];
            $data['sh_school_id']       = $post['sh_school_id'] == '' ? $school_shifts['sh_school_id'] : $post['sh_school_id'];
            $data['sh_title']           = $post['sh_title'] == ''  ? $school_shifts['sh_title'] : $post['sh_title'];
            $data['coupon_id']          = $post['coupon_id'] == '' ? $school_shifts['coupon_id'] : $post['coupon_id'];
            $data['sh_tag_id']          = $post['sh_tag_id'] == '' ? $school_shifts['sh_tag_id'] : $post['sh_tag_id'];
            $data['sh_license_id']      = $post['sh_license_id'] == '' ? $school_shifts['sh_license_id'] : $post['sh_license_id'];
            $data['sh_type']            = $post['sh_type'] == '' ? $school_shifts['sh_type'] : $post['sh_type'];
            $data['sh_original_money']  = abs($post['sh_original_money']) == '' ? $school_shifts['sh_original_money'] : abs($post['sh_original_money']);
            $data['sh_money']           = abs($post['sh_money']) == '' ? $school_shifts['sh_money'] : abs($post['sh_money']);
            $data['order']              = abs($post['order']) == '' ? $school_shifts['order'] : abs($post['order']);
            $data['is_promote']         = $post['is_promote'] == '' ? $school_shifts['is_promote'] : $post['is_promote'];
            $data['sh_description_1']   = trim((string)$post['description_1']);
            $data['sh_description_2']   = trim((string)$post['description_2']);
            $data['sh_info']            = trim((string)$post['shifts_info']);
            $data['is_package']         = $post['is_package'] == '' ? $school_shifts['is_package'] : $post['is_package'];
            $data['deleted']            = $post['deleted'] == '' ? $school_shifts['deleted'] : $post['deleted'];
            $data['updatetime']         = time();

            if ($_FILES['sh_imgurl']['error'] == UPLOAD_ERR_OK) {
                $sh_imgurl = $this->uploadSingleImg('sh_imgurl', 'schoolshifts/'.$data['sh_school_id'].'/'.$data['id'].'/', 'imgurl','2097152','../upload/');  
                $data['sh_imgurl'] = $sh_imgurl['path'];
            } else {
                $data['sh_imgurl'] = $school_shifts['sh_imgurl'];
            }
           
            if ($data['sh_tag_id'] != '') {
                $tag_name = D('School')->getSystemTagById($data['sh_tag_id']);
                if (!empty($tag_name)) {
                    $data['sh_tag'] = $tag_name['tag_name'];
                } else {
                    $data['sh_tag'] = $school_shifts['sh_tag'] != '' ? $school_shifts['sh_tag'] : '';
                }
            } else {
                $data['sh_tag'] = $school_shifts['sh_tag'] != '' ? $school_shifts['sh_tag'] : '';
            }

            if ($data['is_package'] == 1 
                && ( $data['coach_id'] != '' 
                    && $data['coach_id'] != NULL 
                    && $data['coach_id'] != 0 ) 
                ) 
            {
                $this->error('学车套餐仅归属于驾校', U('School/addSchoolShifts'));
            }

            if ($data['sh_license_id'] != '') {
                $license_name = D('Coach')->getLicenseInfoById($data['sh_license_id']);
                if (!empty($license_name)) {
                    $data['sh_license_name'] = $license_name['license_name'];
                } else {
                    $data['sh_license_name'] = $school_shifts['sh_license_name'] != '' ? $school_shifts['sh_license_name'] : '';
                }
            } else {
                $data['sh_license_name'] = $school_shifts['sh_license_name'] != '' ? $school_shifts['sh_license_name'] : '';
            }

            if ($data['sh_license_id'] == '' && $data['sh_type'] == '' && $data['sh_original_money'] == '' && $data['sh_money'] == '') {
                $this->error('请完善信息', U('School/editSchoolShifts'));
            }

            $schoolShifts = D('school_shifts');
            if ($re = $schoolShifts->create($data)) {
                $result = $schoolShifts->where(array('id' => $shfits_id))->fetchSql(false)->save($re);
                if ($result) {
                    action_log('edit_schoolshifts', 'school_shifts', $shfits_id, $this->getLoginUserId());
                    $this->success('编辑成功', U('School/schoolShiftsAdmin'));
                } else {
                    action_log('edit_schoolshifts', 'school_shifts', $shfits_id, $this->getLoginUserId());
                    $this->error('编辑失败', U('School/editSchoolShifts'));
                }
            } else {
                action_log('edit_schoolshifts', 'school_shifts', $shfits_id, $this->getLoginUserId());
                $this->error('编辑失败', U('School/editSchoolShifts'));
            }
        } else {
            $this->assign('school_id', $school_id);
            $this->assign('coponlist', $coponlist);
            $this->assign('coach_list', $coach_list);
            $this->assign('school_list', $school_list);
            $this->assign('licenselist', $licenselist);
            $this->assign('systemtaglist', $systemtaglist);
            $this->assign('school_shifts', $school_shifts);
            $this->display(CONTROLLER_NAME.'/'.__FUNCTION__);
        }  
    }

    //删除班制
    public function delSchoolShifts() {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('School/schoolShiftsAdmin'));
        }
        //make sure AJAX Request
        if (!IS_AJAX) {
            return false;
        }

        $shift_id = I('post.id');
        $res = D('School')->delShifts($shift_id);

        if ($res) {
            $data = array('code'=>200, 'msg'=>'删除成功');
        } else {
            $data = array('code'=>400, 'msg'=>'删除失败');
        }

        action_log('del_schoolshifts', 'school_shifts', $shift_id, $this->getLoginUserId());
        $this->ajaxReturn($data, 'JSON');
    }

    /**
     * 设置班制的套餐状态
     * @param 
     * @return  void
     * @author  wl
     * @date    Apr 11, 2017
     **/
     public function setShiftsPackageStatus () {
        if (IS_AJAX) {
            $post = I('post.');
            $id = $post['id'];
            $shifts_info = D('School')->getSchoolShiftsById($id);
            if ($shifts_info) {
                $sh_coach_id = $shifts_info['coach_id'];
                if ($sh_coach_id != ''
                    && $sh_coach_id != 0
                    && $sh_coach_id != NULL) 
                {
                    $data = array('code'=>102, 'msg'=>"教练的班制不能设置成套餐", 'data'=>'');
                    $this->ajaxReturn($data, 'JSON');
                }
            }
            $status = $post['status'];
            $list = D('School')->setShiftsPackageStatus($id, $status);
            if($list['res']) {
                $data = array('code'=>200, 'msg'=>"设置成功", 'data'=>$list['id']);
            } else {
                $data = array('code'=>102, 'msg'=>"设置失败", 'data'=>'');
            }
        } else {
            $data = array('code'=>102, 'msg'=>"设置失败", 'data'=>'');
        }
        action_log('set_shifts_package_status', 'school_shifts', $id, $this->getLoginUserId());
        $this->ajaxReturn($data, 'JSON');
     }

    /**
     * 设置驾校班制是否推荐的状态设置
     *
     * @return  void
     * @author  wl
     * @date    Nov 01, 2016
     **/
    public function setSchoolShiftsStatus () {
        $id = I('post.id');
        $status = I('post.status');
        $list = D('School')->setSchoolShiftsStatus($id, $status);
        if($list['res']) {
            $data = array('code'=>200, 'msg'=>"设置成功", 'data'=>$list['id']);
        } else {
            $data = array('code'=>102, 'msg'=>"设置失败", 'data'=>'');
        }
        action_log('set_schoolshifts_status', 'school_shifts', $id, $this->getLoginUserId());
        $this->ajaxReturn($data, 'JSON');
    }

    /**
     * 设置驾校班制是否删除的状态设置
     *
     * @return  void
     * @author  wl
     * @date    Nov 01, 2016
     **/
    public function setSchoolShiftsDeletedStatus () {
        $id = I('post.id');
        $status = I('post.status');
        $list = D('School')->setSchoolShiftsDeletedStatus($id, $status);
        if($list['res']) {
            $data = array('code'=>200, 'msg'=>"设置成功", 'data'=>$list['id']);
        } else {
            $data = array('code'=>102, 'msg'=>"设置失败", 'data'=>'');
        }
        action_log('set_shiftsdeleted_status', 'school_shifts', $id, $this->getLoginUserId());
        $this->ajaxReturn($data, 'JSON');
    }

    /**
     * 设置驾校班制的排序
     *
     * @return  void
     * @author  wl
     * @date    Nov 01, 2016
     **/
    public function setSchoolShiftsOrder () {
        if (!IS_AJAX || !IS_POST) {
            $msg = '参数错误';
            $data = '';
            $this->ajaxReturn(array('code' => 101, 'msg' => $msg, 'data' => $data), 'JSON');
        }

        $post = I('post.');
        $update_ok = D('School')->setSchoolShiftsOrder($post);
        if ($update_ok == 101 || $update_ok == 102 || $update_ok == 105) {
            $code = $update_ok;
            $msg = '参数错误';
            $data = '';

        } elseif ($update_ok == 200) {
            $code = $update_ok;
            $msg = '更新成功';
            $data = '';

        } elseif ($update_ok == 400) {
            $code = $update_ok;
            $msg = '更新失败';
            $data = '';

        }
        action_log('set_schoolshifts_order', 'school_shifts', $post['id'], $this->getLoginUserId());
        $this->ajaxReturn(array('code' => $code, 'msg' => $msg, 'data' => $data), 'JSON');

    }
 
// 4.报名点管理模块
    /**
     * 报名点管理列表
     *
     * @return  void
     * @author  wl
     * @date    August 03, 2016
     **/
    public function trainLocationAdmin () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Admin/Index/welcome'));
        }
        $school_id = $this->getLoginauth();
        $school_train_location = D('School')->getTrainLocationLists($school_id);
        $this->assign('school_id', $school_id);
        $this->assign('page', $school_train_location['page']);
        $this->assign('count', $school_train_location['count']);
        $this->assign('tl_info_list', $school_train_location['train_locations']);
        $this->display(CONTROLLER_NAME.'/'.__FUNCTION__);
    }
    
    /**
     * 搜索驾校报名点
     *
     * @return  void
     * @author  wl
     * @date    Dec 14, 2016
     **/
    public function searchSchoolTrainLocation () {
        $param = I('param.');
        $s_keyword = trim((string)$param['s_keyword']);
        if ($s_keyword == '') {
            $this->redirect('School/trainLocationAdmin');
        } else {
            $school_id = $this->getLoginauth();
            $school_train_location = D('School')->searchSchoolTrainLocation($param, $school_id);
            $this->assign('school_id', $school_id);
            $this->assign('s_keyword', $s_keyword);
            $this->assign('page', $school_train_location['page']);
            $this->assign('count', $school_train_location['count']);
            $this->assign('tl_info_list', $school_train_location['train_locations']);
            $this->display('School/trainLocationAdmin');
        }
    }

    /**
     * 驾校报名点的图片展示
     *
     * @return  void
     * @author  wl
     * @date    Dec 15, 2016
     **/
    public function showSchoolTrainLocation () {
        $param = I('param.');
        $id = $param['id'];
        $school_id = $param['school_id'];
        $tl_imgurl = $param['tl_imgurl'];
        $tl_imgurl_all = $param['tl_imgurl_all'];
        $this->assign('id', $id);
        $this->assign('school_id', $school_id);
        $this->assign('tl_imgurl', $tl_imgurl);
        $this->assign('tl_imgurl_all', $tl_imgurl_all);
        $this->display('School/showSchoolTrainLocation');
    }

    /**
     * 添加驾校报名点
     *
     * @return  void
     * @author  wl
     * @date    August 01, 2016
     * @update  Dec 14, 2016
     **/
    public function addTrainLocation () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('School/trainLocationAdmin'));
        }
        $school_id = $this->getLoginauth();
        $school_list = D('Manager')->getSchoolList();
        if (IS_POST) {
            $post = I('post.');
            if ($school_id == 0) {
                if ($post['school_id'] == '') {
                    $data['tl_school_id'] = 0;
                } else {
                    $data['tl_school_id'] = isset($post['school_id']) ? $post['school_id'] : '';
                }
            } else {
                $data['tl_school_id'] = $school_id;
            }
            $data['tl_train_address'] = isset($post['train_address']) ? $post['train_address'] : '';
            $data['tl_phone'] = isset($post['phone']) ? $post['phone'] : '';
            $data['tl_location_x'] = isset($post['location_x']) ? $post['location_x'] : 0;
            $data['tl_location_y'] = isset($post['location_y']) ? $post['location_y'] : 0;
            $data['order'] = abs($post['order']) ? abs($post['order']) : 0;
            $data['addtime'] = time();
            if ($data['tl_school_id'] == '' && $data['tl_train_address'] == '' && $data['tl_phone'] == '' && $data['tl_location_x'] == '' && $data['tl_location_y'] == '') {
                $this->error('请完善信息', U('School/addTrainLocation'));
            }

            $checkSchoolTrain = D('School')->checkSchoolTrain($data['tl_school_id'], $data['tl_train_address']);
            if ($checkSchoolTrain == true) {
                $this->error('改报名点已经存在', U('School/addTrainLocation'));
            }

            $trainLocation = D('school_train_location');
            if ($res = $trainLocation->create($data)) {
                $result = $trainLocation->add($res);
                if ($result) {
                    action_log('add_train_location', 'school_train_location', $result, $this->getLoginUserId());
                    $this->success('添加成功', U('School/trainLocationAdmin'));
                } else {
                    action_log('add_train_location', 'school_train_location', $result, $this->getLoginUserId());
                    $this->error('添加失败', U('School/addTrainLocation'));
                }
            } else {
                    action_log('add_train_location', 'school_train_location', $result, $this->getLoginUserId());
                $this->error('添加失败', U('School/addTrainLocation'));
            }

        } else {
            $this->assign('school_id', $school_id);
            $this->assign('school_list', $school_list);
            $this->display(CONTROLLER_NAME.'/'.__FUNCTION__);
        }
    }
    /**
     * 编辑驾校报名点
     *
     * @return  void
     * @author  wl
     * @date    August 03, 2016
     **/
    public function editTrainLocation () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('School/trainLocationAdmin'));
        }
        $school_id = $this->getLoginauth();
        $param = I('param.');
        $tl_id = $param['id'];
        $sid = $param['school_id'];
        $tl_info = D('School')->getTlInfoById($tl_id);
        // $school_list = D('Manager')->getSchoolList();
        if (IS_POST) {
            $post = I('post.');
            $data['id'] = $post['id'] == '' ? $tl_info['id'] : $post['id'];
            $data['tl_school_id'] = $post['school_id'] == '' ? $tl_info['tl_school_id'] : $post['school_id'];
            $data['tl_train_address'] = $post['train_address'] == '' ? $tl_info['tl_train_address'] : $post['train_address'];
            $data['tl_phone'] = $post['phone'] == '' ? $tl_info['tl_phone'] : $post['phone'];
            $data['tl_location_x'] = $post['location_x'] == '' ? $tl_info['tl_location_x'] : $post['location_x'];
            $data['tl_location_y'] = $post['location_y'] == '' ? $tl_info['tl_location_y'] : $post['location_y'];
            $data['order'] = $post['order'] == '' ? $tl_info['order'] : $post['order'];
            $data['addtime'] = time();
            if (!empty($_FILES['imgurl'])) {
                $tl_imgurl = $this->uploadMore('imgurl', 'schoolTrainLocation/'.$data['id'].'/', 'trainlocation_', '3145728', '../upload/');
                if (!empty($tl_imgurl) && $tl_imgurl != null) {
                    $data['tl_imgurl'] = json_encode($tl_imgurl);
                } else {
                    $data['tl_imgurl'] = $tl_info['tl_imgurl'] != '' ? $tl_info['tl_imgurl'] : '';
                }
            }
            if ($data['tl_school_id'] == '' && $data['tl_train_address'] == '' && $data['tl_phone'] == '' && $data['tl_location_x'] == '' && $data['tl_location_y'] == '') {
                $this->error('请完善信息', U('School/editTrainLocation'));
            }

            $trainLocation = D('school_train_location');
            if ($res = $trainLocation->create($data)) {
                $result = $trainLocation->where(array('id' => $tl_id))->fetchSql(false)->save($res);
                if ($result) {
                    action_log('edit_train_location', 'school_train_location', $tl_id, $this->getLoginUserId());
                    $this->success('编辑成功', U('School/trainLocationAdmin'));
                } else {
                    action_log('edit_train_location', 'school_train_location', $tl_id, $this->getLoginUserId());
                    $this->error('编辑失败', U('School/editTrainLocation'));
                }
            } else {
                action_log('edit_train_location', 'school_train_location', $tl_id, $this->getLoginUserId());
                $this->error('编辑失败', U('School/editTrainLocation'));
            }

        } else {
            $this->assign('school_id', $school_id);
            // $this->assign('school_list', $school_list);
            $this->assign('tl_info', $tl_info);
            $this->display(CONTROLLER_NAME.'/'.__FUNCTION__);
        }
    }

    /**
     * 删除报名点
     *
     * @return  void
     * @author  Gao/wl
     * @update  August 03, 2016
     **/
    public function delTrainLocation() {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('School/trainLocationAdmin'));
        }
        if (!IS_AJAX) {
            return false;
        }

        $tl_id = I('post.id');
        if (!$tl_id) {
            return false;
        }

        $res = D('School')->delTrainLocation($tl_id);

        if ($res) {
            $data = array('code'=>200, 'msg'=>'删除成功');
        } else {
            $data = array('code'=>400, 'msg'=>'删除失败');
        }

        action_log('del_train_location', 'school_train_location', $tl_id, $this->getLoginUserId());
        $this->ajaxReturn($data, 'JSON');
    }
    /**
     * 设置教练报名的排序状态
     *
     * @return  void
     * @author  wl
     * @date    Dec 14, 2016
     **/
    public function setSchoolTrainOrder () {
        if (!IS_AJAX || !IS_POST) {
            $msg = '参数错误';
            $data = '';
            $this->ajaxReturn(array('code' => 101, 'msg' => $msg, 'data' => $data), 'JSON');
        }

        $post = I('post.');
        $update_ok = D('School')->updateSchoolTrainOrder($post);
        if ($update_ok == 101 || $update_ok == 102 || $update_ok == 105) {
            $code = $update_ok;
            $msg = '参数错误';
            $data = '';
            
        } elseif ($update_ok == 200) {
            $code = $update_ok;
            $msg = '更新成功';
            $data = '';

        } elseif ($update_ok == 400) {
            $code = $update_ok;
            $msg = '更新失败';
            $data = '';

        }
        action_log('set_schooltrain_order', 'school_train_location', $post['id'], $this->getLoginUserId());
        $this->ajaxReturn(array('code' => $code, 'msg' => $msg, 'data' => $data), 'JSON');
    }

// 5.驾校轮播图管理模块
    /**
     * 驾校登录时图片展示
     *
     * @return  void
     * @author  wl
     * @date    August 17, 2016
     **/
    public function bannerAdmin() {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Admin/Index/welcome'));
        }
        $school_id = $this->getLoginauth();
        $school_list = D('Manager')->getSchoolList();
        $banner_list = D('School')->getBannerList($school_id);
        $this->assign('school_id', $school_id);
        $this->assign('school_list', $school_list);
        $this->assign('banner_list', $banner_list);
        $this->assign('count', $count);
        $this->display('School/bannerAdmin');
    }

    /**
     * 嘻哈管理员登录时图片展示
     *
     * @return  void
     * @author  wl
     * @date    August 17, 2016
     **/
    public function showBanner () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('School/bannerAdmin'));
        }
        $school_id = $_POST['school_id'];
        if($school_id){
            $bannerlist = D('School')->getBannerList($school_id);
        }
        if (is_array($bannerlist)) {
            $data = array('code' => 200, 'data' => $bannerlist);
        } else {
            $data = array('code' => 200, 'data' => array());
        }
        exit(json_encode($data));
    }

    /**
     * 添加轮播图
     *
     * @return  void
     * @author  wl
     * @date    August 17, 2016
     * @update  August 19, 2016
     **/
    public function addBanner () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('School/bannerAdmin'));
        }
        $school_id = $this->getLoginauth();
        if ($school_id == 0) {
            $school_id = I('post.school_id');
        }
        if (!empty($_FILES)) {
            $bannerurl = D('School')->setBannerUrl($school_id, $_FILES['school_banner']);
            $banner = D('School')->getBannerList($school_id);
            if (count($bannerurl) + count($banner) > 5) {
                $this->error('最多只能添加五张图片', U('School/bannerAdmin'));
                exit;
            }
        } else {
            $this->error('请上传图片', U('School/bannerAdmin'));
            exit();
        }
        $result = D('School')->saveBanner($bannerurl, $school_id);
        if ($result) {
            action_log('add_school_banner', 'school', $result, $this->getLoginUserId());
            $this->success('上传图片成功！', U('School/bannerAdmin'));
            exit;
        } else {
            action_log('add_school_banner', 'school', $result, $this->getLoginUserId());
            $this->error('上传图片失败！', U('School/bannerAdmin'));
            exit;
        }
        $school_list = D('Manager')->getSchoolList();
        $this->assign('school_list', $school_list);
        $this->display('School/bannerAdmin');
    }

    /**
     * 删除驾校的轮播图
     *
     * @return  void
     * @author  wl
     * @date    August 19, 2016
     **/
    public function delBanner () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('School/bannerAdmin'));
        }
        if (IS_AJAX) {
            $school_id = $this->getLoginauth();
            if ($school_id == 0) {
                $school_id = I('post.school_id');
            }
            $url = I('post.url');
            $result = D('School')->delbanner($url, $school_id);
            if ($result) {
                action_log('del_school_banner', 'school', $school_id, $this->getLoginUserId());
                echo 1;
                exit;
            } else {
                action_log('del_school_banner', 'school', $school_id, $this->getLoginUserId());
                echo 2;
                exit;
            }
        } else {
            action_log('del_school_banner', 'school', $school_id, $this->getLoginUserId());
            echo 2;
            exit;
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
        $html = "";
        $html .= "<option value=''>请选择城市</option>";
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
        $html .= "<option value=''>请选择地区</option>";
        foreach ($area_list as $key => $value) {
            $html .= "<option value='".$value['areaid']."'>".$value['area']."</option>";
        }
        echo $html;

    }

}
