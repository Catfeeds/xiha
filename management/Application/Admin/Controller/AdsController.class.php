<?php 
namespace Admin\Controller;
use Think\Controller;
use Think\Page;
use Think\Upload;
/**
 * 广告管理模块
 * @author Sun
 */
class AdsController extends BaseController {

	//构造函数，判断是否登录
	public function _initialize() {
	    if(!session('loginauth')) {
			$this->redirect('Public/login');
			exit();
	    }
	}

// 1.广告订单管理模块
    /**
     * 获取广告订单列表
     *
     * @return  void
     * @author  wl
     * @date    Nov 19, 2016
     **/
    public function adsOrders () {
        $adsOrdersInfo = D('Ads')->getAdsOrders();
        $adsInfo = D('Ads')->getAdsName();
        $this->assign('adsInfo', $adsInfo);
        $this->assign('page', $adsOrdersInfo['page']);
        $this->assign('count', $adsOrdersInfo['count']);
        $this->assign('adsorders', $adsOrdersInfo['adsorders']);
        $this->display('Ads/adsOrders');
    }

    /**
     * 广告订单的搜索
     *
     * @return  void
     * @author  wl
     * @date    Nov 21, 2016
     **/
    public function searchAdsOrders () {
        $param = I('param.');
        $adsInfo = D('Ads')->getAdsName();
        $device = trim((int)$param['device']);
        $pay_type = trim((int)$param['pay_type']);
        $order_status = trim((int)$param['order_status']);
        // $is_promote = trim((int)$param['is_promote']);
        $search_info = trim((string)$param['search_info']);
        $s_keyword = trim((string)$param['s_keyword']);
        $ads_id = trim((int)$param['ads_id']);
        if ($ads_id == '' && $s_keyword == '' && $search_info == '' && $order_status == '' && $pay_type == '' && $device == '') {
            $this->redirect('Ads/adsOrders');
        } else {
            $adsOrdersInfo = D('Ads')->searchAdsOrders($param);
            $this->assign('adsInfo', $adsInfo);
            $this->assign('ads_id', $ads_id);
            $this->assign('device', $device);
            $this->assign('pay_type', $pay_type);
            $this->assign('order_status', $order_status);
            // $this->assign('is_promote', $is_promote);
            $this->assign('search_info', $search_info);
            $this->assign('s_keyword', $s_keyword);
            $this->assign('page', $adsOrdersInfo['page']);
            $this->assign('count', $adsOrdersInfo['count']);
            $this->assign('adsorders', $adsOrdersInfo['adsorders']);
            $this->display('Ads/adsOrders');
        }
    }

    /**
     * 根据买家id从school表中获取买家的手机号码
     *
     * @return  void
     * @author  wl
     * @date    Nov 22, 2016
     **/
    public function getPhoneByBuyId () {
        $buyer_id = I('post.buyer_id');
        if ($buyer_id == '') {
            $buyer_id = 0;
        }
        $buyer_phone = D('Ads')->getPhoneByBuyId($buyer_id);
        if (is_array($buyer_phone)) {
            $data = array('code' => 200, 'data' => $buyer_phone);
        } else {
            $data = array('code' => 200, 'data' => array());
        }
        $this->ajaxReturn($data, 'JSON');
    }

