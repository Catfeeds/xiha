<?php 
namespace Admin\Controller;
use Think\Controller;

/**
 * 优惠卷管理模块
 * @author wl
 */
class CoinController extends BaseController {

	//构造函数，判断是否登录
	public function _initialize() {
	    if(!session('loginauth')) {
			$this->redirect('Public/login');
			exit();
	    }
	}

    /**
     * 金币商城商品管理
     *
     * @return  void
     * @author  wl
     * @date    Oct 14, 2016
     **/
    public function index () {
        $coingoodslist = D('Coin')->getCoinGoodsList();
        $this->assign('page', $coingoodslist['page']);
        $this->assign('count', $coingoodslist['count']);
        $this->assign('coingoodslist', $coingoodslist['coingoodslist']);
        $this->display('Coin/index');
    }

    /**
     * 搜索金币商城商品
     *
     * @return  void
     * @author  wl
     * @date    Oct 11, 2016
     **/
    public function searchCoinGoods () {
        $param = I('param.');
        $search_info = trim((string)$param['search_info']);
        $s_keyword = trim((string)$param['s_keyword']);
        $is_hot = (int)$param['is_hot'];
        $is_recommend = (int)$param['is_recommend'];
        $is_promote = (int)$param['is_promote'];
        $is_publish = (int)$param['is_publish'];
        $is_deleted = (int)$param['is_deleted'];
        if ( $s_keyword == '' && $search_info == '' && $is_hot == '' && $is_recommend == '' && $is_promote == ''  && $is_publish == ''  && $is_deleted == '') {
            $this->redirect('Coin/index');
        } else {
            $coingoodslist = D('Coin')->searchCoinGoods($param);
            $this->assign('is_hot', $is_hot);
            $this->assign('is_recommend', $is_recommend);
            $this->assign('is_promote', $is_promote);
            $this->assign('is_publish', $is_publish);
            $this->assign('is_deleted', $is_deleted);
            $this->assign('s_keyword', $s_keyword);
            $this->assign('search_info', $search_info);
            $this->assign('page', $coingoodslist['page']);
            $this->assign('count', $coingoodslist['count']);
            $this->assign('coingoodslist', $coingoodslist['coingoodslist']);
            $this->display('Coin/index');
        }
    }
    /**
     * 添加金币商城商品
     *
     * @return  void
     * @author  wl
     * @date    Oct 15, 2016
     **/
    public function addCoinGoods () {
        if (IS_POST) {
            $post = I('post.');
            $data['cate_id']                = $post['cate_id'] ? $post['cate_id'] : 1;
            $data['goods_name']             = $post['goods_name'] ? $post['goods_name'] : '';
            $data['goods_original_price']   = intval($post['goods_original_price']) ? intval($post['goods_original_price']) : 0;
            $data['goods_final_price']      = intval($post['goods_final_price']) ? intval($post['goods_final_price']) : 0;
            $data['goods_real_price']       = intval($post['goods_real_price']) ? intval($post['goods_real_price']) : 0; 
            $data['goods_total_num']        = $post['goods_total_num'] ? $post['goods_total_num'] : 1;
            $data['goods_expiretime']       = strtotime($post['expiretime']) ? strtotime($post['expiretime']) : 0;
            $data['order']                  = $post['order'] ? $post['order'] : 50;
            $data['goods_desc']             = $post['goods_desc'] ? $post['goods_desc'] : '';
            $data['goods_detail']           = $post['goods_detail'] ? $post['goods_detail'] : '';            
            $data['is_hot']                 = $post['is_hot'] ? $post['is_hot'] : 1;
            $data['is_recommend']           = $post['is_recommend'] ? $post['is_recommend'] : 1;
            $data['is_promote']             = $post['is_promote'] ? $post['is_promote'] : 1;
            $data['is_publish']             = $post['is_publish'] ? $post['is_publish'] : 1;
            $data['addtime']                = time();
            $data['is_deleted']             = 2; // 1:上架 2:下架
           
            if ($data['cate_id'] == '' && $data['goods_name'] == '' && $data['goods_original_price'] == '' && $data['goods_final_price'] == '' && $data['goods_original_money'] == '' && $data['goods_total_num'] == '' && $data['addtime'] == '' && $data['goods_expiretime'] == '' && $data['goods_desc'] == '' && $data['goods_detail'] == '') {
                $this->error('请完善信息', U('Coin/addCoinGoods'));
            }

            $coingoods = D('coin_goods');
            if ($res = $coingoods->create($data)) {
                $result = $coingoods->add($res);
                if ($result) {
                    action_log('add_coin_goods', 'coin_goods', $result, $this->getLoginUserId());
                    $this->success('添加成功', U('Coin/index'));
                } else {
                    action_log('add_coin_goods', 'coin_goods', $result, $this->getLoginUserId());
                    $this->error('添加失败', U('Coin/addCoinGoods'));
                }
            } else {
                action_log('add_coin_goods', 'coin_goods', $result, $this->getLoginUserId());
                $this->error('添加失败', U('Coin/addCoinGoods'));
            }

        } else {
            $coingoodscatelist = D('Coin')->getCoinCateName();
            $this->assign('coingoodscate', $coingoodscatelist);
            $this->display('Coin/addCoinGoods');
        }

    }

