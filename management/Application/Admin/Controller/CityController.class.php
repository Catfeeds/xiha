<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Page;
use Think\Upload;

class CityController extends BaseController { 
    //构造函数，判断登录状态
    public function _initialize() {
        if (!session('loginauth')) {
            $this->redirect('Public/login');
            exit();
        }
    }

    /**
     * 城市列表的展示
     *
     * @return  void
     * @author  wl
     * @date    Jan 03, 2017
     **/
    public function index () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Admin/Index/welcome'));
        }
        $citylist = D('City')->getCityLists();
        $this->assign('count', $citylist['count']);
        $this->assign('page', $citylist['page']);
        $this->assign('citylist', $citylist['citylist']);
        $this->display('City/index');
    }

    /**
     * 城市列表的搜索
     *
     * @return  void
     * @author  wl
     * @date    Jan 03, 2017
     **/
    public function searchCityList () {
        $param = I('param.');
        $s_keyword = trim((string)$param['s_keyword']);
        $search_info = trim((string)$param['search_info']);
        if ($s_keyword == '' && $search_info == '') {
            $this->redirect('City/index');
        }
        $citylist = D('City')->searchCityLists($param);
        $this->assign('s_keyword', $s_keyword);
        $this->assign('search_info', $search_info);
        $this->assign('count', $citylist['count']);
        $this->assign('page', $citylist['page']);
        $this->assign('citylist', $citylist['citylist']);
        $this->display('City/index');
    }

    /**
     * 设置城市的热门状态
     *
     * @return  void
     * @author  wl
     * @date    Jan 03, 2017
     **/
    public function setHotCity () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('City/index'));
        }
        if (IS_AJAX) {
            $post = I('post.');
            $id = $post['id'];
            $status = $post['status'];
            $result = D('City')->setHotCity($id, $status);
            if ($result['res']) {
                action_log('set_hot_city', 'city', $id, $this->getLoginUserId());
                $data = array('code' => 200, 'msg' => '设置成功', 'data' => $result['id']);
            } else {
                action_log('set_hot_city', 'city', $id, $this->getLoginUserId());
                $data = array('code' => 400, 'msg' => '设置失败', 'data' => '');
            }
        } else {
            action_log('set_hot_city', 'city', $id, $this->getLoginUserId());
            $data = array('code' => 400, 'msg' => '设置失败', 'data' => '');
        }
        $this->ajaxReturn($data, 'JSON');
    }

    /**
     * 添加城市
     *
     * @return  void
     * @author  wl
     * @date    Jan 03, 2017
     **/
    public function addCity () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('City/index'));
        }
        if (IS_POST) {
            $post = I('post.');
            $data_p['province'] = $post['province'] != '' ? $post['province'] : '';
            $data_p['provinceid'] = $post['provinceid'] != '' ? $post['provinceid'] : '';
            
            $data_c['city'] = $post['city'] != '' ? $post['city'] : '';
            $data_c['cityid'] = $post['cityid'] != '' ? $post['cityid'] : '';
            $data_c['fatherid'] = $data_p['provinceid'] != '' ? $data_p['provinceid'] : '';
            $data_c['leter'] = $post['leter'] != '' ? $post['leter'] : '';
            $data_c['spelling'] = $post['spelling'] != '' ? $post['spelling'] : '';
            $data_c['acronym'] = $post['acronym'] != '' ? $post['acronym'] : '';
            $data_c['is_hot'] = $post['is_hot'] != '' ? $post['is_hot'] : 2;
            
            $data_a['area'] = $post['area'] != '' ? $post['area'] : '';
            $data_a['areaid'] = $post['areaid'] != '' ? $post['areaid'] : '';
            $data_a['fatherid'] = $data_c['cityid'] != '' ? $data_c['cityid'] : '';

            $checkArea = D('City')->checkArea($data_p['provinceid'], $data_c['cityid'], $data_a['areaid']);
            if ($checkArea == true) {
                $this->error('该地区已经存在', U('City/addCity'));
            }

            $province = D('province');
            $city = D('city');
            $area = D('area');

            $checkprovince = D('City')->checkProvince($data_p['provinceid']);
            if (!empty($checkprovince)) {
                if ($p = $province->create($data_p)) {
                    $p_result = $province->where(array('id' => $checkprovince['id']))->save($p);
                }
            } else {
                if ($p = $province->create($data_p)) {
                    $p_result = $province->add($p);
                }
            }

            $checkcity = D('City')->checkCity($data_c['cityid']);
            if (!empty($checkcity)) {
                if ($c = $city->create($data_c)) {
                    $c_result = $city->where(array('id' => $checkcity['id']))->save($c);
                }
            } else {
                if ($c = $city->create($data_c)) {
                    $c_result = $city->add($c);
                }
            }

            if ($a = $area->create($data_a)) {
                $a_result = $area->add($a);
            }

            if ($p_result || $c_result || $a_result) {
                action_log('add_city', 'province/city/area', $a_result, $this->getLoginUserId());
                $this->success('添加成功', U('City/index'));
            } else {
                action_log('add_city', 'province/city/area', $a_result, $this->getLoginUserId());
                $this->error('添加失败', U('City/addCity'));
            }

        } else {
            $this->display('City/addCity');
        }
    }

    /**
     * 编辑城市
     *
     * @return  void
     * @author  wl
     * @date    Jan 03, 2017
     **/
    public function editCity () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('City/index'));
        }
        $param = I('param.');
        $area_id = $param['aid'];
        $city_id = $param['id'];
        $citylist = D('City')->getCityListById($area_id, $city_id);
        if (IS_POST) {
            $post = I('post.');
            $data_p['province'] = $post['province'] != '' ? $post['province'] : $citylist['province'];
            $data_p['provinceid'] = $post['provinceid'] != '' ? $post['provinceid'] : $citylist['provinceid'];
            
            $data_c['city'] = $post['city'] != '' ? $post['city'] : $citylist['city'];
            $data_c['cityid'] = $post['cityid'] != '' ? $post['cityid'] : $citylist['cityid'];
            $data_c['fatherid'] = $data_p['provinceid'] != '' ? $data_p['provinceid'] : $citylist['provinceid'];
            $data_c['leter'] = $post['leter'] != '' ? $post['leter'] : $citylist['leter'];
            $data_c['spelling'] = $post['spelling'] != '' ? $post['spelling'] : $citylist['spelling'];
            $data_c['acronym'] = $post['acronym'] != '' ? $post['acronym'] : $citylist['acronym'];
            $data_c['is_hot'] = $post['is_hot'] != '' ? $post['is_hot'] : $citylist['is_hot'];
            
            $data_a['area'] = $post['area'] != '' ? $post['area'] : $citylist['area'];
            $data_a['areaid'] = $post['areaid'] != '' ? $post['areaid'] : $citylist['areaid'];
            $data_a['fatherid'] = $data_c['cityid'] != '' ? $data_c['cityid'] : $citylist['cityid'];

            $province = D('province');
            $city = D('city');
            $area = D('area');
            
            if ($p = $province->create($data_p)) {
                $p_result = $province->where(array('id' => $citylist['pid']))->save($p);
            }

            if ($c = $city->create($data_c)) {
                $c_result = $city->where(array('id' => $citylist['id']))->save($c);
            }

            if ($a = $area->create($data_a)) {
                $a_result = $city->where(array('id' => $citylist['aid']))->save($a);
            }

            if ($p_result || $c_result || $a_result) {
                action_log('edit_city', 'province/city/area', $citylist['aid'], $this->getLoginUserId());
                $this->success('修改成功', U('City/index'));
            } else {
                action_log('edit_city', 'province/city/area', $citylist['aid'], $this->getLoginUserId());
                $this->error('未做任何修改', U('City/editCity'));
            }

        } else {
            $this->assign('citylist', $citylist);
            $this->display('City/editCity');
        }
    }

    /**
     * 删除城市
     *
     * @return  void
     * @author  wl
     * @date    Jan 03, 2017
     **/
    public function delCity () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('City/index'));
        }
        if (IS_AJAX) {
            $post = I('post.');
            $id = $post['id'];
            $result = D('City')->delCity($id);
            if ($result) {
                action_log('del_city', 'area', $id, $this->getLoginUserId());
                $data = array('code' => 200, 'msg' => '删除成功', 'data' => $result);
            } else {
                action_log('del_city', 'area', $id, $this->getLoginUserId());
                $data = array('code' => 400, 'msg' => '删除失败', 'data' => '');
            }
        } else {
                action_log('del_city', 'area', $id, $this->getLoginUserId());
            $data = array('code' => 400, 'msg' => '删除失败', 'data' => '');
        }
        $this->ajaxReturn($data, 'JSON');
    }


}
?>
