<?php
    // 首页
defined('BASEPATH') OR exit('No direct script access allowed');

class Mcity extends CI_Model {

    public $province_tbname = 'cs_province';
    public $city_tbname = 'cs_city';
    public $area_tbname = 'cs_area';
    public $cityid;
    public $city;
    public $fatherid;
    public $leter;
    public $spelling;
    public $acronym;
    public $is_hot;

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * 获取区域数量
     * @param data
     * @return void
     **/
    public function getAreaPageNum($param, $limit)
    {
        $map = [];
        $complex = [];
        $keywords = '';
        if ($param) {
            if ($param['is_hot'] != '') {
                $map['city.is_hot'] = $param['is_hot'];
            } 

            if ($param['keywords'] != '') {
                $keywords = $param['keywords'];
                $complex = [
                    'province' => $keywords,
                    'provinceid' => $keywords,
                    'city' => $keywords,
                    'cityid' => $keywords,
                    'area' => $keywords,
                    'areaid' => $keywords,
                    'spelling' => $keywords,
                    'acronym' => $keywords
                ];
            }
        }

        if ( ! empty($complex)) {
            $query = $this->db
                ->where($map)
                ->group_start()
                    ->or_like($complex)
                ->group_end();

        } else {
            $query = $this->db
                ->where($map);
        }

        $count =  $this->db
            ->from("{$this->area_tbname} as area")
            ->join("{$this->city_tbname} as city", 'city.cityid = area.fatherid', 'left')
            ->join("{$this->province_tbname} as province", 'province.provinceid = city.fatherid', 'left')
            ->where($map)
            ->count_all_results();
        
        $pagenum = (int) ceil( $count / $limit );

        $page_info = [
            'pagenum' => $pagenum,
            'count' => $count
        ];

        return $page_info;

    }

    /**
     * 获取城市列表
     * @param data
     *
     * @return void
     **/
    public function getAreaList($param, $start, $limit)
    {
        $map = [];
        $complex = [];
        $keywords = '';
        if ($param) {
            if ($param['is_hot'] != '') {
                $map['city.is_hot'] = $param['is_hot'];
            } 

            // if ($param['keywords'] != '') {
                $keywords = $param['keywords'];
                $complex = [
                    'province' => $keywords,
                    'provinceid' => $keywords,
                    'city' => $keywords,
                    'cityid' => $keywords,
                    'area' => $keywords,
                    'areaid' => $keywords,
                    'spelling' => $keywords,
                    'acronym' => $keywords
                ];
            // }
        }

        $count =  $this->db
            ->from("{$this->area_tbname} as area")
            ->join("{$this->city_tbname} as city", 'city.cityid = area.fatherid', 'left')
            ->join("{$this->province_tbname} as province", 'province.provinceid = city.fatherid', 'left')
            ->where($map)
            ->group_start()
                ->or_like($complex)
            ->group_end()
            ->count_all_results();

        $pagenum = (int) ceil( $count / $limit );
            
        $arealist = $this->db
            ->from("{$this->area_tbname} as area")
            ->select(
                'provinceid, 
                 province, 
                 city.id as city_id,
                 cityid, 
                 city,
                 is_hot,
                 spelling,
                 acronym,
                 area.id as area_id,
                 areaid,
                 area'
            )
            ->join("{$this->city_tbname} as city", 'city.cityid = area.fatherid', 'left')
            ->join("{$this->province_tbname} as province", 'province.provinceid = city.fatherid', 'left')
            ->where($map)
            ->group_start()
                ->or_like($complex)
            ->group_end()
            ->order_by('city.is_hot', 'asc')
            ->order_by('area.id', 'desc')
            ->limit($limit, $start)
            ->get()
            ->result_array();
        
        $area_list = [
            'list' => $arealist,
            'pagenum' => $pagenum,
            'count' => $count
        ];
        return $area_list;

    }
    
    /**
     * 新增数据
     * @param data
     * @param tblname
     * @return void
     **/
    public function add($data, $tblname)
    {
        $result = $this->db
            ->insert($tblname, $data);
        return $this->db->insert_id();
    }

