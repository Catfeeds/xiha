<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Page;
use Think\Upload;

/**
 * 车辆管理控制器类--
 *
 * @author
 **/
class CarsController extends BaseController {
    //构造函数，判断登录状态
    public function _initialize() {
        if (!session('loginauth')) {
            $this->redirect('Public/login');
            exit();
        }
    }
// 1.车辆列表
    /**
     * 显示车辆列表(根据school_id获得不同的驾校登录显示不同的列表)
     *
     * @return
     * @author 	wl
     * @date 	August 01, 2016
     **/
    public function index() {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Admin/Index/welcome'));
        }
        $school_id = $this->getLoginauth(); //获取驾校id
        $carLists = D('Cars')->getCarsList($school_id);
        $this->assign('school_id', $school_id);
        $this->assign('page', $carLists['page']);
        $this->assign('count', $carLists['count']);
        $this->assign('carslist', $carLists['lists']);
        $this->display(CONTROLLER_NAME.'/'.__FUNCTION__);
    }

    /**
     * 通过条件搜索车辆的相关信息
     *
     * @return 	void
     * @author 	wl
     * @date	August 11, 2016
     **/
    public function searchCarsInfo () {
        $school_id = $this->getLoginauth();
        $param = I('param.');
        $s_keyword = $param['s_keyword'];
        $search_info = $param['search_info'];
        $car_type = $param['car_type'];
        if (trim($s_keyword) == '' && $search_info == '' && $car_type == '') {
            $this->redirect('Cars/index');
        } else {
            $carLists = D('Cars')->searchCarsInfo($param, $school_id);
            $this->assign('s_keyword', $s_keyword);
            $this->assign('search_info', $search_info);
            $this->assign('car_type', $car_type);
            $this->assign('school_id', $school_id);
            $this->assign('page', $carLists['page']);
            $this->assign('count', $carLists['count']);
            $this->assign('carslist', $carLists['lists']);
            $this->display('Cars/index');
        }
    }
    /**
     * 预览驾校车辆的图片
     *
     * @return  void
     * @author  wl
     * @date    Dec 18, 2016
     **/
    public function showCarsImg () {
        $param = I('param.');
        $id = $param['id'];
        $school_id = $param['school_id'];
        $original_imgurl_all = $param['original_imgurl_all'];
        $original_imgurl = $param['original_imgurl'];
        $this->assign('original_imgurl_all', $original_imgurl_all);
        $this->display('Cars/showCarsImg');
    }

    /**
     * 通过school_id获得车辆型号表中的信息
     *
     * @return 	void
     * @author 	wl
     * @date 	August 18, 2016
     **/
    public function getCarsCategoryByIds () {
        $school_id = $this->getLoginauth();
        if ($school_id == 0) {
            $sid = I('post.school_id');
            $carscatelist = D('Cars')->getCarsCategoryByIds($sid);
            if (is_array($carscatelist)) {
                $data = array('code' => 200, 'data' => $carscatelist);
            } else {
                $data = array('code' => 200, 'data' => array());
            }
            exit(json_encode($data));
        } else {
            $carscatelist = D('Cars')->getCarsCategoryByIds($school_id);
            if (is_array($carscatelist)) {
                $data = array('code' => 200, 'data' => $carscatelist);
            } else {
                $data = array('code' => 200, 'data' => array());
            }
            exit(json_encode($data));
        }
    }

    /**
     * 通过name获得车辆型号表中的信息
     *
     * @return 	void
     * @author 	wl
     * @date 	August 22, 2016
     **/
    public function getCarsCategoryByName () {
        $school_id = $this->getLoginauth();
        $name 		= I('post.name');
        $brandlist = D('Cars')->getCarsCategoryByName($name);
        if (is_array($brandlist)) {
            $data = array('code' => 200, 'data' => $brandlist);
        } else {
            $data = array('code' => 200, 'data' => array());
        }
        exit(json_encode($data));
    }

    /**
     * 根据驾校id进行添加车辆信息
     *
     * @return 	void
     * @author 	wl
     * @date 	August 01, 2016
     * @update 	August 02, 2016
     * @update 	August 18, 2016
     * @update  August 22, 2016
     * @update 	Jan 06, 2017
     **/
    public function addCar () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Cars/index'));
        }
        $school_id = $this->getLoginauth();
        $school_list = D('Manager')->getSchoolList();
        // $carcategorylist = D('Cars')->getCarsCategoryByIds($school_id);
        $carcategorylist = D('Cars')->getCarCategory();
        if (IS_POST) {
            $post = I('post.');
            if ($school_id == 0) {
                if ($post['school_id'] == '') {
                    $data['school_id'] = 0;
                } else {
                    $data['school_id'] = $post['school_id'] ? $post['school_id'] : '';
                }
            } else {
                $data['school_id'] = $school_id;
            }
            $data['name'] = $post['car_name'] ? $post['car_name'] : '';
            $data['car_cate_id'] = $post['car_cate_id'] ? $post['car_cate_id'] : 0 ;
            $data['car_no'] = $post['car_no'] ? $post['car_no'] : '' ;
            $data['car_type'] = $post['car_type'] ? $post['car_type'] : 2;
            $data['addtime'] = time();
           
            // if ($data['car_cate_id'] != '') {
            //     $carsname = D('Cars')->getCarsNameById($data['car_cate_id']);
            //     $data['name'] = $carsname != '' ? $carsname : '';
            // } else {
            //     $data['name'] = '';
            // }
            if ($data['school_id'] == '' && $data['name'] == '' && $data['car_no'] == '' && $data['car_cate_id'] == '' && $data['car_type'] == '') {
                $this->error('请完善信息', U('Cars/addCar'));
            }

            $checkcars = D('Cars')->checkCars($data['school_id'], $data['name'], $data['car_no']);
            if ($checkcars == true) {
                $this->error('该类型的车已经存在', U('Cars/addCar'));
            }

            $cars = D('cars');
            if ($re = $cars->create($data)) {
                $result = $cars->add($re);
                if ($result) {
                    if (!empty($_FILES)) {
                        // UplodTwoImg()上传图片存到两个字段中，original_imgurl原图字段，imgurl缩略图字段
                        $update_data = $this->UplodTwoImg('imgurl', 'original_imgurl', 'car_img', 'cars/'.$result.'/', 'carsimg_', '3145728', '../upload/');
                        if ( $update_data ) {
                            $update = D('cars')->where(array('id' => $result))->fetchSql(false)->data($update_data)->save();
                        }
                    }
                    action_log('add_car', 'cars', $result, $this->getLoginUserId());
                    $this->success('添加成功', U('Cars/index'));
                } else {
                    action_log('add_car', 'cars', $result, $this->getLoginUserId());
                    $this->error('添加失败！', U('Cars/addCar'));
                }
            } else {
                action_log('add_car', 'cars', $result, $this->getLoginUserId());
                $this->error('添加失败！', U('Cars/addCar'));
            }

        } else {
            $this->assign('school_id', $school_id);
            $this->assign('school_list', $school_list);
            // $this->assign('brandlist', $brandlist);
            $this->assign('carcategorylist', $carcategorylist);
            $this->display(CONTROLLER_NAME.'/'.__FUNCTION__);
        }
    }

    /**
     * 根据驾校id进行添加车辆信息
     *
     * @return 	void
     * @author 	wl
     * @date 	August 02, 2016
     * @update 	August 11, 2016
     * @update 	August 22, 2016
     **/
    public function editCar () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Cars/index'));
        }
        $school_id = $this->getLoginauth();
        $param = I('param.');
        $ids = $param['id'];
        $sid = $param['school_id'];
        $category_id = $param['car_cate_id'];
        $carcategorylist = D('Cars')->getCarCategory();
        $school_list = D('Manager')->getSchoolList();
        $car_info = D('Cars')->getCarInfoById($ids);
        if (IS_POST) {
            $post= I('post.');
            $data['id'] = $post['id'] == '' ? $car_info['cid'] : $post['id'];
            $data['school_id'] = $post['school_id'] == '' ? $car_info['school_id'] : $post['school_id'];
            $data['name'] = $post['car_name'] == '' ? $car_info['name'] : $post['car_name'];
            $data['car_no'] = $post['car_no'] == '' ? $car_info['car_no'] : $post['car_no'];
            $data['car_type'] = $post['car_type'] == '' ? $car_info['car_type'] : $post['car_type'];
            $data['car_cate_id'] = $post['car_cate_id'] == '' ? $car_info['car_cate_id'] : $post['car_cate_id'];
            $data['addtime'] = time();
            if (!empty($_FILES)) {
                $update_data = $this->UplodTwoImg('imgurl', 'original_imgurl', 'car_img', 'cars/'.$data['id'].'/', 'carsimg_', '3145728', '../upload/');
                if (!empty($update_data)) {
                    $data['imgurl'] = $update_data['imgurl'];
                    $data['original_imgurl'] = $update_data['original_imgurl'];
                } else {
                    $data['imgurl'] = $car_info['imgurl'] != '' ? $car_info['imgurl'] : '';
                    $data['original_imgurl'] = $car_info['original_imgurl'] != '' ? $car_info['original_imgurl'] : '';
                }
            } else {
                $data['imgurl'] = $car_info['imgurl'] != '' ? $car_info['imgurl'] : '';
                $data['original_imgurl'] = $car_info['original_imgurl'] != '' ? $car_info['original_imgurl'] : '';
            }
            if ($data['school_id'] == '' && $data['name'] == '' && $data['car_no'] == '' && $data['car_cate_id'] == '' && $data['car_type'] == '') {
                $this->error('请完善信息', U('Cars/editCar'));
            }

            $cars = D('cars');
            if ($re = $cars->create($data)) {
                $result = $cars->where(array('id' => $ids))->fetchSql(false)->save($re);
                if ($result) {
                    action_log('edit_car', 'cars', $ids, $this->getLoginUserId());
                    $this->success('修改成功！', U('Cars/index'));
                } else {
                    action_log('edit_car', 'cars', $ids, $this->getLoginUserId());
                    $this->error('您还未进行任何修改', U('Cars/editCar'));
                }
            } else {
                action_log('edit_car', 'cars', $ids, $this->getLoginUserId());
                $this->error('您还未进行任何修改', U('Cars/editCar'));
            }

        } else {
            $this->assign('school_id', $school_id);
            $this->assign('school_list', $school_list);
            $this->assign('car_info', $car_info);
            $this->assign('carcategorylist', $carcategorylist);
            $this->display(CONTROLLER_NAME.'/'.__FUNCTION__);
        }
    }


    /**
     * 删除车辆(根据post提交过来的id)
     *
     * @return 	void
     * @author 	wl
     * @date 	August 01, 2016
     **/
    public function delCars() {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Cars/index'));
        }
        if (IS_AJAX) {
            $car_ids = I('post.id');
            $result  = D("Cars")->delCarsList($car_ids);
            if ($result) {
                $data = array('code'=>'200', 'msg'=>'删除成功', 'data'=>$id);
            } else {
                $data = array('code'=>'400', 'msg'=>'删除失败', 'data'=>$id);
            }
        } else {
            $data = array('code'=>'400', 'msg'=>'删除失败', 'data'=>$id);
        }
        action_log('del_car', 'cars', $car_ids, $this->getLoginUserId());
        $this->ajaxReturn($data, 'JSON');
    }