    /**
     * 编辑金币商城商品
     *
     * @return  void
     * @author  wl
     * @date    Oct 15, 2016
     **/
    public function editCoinGoods () {
        $id = I('param.id');
        $coinGoodsList = D('Coin')->getCoinGoodsById($id);
        if (IS_POST) {
            $post = I('post.');
            $data['cate_id'] = $post['cate_id'] ? $post['cate_id'] : 1;
            $data['goods_name'] = $post['goods_name'] == '' ? $coinGoodsList['goods_name'] : $post['goods_name'];
            $data['goods_original_price'] = $post['goods_original_price'] == '' ? $coinGoodsList['goods_original_price'] : $post['goods_original_price'];
            $data['goods_final_price'] = intval($post['goods_final_price']) == '' ? $coinGoodsList['goods_final_price'] : intval($post['goods_final_price']); 
            $data['goods_total_num'] = $post['goods_total_num'] == '' ? $coinGoodsList['goods_total_num'] : $post['goods_total_num'];
            $data['goods_expiretime'] = strtotime($post['expiretime']) ? strtotime($post['expiretime']) : 0;
            $data['order'] = intval($post['order']) > 0 ? intval($post['order']) : 50;
            $data['goods_desc'] = $post['goods_desc'] == '' ? $coinGoodsList['goods_desc'] : $post['goods_desc'];
            $data['goods_detail'] = $post['goods_detail'] == '' ? $coinGoodsList['goods_detail'] : $post['goods_detail'];            
            $data['is_hot'] = $post['is_hot'] == '' ? $coinGoodsList['is_hot'] : $post['is_hot'];
            $data['is_recommend'] = $post['is_recommend'] == '' ? $coinGoodsList['is_recommend'] : $post['is_recommend'];
            $data['is_promote'] = $post['is_promote'] == '' ? $coinGoodsList['is_promote'] : $post['is_promote'];
            $data['is_publish'] = $post['is_publish'] == '' ? $coinGoodsList['is_publish'] : $post['is_publish'];
            $data['updatetime'] = time();

            if ($data['cate_id'] == '' && $data['goods_name'] == '' && $data['goods_original_price'] == '' && $data['goods_final_price'] == '' && $data['goods_original_money'] == '' && $data['goods_total_num'] == '' && $data['addtime'] == '' && $data['goods_expiretime'] == '' && $data['goods_desc'] == '' && $data['goods_detail'] == '') {
                $this->error('请完善信息', U('Coin/editCoinGoods'));
            }

            $coingoods = D('coin_goods');
            if ($res = $coingoods->create($data)) {
                $result = $coingoods->where(array('id' =>$id))->save($res);
                if ($result) {
                    action_log('edit_coin_goods', 'coin_goods', $id, $this->getLoginUserId());
                    $this->success('修改成功', U('Coin/index'));
                } else {
                    action_log('edit_coin_goods', 'coin_goods', $id, $this->getLoginUserId());
                    $this->error('修改失败', U('Coin/editCoinGoods'));
                }
            } else {
                action_log('edit_coin_goods', 'coin_goods', $id, $this->getLoginUserId());
                $this->error('修改失败', U('Coin/editCoinGoods'));
            }

        } else {
            $coingoodscatelist = D('Coin')->getCoinCateName();
            $this->assign('coingoodscate', $coingoodscatelist);
            $this->assign('coingoodslist', $coinGoodsList);
            $this->display('Coin/editCoinGoods');
        }
    }

