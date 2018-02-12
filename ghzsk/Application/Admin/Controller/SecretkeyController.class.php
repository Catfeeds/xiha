<?php  
namespace Admin\Controller;
use Think\Controller;
use Think\Page;
/**
* 车载注册模块
* @author  wl
* @date    August 16, 2016
**/
class SecretkeyController extends BaseController {
    public function _initialize() {
        if (!session('loginauth')) {
            $this->redirect('Public/login');
            exit;
        }
        $this->SecretkeyModel = D('Secretkey');
    }

    /**
     * 车载注册列表
     *
     * @return  void
     * @author  wl
     * @date    August 16, 2016
     **/
    public function index() {
        $group_id       = $this->getGroupId();
        $user_id        = $this->getLoginUserId();
        $secretkey_list = $this->SecretkeyModel->getSecretKeyList();
        $this->assign('group_id', $group_id);
        $this->assign('user_id', $user_id);
        $this->assign('secretkey_list', $secretkey_list['list']);
        $this->assign('page', $secretkey_list['page']);
        $this->assign('count', $secretkey_list['count']);
        $this->display();
    }

    /**
     * 搜索车载信息
     *
     * @return  void
     * @author  wl
     * @date    August 16, 2016
     **/
    public function searchSecretkey () {
        $param = I('param.');
        $school_name  = $param['school_name'];
        $day_number = $param['day_number'];
        if (trim($school_name) == '' && trim($day_number) == '') {
            $this->redirect('Secretkey/index');
            exit;
        }
        $secretkey_list  = $this->SecretkeyModel->searchSecretkey($param);
        $this->assign('school_name', $school_name);
        $this->assign('day_number', $day_number);
        $this->assign('page', $secretkey_list['page']);
        $this->assign('count', $secretkey_list['count']);
        $this->assign('secretkey_list', $secretkey_list['list']);
        $this->display('Secretkey/index');
    }

    /**
    * 添加车载信息
    *
    * @return  void
    * @author  wl
    * @date    August 16, 2016
    **/
    public function addSecretkey () {
        if (IS_POST) {
            $post = I('post.');
            $data['applicant']      = $post['applicant'] ? $post['applicant'] : ''; 
            $data['addtime']        = strtotime($post['addtime'], time()) ? strtotime($post['addtime'], time()) : ''; 
            $data['system_type']    = $post['system_type'] ? $post['system_type'] : 1; 
            $data['register_type']  = $post['register_type'] ? $post['register_type'] : 1; 
            $data['school_name']    = $post['school_name'] ? $post['school_name'] : ''; 
            $data['school_address'] = $post['school_address'] ? $post['school_address'] : ''; 
            $data['school_phone']   = $post['school_phone'] ? $post['school_phone'] : ''; 
            $data['machine_code']   = $post['machine_code'] ? $post['machine_code'] : ''; 
            $data['register_code']  = $post['register_code'] ? $post['register_code'] : ''; 
            $data['register_time']  = strtotime($post['register_time'], time()) ? strtotime($post['register_time'], time()) : ''; 
            $data['expire_time']    = strtotime($post['expire_time'], time()) ? strtotime($post['expire_time'], time()) : ''; 
            $secretkey = D('secretkey');
            if ($res = $secretkey->create($data)) {
                $result = $secretkey->add($res);
                if ($result) {
                  $this->success('添加成功！', U('Secretkey/index'));
                  exit;
                } else {
                  $this->error('添加失败！', U('Secretkey/addSecretkey'));
                  exit;
                }
            } else {
                $this->error('添加失败！', U('Secretkey/addSecretkey'));
                exit; 
            }
        }
        $this->display();
    }
    /**
     * 编辑车载信息
     *
     * @return  void
     * @author  wl
     * @date    August 16, 2016
     **/
    public function editSecretkey () {
        $id             = I('param.id');
        $secretkey_list = D('Secretkey')->getSecretkeyById($id);
        if (IS_POST) {
            $post = I('post.');
            $data['applicant']      = $post['applicant'] == '' ? $secretkey_list['applicant'] : $post['applicant']; 
            $data['addtime']        = strtotime($post['addtime'], time()) ? strtotime($post['addtime'], time()) : ''; 
            $data['system_type']    = $post['system_type'] == '' ? $secretkey_list['system_type'] : $post['system_type']; 
            $data['register_type']  = $post['register_type'] == '' ? $secretkey_list['register_type'] : $post['register_type']; 
            $data['school_name']    = $post['school_name'] ? $secretkey_list['school_name'] : $post['school_name']; 
            $data['school_address'] = $post['school_address'] ? $secretkey_list['school_address'] :$post['school_address']; 
            $data['school_phone']   = $post['school_phone'] ? $secretkey_list['school_phone'] : $post['school_phone']; 
            $data['machine_code']   = $post['machine_code'] ? $secretkey_list['machine_code'] : $post['machine_code']; 
            $data['register_code']  = $post['register_code'] ? $secretkey_list['register_code'] : $post['register_code']; 
            $data['register_time']  = strtotime($post['register_time'], time()) ? strtotime($post['register_time'], time()) : ''; 
            $data['expire_time']    = strtotime($post['expire_time'], time()) ? strtotime($post['expire_time'], time()) : ''; 
            $secretkey = D('secretkey');
            if ($res = $secretkey->create($data)) {
                $result = $secretkey->where(array('id' => $id))->fetchSql(false)->save($res);
                if ($result) {
                    $this->success('编辑成功！', U('Secretkey/index'));
                    exit;
                } else {
                    $this->error('编辑失败！', U('Secretkey/editSecretkey'));
                    exit;
                }
            } else {
                $this->error('编辑失败！', U('Secretkey/editSecretkey'));
                exit; 
            }
        }
        $this->assign('secretkey_list', $secretkey_list);
        $this->display('Secretkey/addSecretkey');
    }

    /**
     * 删除车载信息
     *
     * @return  void
     * @author  wl
     * @date    August 16, 2016
     **/
    public function delSecretkey () {
        if (IS_AJAX) {
            $id = I('post.id');
            $result = D('Secretkey')->delSecretkey($id);
            if ($result) {
                $data = array('code' => 200, 'msg' => '删除成功', 'data' =>$id);
            } else {
                $data = array('code' => 103, 'msg' => '删除失败', 'data' =>'');
            }
        } else {
            $data = array('code' => 103, 'msg' => '删除失败', 'data' =>'');
        }
        $this->ajaxReturn($data, 'JSON');
    }



   
}

?>