    /**
     * 添加新的广告订单
     *
     * @param   buyer_type 2：驾校（暂时，后期改动）
     * @return  void
     * @author  wl
     * @date    Nov 21, 2016
     **/
    public function addAdsOrders () {
        $school_id = $this->getLoginauth();
        $adslist = D('Ads')->getAdsName();
        $buyernamelist = D('Ads')->getBuyerName($school_id);
        if (IS_POST) {
            $post = I('post.');
            $data['ads_title'] = trim($post['ads_title']) ? trim($post['ads_title']) : '';
            $data['ads_id'] = $post['ads_id'] ? $post['ads_id'] : '';
            $data['buyer_type'] = 2;
            if ($post['buyer_id'] == '') {
                $data['buyer_id'] = 0;
            } else {
                $data['buyer_id'] = $post['buyer_id'] ? $post['buyer_id'] : 0;
            }
            if ($data['buyer_id'] == 0 ) {
                $data['buyer_name'] = '嘻哈平台';
            } else {
                $data['buyer_name'] = D('ads')->getNameByBuyerId($data['buyer_id']);
            }
            $data['buyer_phone'] = $post['buyer_phone'] ? $post['buyer_phone'] : '0551-65653272';
            $data['resource_type'] = $post['resource_type'] ? $post['resource_type'] : 1;
            $data['ads_url'] = $post['ads_url'] ? $post['ads_url'] : '0551-65653272';
            $data['addtime'] = strtotime($post['addtime']) ? strtotime($post['addtime']) : 0;
            $data['over_time'] = strtotime($post['over_time']) ? strtotime($post['over_time']) : 0;
            $data['loop_time'] = $post['loop_time'] ? $post['loop_time'] : 1;
            $data['original_price'] = $post['original_price'] ? $post['original_price'] : 100;
            $data['final_price'] = $post['final_price'] ? $post['final_price'] : 100;
            $data['device'] = $post['device'] ? $post['device'] : 3;
            $data['is_promote'] = $post['is_promote'] ? $post['is_promote'] : 0;
            $data['pay_type'] = $post['pay_type'] ? $post['pay_type'] : 1;
            $data['order_status'] = $post['order_status'] ? $post['order_status'] : 1;
            $data['order_no'] = $post['so_order_no'] ? $post['so_order_no'] : 1;
            $data['unique_trade_no'] = guid(false);
            $ads_order = D('ads_order');
            $checkadsorders = D('Ads')->checkAdsOrders($data['ads_id'], $data['buyer_id'], $data['buyer_type'], $data['device']);
            if (!empty($checkadsorders)) {
                $this->error('您添加的广告已经存在');
            }
            if ($res = $ads_order->create($data)) {
                $result = $ads_order->add($res);
                if ($result) {
                    if (!empty($_FILES)) {
                        if (!empty($_FILES['resource_url']) && $_FILES['resource_url']['error'] === UPLOAD_ERR_OK) {
                            $resource_url = $this->uploadSingleImg('resource_url', 'ads/'.$result.'/', 'ads_', '3145728', '../upload/' );
                            $update_url['resource_url'] = $resource_url['path'];
                        } else {
                            $update_url['resource_url'] = '';
                        }
                        if ($r = $ads_order->create($update_url)) {
                            $update_resource_url = $ads_order->where(array('id' => $result))->save($r);
                        }
                    } 
                    $this->success('添加成功', U('Ads/adsOrders'));
                } else {
                    $this->error('添加失败', U('Ads/addAdsOrders'));
                }
            } else {
                $this->error('添加失败', U('Ads/addAdsOrders'));
            }
        } else {
            $this->assign('adslist', $adslist);
            $this->assign('school_id', $school_id);
            $this->assign('buyernamelist', $buyernamelist);
            $this->display('Ads/addAdsOrders');
        }
    }