    /**
     * 删除商品分类信息
     *
     * @return  void
     * @author  wl
     * @date    Oct 14, 2016
     **/
    public function delCoinGoods(){
        if(IS_AJAX){
            $cid =I('post.id');
            $res = D('Coin')->delCoinGoods($cid);
            if($res){
                $data=array('code'=>200,'msg'=>"删除成功",'data'=>'');
            } else {
                $data=array('code'=>101,'msg'=>"删除失败",'data'=>'');
            }
        } else {
            $data=array('code'=>102,'msg'=>"删除失败",'data'=>'');
        }
        action_log('del_coin_goods', 'coin_goods', $cid, $this->getLoginUserId());
        $this->ajaxReturn($data,'JSON');
    }
    /**
    * 设置商品的排序状态
    *
    * @return  void
    * @author  wl
    * @date    Oct 14, 2016
    **/
    public function setCoinGoodsOrder () {
        if (!IS_AJAX || !IS_POST) {
            $msg = '参数错误';
            $data = '';
            $this->ajaxReturn(array('code' => 101, 'msg' => $msg, 'data' => $data), 'JSON');
        }

        $post = I('post.');
        $update_ok = D('Coin')->updateCoinGoodsOrder($post);
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
        action_log('set_coingoods_order', 'coin_goods', $post['id'], $this->getLoginUserId());
        $this->ajaxReturn(array('code' => $code, 'msg' => $msg, 'data' => $data), 'JSON');
    }
    /**
     * 设置是否热销的状态
     *
     * @return  void
     * @author  wl
     * @date    Oct 14, 2016
     **/
    public function setHotStatus() {
        $id = I('post.id');
        $status = I('post.status');
        $list = D('Coin')->setHotStatus($id, $status);
        if($list['res']) {
            $data = array('code'=>200, 'msg'=>"设置成功", 'data'=>$list['id']);
        } else {
            $data = array('code'=>102, 'msg'=>"设置失败", 'data'=>'');
        }
        action_log('set_coingoods_hot', 'coin_goods', $id, $this->getLoginUserId());
        $this->ajaxReturn($data, 'JSON');
    }
    /**
     * 设置是否推荐的状态
     *
     * @return  void
     * @author  wl
     * @date    Oct 14, 2016
     **/
    public function setRecommendStatus() {
        $id = I('post.id');
        $status = I('post.status');
        $list = D('Coin')->setRecommendStatus($id, $status);
        if($list['res']) {
            $data = array('code'=>200, 'msg'=>"设置成功", 'data'=>$list['id']);
        } else {
            $data = array('code'=>102, 'msg'=>"设置失败", 'data'=>'');
        }
        action_log('set_coingoods_recommend', 'coin_goods', $id, $this->getLoginUserId());
        $this->ajaxReturn($data, 'JSON');
    }
    /**
     * 设置是否促销的状态
     *
     * @return  void
     * @author  wl
     * @date    Oct 14, 2016
     **/
    public function setPromoteStatus() {
        $id = I('post.id');
        $status = I('post.status');
        $list = D('Coin')->setPromoteStatus($id, $status);
        if($list['res']) {
            $data = array('code'=>200, 'msg'=>"设置成功", 'data'=>$list['id']);
        } else {
            $data = array('code'=>102, 'msg'=>"设置失败", 'data'=>'');
        }
        action_log('set_coingoods_promote', 'coin_goods', $id, $this->getLoginUserId());
        $this->ajaxReturn($data, 'JSON');
    }
    /**
     * 设置是否发布的状态
     *
     * @return  void
     * @author  wl
     * @date    Oct 14, 2016
     **/
    public function setPublishStatus() {
        $id = I('post.id');
        $status = I('post.status');
        $list = D('Coin')->setPublishStatus($id, $status);
        if($list['res']) {
            $data = array('code'=>200, 'msg'=>"设置成功", 'data'=>$list['id']);
        } else {
            $data = array('code'=>102, 'msg'=>"设置失败", 'data'=>'');
        }
        action_log('set_coingoods_publish', 'coin_goods', $id, $this->getLoginUserId());
        $this->ajaxReturn($data, 'JSON');
    }

