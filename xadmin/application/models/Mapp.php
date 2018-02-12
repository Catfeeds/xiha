<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mapp extends CI_Model {

    public $app_version_tbl = 'cs_app_version';
    public $feedback_tbl = 'cs_feedback';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

// 1、app上线记录
    /**
     * 获取App上线记录的页数信息
     *
     * @return void
     **/
    public function getAppPageNum($param, $limit) 
    {
        $map = [];
        $app_name = '';
        if ($param) {
            if ($param['ostype'] != '') {
                $map['os_type'] = $param['ostype'];
            }

            if ($param['apptype'] != '') {
                $map['app_client'] = $param['apptype'];
            }

            if ($param['force'] !== '') {
                $map['is_force'] = $param['force'];
            }

            if ($param['keywords'] != '') {
                $app_name = $param['keywords'];
            }
        }

        $count = $this->db
            ->where($map)
            ->like('app_name', $app_name)
            ->count_all_results($this->app_version_tbl);
        
        $pagenum = (int)ceil( $count / $limit);

        $page_info = [
            'pagenum' => $pagenum,
            'count' => $count
        ];

        return $page_info;
    }



    /**
     * 获取app上线记录列表
     *
     * @return void
     **/
    public function getAppRecordsList($param, $start, $limit) 
    {
        $map = [];
        $app_name = '';
        if ($param) {
            if ($param['ostype'] != '') {
                $map['os_type'] = $param['ostype'];
            }

            if ($param['apptype'] != '') {
                $map['app_client'] = $param['apptype'];
            }

            if ($param['force'] !== '') {
                $map['is_force'] = $param['force'];
            }

            if ($param['keywords'] != '') {
                $app_name = $param['keywords'];
            }
        }

        $count = $this->db
            ->where($map)
            ->like('app_name', $app_name)
            ->count_all_results($this->app_version_tbl);

        $pagenum = (int)ceil( $count / $limit);
        $apprecordslist = $this->db
            ->from($this->app_version_tbl)
            ->where($map)
            ->like('app_name', $app_name)
            ->order_by('id', 'DESC')
            ->limit($limit, $start)
            ->get()
            ->result_array();
        if ( ! empty($apprecordslist)) {
            foreach ($apprecordslist as $index => $value) {
                if ($value['app_name'] == '') {
                    $apprecordslist[$index]['app_name'] = '--';
                }
                // handle addtime
                if ( $value['addtime'] != '' 
                    && $value['addtime'] != 0) 
                {
                    $apprecordslist[$index]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);                    
                } else {
                    $apprecordslist[$index]['addtime'] = '--';                    
                }

                // handle upload path
                $app_download_url = $this->mbase->buildUrl($value['app_download_url']);
                $apprecordslist[$index]['app_download_url'] = $app_download_url;

            }
        }

        $app_records_list = [
            'list' => $apprecordslist,
            'page' => $pagenum,
            'count' => $count,
        ];

        return $app_records_list;

    }

    /**
     * 新增app上线记录
     * @param data
     * 
     * @return void
     **/
    public function addAppOnlineRecords ($data) 
    {
        $this->db->insert($this->app_version_tbl, $data);
        return $this->db->insert_id();
    }

    /**
     * 删除app上线记录
     * @param id
     * @return void
     **/
    public function delAppRecords ( array $id)
    {
        $result = $this->db
            ->where_in('id', $id)
            ->delete($this->app_version_tbl);

        return $result;
    }
    
    /**
     * 获取单条记录
     * @param id 
     *
     * @return void
     **/
    public function getAppListById($id)
    {
        $query = $this->db
            ->get_where($this->app_version_tbl, ['id'=>$id]);
        $app_list = $query->row_array();
        if ($app_list) {
            $app_list['app_download_url'] = $this->mbase->buildUrl($app_list['app_download_url']);
        }
        return $app_list;
    }

// 2、app反馈列表
    /**
     * 获取反馈列表的页数
     * @param   $param
     * @param   $limit
     * @return void
     **/
    public function getFeedBackNum($param, $limit)
    {
        $map = [];
        $complex = [];
        $keywords = '';
        if ($param) {
            if ($param['utype'] != '') {
                $map['user_type'] = $param['utype'];
            }

            if ($param['solved'] != '') {
                $map['is_solved'] = $param['solved'];
            }

            // if ($param['keywords'] != '') {
                $keywords = $param['keywords'];
                $complex['name'] = $keywords;
                $complex['phone'] = $keywords;
            // }
        }

        $count = $this->db
            ->from($this->feedback_tbl)
            ->where($map)
            ->group_start()
                ->or_like($complex)
            ->group_end()
            ->count_all_results();
        $pagenum = (int) ceil( $count / $limit );

        $page_info = [
            'pagenum'   => $pagenum,
            'count'     => $count
        ];

        return $page_info;

    }

    /**
     * 获取反馈列表
     * @param  $param
     * @param  $start
     * @param  $limit
     * @return void
     **/
    public function getFeedBackList($param, $start, $limit)
    {
        $map = [];
        $keywords = '';
        $complex = [];
        if ($param) {
            if ($param['utype'] != '') {
                $map['user_type'] = $param['utype'];
            }

            if ($param['solved'] != '') {
                $map['is_solved'] = $param['solved'];
            }

            // if ($param['keywords'] != '') {
                $keywords = $param['keywords'];
                $complex['name'] = $keywords;
                $complex['phone'] = $keywords;
            // }
        }

        $count = $this->db
            ->where($map)
            ->group_start()
                ->or_like($complex)
            ->group_end()
            ->count_all_results($this->feedback_tbl);
        $pagenum = (int) ceil( $count / $limit );

        $feedbacklist = $this->db
            ->from($this->feedback_tbl)
            ->where($map)
            ->group_start()
                ->or_like($complex)
            ->group_end()
            ->order_by('id', 'desc')
            ->limit($limit, $start)
            ->get()
            ->result_array();
        if ( ! empty($feedbacklist)) {
            foreach ($feedbacklist as $index => $value) {
                if ($value['addtime'] != '' 
                    && $value['addtime'] != 0) {
                        $feedbacklist[$index]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $feedbacklist[$index]['addtime'] = '--';
                }

                if ($value['name'] == '') {
                    $feedbacklist[$index]['name'] = '--';
                }

                if ($value['phone'] == '') {
                    $feedbacklist[$index]['phone'] = '--';
                }

                if ($value['content'] == '') {
                    $feedbacklist[$index]['content'] = '--';
                }
               
            }
        }

        $feedback_list = [
            'list' => $feedbacklist,
            'pagenum' => $pagenum,
            'count' => $count,
        ];

        return $feedback_list;

    }


    /**
     * 更新数据
     * @param $data
     * @param $tblname
     *
     * @return void
     **/
    public function updateData ($data, $tblname)
    {   
        $where = ['id' => $data['id']];
        $update_ok = $this->db
            ->where($where)
            ->update($tblname, $data);
        return $update_ok;
    }

    /**
     * 删除app反馈列表
     * @param id
     * @return void
     **/
     public function delFeedBack ( array $id)
     {
         $result = $this->db
             ->where_in('id', $id)
             ->delete($this->feedback_tbl);
 
         return $result;
     }



}





