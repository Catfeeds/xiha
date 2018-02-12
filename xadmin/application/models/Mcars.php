<?php
    // 首页
defined('BASEPATH') OR exit('No direct script access allowed');

class Mcars extends Mbase {


    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->cars_tbl = $this->db->dbprefix('cars');
        $this->cate_tbl = $this->db->dbprefix('car_category');
        $this->school_tbl = $this->db->dbprefix('school');
        $this->video_tbl = $this->db->dbprefix('video');
    }

    /**
     * 获取页码信息
     * @param $data
     * @param $limit
     * @param $type
     * @return void
     **/
    public function getCarsPageNum($school_id, $param, $limit, $type)
    {
        $map = [];
        if ( $type == 'category') { // 车型
            if ($param) {
                if ( $param['keywords'] != '') {
                    $keywords = $param['keywords'];
                    $map = [
                        'name' => $keywords,
                        'brand' => $keywords,
                        'subtype' => $keywords
                    ];
                }
            }

            $count = $this->db
                ->from($this->cate_tbl)
                ->or_like($map)
                ->count_all_results();

        } elseif ( $type == "coachcar" ) { // 教练车辆
            $complex = [];
            if ( $school_id > 0) {
                $map['school_id'] = $school_id;
            }
            if ($param) {
                if ( $param['ctype'] != '') {
                    $map['car_type'] = $param['ctype'];
                }

                if ( $param['keywords'] != '') {
                    $keywords = $param['keywords'];
                    $complex = [
                        'school.s_school_name' => $keywords,
                        'cars.name' => $keywords,
                        'cars.car_no' => $keywords
                    ];
                }
            }

            if ( ! empty($complex)) {
                $query = $this->db->from("{$this->cars_tbl} as cars")
                    ->join("{$this->school_tbl} as school", "school.l_school_id = cars.school_id", "LEFT")
                    ->where($map)
                    ->group_start()
                        ->or_like($complex)
                    ->group_end();
            } else {
                $query = $this->db->from("{$this->cars_tbl} as cars")
                    ->join("{$this->school_tbl} as school", "school.l_school_id = cars.school_id", "LEFT")
                    ->where($map);
            }

            $count = $query->count_all_results();

        } elseif ( $type == "video" ) {
            
            $complex = [];
            if ($param) {
                if ( $param['ctype'] != '') {
                    $map['car_type'] = $param['ctype'];
                }

                if ( $param['open'] !== '') {
                    $map['is_open'] = $param['open'];
                }

                if ( $param['course'] != '') {
                    $map['course'] = $param['course'];
                }

                $complex['title'] = $param['keywords'];
            }

            $count = $this->db->from("{$this->video_tbl} as video")
                ->where($map)
                ->group_start()
                    ->or_like($complex)
                ->group_end()
                ->count_all_results();

        }

        $pagenum = (int) ceil( $count / $limit );
        $page_info = [
            'pagenum' => $pagenum,
            'count' => $count,
        ];

        return $page_info;

    }

    /**
     * 获取车辆管理中的数据
     * @param $param
     * @param $start
     * @param $limit
     * @param $type
     * @return void
     **/
    public function getCarsInfo($school_id, $param, $start, $limit, $type)
    {
        $map = [];
        if ( $type == 'category') { // 车型
            if ($param) {
                if ( $param['keywords'] != '') {
                    $keywords = $param['keywords'];
                    $map = [
                        'name' => $keywords,
                        'brand' => $keywords,
                        'subtype' => $keywords
                    ];
                }
            }

            $list = $this->db
                ->from($this->cate_tbl)
                ->or_like($map)
                ->order_by('id', 'desc')
                ->limit($limit, $start)
                ->get()
                ->result_array();
            if ( ! empty($list)) {
                foreach ($list as $index => $value) {
                    if ( $value['addtime'] != ''
                        && $value['addtime'] != 0) {
                        $list[$index]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                    } else {
                        $list[$index]['addtime'] = '--';
                    }

                    $list[$index]['point_text_url'] = $this->mbase->buildUrl($value['point_text_url']);
                }
            }

        } elseif ( $type == "coachcar" ) { // 教练车辆
            $complex = [];
            if ( $school_id > 0) {
                $map['school_id'] = $school_id;
            }
            if ($param) {
                if ( $param['ctype'] != '') {
                    $map['car_type'] = $param['ctype'];
                }

                // if ( $param['keywords'] != '') {
                    $keywords = $param['keywords'];
                    $complex = [
                        'school.s_school_name' => $keywords,
                        'cars.name' => $keywords,
                        'cars.car_no' => $keywords
                    ];
                // }
            }

            $list = $this->db->from("{$this->cars_tbl} as cars")
                ->select(
                    'cars.*,
                     school.s_school_name as school_name,
                     school.l_school_id'
                )
                ->join("{$this->school_tbl} as school", "school.l_school_id = cars.school_id", "LEFT")
                ->where($map)
                ->group_start()
                    ->or_like($complex)
                ->group_end()
                ->limit($limit, $start)
                ->order_by('id', 'desc')
                ->get()
                ->result_array();
            $imgurl = [];
            $imgurl_arr = [];
            if ( ! empty($list)) {
                foreach ($list as $index => $value) {
                    $cate_id = $value['car_cate_id'];
                    $cate_info = $this->getCarCate($cate_id);
                    if ( ! empty($cate_info)) {
                        $list[$index]['car_no_name'] = $cate_info['name'];
                    } else {
                        $list[$index]['car_no_name'] = '--';
                    }

                    if ($value['addtime'] != '' && $value['addtime'] != 0) {
                        $list[$index]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                    } else {
                        $list[$index]['addtime'] = '--';
                    }

                    // handle picture
                    if ( $value['imgurl'] != '') {
                        $imgurl = json_decode($value['imgurl'], true);
                        
                        if ( ! is_array($imgurl) OR empty($imgurl)) {
                            $list[$index]['car_imgurl'] = '';
                        }

                        if ( ! empty($imgurl)) {
                            foreach ($imgurl as $key => $url) {
                                $url = $this->mbase->buildUrl($url);
                                if ($url != '') {
                                    $list[$index]['car_imgurl']['car_imgurl_'.$key] = $url;
                                } else {
                                    $list[$index]['car_imgurl'] = '';
                                }
                            }
                        } 

                    } elseif ($value['original_imgurl'] != '') {

                        $imgurl = json_decode($value['original_imgurl'], true);

                        if ( ! is_array($imgurl) OR empty($imgurl)) {
                            $list[$index]['car_imgurl'] = '';
                        }

                        if ( ! empty($imgurl)) {
                            foreach ($imgurl as $key => $url) {
                                $url = $this->mbase->buildUrl($url);
                                if ($url != '') {
                                    $list[$index]['car_imgurl']['car_imgurl_'.$key] = $url;
                                } else {
                                    $list[$index]['car_imgurl'] = '';
                                }
                            }
                        } 

                    } else {
                        $list[$index]['car_imgurl'] = '';

                    }


                    if ( $value['name'] == '') {
                        $list[$index]['name'] = '--';
                    }
                    
                    if ( $value['car_no'] == '') 
                    {
                        $list[$index]['car_no'] = '--';
                    } 

                    if ( $value['car_type'] == ''  
                        OR ! in_array($value['car_type'], [1, 2, 3]))
                    {
                        $list[$index]['car_type'] = 0;
                    }

                    if ( $value['school_name'] == '') 
                    {
                        $list[$index]['school_name'] = '--';
                    } 
                }
            }

        } elseif ( $type == "video" ) { // 学车视频

            $list = $this->getVideoList($param, $start, $limit);

        }
        
        $data = ['list' => $list];

        return $data;

    }

    // 获取视频列表
    public function getVideoList($param, $start, $limit)
    {
        $map = [];
        $complex = [];
        if ($param) {
            if ( $param['ctype'] != '') {
                $map['car_type'] = $param['ctype'];
            }

            if ( $param['open'] != '') {
                $map['is_open'] = $param['open'];
            }

            if ( $param['course'] != '') {
                $map['course'] = $param['course'];
            }

            $complex['title'] = $param['keywords'];
        }

        $list = $this->db->from("{$this->video_tbl} as video")
            ->where($map)
            ->group_start()
                ->or_like($complex)
            ->group_end()
            ->order_by('v_order', 'asc')
            ->order_by('id', 'desc')
            ->limit($limit, $start)
            ->get()
            ->result_array();
        $car_type = ['car' => '小车', 'bus' => '客车', 'truck' => '货车', 'moto' => '摩托车'];
        if ( ! empty($list)) {
            foreach ($list as $index => $video) {

                foreach ($car_type as $key => $car) {
                    if ($video['car_type'] == $key) {
                        $list[$index]['car_type_name'] = $car_type[$key];
                    }
                }

                if ($video['course'] == 'kemu2') {
                    $list[$index]['course_name'] = '科目二';
                } elseif ($video['course'] == 'kemu3') {
                    $list[$index]['course_name'] = '科目三';
                }
                
                if ($video['addtime'] != 0 AND $video['addtime'] != '') {
                    $list[$index]['addtime'] = date('Y-m-d H:i:s', $video['addtime']);
                } else {
                    $list[$index]['addtime'] = '--';
                }

                if ($video['updatetime'] != 0 AND $video['updatetime'] != '') {
                    $list[$index]['updatetime'] = date('Y-m-d H:i:s', $video['updatetime']);
                } else {
                    $list[$index]['updatetime'] = '--';
                }

                // 视频时间转化
                $video_time = $video['video_time'];
                if ($video_time > 60 AND ($video_time % 60) > 0) {
                    $min = floor($video_time / 60);
                    $sec = $video_time - 60 * $min;
                    $list[$index]['video_time'] = $min . '分' . $sec . '秒';
                } else {
                    $list[$index]['video_time'] = $min . '分钟';
                }

                $list[$index]['pic_url'] = $this->mbase->buildUrl($video['pic_url']);
                $list[$index]['video_url'] = $this->mbase->buildUrl($video['video_url']);
            }
        }
        return $list;
    }

    /**
     * 新增数据
     * @param 
     * @return void
     **/
    public function add($data, $tblname)
    {
        $this->db->insert($tblname, $data);
        return $this->db->insert_id();
    }

    /**
     * 更新数据
     * @param 
     * @return void
     **/
    public function editData($data, $tblname)
    {
        $where = ['id' => $data['id']];
        $update_ok = $this->db
            ->where($where)
            ->update($tblname, $data);
        return $update_ok;
    }

    /**
     * 删除数据
     * @param 
     * @return void
     **/
    public function del($id, $tblname)
    {
        if ( ! is_array($id)) {
            $ids_arr = (array)$id;
        }
        
        $result = $this->db
            ->where_in('id', $ids_arr)
            ->delete($tblname);

        return $result;
    }

    /**
     * 获取科目视频信息
     * @param   $id
     * @return  void
     **/
    public function getVideoInfoById($id)
    {
        $map = ['id' => $id];
        $query = $this->db
            ->get_where($this->video_tbl, $map);
        $video_info = $query->row_array();
        if ( ! empty($video_info)) {
            $video_info['http_video_url'] = $this->mbase->buildUrl($video_info['video_url']);
            $video_info['http_pic_url'] = $this->mbase->buildUrl($video_info['pic_url']);
        }
        return $video_info;
    }

    /**
     * 获取科目视频信息
     * @param   $id
     * @return  void
     **/
     public function getVideoByCondition($title, $car_type, $course)
     {
         $map = ['title' => $title, 'car_type' => $car_type, 'course' => $course];
         $query = $this->db
             ->get_where($this->video_tbl, $map);
         $video_info = $query->row_array();
         return $video_info;
     }


    /**
     * 获取车型信息
     * @param $id
     * @return void
     **/
    public function getCarCate($id)
    {
        $condition = ['id'=>$id];
        $cate_list = $this->db
            ->get_where($this->cate_tbl, $condition)
            ->row_array();
        return $cate_list;
    }

    /**
     * 获取教练车辆信息[单条]
     * @param $id
     * @return void
     **/
    public function getCarByID($id)
    {
        $condition = ['cars.id'=>$id];
        $car_list = $this->db
            ->select(
                'cars.*,
                 school.s_school_name as school_name,
                 cate.id as cate_id,
                 cate.name as cate_name'
            )
            ->from("{$this->cars_tbl} as cars")
            ->join("{$this->school_tbl} as school", "school.l_school_id = cars.school_id", "LEFT")
            ->join("{$this->cate_tbl} as cate", "cate.id = cars.car_cate_id", "LEFT")
            ->where($condition)
            ->get()
            ->result_array();
        $list = [];
        if ( ! empty($car_list)) {
            foreach ( $car_list as $index => $value) {
                $imgurl = json_decode($value['imgurl'], true);
                foreach ($imgurl as $key => $url) {
                    $imgurl_number = ['one', 'two', 'three'];
                    if ( $url != '') {
                        $value['imgurl_'.$imgurl_number[$key]] = $url;
                        $value['http_imgurl_'.$imgurl_number[$key]] = $this->mbase->buildUrl($url);
                    } 
                }
                $list = $value;
                foreach ($imgurl_number as $no) {
                    if (! isset($list['imgurl_'.$no])) {
                        $list['imgurl_'.$no] = '';
                        $list['http_imgurl_'.$no] = '';
                    }
                }
            }
        }
        return $list;
    }

    /**
     * 获取车型列表
     * @param key
     * @return void
     **/
    public function searchCarCateList($key)
    {
        $car_cate_list = $this->db 
            ->from($this->cate_tbl)
            ->select('id as car_cate_id, name')
            ->like('name', $key)
            ->get()
            ->result_array();
        return $car_cate_list;
    }

    //根据驾校id获取驾校车辆列表
    // public function getCarsList($school_id, $start, $limit) {
    //     $list = array();
    //     $map = '';
    //     if ($school_id != 0) {
    //         $map = "cars.school_id='{$school_id}'"; 
    //     }else {
    //         $map = array();
    //     }
    //     $count = $this->db->from("{$this->cars_tbl} as cars")
    //             ->join("{$this->cate_tbl} as car_category", 'car_category.id = cars.car_cate_id', 'left')
    //             ->join("{_tbl_ as schoolt, "school.l_school = cars.school_id", "LEFT"bl} as school", 'school.l_school_id = cars.school_id', 'left')
    //             ->where($map)
    //             ->count_all_results();
    //     $page = (int) ceil($count / $limit);
    //     $carlist = $this->db->select('cars.*,car_category.subtype,car_category.name as category_name,l_school_id,s_school_name')
    //         ->from("{$this->cars_tbl} as cars")
    //         ->join("{$this->cate_tbl} as car_category", 'car_category.id = cars.car_cate_id', 'left')
    //         ->join("{_tbl_ as schoolt, "school.l_school = cars.school_id", "LEFT"bl} as school", 'school.l_school_id = cars.school_id', 'left')
    //         ->where($map)->order_by('cars.id', 'DESC')
    //         ->limit($limit, $start)->get()->result_array();
    //     if (!empty($carlist)) {
    //         foreach ($carlist as $key => $value) {
    //             if ($value['imgurl'] != '') {
    //                 if ($value['imgurl'] != '') {
    //                     $imgurl = json_decode($value['imgurl'], true);
    //                     if (!is_array($imgurl)) {
    //                         $carlist[$key]['cars_imgurl'] = array();
    //                     }
    //                     if (!empty($imgurl)) {
    //                         foreach ($imgurl as $k => $v) {
    //                             //$carlist[$key]['cars_imgurl'][$k]['original_imgurl_all'] = $this->buildUrl($v);
    //                             $carlist[$key]['cars_imgurl'][$k]['original_imgurl'] = $v;
    //                         }
    //                     } else {
    //                         $carlist[$key]['cars_imgurl'] = array();
    //                     }
    //                 } else {
    //                     $carlist[$key]['cars_imgurl'] = array();
    //                 }
    //             } else {
    //                 $original_imgurl = json_decode($value['original_imgurl'], true);
    //                 if (!is_array($original_imgurl)) {
    //                     $carlist[$key]['cars_imgurl'] = array();
    //                 }
    //                 if (!empty($original_imgurl)) {
    //                     foreach ($original_imgurl as $k => $v) {
    //                         //$carlist[$key]['cars_imgurl'][$k]['original_imgurl_all'] = $this->buildUrl($v);
    //                         $carlist[$key]['cars_imgurl'][$k]['original_imgurl'] = $v;
    //                     }
    //                 } else {
    //                     $carlist[$key]['cars_imgurl'] = array();
    //                 }
    //             }

    //             if ($value['subtype'] == null) {
    //                 $carlist[$key]['subtype'] = '--';
    //             }

    //             if ($value['s_school_name'] == '') {
    //                 $carlist[$key]['school_name'] = '--';
    //             } else {
    //                 $carlist[$key]['school_name'] = $value['s_school_name'];
    //             }

    //             if ($value['car_no'] == '') {
    //                 $carlist[$key]['car_number'] = '--';
    //             } else {
    //                 $carlist[$key]['car_number'] = $value['car_no'];
    //             }

    //             if ($value['name'] == '') {
    //                 $carlist[$key]['name'] = '--';
    //             } 

    //             if ($value['addtime'] != 0) {
    //                 $carlist[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
    //             } else {
    //                 $carlist[$key]['addtime'] = '--';
    //             }

    //             if ($value['category_name'] == '') {
    //                 $carlist[$key]['category_name'] = '--';
    //             }

    //         }
    //     }
    //     $list = array('lists' => $carlist, 'page' => $page, 'count' => $count);
    //     return $list;
    // }

    public function getCarsByIds ($school_id) {
        $carslist = $this->db->select('c.id, name, school_id, car_no, car_type')
            ->from("{$this->cars_tbl} as c")
            ->where(array('school_id'=>$school_id))
            ->get()
            ->result_array();
        return $carslist;
    }

}