    /**
     * 设置上下架状态
     *
     * @return  void
     * @author  wl
     * @date    2017-05-16
     **/
    public function setDeletedStatus() {
        $id = I('post.id');
        $status = I('post.status');
        $list = D('Coin')->setDeletedStatus($id, $status);
        if($list['res']) {
            $data = array('code'=>200, 'msg'=>"设置成功", 'data'=>$list['id']);
        } else {
            $data = array('code'=>102, 'msg'=>"设置失败", 'data'=>'');
        }
        action_log('set_coingoods_deleted', 'coin_goods', $id, $this->getLoginUserId());
        $this->ajaxReturn($data, 'JSON');
    }

    /**
     * 商品分类列表展示
     *
     * @return  void
     * @author  wl
     * @date    Oct 14, 2016
     **/
    public function goodsCategory () {
        $coinCategoryList = D('Coin')->getCoinCategoryList();
        $this->assign('page', $coinCategoryList['page']);
        $this->assign('count', $coinCategoryList['count']);
        $this->assign('coincategorylist', $coinCategoryList['coincategorylist']);
        $this->display('Coin/goodsCategory');
    }

    /**
     * 搜索优惠券信息
     *
     * @return  void
     * @author  wl
     * @date    Oct 11, 2016
     **/
    public function searchCoinCategory () {
        $param = I('param.');
        $s_keyword = trim((string)$param['s_keyword']);
        if ( $s_keyword == '') {
            $this->redirect('Coin/goodsCategory');
        } else {
            $coinCategoryList = D('Coin')->searchCoinCategory($param);
            $this->assign('s_keyword', $s_keyword);
            $this->assign('page', $coinCategoryList['page']);
            $this->assign('count', $coinCategoryList['count']);
            $this->assign('coincategorylist', $coinCategoryList['coincategorylist']);
            $this->display('Coin/goodsCategory');
        }
    }

   /**
     * 添加商品分类
     *
     * @return  void
     * @author  wl
     * @date    Oct 11, 2016
     **/
    public function addCoinCategory () {
        if (IS_POST) {
            $post = I('post.');
            $data['cate_name']  = $post['cate_name'] ? $post['cate_name'] : '' ;
            $data['cate_desc']  = $post['cate_desc'] ? $post['cate_desc'] : '' ;
            $data['order'] = intval($post['order']) > 0 ? intval($post['order']) : 0;
            $data['addtime']    = time() ;
            $coingoodscategory  = D('coingoods_category');
            if ($res = $coingoodscategory->create($data)) {
                $result = $coingoodscategory->fetchSql(false)->add($res);
                if ($result) {
                    action_log('add_coin_category', 'coingoods_category', $result, $this->getLoginUserId());
                    $this->success('添加成功', U('Coin/goodsCategory'));
                } else {
                    action_log('add_coin_category', 'coingoods_category', $result, $this->getLoginUserId());
                    $this->error('添加失败', U('Coin/addCoinCategory'));
                }
            } else {
                action_log('add_coin_category', 'coingoods_category', $result, $this->getLoginUserId());
                $this->error('添加失败', U('Coin/addCoinCategory'));
            }

        } else {
            $this->display('Coin/addCoinCategory');
        }
    }

    /**
     * 编辑商品分类信息
     *
     * @return  void
     * @author  wl
     * @date    Oct 11, 2016
     **/
    public function editCoinCategory () {
        $id = I('param.id');
        $coingoodscatelist = D('Coin')->getCoinCateListById($id);
        if (IS_POST) {
            $post = I('post.');
            $data['cate_name']  = $post['cate_name'] == '' ? $coingoodscatelist['cate_name'] : $post['cate_name'] ;
            $data['cate_desc']  = $post['cate_desc'] == '' ? $coingoodscatelist['cate_desc'] : $post['cate_desc'] ;
            $data['order'] = intval($post['order']) > 0 ? intval($post['order']) : $coingoodscatelist['order'] ;
            $data['updatetime'] = time();
            $coingoodscategory = D('coingoods_category');
            if ($res = $coingoodscategory->create($data)) {
                $result = $coingoodscategory->where(array('id' => $id))->fetchSql(false)->save($res);
                if ($result) {
                    action_log('edit_coin_category', 'coingoods_category', $id, $this->getLoginUserId());
                    $this->success('修改成功', U('Coin/goodsCategory'));
                } else {
                    action_log('edit_coin_category', 'coingoods_category', $id, $this->getLoginUserId());
                    $this->error('未做任何修改', U('Coin/editCoinCategory'));
                }
            } else {
                action_log('edit_coin_category', 'coingoods_category', $id, $this->getLoginUserId());
                $this->error('未做任何修改', U('Coin/editCoinCategory'));
            }
        } else {
            $this->assign('coingoodscatelist', $coingoodscatelist);
            $this->display('Coin/editCoinCategory');
        }
    }