    /**
     * 编辑广告订单列表
     *
     * @return  void
     * @author  wl
     * @date    Nov 22, 2016
     **/
    public function editAdsOrders () {
        $id = I('param.id');
        $ads_id = I('param.ads_id');
        $school_id = $this->getLoginauth();
        $adslist = D('Ads')->getAdsName();
        $buyernamelist = D('Ads')->getBuyerName($school_id);
        $adsorderslist = D('Ads')->getAdsOrdersById($id);
        if (IS_POST) {
            $post = I('post.');
            $data = array();
            $data['ads_title'] = trim($post['ads_title']) == '' ? $adsorderslist['ads_title'] : trim($post['ads_title']);
            $data['ads_id'] = $post['ads_id'] == '' ? $adsorderslist['ads_id'] : $post['ads_id'];
            $data['buyer_type'] = 2;
            if ($post['buyer_id'] == '') {
                $data['buyer_id'] = 0 ? 0 : $adsorderslist['buyer_id'];
            } else {
                $data['buyer_id'] = $post['buyer_id'] ? $post['buyer_id'] : 0;
            }
            if ($data['buyer_id'] == 0 ) {
                $data['buyer_name'] = '嘻哈平台';
            } else {
                $data['buyer_name'] = D('ads')->getNameByBuyerId($data['buyer_id']);
            }
            $data['buyer_phone']    = $post['buyer_phone'] ? $post['buyer_phone'] : '0551-65653272';
            $data['resource_type']  = $post['resource_type'] == '' ? $adsorderslist['resource_type'] : $post['resource_type'];
            $data['ads_url']        = $post['ads_url'] ? $post['ads_url'] : '0551-65653272';
            $data['addtime']        = strtotime($post['addtime']) == '' ? $adsorderslist['addtime'] : strtotime($post['addtime']);
            $data['over_time']      = strtotime($post['over_time']) == '' ? $adsorderslist['over_time'] : strtotime($post['over_time']);
            $data['loop_time']      = $post['loop_time'] ? $post['loop_time'] : 1;
            $data['original_price'] = $post['original_price'] == '' ? $adsorderslist['original_price'] : $post['original_price'];
            $data['final_price']    = $post['final_price'] == '' ? $adsorderslist['final_price'] : $post['final_price'];
            $data['device']         = $post['device'] ? $post['device'] : 1;
            $data['is_promote']     = $post['is_promote'] ? $post['is_promote'] : 0;
            $data['pay_type']       = $post['pay_type'] ? $post['pay_type'] : 1;
            $data['order_status']   = $post['order_status'] ? $post['order_status'] : 1;
            $data['order_no']       = $post['so_order_no'] ? $post['so_order_no'] : 1;
            $data['unique_trade_no'] = guid(false);
            $url = $adsorderslist['resource_url'];
            $count = count($url);
            if (!empty($_FILES['resource_url']) && $_FILES['resource_url']['error'] === UPLOAD_ERR_OK) {
                $resource_url = $this->uploadSingleImg('resource_url', 'ads/'.$id.'/', 'ads_', '3145728', '../upload/' );
                $data['resource_url'] = $resource_url['path'];
            } else {
                $data['resource_url'] = $adsorderslist['resource_url'] != '' ? $adsorderslist['resource_url'] : '';
            }
            $ads_order = D('ads_order');
            if ($res = $ads_order->create($data)) {
                $result = $ads_order->where(array('id' => $id))->fetchSql(false)->save($res);
                if ($re || $result) {
                    $this->success('编辑成功', U('Ads/adsOrders'));
                } else {
                    $this->error('编辑失败', U('Ads/editAdsOrders'));
                }
            } else {
                $this->error('编辑失败', U('Ads/editAdsOrders'));
            }
        } else {
            $this->assign('adslist', $adslist);
            $this->assign('school_id', $school_id);
            $this->assign('buyernamelist', $buyernamelist);
            $this->assign('adsorderslist', $adsorderslist);
            $this->display('Ads/editAdsOrders');
        }

    }

    /**
     * 设置订单状态（广告）
     *
     * @return  void
     * @author  wl
     * @date    Mar 14, 2017
     **/
    public function setAdsOrdersStatus () {
        if (IS_AJAX) {
            $post = I('post.');
            $id = $post['id'];
            $current_status = $post['status'];
            $original_status = $post['original_status'];
            $now_time = time();
            $ads_orders_info = D('Ads')->getAdsOrdersById($id);
            $add_time = strtotime($ads_orders_info['addtime']);
            $expire_time = strtotime($ads_orders_info['over_time']);
            if ($expire_time - $now_time < 0) { // 过期时间判断
                $data = array('code' => 102, 'msg' => '该券已过期，只可删除', );
                $this->ajaxReturn($data, 'JSON');
            }
            // if ($current_status == $original_status) { // 修改成了当前的状态
            //     $data = array('code' => 101, 'msg' => '修改的状态不能为当前状态');
            //     $this->ajaxReturn($data, 'JSON');
            // }
            if ($original_status == 1002) { // 已付款
                if ($current_status == 1003 || $current_status == 1005) { 
                    $data = array('code' => 103, 'msg' => '该券已付过款,您可设置退款中', 'data' => $current_status);
                    $this->ajaxReturn($data, 'JSON');

                } else if ($current_status == 1002) {
                    $data = array('code' => 103, 'msg' => '修改的状态不能为当前状态', 'data' => $current_status);
                    $this->ajaxReturn($data, 'JSON');

                } else {
                    $result = D('Ads')->setAdsOrdersStatus($id, $current_status);
                    if ($result) {
                        $data = array('code' => 200, 'msg' => '设置成功', 'data' => $current_status);
                    } else {
                        $data = array('code' => 400, 'msg' => '设置失败', 'data' => '');
                    }
                    $this->ajaxReturn($data, 'JSON');
                }

            } else if ($original_status == 1003) { // 未付款

                if ($current_status == 1006) {
                    $data = array('code' => 103, 'msg' => '您还未付款', 'data' => $current_status);
                    $this->ajaxReturn($data, 'JSON');

                } else if ($current_status == 1006) {
                    $data = array('code' => 103, 'msg' => '修改的状态不能为当前状态', 'data' => $current_status);
                    $this->ajaxReturn($data, 'JSON');

                } else {
                    $result = D('Ads')->setAdsOrdersStatus($id, $current_status);
                    if ($result) {
                        $data = array('code' => 200, 'msg' => '设置成功', 'data' => $current_status);
                    } else {
                        $data = array('code' => 400, 'msg' => '设置失败', 'data' => '');
                    }
                    $this->ajaxReturn($data, 'JSON');
                }

            } else if ($original_status == 1005) { // 已取消

                $data = array('code' => 103, 'msg' => '您只可删除此订单', 'data' => $current_status);
                $this->ajaxReturn($data, 'JSON');

            } else if ($original_status == 1006) { // 退款中

                if ($current_status == 1002) {
                    $data = array('code' => 103, 'msg' => '您只可取消或删除该订单', 'data' => $current_status);
                    $this->ajaxReturn($data, 'JSON');
                }
            }

            $result = D('Ads')->setAdsOrdersStatus($id, $current_status);
            if ($result) {
                $data = array('code' => 200, 'msg' => '设置成功', 'data' => $current_status);
            } else {
                $data = array('code' => 400, 'msg' => '设置失败', 'data' => '');
            }

        } else {
            $data = array('code' => 400, 'msg' => '设置失败', 'data' => '');
        }
        $this->ajaxReturn($data, 'JSON');
    }