// 2.车辆型号列表部分
    /**
     * 获得车辆型号的列表
     *
     * @return 	void
     * @author 	wl
     * @date	August 09, 2016
     **/
    public function carsCategory () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Cars/carsCategory'));
        }
        $school_id = $this->getLoginauth();
        $carscatelist = D('Cars')->getCarsCategory($school_id);
        $this->assign('school_id', $school_id);
        $this->assign('page', $carscatelist['page']);
        $this->assign('count', $carscatelist['count']);
        $this->assign('carcategorylist', $carscatelist['carscatelist']);
        $this->display(CONTROLLER_NAME.'/'.__FUNCTION__);
    }
    /**
     * 搜索车辆型号
     *
     * @return 	void
     * @author 	wl
     * @date	August 09, 2016
     **/
    public function searchCarsCategory () {
        $school_id = $this->getLoginauth();
        $param = I('param.');
        $s_keyword = $param['s_keyword'];
        if (trim($s_keyword) == '') {
            $this->redirect('Cars/carsCategory');
        }
        $carscatelist = D('Cars')->searchCarsCategory($param, $school_id);
        $this->assign('school_id', $school_id);
        $this->assign('s_keyword', $s_keyword);
        $this->assign('page', $carscatelist['page']);
        $this->assign('count', $carscatelist['count']);
        $this->assign('carcategorylist', $carscatelist['carscatelist']);
        $this->display('Cars/carsCategory');
    }

    /**
     * 添加车辆型号
     *
     * @return 	void
     * @author 	wl
     * @date	August 09, 2016
     **/
    public function addCarsCategory () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Cars/carsCategory'));
        }
        $school_id = $this->getLoginauth();
        $school_list = D('Manager')->getSchoolList();
        if (IS_POST) {
            $post = I('post.');
            if ($school_id == 0) {
                if ($post['school_id'] == '') {
                    $data['school_id'] = 0;
                } else {
                    $data['school_id'] = $post['school_id'];
                }
            }
            $data['brand'] = $post['brand'] ? $post['brand'] : '';
            $data['subtype'] = $post['subtype'] ? $post['subtype'] : '';
            $data['name'] = $post['brand'].$post['subtype'] ? $post['brand'].$post['subtype'] : '';
            $data['addtime'] = time();
            if (!empty($_FILES)) {
                if ($_FILES['point_texturl']['error'] === UPLOAD_ERR_OK) {
                    $carurl = $this->uploadSingleImg('point_texturl', 'car_category/'.$data['school_id'].'/', 'imgurl_','3145728','../upload/', array('txt'));
                    $data['point_text_url'] = $carurl['path'];
                } else {
                    $data['point_text_url'] = '';
                }
            }

            if ($data['school_id'] == '' && $data['brand'] == '' && $data['subtype'] == '') {
                $this->error('请完善信息', U('Cars/carsCategory'));
            }

            $carscatelist = D('Cars')->carsListByCondition($data['name'], $data['school_id']);
            if ($carscatelist == true) {
                $this->error('车型已经存在', U('Cars/addCarsCategory'));
            }

            $carsCategory = D('car_category');
            if ($res = $carsCategory->create($data)) {
                $result = $carsCategory->add($res);
                if ($result) {
                    action_log('add_cars_category', 'car_category', $result, $this->getLoginUserId());
                    $this->success('添加成功', U('Cars/carsCategory'));
                } else {
                    action_log('add_cars_category', 'car_category', $result, $this->getLoginUserId());
                    $this->error('添加失败', U('Cars/addCarsCategory'));
                }
            } else {
                action_log('add_cars_category', 'car_category', $result, $this->getLoginUserId());
                $this->error('添加失败', U('Cars/addCarsCategory'));
            }
        } else {
            $school_list = D('Manager')->getSchoolList();
            $this->assign('school_id', $school_id);
            $this->assign('school_list', $school_list);
            $this->display(CONTROLLER_NAME.'/'.__FUNCTION__);
        }
    }

    /**
     * 编辑车辆型号
     *
     * @return 	void
     * @author 	wl
     * @date	August 09, 2016
     **/
    public function editCarsCategory () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Cars/carsCategory'));
        }
        $school_id = $this->getLoginauth();
        $param = I('param.');
        $cid = $param['id'];
        $car_category_list = D('Cars')->getCarsCategoryById($cid);
        if (IS_POST) {
            $post = I('post.');
            $data['school_id'] = $post['school_id'] == '' ? $car_category_list['school_id'] : $post['school_id'];
            $data['brand'] = $post['brand'] == '' ? $car_category_list['brand'] : $post['brand'];
            $data['subtype'] = $post['subtype'] == '' ? $car_category_list['subtype'] : $post['subtype'];
            $data['name'] = $data['brand'].$data['subtype'] == '' ? $car_category_list['name'] : $data['brand'].$data['subtype'];
            $data['addtime'] = time();
            if (!empty($_FILES)) {
                if ($_FILES['point_texturl']['error'] === UPLOAD_ERR_OK) {
                    $carurl = $this->uploadSingleImg('point_texturl', 'car_category/'.$data['school_id'].'/', 'imgurl_','3145728','../upload/', array('txt'));
                    $data['point_text_url'] = $carurl['path'];
                } else {
                    $data['point_text_url'] = $car_category_list['point_text_url'] ? $car_category_list['point_text_url'] : '';
                }
            } else {
                $data['point_text_url'] = $car_category_list['point_text_url'] ? $car_category_list['point_text_url'] : '';
            }

            if ($data['school_id'] == '' && $data['brand'] == '' && $data['subtype'] == '') {
                $this->error('请完善信息', U('Cars/carsCategory'));
            }

            $carsCategory = D('car_category');
            if ($res = $carsCategory->create($data)) {
                $result = $carsCategory->where(array('id' => $cid))->fetchSql(false)->save($res);
                if ($result) {
                    action_log('edit_cars_category', 'car_category', $cid, $this->getLoginUserId());
                    $this->success('编辑成功', U('Cars/carsCategory'));
                } else {
                    action_log('edit_cars_category', 'car_category', $cid, $this->getLoginUserId());
                    $this->error('未做任何修改', U('Cars/editCarsCategory'));
                }
            } else {
                action_log('edit_cars_category', 'car_category', $cid, $this->getLoginUserId());
                $this->error('未做任何修改', U('Cars/editCarsCategory'));
            }
        } else {
            $school_list = D('Manager')->getSchoolList();
            $this->assign('school_id', $school_id);
            $this->assign('school_list', $school_list);
            $this->assign('car_cate_list', $car_category_list);
            $this->display(CONTROLLER_NAME.'/'.__FUNCTION__);
        }
    }

    /**
     * 删除车辆型号
     *
     * @return 	void
     * @author 	wl
     * @date 	August 09, 2016
     **/
    public function delCarsCategory () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Cars/carsCategory'));
        }
        if (IS_AJAX) {
            $cid = I('post.id');
            $result = D("Cars")->delCarsCategory($cid);
            if ($result) {
                $data = array('code'=>'200', 'msg'=>'删除成功', 'data'=>$cid);
            } else {
                $data = array('code'=>'400', 'msg'=>'删除失败', 'data'=>'');
            }
        } else {
            $data = array('code'=>'400', 'msg'=>'删除失败', 'data'=>'');
        }
        action_log('del_cars_category', 'car_category', $cid, $this->getLoginUserId());
        $this->ajaxReturn($data, 'JSON');
    }