    /**
     * 删除商品分类信息
     *
     * @return  void
     * @author  wl
     * @date    Oct 14, 2016
     **/
    public function delCoinCategory(){
        if(IS_AJAX){
            $cid =I('post.id');
            $res = D('Coin')->delCoinGoodsCategory($cid);
            if($res){
                $data=array('code'=>200,'msg'=>"删除成功",'data'=>'');
            } else {
                $data=array('code'=>101,'msg'=>"删除失败",'data'=>'');
            }
        } else {
            $data=array('code'=>102,'msg'=>"删除失败",'data'=>'');
        }
        action_log('del_coin_category', 'coingoods_category', $cid, $this->getLoginUserId());
        $this->ajaxReturn($data,'JSON');
    }

    /**
    * 设置商品分类的排序状态
    *
    * @return  void
    * @author  wl
    * @date    Oct 14, 2016
    **/
    public function setCoinCateOrder () {
        if (!IS_AJAX || !IS_POST) {
            $msg = '参数错误';
            $data = '';
            $this->ajaxReturn(array('code' => 101, 'msg' => $msg, 'data' => $data), 'JSON');
        }

        $post = I('post.');
        $update_ok = D('Coin')->updateCoinCateOrder($post);
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
        action_log('set_coincate_order', 'coingoods_category', $post['id'], $this->getLoginUserId());
        $this->ajaxReturn(array('code' => $code, 'msg' => $msg, 'data' => $data), 'JSON');
    }


// 3、金币商城规则管理

    /**
     * 金币商城规则管理列表的展示
     *
     * @return  void
     * @author  wl
     * @date    Oct 17, 2016
     **/
    public function coinRule () {
        $coinrulelist = D('Coin')->getCoinRuleList();
        $this->assign('count', $coinrulelist['count']);
        $this->assign('page', $coinrulelist['page']);
        $this->assign('coinrulelist', $coinrulelist['coinrulelist']);
        $this->display('Coin/coinRule');
    } 

    /**
     * 搜索金币规则信息
     *
     * @return  void
     * @author  wl
     * @date    Oct 17, 2016
     **/
    public function searchCoinRule () {
        $param = I('param.');
        $s_keyword = trim((string)$param['s_keyword']);
        if ( $s_keyword == '' ) {
            $this->redirect('Coin/coinRule');
        } else {
            $coinrulelist = D('Coin')->searchCoinRule($param);
            $this->assign('s_keyword', $s_keyword);
            $this->assign('page', $coinrulelist['page']);
            $this->assign('count', $coinrulelist['count']);
            $this->assign('coinrulelist', $coinrulelist['coinrulelist']);
            $this->display('Coin/coinRule');
        }
    }
    /**
     * 添加金币规则信息
     *
     * @return  void
     * @author  wl
     * @date    Oct 17, 2016
     **/
    public function addCoinRule () {
        if (IS_POST) {
            $post = I('post.');
            $data['title'] = $post['title'] ? $post['title'] : '';
            $data['slug'] = $post['slug'] ? $post['slug'] : '';
            $data['coin_num'] = intval($post['coin_num']) > 0 ? intval($post['coin_num']) : 0;
            $data['rule_starttime'] = strtotime($post['rule_starttime']) ? strtotime($post['rule_starttime']) : '';
            $data['rule_endtime'] = strtotime($post['rule_endtime']) ? strtotime($post['rule_endtime']) : '';
            $data['description'] = $post['description'] ? $post['description'] : '';
            $data['addtime'] = time();

            if ($data['title'] == '' && $data['slug'] == '' && $data['rule_starttime'] == '' && $data['rule_endtime'] == '' && $data['description'] == '') {
                $this->error('请先完善信息', U('Coin/addCoinRule'));
            }

            $coinRule = D('coin_rule');
            if ($res = $coinRule->create($data)) {
                $result = $coinRule->add($res);
                if ($result) {
                    action_log('add_coin_rule', 'coin_rule', $result, $this->getLoginUserId());
                    $this->success('添加成功', U('Coin/coinRule'));
                } else {
                    action_log('add_coin_rule', 'coin_rule', $result, $this->getLoginUserId());
                    $this->error('添加失败', U('Coin/addCoinRule'));
                }
            } else {
                action_log('add_coin_rule', 'coin_rule', $result, $this->getLoginUserId());
                $this->error('添加失败', U('Coin/addCoinRule'));
            }
        } else {
            $this->display('Coin/addCoinRule');
        }
    }

