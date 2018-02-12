<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mproduct extends CI_Model {

    public $client_version_tbl = 'mi_client_version';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('mbase');
    }

    /**
     * 获取页码信息
     * @param $param
     * @param $limit
     * @return void
     **/
    public function getPageNum($param, $limit)
    {
        $map = [];
        if ($param) {
            if ($param['keywords'] != '') {
                $map['client_name'] = $param['keywords'];
                $map['update_log'] = $param['keywords'];
            }
        }

        $count = $this->db
            ->where($map)
            ->or_like($map)
            ->count_all_results($this->client_version_tbl);
        
        $pagenum = (int)ceil( $count / $limit);

        $page_info = [
            'pagenum' => $pagenum,
            'count' => $count
        ];

        return $page_info;

    }

    /**
     * 获取产品更新数据
     * @param [$param | $start | $limit]
     * @return void
     **/
    public function getProuctList($param, $start, $limit)
    {
        $map = [];
        if ($param) {
            if ($param['keywords'] != '') {
                $map['client_name'] = $param['keywords'];
                $map['update_log'] = $param['keywords'];
            }
        }

        $list = $this->db
            ->from($this->client_version_tbl)
            ->or_like($map)
            ->limit($limit, $start)
            ->order_by('id', 'desc')
            ->get()
            ->result_array();
        if ( ! empty($list)) {
            foreach ($list as $key => $value) {
                if ($value['addtime'] != '' 
                    AND $value['addtime'] != 0 
                    AND $value['addtime'] != NULL) {
                    $list[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $list[$key]['addtime'] = '--';
                }

                if ($value['updatetime'] != '' 
                    AND $value['updatetime'] != 0 
                    AND $value['updatetime'] != NULL) {
                    $list[$key]['updatetime'] = date('Y-m-d H:i:s', $value['updatetime']);
                } else {
                    $list[$key]['updatetime'] = '--';
                }

                $list[$key]['download_url'] = $this->mbase->buildUrl($value['download_url']);
            }
        }

        $page_info = $this->getPageNum($param, $limit);
        $page_num = $page_info['pagenum'];
        $count = $page_info['count'];
        $data = [
            'pagenum' => $page_num,
            'count' => $count,
            'list' => $list,
        ];

        return $data;

    }

    /**
     * 获取单条广告信息
     * @param
     * @return void
     **/
    public function getProductById($id)
    {
        $list = $this->db 
            ->from($this->client_version_tbl)
            ->where('id', $id)
            ->get()
            ->result_array();
        $client_list = [];
        if ( ! empty($list)) {
            foreach ($list as $key => $value) {
                $client_list = $value;
            }
        }
        return $client_list;
    }

    /**
     * 新增数据
     * @param $data | $tblname
     * @return void
     */
    public function add($data, $tblname)
    {
        $this->db->insert( $tblname, $data);
        return $this->db->insert_id();
    }

    /**
     * 修改数据
     * @param $data | $tblname
     * @return void
     */
    public function edit($field, $value, $data, $tblname)
    {
        $where = [$field => $value];
        $update_ok = $this->db
            ->where($where)
            ->update($tblname, $data);
        return $update_ok;
    }

    /**
     * 删除客户端上线记录
     * @param id
     * @return void
     **/
    public function del ($id, $tblname)
    {
        if ( ! is_array($id)) {
            $id = explode(',', $id);
        }
        
        $result = $this->db
            ->where_in('id', $id)
            ->delete($tblname);
        return $result;
    }


}