// 3.学车视频管理模块
    /**
     * 学车视频列表的展示
     *
     * @return  void
     * @author  wl
     * @date    Oct 26, 2016
     **/
    public function learnVideo () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Cars/learnVideo'));
        }
        $learnVideoList = D('Cars')->getLearnVideoList();
        $this->assign('page', $learnVideoList['page']);
        $this->assign('count', $learnVideoList['count']);
        $this->assign('learnvideolist', $learnVideoList['learnvideolist']);
        $this->display('Cars/learnVideo');
    }

    /**
     * 学车视频列表的搜素
     *
     * @return  void
     * @author  wl
     * @date    Oct 26, 2016
     **/
    public function searchLearnVideo () {
        $param = I('param.');
        $s_keyword = trim((string)$param['s_keyword']);
        if ($s_keyword == '') {
            $this->redirect('Cars/learnVideo');
        } else {
            $learnVideoList = D('Cars')->searchLearnVideo($param);
            $this->assign('s_keyword', $s_keyword);
            $this->assign('page', $learnVideoList['page']);
            $this->assign('count', $learnVideoList['count']);
            $this->assign('learnvideolist', $learnVideoList['learnvideolist']);
            $this->display('Cars/learnVideo');
        }
    }

    /**
     * 展示学车视频
     *
     * @return  void
     * @author  wl
     * @date    Dec 20 2016
     **/
    public function showLearnVideo () {
        $param = I('param.');
        $id = $param['id'];
        $video_url = $param['video_url'];
        $this->assign('id', $id);
        $this->assign('video_url', $video_url);
        $this->display('Cars/showLearnVideo');
    }

    /**
     * 添加学车视频
     *
     * @return  void
     * @author  wl
     * @date    Oct 27, 2016
     **/
    public function addLearnVideo () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Cars/learnVideo'));
        }
        if (IS_POST) {
            $post = I('post.');
            $data['title'] = $post['title'] ? $post['title'] : '';
            $data['stype'] = $post['stype'] ? $post['stype'] : 2;
            $data['is_open'] = $post['is_open'] ? $post['is_open'] : 1;
            $data['addtime'] = time();
            if (!empty($_FILES)) {
                 if ($_FILES['video_url']['error'] === UPLOAD_ERR_OK) {
                    $video_url = $this->uploadSingleImg('video_url', 'Cars/video/', 'video_','104857600','../upload/', array('mp4', 'mkv', 'wmv', 'mpg', 'rm', 'rmvb', 'mpeg', 'vob'));
                    $data['video_url'] = $video_url['path'];
                } else {
                    $data['video_url'] = '';
                }
            } else {
                $data['video_url'] = '';
            }
            if ($data['title'] == '' && $data['stype'] == '') {
                $this->error('请完善信息', U('Cars/addLearnVideo'));
            }
            $checkVideo = D('Cars')->checkLearnVideo($data['title']);
            if ($checkVideo == true) {
                $this->error('改视频信息已经存在', U('Cars/addLearnVideo'));
            }

            $learnVideo = D('learn_video');
            if ($res = $learnVideo->create($data)) {
                $result = $learnVideo->fetchSql(false)->add($res);
                if ($result) {
                    action_log('add_learn_video', 'learn_video', $result, $this->getLoginUserId());
                    $this->success('添加成功', U('Cars/learnVideo'));
                } else {
                    action_log('add_learn_video', 'learn_video', $result, $this->getLoginUserId());
                    $this->error('添加失败', U('Cars/addLearnVideo'));
                }
            } else {
                action_log('add_learn_video', 'learn_video', $result, $this->getLoginUserId());
                $this->error('添加失败', U('Cars/addLearnVideo'));
            }

        } else {
            $this->display('Cars/addLearnVideo');
        }
    }

    /**
     * 编辑学车视频
     *
     * @return  void
     * @author  wl
     * @date    Oct 27, 2016
     **/
    public function editLearnVideo () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Cars/learnVideo'));
        }
        $id = I('param.id');
        $learnvideolist = D('Cars')->getLearnVideoById($id);
        if (IS_POST) {
            $post = I('post.');
            $data['title'] = $post['title'] == '' ? $learnvideolist['title'] : $post['title'];
            $data['stype'] = $post['stype'] == '' ? $learnvideolist['stype'] : $post['stype'];
            $data['is_open'] = $post['is_open'] == '' ? $post['is_open'] : 1;
            $data['updatetime'] = time();
            if (!empty($_FILES)) {
                 if ($_FILES['video_url']['error'] == 0) {
                    $video_url = $this->uploadSingleImg('video_url', 'Cars/video/', 'video_','104857600','../upload/', array('mp4', 'mkv', 'wmv', 'mpg', 'rm', 'rmvb', 'mpeg', 'vob'));
                    $data['video_url'] = $video_url['path'];
                } else {
                    $data['video_url'] = $learnvideolist['video_url'] != '' ? $learnvideolist['video_url'] : '';
                }
            } else {
                $data['video_url'] = $learnvideolist['video_url'] != '' ? $learnvideolist['video_url'] : '';
            }

            if ($data['title'] == '' && $data['stype'] == '') {
                $this->error('请完善信息', U('Cars/addLearnVideo'));
            }

            $learnVideo = D('learn_video');
            if ($res = $learnVideo->create($data)) {
                $result = $learnVideo->where(array('id' => $id))->fetchSql(false)->save($res);
                if ($result) {
                    action_log('edit_learn_video', 'learn_video', $id, $this->getLoginUserId());
                    $this->success('编辑成功', U('Cars/learnVideo'));
                } else {
                    action_log('edit_learn_video', 'learn_video', $id, $this->getLoginUserId());
                    $this->error('编辑失败', U('Cars/editLearnVideo'));
                }
            } else {
                action_log('edit_learn_video', 'learn_video', $id, $this->getLoginUserId());
                $this->error('编辑失败', U('Cars/editLearnVideo'));
            }

        } else {
            $this->assign('learnvideolist', $learnvideolist);
            $this->display('Cars/editLearnVideo');
        }

    }

    /**
     * 设置开启的状态
     *
     * @return  void
     * @author  wl
     * @date    Oct 27, 2016
     **/
    public function setOpenStatus () {
        $id = I('post.id');
        $status = I('post.status');
        $list = D('Cars')->setOpenStatus($id, $status);
        if($list['res']) {
            $data = array('code'=>200, 'msg'=>"设置成功", 'data'=>$list['id']);
        } else {
            $data = array('code'=>400, 'msg'=>"设置失败", 'data'=>'');
        }
        action_log('set_learnvideo_status', 'learn_video', $id, $this->getLoginUserId());
        $this->ajaxReturn($data, 'JSON');
    }

    /**
     * 删除学车视频
     *
     * @return  void
     * @author  wl
     * @date    Oct 27, 2016
     **/
    public function delLearnVideo () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Cars/learnVideo'));
        }
        if (IS_AJAX) {
            $id = I('post.id');
            $result = D('Cars')->delLearnVideo($id);
            if ($result) {
                $data = array('code' => 200, 'msg' => '删除成功', 'data' => $id);
            } else {
                $data = array('code' => 400, 'msg' => '删除失败', 'data' => '');
            }
        } else {
            $data = array('code' => 400, 'msg' => '删除失败', 'data' => '');
        }
        action_log('del_learn_video', 'learn_video', $id, $this->getLoginUserId());
        $this->ajaxReturn($data, 'JSON');
    }


}