    /**
     * 编辑金币规则信息
     *
     * @return  void
     * @author  wl
     * @date    Oct 17, 2016
     **/
    public function editCoinRule () {
        $id = I('param.id');
        $coinrulelist = D('Coin')->getCoinRuleListById($id);
        if (IS_POST) {
            $post = I('post.');
            $data['title'] = $post['title'] == '' ? $coinrulelist['title'] : $post['title'];
            $data['slug'] = $post['slug'] == '' ? $coinrulelist['slug'] : $post['slug'];
            $data['coin_num'] = intval($post['coin_num']) > 0 ? intval($post['coin_num']) : $coinrulelist['coin_num'];
            $data['rule_starttime'] = strtotime($post['rule_starttime']) == '' ? strtotime($coinrulelist['rule_starttime']) : strtotime($post['rule_starttime']);
            $data['rule_endtime'] = strtotime($post['rule_endtime']) == '' ? strtotime($coinrulelist['rule_endtime']) : strtotime($post['rule_endtime']);
            $data['description'] = $post['description'] == '' ? $coinrulelist['description'] : $post['description'];
            $data['addtime'] = time();
            $coinRule = D('coin_rule');
            if ($res = $coinRule->create($data)) {
                $result = $coinRule->where(array('id' => $id))->save($res);
                if ($result) {
                    action_log('edit_coin_rule', 'coin_rule', $id, $this->getLoginUserId());
                    $this->success('编辑成功', U('Coin/coinRule'));
                } else {
                    action_log('edit_coin_rule', 'coin_rule', $id, $this->getLoginUserId());
                    $this->error('未做任何修改', U('Coin/editCoinRule'));
                }
            } else {
                action_log('edit_coin_rule', 'coin_rule', $id, $this->getLoginUserId());
                $this->error('未做任何修改', U('Coin/editCoinRule'));
            }
        } else {
            $this->assign('coinrulelist', $coinrulelist);
            $this->display('Coin/editCoinRule');
        }

    }

    /**
     * 金币商城规则管理列表的删除功能
     *
     * @return  void
     * @author  wl
     * @date    Oct 17, 2016
     **/
    public function delCoinRule () {
        if(IS_AJAX){
            $cid =I('post.id');
            $res = D('Coin')->delCoinRule($cid);
            if($res){
                $data=array('code'=>200,'msg'=>"删除成功",'data'=>'');
            } else {
                $data=array('code'=>101,'msg'=>"删除失败",'data'=>'');
            }
        } else {
            $data=array('code'=>102,'msg'=>"删除失败",'data'=>'');
        }
        action_log('del_coin_rule', 'coin_rule', $cid, $this->getLoginUserId());
        $this->ajaxReturn($data,'JSON');
    }

// 4、金币兑换记录管理

    /**
     * 金币兑换记录管理列表展示
     *
     * @return  void
     * @author  wl
     * @date    Oct 17, 2016
     **/
    public function exchangeOrders () {
        $exchangeOrders = D('Coin')->getExchangeOrders();
        $this->assign('count', $exchangeOrders['count']);
        $this->assign('page', $exchangeOrders['page']);
        $this->assign('exchangeorders', $exchangeOrders['exchangeorder']);
        $this->display('Coin/exchangeOrders');
    }

    /**
    * 金币商城兑换记录管理中的搜索功能
    *
    * @return  void
    * @author  wl
    * @date    Oct 17, 2016
    **/
    public function searchExchangeOrders () {
        $param = I('param.');
        $search_info = trim((string)$param['search_info']);
        $s_keyword = trim((string)$param['s_keyword']);
        $pay_status = (int)$param['pay_status'];
        $exchange_status = (int)$param['exchange_status'];
        if ( $s_keyword == '' && $search_info == '' && $pay_status == '' && $exchange_status == '') {
            $this->redirect('Coin/exchangeOrders');
        } else {
            $exchangeOrders = D('Coin')->searchExchangeOrders($param);
            $this->assign('s_keyword', $s_keyword);
            $this->assign('search_info', $search_info);
            $this->assign('pay_status', $pay_status);
            $this->assign('exchange_status', $exchange_status);
            $this->assign('count', $exchangeOrders['count']);
            $this->assign('page', $exchangeOrders['page']);
            $this->assign('exchangeorders', $exchangeOrders['exchangeorder']);
            $this->display('Coin/exchangeOrders');
        }
    }