    /**
     * 广告的图片展示
     *
     * @return  void
     * @author  wl
     * @date    Nov 23, 2016
     **/
    public function show () {
        $param = I('param.');
        $id = trim((int)$param['id']);
        $ads_id = trim((int)$param['ads_id']);
        $resource_url = trim((string)$param['resource_url']);
        $resource_type = trim((string)$param['resource_type']);
        if (empty($param)) {
            $this->error('预览失败');
        } else {
            $this->assign('id', $id);
            $this->assign('ads_id', $ads_id);
            $this->assign('resource_url', $resource_url);
            $this->assign('resource_type', $resource_type);
            $this->display('Ads/show');
        }
    }

    /**
     * 删除广告订单的资源图片
     *
     * @return  void
     * @author  wl
     * @date    Nov 23, 2016
     **/
    public function delResourceUrl () {
        if (IS_AJAX) {
            $post = I('post.'); 
            $id = $post['id'];
            $resourceurl = $post['resource_url'];
            $resourceurlall = $post['resource_url_all'];
            $result = D('Ads')->delResourceUrl($id, $resourceurl);
            if ($result) {
                $data = array('code' => 200, 'msg' =>'删除成功', 'data' => $id);
            } else {
                $data = array('code' => 400, 'msg' =>'删除失败', 'data' => $id);
            }
        }
        $this->ajaxReturn($data, 'JSON');
    }

    /**
     * 删除广告订单
     *
     * @return  void
     * @author  wl
     * @date    Nov 21, 2016
     **/
    public function delAdsOrders () {
        if (IS_AJAX) {
            $id = I('post.id');
            $result = D('Ads')->delAdsOrders($id);
            if ($result) {
                $data = array('code' => 200, 'msg' =>'删除成功', 'data' => $id);
            } else {
                $data = array('code' => 400, 'msg' =>'删除失败', 'data' => $id);
            }
        }
        $this->ajaxReturn($data, 'JSON');
    }

    /**
     * 设置打折状态
     *
     * @return  void
     * @author  wl
     * @date    Nov 21, 2016
     **/
    public function setPromoteStatus () {
        if(IS_AJAX) {
            $id = I('post.id');
            $status = I('post.status');
            $res = D('Ads')->setPromoteStatus($id,$status);
            if($res['is_promote']) {
                $data = array('code'=>200, 'msg'=>"设置成功", 'data'=>$id);
            } else {
                $data = array('code'=>102, 'msg'=>"设置失败", 'data'=>'');
            }
        } else {
            $data = array('code'=>102, 'msg'=>"设置失败", 'data'=>'');    
        }
        action_log('set_adspromote_status', 'ads_order', $id, $this->getLoginUserId());
        $this->ajaxReturn($data, 'JSON');
    }

// 2.广告管理模块
    /**
     * 获取广告列表
     *
     * @return  void
     * @author  wl
     * @date    Sep 21, 2016
     **/
    public function adsManage () {
        $publisher_id = $this->getLoginauth();
        $adsinfo = D('Ads')->getAdsList($publisher_id);
        $this->assign('publisher_id', $publisher_id);
        $this->assign('page', $adsinfo['page']);
        $this->assign('count', $adsinfo['count']);
        $this->assign('ads_info', $adsinfo['ads_info']);
        $this->display('Ads/adsManage');
    }
    /**
     * 删除广告管理(逻辑删除)
     *
     * @return  void
     * @author  wl
     * @date    Sep 23, 2016
     **/
    public function delAdsManage () {
        if (IS_AJAX) {
            $id = I('param.id');
            $result = D('Ads')->delAdsManage($id);
            if ($result) {
                $data = array('code' => 200, 'msg' => '设置成功', 'data' => $id);
            } else {
                $data = array('code' => 103, 'msg' => '设置失败', 'data' => '');
            }
        } else {
            return false;
        }
        action_log('del_adsmanage', 'ads', $id, $this->getLoginUserId());
        $this->ajaxReturn($data, 'JSON');
    }

