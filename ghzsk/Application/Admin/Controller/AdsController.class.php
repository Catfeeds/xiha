<?php 
namespace Admin\Controller;
use Think\Controller;

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

	//广告位管理
	public function adsPosition() {
		$res = D('AdsPosition')->getAdsPositions();
		$ads_positions = $res[0];
		$count = $res[1];
		$this->assign('count', $count);
		$this->assign('ads_positions', $ads_positions);
		$this->display();
	}

	//广告管理
	public function adsManage() {
		$publisher_id =  $this->getLoginauth();//获取当前登录者id
		$res = D('Ads')->getAds($publisher_id);
		$ads_info = $res[0];
		$count = $res[1];
		$page = $res[2];
		$this->assign('count', $count);
		$this->assign('page', $page);
		$this->assign('ads_info', $ads_info);
		$this->display();
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
				$this->ajaxReturn($data, 'JSON');	
			} else {
				$data = array('code'=>400, 'data'=>'', 'msg'=>"添加失败");
				$this->ajaxReturn($data, 'JSON');	
			}
			
		} else{
			$this->display();
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
				$this->ajaxReturn($data, 'JSON');	
			} else {
				$data = array('code'=>400, 'data'=>'', 'msg'=>"编辑失败");
				$this->ajaxReturn($data, 'JSON');	
			}
		} else {
			$id = I('get.id');
			$ads_position = D('AdsPosition')->findPosition($id);
			$this->assign('ads_position', $ads_position);
			$this->display();
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
        $this->display();
    }

    public function addAdsLevel() {
        $this->display();
    }

    public function addAdsLevelDo() {
        if (!IS_POST) {
            $code = 101;
            $msg = '参数错误';
            $data = '';
            $this->ajaxReturn(array('code' => $code, 'data' => $data, 'msg' => $msg));
        }

        $add_ok = D('AdsLevel')->addOneRecord(I('post.'));

        if ( $add_ok ) {
            $code = 200;
            $msg = '添加成功';
            $data = '';
            $this->success('添加成功', U('Ads/adsLevel'), 2);
        } else {
            $code = 400;
            $msg = '添加失败';
            $data = '';
            $this->error('添加失败', U('Ads/adsLevel'), 3);
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
        if ( $update_ok == 101 || $update_ok == 102 || $update_ok == 105 || $update_ok == 107) {
            $code = $update_ok;
            $msg =  '参数错误';
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
        $this->ajaxReturn(array('code' => $code, 'data' => $data, 'msg' => $msg));
    }

    //删除广告
    public function delAds() {

    	$id = I('post.id');//广告id
    	$order_num = D('AdsOrder')->getAdsOrders($id);//判断广告订单是否存在
    	if ($order_num == false) {	//该广告没有订单，ads_status彻底失效3
    		$status = 3;
    	} else {	//该广告有订单，ads_status不招租2
    		$status = 2;
    	}
		$change_status = D('Ads')->changeAdsStatus($id, $status);
		if ($change_status) {
			$this->ajaxReturn(array('code' => 200, 'data' => '', 'msg' => '删除成功'));
		} else {
			$this->ajaxReturn(array('code' => 400, 'data' => '', 'msg' => '删除失败'));
		}
    	
    }
}

 ?>
