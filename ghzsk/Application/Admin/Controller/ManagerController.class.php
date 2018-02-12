<?php  
namespace Admin\Controller;
use Think\Controller;
use Think\Page;
/**
 * 管理员模块
 * @author wl
 **/
class ManagerController extends BaseController {
    public $ManagerModel;
    public function _initialize() {
        if (!session('loginauth')) {
            $this->redirect('Public/login');
            exit;
        }
        $this->ManagerModel = D('Manager');
    }
    /**
     * 管理员列表
     *
     * @return  void
     * @author  wl
     * @date    August 15, 2016
     **/
    public function index() {
        $group_id   = $this->getGroupId();
        $user_id    = $this->getLoginUserId();
        $user_list  = $this->ManagerModel->getUserList();
        $this->assign('userlist', $user_list['list']);
        $this->assign('page', $user_list['page']);
        $this->assign('count', $user_list['count']);
        $this->assign('group_id', $group_id);
        $this->assign('user_id', $user_id);
        $this->display();
    }

    /**
     * 搜索用户
     *
     * @return  void
     * @author  wl
     * @date    August 15, 2016
     **/
    public function searchMember () {
        $group_id   = $this->getGroupId();
        $user_id    = $this->getLoginUserId();
        $param = I('param.');
        $s_keyword = $param['s_keyword'];
        if (trim($s_keyword) == '') {
            $this->redirect('Manager/index');
            exit;
        }
        $user_list  = $this->ManagerModel->searchMember($param);
        $this->assign('s_keyword', $s_keyword);
        $this->assign('userlist', $user_list['list']);
        $this->assign('page', $user_list['page']);
        $this->assign('count', $user_list['count']);
        $this->assign('group_id', $group_id);
        $this->assign('user_id', $user_id);
        $this->display('Manager/index');
    }

    /**
     * 添加用户
     *
     * @return  void
     * @author  wl
     * @date    August 15, 2016
     **/
    public function addMember () {
        $group_id   = $this->getGroupId();
        $user_id    = $this->getLoginUserId();
        $group_list = D('Manager')->getGroupList($group_id);
        if (IS_POST) {
            $post = I('post.');
            $data['user_password']  = md5('ghgd2016'); 
            $data['user_account']   = $post['user_account'] ? $post['user_account'] : ''; 
            $data['user_name']      = $post['user_name'] ? $post['user_name'] : ''; 
            $data['user_phone']     = $post['user_phone'] ? $post['user_phone'] : ''; 
            $data['group_id']       = $post['group_id'] ? $post['group_id'] : 2; 
            $data['add_time']       = time(); 
            $article = D('user');
            if ($res = $article->create($data)) {
                $result = $article->add($res);
                if ($result) {
                  $this->success('添加成功！', U('Manager/index'));
                  exit;
                } else {
                  $this->error('添加失败！', U('Manager/addMember'));
                  exit;
                }
            }

        }
        $this->assign('group_list', $group_list['list']);
        $this->display();
    }

    /**
     * 编辑用户
     *
     * @return  void
     * @author  wl
     * @date    August 15, 2016
     **/
    public function editMember () {
        $group_id       = $this->getGroupId();
        $user_id        = $this->getLoginUserId();
        $user_name      = $this->getLoginName();
        $id             = I('param.id');
        $member_list    = D('Manager')->getMemberById($id);
        $group_list     = D('Manager')->getGroupList($group_id);
        if (IS_POST) {
            $post = I('post.');
            $data['id']             = $post['id'];
            $data['user_password']  = $post['user_password'] =='' ? $member_list['user_password'] : md5($post['user_password']); 
            $data['user_account']   = $post['user_account'] ? $post['user_account'] : ''; 
            $data['user_name']      = $post['user_name'] ? $post['user_name'] : $member_list['user_name']; 
            $data['user_phone']     = $post['user_phone'] ? $post['user_phone'] : $member_list['user_phone']; 
            $data['group_id']       = $post['group_id'] ? $post['group_id'] : 2; 
            $data['add_time']       = time(); 
            $article = D('user');
            if ($res = $article->create($data)) {
                $result = $article->where(array('id' => $id))->save($res);
                if ($result) {
                  $this->success('修改成功成功！', U('Manager/index'));
                  exit;
                } else {
                  $this->error('修改失败！', U('Manager/editMember'));
                  exit;
                }
            }
        }
        $this->assign('member_list', $member_list);
        $this->assign('group_list', $group_list['list']);
        $this->display();
    }


    /**
     * 删除用户
     *
     * @return  void
     * @author  wl
     * @date    August 15, 2016
     **/
    public function delMember () {
        if (IS_AJAX) {
            $id = I('post.id');
            $result = D('Manager')->delMember($id);
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