    /**
     * 更新数据
     * @param $param
     * @param $tblname
     * @return void
     **/
    public function updateData($data, $tblname)
    {
        $id = $data['id'];
        $condition = [
            'id' => $id
        ];
        $result = $this->db
            ->from($tblname)
            ->where($condition)
            ->update($tblname, $data);
        return $result;
    }

    /**
     * 删除
     * @param id
     * @return void
     **/
    public function del($data, $tblname)
    {
        $result = $this->db
            ->from($tblname)
            ->where($data)
            ->delete();
        return $result;
    }

    /**
     * 获取单条省市区信息
     * @param $id
     * @param $tbkname
     * @return void
     **/
    public function getPcaById($id, $name = 'area')
    {   
        if ($name == "area") {
            $condition = [
                'area.id' => $id
            ];
            $result = $this->db
                ->from("{$this->area_tbname} as area")
                ->join("{$this->city_tbname} as city", "city.cityid = area.fatherid", "left")
                ->where( $condition)
                ->select(
                    'area.id,
                    area.areaid,
                    area.area,
                    area.fatherid,
                    city.city,
                    cityid'
                )
                ->get()
                ->result_array();

        } elseif ($name == 'city') {
            $condition = [
                'city.id' => $id
            ];
            $result = $this->db
                ->from("{$this->city_tbname} as city")
                ->join("{$this->province_tbname} as province", "province.provinceid = city.fatherid", "left")
                ->where($condition)
                ->select(
                    'city.id,
                     city.cityid,
                     city.city,
                     city.fatherid,
                     city.leter,
                     city.acronym,
                     city.spelling,
                     city.is_hot,
                     province.province,
                     provinceid'
                )
                ->get()
                ->result_array();
        }

        $list = [];
        if ($result) {
            foreach ($result as $index => $value) {
                $list = $value;
            }
        }
        return $list;
    }

    
    /**
     * 获取搜索结果 [省 | 市]
     * @param $key
     * @return void
     **/
    public function searchCityInfo($key, $name)
    {
        if ($name == "city") {
            $map = [
                "city" => $key,
                "spelling" => $key,
                "leter" => $key,
                "acronym" => $key,
                "city" => $key
            ];
            $result = $this->db
                ->from($this->city_tbname)
                ->select('cityid, city')
                ->or_like($map)
                ->get()
                ->result_array();

        } elseif ($name == "province") {
            $result = $this->db
                ->from($this->province_tbname)
                ->select('provinceid, province')
                ->or_like('province', $key)
                ->get()
                ->result_array();
        }

        return $result;

    }

    /**
     * 检查重复状态
     * @param $condition
     * @param $tblname
     * @return void
     **/
    public function check($condition, $tblname)
    {
        $result = $this->db
            ->from($tblname)
            ->where($condition)
            ->get()
            ->result_array();
        return $result;
    }

    /**
     * 获取城市列表
     * @param $param
     *
     * @return void
     **/
    public function getCityList($param, $start, $limit)
    {
        $map = [];
        $complex = [];
        $keywords = '';
        if ( ! empty($param)) {
            if ($param['is_hot'] != '') {
                $map['is_hot'] = $param['is_hot'];
            }

            // if ($param['keywords'] != '') {
                $keywords = $param['keywords'];
                $complex = [
                    'city' => $keywords,
                    'cityid' => $keywords,
                    'province' => $keywords,
                    'provinceid' => $keywords,
                    'spelling' => $keywords,
                    'leter' => $keywords,
                    'acronym' => $keywords,
                ];
                
            // }
        }

        $count = $this->db
            ->from("{$this->city_tbname} as city")
            ->join("{$this->province_tbname} as province", "province.provinceid = city.fatherid", "left")
            ->where($map)
            ->group_start()
                ->or_like($complex)
            ->group_end()
            ->count_all_results();

        $pagenum = (int) ceil($count / $limit);
        $city_list = $this->db
            ->select(
                'city.*,
                 province,
                 provinceid'
            )
            ->from("{$this->city_tbname} as city")
            ->join("{$this->province_tbname} as province", "province.provinceid = city.fatherid", "left")
            ->where($map)
            ->group_start()
                ->or_like($complex)
            ->group_end()
            ->order_by('is_hot', 'asc')
            ->order_by('id', 'desc')
            ->limit($limit, $start)
            ->get()
            ->result_array();
        $data = [
            'list' => $city_list,
            'pagenum' => $pagenum,
            'count' => $count
        ];

        return $data;

    }