    /**
     * 添加广告
     *
     * @return  void
     * @author  wl
     * @date    Sep 23, 2016
     **/
    public function addAdsManage () {
        $publisher_id = $this->getLoginauth();
        $ads_positions = D('AdsPosition')->getAdsPositions();
        $ads_level = D('AdsLevel')->getAllRecords();
        $province_list = D('Province')->getProvinceList();
        if (IS_POST) {
            $post = I('post.');
            $data['scene_id']       = $post['scene'] ? $post['scene'] : 101;
            $data['level_id']       = $post['level'] ? $post['level'] : '1,2';
            $data['publisher_id']   = $publisher_id;
            $data['publisher_type'] = 0;
            $data['province_id']    = $post['province'] ? $post['province'] : 0;
            $data['area_id']        = $post['area'] ? $post['area'] : 0;
            $data['city_id']        = $post['city'] ? $post['city'] : 0;
            $data['limit_time']     = $post['limit_time'] ? $post['limit_time'] : 3;
            $data['limit_num']      = $post['limit_num'] ? $post['limit_num'] : 1;
            $data['title']          = $post['title'] ? $post['title'] : '';
            $data['intro']          = $post['intro'] ? $post['intro'] : '';
            $data['ads_status']     = $post['show'] ? $post['show'] : 2;
            $data['sort_order']     = $post['sort_order'] ? $post['sort_order'] : 0;
            $data['addtime']        = time();
            $data_info['device']    = $post['device'] != '' ? $post['device'] : '1,2';
            $data_info['resource_type'] = $post['resource_type'] != '' ? $post['resource_type'] : '1';
            $data_info['addtime']   = time();
            $ads = D('ads');
            $ads_info = D('ads_info');
            if ($res = $ads->create($data)) {
                $result = $ads->add($res);
                if ($result) {
                    $data_info['ads_id'] = $result; 
                    if ($re = $ads_info->create($data_info)) {
                        $add_ads_info = $ads_info->add($re);
                        if ($add_ads_info) {
                            action_log('add_adsmanage', 'ads', $result, $this->getLoginUserId());
                            $this->success('添加成功', U('Ads/adsManage'));
                        } else {
                            action_log('add_adsmanage', 'ads', $result, $this->getLoginUserId());
                            $this->error('添加失败', U('Ads/addAdsManage'));
                        }
                    }
                } else {
                    action_log('add_adsmanage', 'ads', $result, $this->getLoginUserId());
                    $this->error('添加失败', U('Ads/addAdsManage'));
                }
            } else {
                action_log('add_adsmanage', 'ads', $result, $this->getLoginUserId());
                $this->error('添加失败', U('Ads/addAdsManage'));
            }

        } else {
            $this->assign('province_list', $province_list);
            $this->assign('ads_positions', $ads_positions[0]);
            $this->assign('ads_level', $ads_level['ads_level']);
            $this->display('Ads/addAdsManage');
        }
    }
     /**
     * 编辑广告
     *
     * @return  void
     * @author  wl
     * @date    Sep 23, 2016
     **/
    public function editAdsManage () {
        $publisher_id = $this->getLoginauth();
        $id = I('param.id');
        $ads_positions = D('AdsPosition')->getAdsPositions();
        $ads_level = D('AdsLevel')->getAllRecords();
        $province_list = D('Province')->getProvinceList();
        $adsManageList = D('Ads')->getAdsListById($id);
        if (IS_POST) {
            $post = I('post.');
            $data['id']             = $post['id'] == '' ? $adsManageList['ads_id'] : $post['id'];
            $data['title']          = $post['title'] == '' ? $adsManageList['ads_title'] : $post['title'];
            $data['scene_id']       = $post['scene'] == '' ? $adsManageList['scene'] : $post['scene'];
            $data['level_id']       = $post['level'] == '' ? $adsManageList['level_id'] : $post['level'];
            $data['publisher_id']   = $adsManageList['ads_publisher_id'];
            $data['publisher_type'] = 0;
            $data['province_id']    = $post['province'] == ''  ? $adsManageList['province_id'] : $post['province'];
            $data['area_id']        = $post['area'] == '' ? $adsManageList['area_id'] : $post['area'];
            $data['city_id']        = $post['city'] == '' ? $adsManageList['city_id'] : $post['city'];
            $data['limit_time']     = $post['limit_time'] == '' ? $adsManageList['limit_time'] : $post['limit_time'];
            $data['limit_num']      = $post['limit_num'] == '' ? $adsManageList['limit_num'] : $post['limit_num'];
            $data['intro']          = $post['intro'] == '' ? $adsManageList['ads_intro'] : $post['intro'];
            $data['ads_status']     = $post['show'] ? $post['show'] : 1;
            $data['sort_order']     = $post['sort_order'] == '' ? $adsManageList['sort_order'] : $post['sort_order'];
            $data_info['device']    = $post['device'] != '' ? $post['device'] : $adsManageList['device'];
            $data_info['resource_type'] = $post['resource_type'] != '' ? $post['resource_type'] : $adsManageList['resource_type'];
            $data_info['ads_id']    = $adsManageList['ads_id'];
            $data_info_id           = $post['ads_info_id'] == '' ? $adsManageList['ads_info_id'] : $post['ads_info_id'];
            $ads = D('ads');
            $ads_info = D('ads_info');
            if ($data_info_id == '') {
                $data_info['addtime'] = time();
                if ($r = $ads_info->create($data_info)) {
                    $data_info_id = $ads_info->add($r);
                }
                $check_ads_info = D('Ads')->checkAdsId($data_info['ads_id']);
                if (!$check_ads_info) {
                    $this->error('您已此账号添加过', U('System/editAdsManage'));
                }
            }
            if ($res = $ads->create($data)) {
                $ads_result = $ads->where(array('id' => $data['id']))->fetchSql(false)->save($res);
            } 

            if ($re = $ads_info->create($data_info)) {
                $ads_info_result = $ads_info->where(array('id' => $data_info_id))->fetchSql(false)->save($re);
            }

            if ($ads_result || $ads_info_result) {
                action_log('edit_adsmanage', 'ads', $id, $this->getLoginUserId());
                $this->success('编辑成功', U('Ads/adsManage'));
            } else {
                action_log('edit_adsmanage', 'ads', $id, $this->getLoginUserId());
                $this->error('未做任何修改', U('Ads/editAdsManage'));
            }
        } else {
            $this->assign('province_list', $province_list);
            $this->assign('ads_positions', $ads_positions[0]);
            $this->assign('ads_level', $ads_level['ads_level']);
            $this->assign('adsManageList', $adsManageList);
            $this->display('Ads/editAdsManage');
        }
    }