    /**
     * 金币商城兑换记录管理中的删除功能
     *
     * @return  void
     * @author  wl
     * @date    Oct 17, 2016
     **/
    public function delExchangeOrders () {
        if(IS_AJAX){
            $cid =I('post.id');
            $res = D('Coin')->delExchangeOrders($cid);
            if($res){
                $data=array('code'=>200,'msg'=>"删除成功",'data'=>'');
            } else {
                $data=array('code'=>101,'msg'=>"删除失败",'data'=>'');
            }
        } else {
            $data=array('code'=>102,'msg'=>"删除失败",'data'=>'');
        }
        action_log('del_exchange_order', 'exchange_orders', $cid, $this->getLoginUserId());
        $this->ajaxReturn($data,'JSON');
    }

    // 生成唯一兑换码 
    public function createcode () {
        $s_zhifu_dm = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
        $data = array('code'=>-1, 'data'=>$s_zhifu_dm);
        $this->ajaxReturn($data, 'JSON'); 
    }

// 5、商品轮播图管理模块

    /**
     * 轮播图展示列表和添加轮播图
     *
     * @return  void
     * @author  wl
     * @date    Oct 18, 2016
     **/
    public function goodsBannerAdmin () {
        $school_id = $this->getLoginauth();
        $goodslist = D('Coin')->getGoodsNameList();
        $this->assign('goodslists', $goodslist);
        $this->display('Coin/goodsBannerAdmin');
    }
   
    /**
    * 对应的商品图片的展示
    *
    * @return  void
    * @author  wl
    * @date    Oct 18, 2016
    **/
    public function showBanner () {
        $goods_id = $_POST['goods_id'];
        if($goods_id){
            $bannerlist = D('Coin')->getBannerList($goods_id);
        }
        if (is_array($bannerlist)) {
            $data = array('code' => 200, 'data' => $bannerlist);
        } else {
            $data = array('code' => 200, 'data' => array());
        }
        exit(json_encode($data));
    }

    /**
    * 对应的商品图片的展示
    *
    * @return  void
    * @author  wl
    * @date    Oct 18, 2016
    **/
    public function addBanner () {
        $school_id = $this->getLoginauth();
        if (IS_POST) {
            $goods_id = I('post.goods_id');
            if (!empty($_FILES)) {
                $getBannerUrl = D('Coin')->getBannerUrl($goods_id, $_FILES['goods_img_banner']);
                $bannerlist = D('Coin')->getBannerList($goods_id);
                if (count($getBannerUrl) + count($bannerlist) > 5) {
                    $this->error('最多添加5张图片', U('Coin/goodsBannerAdmin'));
                }
            } else {
                $this->error('请上传图片', U('Coin/goodsBannerAdmin'));
                exit();
            }

            $result = D('Coin')->saveBanner($getBannerUrl,$goods_id);
            if ($result) {
                action_log('add_coingoods_banner', 'coin_goods', $result, $this->getLoginUserId());
                $this->success('添加成功', U('Coin/goodsBannerAdmin'));
            } else {
                action_log('add_coingoods_banner', 'coin_goods', $result, $this->getLoginUserId());
                $this->error('添加失败', U('Coin/goodsBannerAdmin'));
            }

        } else {
            $goodslist = D('Coin')->getGoodsNameList();
            $this->assign('goodslists', $goodslist);
            $this->display('Coin/goodsBannerAdmin');
        }

    } 


    /**
    * 删除商品图片
    *
    * @return  void
    * @author  wl
    * @date    Oct 18, 2016
    **/
    public function delBanner () {
        if (IS_AJAX) {
            $goods_id = $_POST['goods_id'];
            $url = I('post.url');
            $result = D('Coin')->delBanner($url, $goods_id);
            if ($result) {
                action_log('del_coingoods_banner', 'coin_goods', $goods_id, $this->getLoginUserId());
                echo '1';
                exit;
            } else {
                action_log('del_coingoods_banner', 'coin_goods', $goods_id, $this->getLoginUserId());
                echo '2';
                exit;
            }    
        } else {
            action_log('del_coingoods_banner', 'coin_goods', $goods_id, $this->getLoginUserId());
            echo '2';
            exit;
        }   
    }




}

