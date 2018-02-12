<?php 
namespace Admin\Model;
use Think\Model;
use Think\Page;
/**
 * 车辆管理模型类
 *
 * @return 
 * @author 
 **/
class AppModel extends BaseModel {
    public $tableName = 'app_version';
// 1.App版本管理部分
    /**
     * 获得app版本信息
     *
     * @return 	void
     * @author 	wl
     * @date 	August 11, 2016
     **/
    public function getAppList () {
        $count = $this->fetchSql(false)->count();
        $Page = new Page($count, 10);
        $page = $this->getPage($count, 10);
        $applists = array();
        $applist = $this->table(C('DB_PREFIX').'app_version')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order('id DESC')
            ->fetchSql(false)
            ->select();
        if ($applist) {
            foreach($applist as $key => $value){

                if ($value['addtime'] != 0) {
                    $applist[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $applist[$key]['addtime'] = '--';
                }

                if ($value['app_download_url']) {
                    $applist[$key]['app_download_url'] = C('HTTP_MHOST').$value['app_download_url'];
                } else {
                    $applist[$key]['app_download_url'] = '';
                }
            }
        }
        $applists = array('applist' => $applist, 'page' => $page, 'count' => $count);
        return $applists;
    }
    /**
     * 根据条件搜索版本信息
     *
     * @return 	void
     * @author 	wl
     * @date 	August 11, 2016
     **/
    public function searchAppInfo ($param) {
        $map = array();
        $keyword = '%'.$param['s_keyword'].'%';
        
        if ($param['s_keyword'] != '') {
            $map['app_name'] = array('like', $keyword);
        } 

        if ($param['os_type'] != 0) {
            $map['os_type'] = array('EQ', $param['os_type']);
        }

        if ($param['app_client'] != 0) {
            $map['app_client'] = array('EQ', $param['app_client']);
        }
        
        $count = $this->where($map)
            ->fetchSql(false)
            ->count();
        $Page = new Page($count, 10, $param);
        $page = $this->getPage($count, 10, $param);
        $applists = array();
        $applist = $this->table(C('DB_PREFIX').'app_version')
            ->where($map)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order('id DESC')
            ->fetchSql(false)
            ->select();
        if ($applist) {
            foreach($applist as $key => $value){

                if ($value['addtime'] != 0) {
                    $applist[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $applist[$key]['addtime'] = '--';
                }

                if ($value['app_download_url']) {
                    $applist[$key]['app_download_url'] = C('HTTP_MHOST').$value['app_download_url'];
                } else {
                    $applist[$key]['app_download_url'] = '';
                }
            }
        }
        $applists = array('applist' => $applist, 'page' => $page, 'count' => $count);
        return $applists;
    }

    /**
     * 删除版本信息
     *
     * @return 	void
     * @author 	wl
     * @date	August 11, 2016
     **/
    public function delApp ($aid) {
        if (!is_numeric($aid)) {
            return false;
        }
        $result = $this->where('id = :aid')
            ->bind(['aid' => $aid])
            ->fetchSql(false)
            ->delete();
        return $result;
    }

    /**
     * 设置app升级的强制状态
     *
     * @return 	void
     * @author 	wl
     * @date	Dec 28, 2016
     **/
    public function setForceStatus ($id, $status) {
        if (!is_numeric($id) && !is_numeric($status)) {
            return false;
        }
        $list = array();
        $data = array('is_force' => $status);
        $result = M('app_version')
            ->where(array('id' => $id))
            ->data($data)
            ->fetchSql(false)
            ->save();
        $list['res'] = $result;
        $list['id'] = $id;
        return $list;
    }

    /**
     * 根据id获得版本单条信息
     *
     * @return 	void
     * @author 	wl
     * @date    August 12, 2016
     **/
    public function getAppListById ($id) {
        if (!is_numeric($id)) {
            return false;
        }
        $applist = $this->where('id = :pid')
            ->bind(['pid' => $id])
            ->find();
        return $applist;
    }


// 2.App反馈管理
    /**
     * 获取app
     *
     * @return 	void
     * @author 	wl
     * @date	Oct 25, 2016
     **/
    public function getAppfeedBacklist () {
        $count = $this->table(C('DB_PREFIX').'feedback f')
            ->count();
        $Page = new Page($count, 10);
        $page = $this->getPage($count, 10);
        $appfeedbacklists = array();
        $appfeedbacklist = $this->table(C('DB_PREFIX').'feedback f')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order('f.id DESC')
            ->fetchSql(false)
            ->select();
        if ($appfeedbacklist) {
            foreach ($appfeedbacklist as $key => $value) {
                if ($value['addtime'] != 0) {
                    $appfeedbacklist[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $appfeedbacklist[$key]['addtime'] = '';
                }

                if ($value['name'] != '') {
                    $appfeedbacklist[$key]['name'] = $value['name'];
                } else {
                    $appfeedbacklist[$key]['name'] = '--';
                }

                if ($value['user_type'] != '') {
                    $appfeedbacklist[$key]['user_type'] = $value['user_type'];
                } else {
                    $appfeedbacklist[$key]['user_type'] = 0;
                }
                
                if ($value['phone'] == '') {
                    $appfeedbacklist[$key]['phone'] = '--';
                }

                if ($value['content'] == '') {
                    $appfeedbacklist[$key]['content'] = '--';
                }
            }
        }
        $appfeedbacklists = array('appfeedbacklist' => $appfeedbacklist, 'count' => $count, 'page' => $page);
        return $appfeedbacklists;
    }

    /**
     * 获取app
     *
     * @return 	void
     * @author 	wl
     * @date	Oct 25, 2016
     **/
    public function searchAppFeedBack ($param) {
        $map = array();
        $complex = array();
        $s_keyword = '%'.$param['s_keyword'].'%';
        if ($param['search_info'] == '') {
            $complex['phone'] = array('LIKE', $s_keyword);
            $complex['name'] = array('LIKE', $s_keyword);
            $complex['f.content'] = array('LIKE', $s_keyword);
            $complex['_logic']	= 'OR';
        } else {
            if ($param['search_info'] == 'content') {
                $param['search_info'] = 'f.content';
            }
            $complex[$param['search_info']] = array('like', $s_keyword);
        }
        $map['_complex'] = $complex;
        $count = $this->table(C('DB_PREFIX').'feedback f')
            ->where($map)
            ->count();
        $Page = new Page($count, 10, $param);
        $page = $this->getPage($count, 10, $param);
        $appfeedbacklists = array();
        $appfeedbacklist = $this->table(C('DB_PREFIX').'feedback f')
            ->where($map)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order('f.id DESC')
            ->fetchSql(false)
            ->select();
        if ($appfeedbacklist) {
            foreach ($appfeedbacklist as $key => $value) {
                if ($value['addtime'] != 0) {
                    $appfeedbacklist[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $appfeedbacklist[$key]['addtime'] = '';
                }

                if ($value['name'] != '') {
                    $appfeedbacklist[$key]['name'] = $value['name'];
                } else {
                    $appfeedbacklist[$key]['name'] = '--';
                }

                if ($value['user_type'] != '') {
                    $appfeedbacklist[$key]['user_type'] = $value['user_type'];
                } else {
                    $appfeedbacklist[$key]['user_type'] = 0;
                }
                
                if ($value['phone'] == '') {
                    $appfeedbacklist[$key]['phone'] = '--';
                }
                
                if ($value['content'] == '') {
                    $appfeedbacklist[$key]['content'] = '--';
                }
            }
        }
        $appfeedbacklists = array('appfeedbacklist' => $appfeedbacklist, 'count' => $count, 'page' => $page);
        return $appfeedbacklists;
    }

    /**
     * 删除反馈
     *
     * @return 	void
     * @author 	wl
     * @date 	Oct 25, 2016
     **/
    public function delAppFeedBack ($id) {
        if (!is_numeric($id)) {
            return false;
        }

        $result = M('feedback')->where(array('id' => $id))
            ->fetchSql(false)
            ->delete();
        return $result;
    }
    
}
?>