    /**
     * 双击单元格进行排序
     *
     * @return  void
     * @author  wl
     * @date    Sep 24, 2016
     **/
    public function changeAdsOrder () {
        if (!IS_POST || !IS_AJAX) {
            $msg = "参数错误";
            $data = '';
            $this->ajaxReturn(array('code' => 101, 'msg' => $msg, 'data' => $data), "JSON");
        }
        $post = I('post.');
        $update_ok = D('Ads')->updateAdsOrder($post);
        if ($update_ok == 101 || $update_ok == 102 || $update_ok == 105 ) {
            $code = $update_ok;
            $msg = '参数错误';
            $data = '';

        } elseif ($update_ok == 200) {
            $code = $update_ok;
            $msg = '修改成功';
            $data = '';

        } elseif ($update_ok == 400) {
            $code = $update_ok;
            $msg = '修改失败';
            $data = '';
        }
        action_log('set_adsmanage_order', 'ads', $post['id'], $this->getLoginUserId());
        $this->ajaxReturn(array('code' => $code, 'msg' => $msg, 'data' => $data), "JSON");
    }

    /**
     * 设置排序
     *
     * @return  void
     * @author  wl
     * @date    Sep 23, 2016
     **/
    public function setAdsOrder () {
        if (IS_AJAX) {
            $post = I('post.');
            $id = $post['id'];
            $data['sort_order'] = $post['sort_order'] ? $post['sort_order']: 0;
            $adsManage = M('ads');
            if ($res = $adsManage->create($data)) {
                $result = $adsManage->where(array('id' => $id))->fetchSql(false)->save($res);
                if ($result) {
                    $data = array('code' => 200, 'msg' => '修改成功', 'data' => $data['i_order']);
                } else {
                    $data = array('code' => 103, 'msg' => '修改失败', 'data' => '');
                }
            } else {
                $data = array('code' => 103, 'msg' => '修改失败', 'data' => '');
            }
        }
        $this->ajaxReturn($data, "JSON");

    }