    // 获取城市
    public function getAllCityList($start, $limit)
    {
        $query = $this->db->get($this->city_tbname, $limit, $start);
        return ['list'=>$query->result()];
    }
    // 获取热门城市
    public function getHotCityList($start='', $limit='') {
        if($start == '' || $limit == '') {
            $query = $this->db->get($this->city_tbname);
        } else {
            $query = $this->db->get($this->city_tbname, $limit, $start);
        }
        return ['list'=>$query->result()];
    }

    // 获取省份 
    public function getProvinceList($start='', $limit='') {
        if($start == '' || $limit == '') {
            $query = $this->db->get($this->province_tbname);
        } else {
            $query = $this->db->get($this->province_tbname, $limit, $start);
        }
        return ['list'=>$query->result()];
    }

    
    // 根据省份ID获取城市
    public function getCityListByProvinceId($city_id, $start='', $limit='') {
        if($start == '' || $limit == '') {
            $query = $this->db->where('fatherid', $city_id)->get($this->city_tbname);
        } else {
            $query = $this->db->where('fatherid', $city_id)->get($this->city_tbname, $limit, $start);
        }
        return ['list'=>$query->result()];
    }

    // 根据城市ID获取区域
    public function getAreaListByCityId($city_id, $start='', $limit='') {
        if($start == '' || $limit == '') {
            $query = $this->db->where('fatherid', $city_id)->get($this->area_tbname);
        } else {
            $query = $this->db->where('fatherid', $city_id)->get($this->area_tbname, $limit, $start);
        }
        return ['list'=>$query->result()];
    }

    public function getCityPageNum($param = [], $limit)
    {
        if ( ! empty($param) ) {
            $map = [];
            $keywords = '';
            $complex = [];
            if ($param['is_hot'] != '') {
                $map['is_hot'] = $param['is_hot'];
            }

            // if ($param['keywords'] != '') {
                $keywords = $param['keywords'];
                $complex = [
                    'city' => $keywords,
                    'cityid' => $keywords,
                    'province' => $keywords,
                    'provinceid' => $keywords,
                    'spelling' => $keywords,
                    'leter' => $keywords,
                    'acronym' => $keywords,
                ];
            // }

            $count = $this->db
                ->from("{$this->city_tbname} as city")
                ->join("{$this->province_tbname} as province", "province.provinceid = city.fatherid", "left")
                ->where($map)
                ->group_start()
                    ->or_like($complex)
                ->group_end()
                ->count_all_results();
            $pagenum = (int) ceil($count / $limit);

            $data = [
                'pagenum' => $pagenum,
                'count' => $count
            ];
            return $data;
        } else {
            $count = $this->db->count_all('city');
            return ['pn'=>(int) ceil($count / $limit), 'count'=>$count];

        }
    }

    // 根据provinceid获取城市名
    public function getProvinceInfoByCondition($select, $wherecondition) {
        $query = $this->db->select($select)->where($wherecondition)->get($this->province_tbname);
        return $query->row_array();
    }

    // 根据cityid获取城市名
    public function getCityInfoByCondition($select, $wherecondition) {
        $query = $this->db->select($select)->where($wherecondition)->get($this->city_tbname);
        return $query->row_array();
    }

    // 根据areaid获取城市名
    public function getAreaInfoByCondition($select, $wherecondition) {
        $query = $this->db->select($select)->where($wherecondition)->get($this->area_tbname);
        return $query->row_array();
    }
}
?>