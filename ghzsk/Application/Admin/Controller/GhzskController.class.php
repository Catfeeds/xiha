<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Page;
class GhzskController extends BaseController {
    //构造函数
    public function _initialize() {    
        if(!session('loginauth')) {       //如果不存在用户登录session
                $this->redirect('Public/login');  //没有权限，重定向至登录页面
                exit();
        }
    }
    /**
     * 获得知识库管理列表
     *
     * @return void
     * @author  wl
     * @date    August 15, 2016
     **/
    public function index () {
        $group_id = $this->getGroupId();
        $user_id = $this->getLoginUserId();
        $author_list = D('Ghzsk')->getAuthorList();
        $categoryList = D('Ghzsk')->getCategoryList();
        $this->assign('category', $categoryList);
        $this->assign('group_id', $group_id);
        $this->assign('user_id', $user_id);
        $this->assign('page', $author_list['page']);
        $this->assign('count', $author_list['count']);
        $this->assign('author_list', $author_list['list']);
        $this->display();
    }
    

    /**
     * 添加搜索功能
     *
     * @return  void
     * @author  wl
     * @date    August 15, 2016
     **/
    public function searchZsk () {
        $group_id   = $this->getGroupId();
        $user_id    = $this->getLoginUserId();
        $categoryList = D('Ghzsk')->getCategoryList();
        $param      = I('param.');
        $search_type= $param['search_type'];
        $s_keyword = $param['s_keyword'];
        $search_cate = $param['search_cate'];
        if (trim($s_keyword) == '' && trim($search_cate) == '') {
            $this->redirect('Ghzsk/index');
            exit;
        }
        $author_list  = D('Ghzsk')->searchZsk($param);
        $this->assign('s_keyword', $s_keyword);
        $this->assign('search_type', $search_type);
        $this->assign('search_cate', $search_cate);
        $this->assign('category', $categoryList);
        $this->assign('group_id', $group_id);
        $this->assign('user_id', $user_id);
        $this->assign('page', $author_list['page']);
        $this->assign('count', $author_list['count']);
        $this->assign('author_list', $author_list['list']);
        $this->display('Ghzsk/index');

    }

    /**
     * 在知识库列表中添加相关知识
     *
     * @return  void
     * @author  wl
     * @date    August 15, 2016
     **/
    public function addZsk () {
        $user_id = $this->getLoginUserId();
        $user_name = $this->getLoginName();
        $categoryList = D('Ghzsk')->getCategoryList();
        if (IS_POST) {
            $post = I('post.');
            $data['author']     = $user_name ? $post['name'] : $user_name;
            $data['title']      = $post['title'] ? $post['title'] : '';
            $data['cate_id']    = $post['cate'] ? $post['cate'] : '';
            $data['system_type']= $post['type'] ? $post['type'] : 1;
            $data['content']    = $post['method'] ? $post['method'] : '';
            $data['author_id']  = $user_id;
            // $data['short_desc'] = $post['description'] ? $post['description'] : '';
            $data['created']    = time();
            if (!empty($_FILES)) {
                if ($_FILES['doctext']['error'] == 0) {
                    $attachment = $this->upload('doctext','Ghzsk/zsk/'.$user_id.'/','zsk_','10485760','../upload/', array('jpg', 'jpeg', 'png', 'gif', 'txt', 'doc', 'pdf'));//10485760 ->10M
                    $data['attachment']         = $attachment['path'];
                } else {
                    $data['attachment'] = '';
                }
            }
            $article = D('post');
            if ($res = $article->create($data)) {
                $result = $article->add($res);
                if ($result) {
                  $this->success('添加成功！', U('Ghzsk/index'));
                  exit;
                } else {
                  $this->error('添加失败！', U('Ghzsk/addZsk'));
                  exit;
                }
            }
        }
        $this->assign('user_id', $user_id);
        $this->assign('user_name', $user_name);
        $this->assign('category', $categoryList);
        $this->display();
    }

    /**
     * 在知识库列表中编辑相关知识
     *
     * @return  void
     * @author  wl
     * @date    August 15, 2016
     **/
    public function editZsk () {
        $user_id = $this->getLoginUserId();
        $user_name = $this->getLoginName();
        $categoryList = D('Ghzsk')->getCategoryList();
        $id = I('param.id');
        $author_list = D('Ghzsk')->getZskById($id);
        if (IS_POST) {
            $post               = I('post.');
            $data['id']         = $author_list['id'];
            $data['author']     = $user_name ? $post['name'] : $user_name;
            $data['title']      = $post['title'] ? $post['title'] : '';
            $data['cate_id']    = $post['cate'] ? $post['cate'] : '';
            $data['system_type']= $post['type'] ? $post['type'] : 1;
            $data['content']    = $post['method'] ? $post['method'] : '';
            $data['author_id']  = $user_id;
            // $data['short_desc'] = $post['description'] ? $post['description'] : '';
            $data['modified']    = time();
            if (!empty($_FILES)) {
                if ($_FILES['doctext']['error'] == 0) {
                    $attachment = $this->upload('doctext','Ghzsk/zsk/'.$id.'/','zsk_','10485760','../upload/');//10485760 ->10M
                    $data['attachment'] = $attachment['path'];
                } else {
                    $data['attachment'] = $author_list['attachment'] ? $author_list['attachment'] : '' ;
                }
            }
            $article = D('post');
            if ($res = $article->create($data)) {
                $result = $article->where(array('id' => $id))->save($res);
                if ($result) {
                  $this->success('修改成功！', U('Ghzsk/index'));
                  exit;
                } else {
                  $this->error('修改失败！', U('Ghzsk/editZsk'));
                  exit;
                }
            }
        }
        $this->assign('user_id', $user_id);
        $this->assign('user_name', $user_name);
        $this->assign('author_list', $author_list);
        $this->assign('category', $categoryList);
        $this->display();
    }
    /**
     * 通过点击连接获得解决方法
     *
     * @return  void
     * @author  wl
     * @date    August 15, 2016
     **/
    public function show () {
        $id = I('param.id');
        $author_list = D('Ghzsk')->getZskById($id);
        $content = htmlspecialchars_decode($author_list['content']);
        $this->assign('content', $content);
        $this->display('Ghzsk/show');
    }

    /**
     * 删除用户
     *
     * @return  void
     * @author  wl
     * @date    August 15, 2016
     **/
    public function delZsk () {
        if (IS_AJAX) {
            $id = I('post.id');
            $result = D('Ghzsk')->delZsk($id);
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