	//广告位管理
	public function adsPosition() {
		$res = D('AdsPosition')->getAdsPositions();
		$ads_positions = $res[0];
		$count = $res[1];
		$this->assign('count', $count);
		$this->assign('ads_positions', $ads_positions);
		$this->display(CONTROLLER_NAME.'/'.__FUNCTION__);
	}


	//添加广告位
	public function addAdsPosition() {
		if (I('post.')) {
			$condition['title'] = I('post.short-name'); //简短标题
			$condition['description'] = I('post.description');//基本描述
			$device = I('post.device');//客户端类型
			$position = I('post.position');//位置
			$condition['scene'] = intval($device.'0'.$position);//场景
			$findScene = D('AdsPosition')->findScene($condition['scene']);
			if ($findScene) {
				$data = array('code'=>400, 'data'=>'', 'msg'=>"该广告位已经存在");
				$this->ajaxReturn($data, 'JSON');	
			}
			$condition['addtime'] = time();//添加时间
			$add = D('AdsPosition')->addAdsPosition($condition);//添加广告位
			if ($add) {
				$data = array('code'=>200, 'data'=>'', 'msg'=>"添加成功");
                action_log('add_ads_position', 'ads_position', $add, $this->getLoginUserId());
				$this->ajaxReturn($data, 'JSON');	
			} else {
				$data = array('code'=>400, 'data'=>'', 'msg'=>"添加失败");
                action_log('add_ads_position', 'ads_position', $add, $this->getLoginUserId());
				$this->ajaxReturn($data, 'JSON');	
			}
			
		} else{
			$this->display(CONTROLLER_NAME.'/'.__FUNCTION__);
		}
	}

	public function checkScene() {
		$device = I('post.device');//客户端类型
		$position = I('post.position');//位置
		$condition['scene'] = intval($device.'0'.$position);//场景
		$findScene = D('AdsPosition')->findScene($condition['scene']);
		if ($findScene) {
				$data = array('code'=>400, 'data'=>'', 'msg'=>":( 该广告位已经存在");
				$this->ajaxReturn($data, 'JSON');	
		} else {
				$data = array('code'=>200, 'data'=>'', 'msg'=>":) 该广告位可以发布");
				$this->ajaxReturn($data, 'JSON');	
		}
	}
	//编辑广告位
	public function editAdsPosition() {
		if (I('post.')) {
			$condition['id'] = I('post.edit_id'); //id
			$condition['description'] = I('post.description');//基本描述
			$condition['addtime'] = time();//编辑时间
			$edit = D('AdsPosition')->editAdsPosition($condition);//编辑广告位
			if ($edit) {
				$data = array('code'=>200, 'data'=>'', 'msg'=>"编辑成功");
                action_log('edit_ads_position', 'ads_position', $condition['id'], $this->getLoginUserId());
				$this->ajaxReturn($data, 'JSON');	
			} else {
				$data = array('code'=>400, 'data'=>'', 'msg'=>"编辑失败");
                action_log('edit_ads_position', 'ads_position', $condition['id'], $this->getLoginUserId());
				$this->ajaxReturn($data, 'JSON');	
			}
		} else {
			$id = I('get.id');
			$ads_position = D('AdsPosition')->findPosition($id);
			$this->assign('ads_position', $ads_position);
			$this->display(CONTROLLER_NAME.'/'.__FUNCTION__);
		}	
	}

	//删除广告位
	public function delAdsPosition() {
		$id = I('post.id');
		$del = D('AdsPosition')->delPosition($id);
		if ($del) {
			$data = array('code'=>'200', 'msg'=>'删除成功', 'data'=>$id);
		} else {
			$data = array('code'=>'400', 'msg'=>'删除失败', 'data'=>$id);
		}
        action_log('del_ads_position', 'ads_position', $id, $this->getLoginUserId());
		$this->ajaxReturn($data, 'JSON');
	}
	/* 
     * 广告等级管理部分
     * @author Gao
     */
    // 广告等级
    public function adsLevel () {
        $res = D('AdsLevel')->getAllRecords();
        $count = $res['count'];
        $page = $res['page'];
        $ads_level = $res['ads_level'];
        $this->assign('count', $count);
        $this->assign('list', $ads_level);
        $this->assign('page', $page);
        $this->display(CONTROLLER_NAME.'/'.__FUNCTION__);
    }

    public function addAdsLevel() {
        $this->display(CONTROLLER_NAME.'/'.__FUNCTION__);
    }

    public function addAdsLevelDo() {
        if (!IS_POST) {
            $code = 101;
            $msg = '参数错误';
            $data = '';
            $this->ajaxReturn(array('code' => $code, 'data' => $data, 'msg' => $msg));
        }
        $post = I('post.');
        $i_level = $post['level_id'];
        $level_list = M('ads_level')
                            ->where(array('level_id' => $i_level))
                            ->fetchSql(false)
                            ->find();
        if ( !empty($level_list) ) {
            $this->error('您添加的等级已经存在', U('Ads/addAdsLevelDo'));
        }
        
        $add_ok = D('AdsLevel')->addOneRecord($post);
        if ( $add_ok ) {
            $code = 200;
            $msg = '添加成功';
            $data = '';
            action_log('add_ads_level', 'ads_level', $add_ok, $this->getLoginUserId());
            $this->success('添加成功', U('Ads/adsLevel'), 2);
        } else {
            $code = 400;
            $msg = '添加失败';
            $data = '';
            action_log('add_ads_level', 'ads_level', $add_ok, $this->getLoginUserId());
            $this->error('添加失败', U('Ads/addAdsLevelDo'), 3);
        }
    }

    public function delAdsLevel() {
        if (!IS_AJAX || !IS_POST) {
            $code = 101;
            $msg = '参数错误';
            $data = '';
            $this->ajaxReturn(array('code' => $code, 'data' => $data, 'msg' => $msg));
        }

        if ( empty(I('post.id')) ) {
            $code = 102;
            $msg = '参数错误';
            $data = '';
            $this->ajaxReturn(array('code'=>$code,'data'=>$data,'msg'=>$msg));
        }

        $id = I('post.id');

        $delete_ok = D('AdsLevel')->delOneRecord($id);
        if ( $delete_ok ) {
            $code = 200;
            $msg = '删除成功';
            $data = '';
        } else {
            $code = 400;
            $msg = '删除失败';
            $data = '';
        }
        action_log('del_ads_level', 'ads_level', $id, $this->getLoginUserId());
        $this->ajaxReturn(array('code' => $code, 'data' => $data, 'msg' => $msg));
    }

    public function updateLevel() {
        if (!IS_AJAX || !IS_POST) {
            $msg = '参数错误';
            $data = '';
            $this->ajaxReturn(array('code' => 101, 'data' => $data, 'msg' => $msg));
        }

        $p_params = I('post.');
        $update_ok = D('AdsLevel')->updateOneRecordPartial($p_params);
        if ( $update_ok == 101 || $update_ok == 102 || $update_ok == 105) {
            $code = $update_ok;
            $msg =  '参数错误';
            $data = '';
        } elseif ($update_ok == 107 ) {
            $code = $update_ok;
            $msg =  '未做任何修改';
            $data = '';
        } elseif ( $update_ok == 200 ) {
            $code = 200;
            $msg = '更新成功';
            $data = '';
        } elseif ( $update_ok == 400 ) {
            $code = 400;
            $msg = '更新失败';
            $data = '';
        }
        action_log('update_ads_level', 'ads_level', $p_params['id'], $this->getLoginUserId());
        $this->ajaxReturn(array('code' => $code, 'data' => $data, 'msg' => $msg));
    }

    // 删除广告
    public function delAds() {

    	$id = I('post.id'); // 广告id
    	$order_num = D('AdsOrder')->getAdsOrders($id);//判断广告订单是否存在
    	if ($order_num == false) {	// 该广告没有订单，ads_status彻底失效3
    		$status = 3;
    	} else {	// 该广告有订单，ads_status不招租2
    		$status = 2;
    	}
		$change_status = D('Ads')->changeAdsStatus($id, $status);
		if ($change_status) {
			$this->ajaxReturn(array('code' => 200, 'data' => '', 'msg' => '删除成功'));
		} else {
			$this->ajaxReturn(array('code' => 400, 'data' => '', 'msg' => '删除失败'));
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

    //自动生成订单号
    public function createno() {
        $s_order_no = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
        $data = array('code'=>-1, 'data'=>$s_order_no);
        $this->ajaxReturn($data, 'JSON');
    }


}
?>
