<?php
namespace Admin\Model;
use Think\Model;
use Think\Page;

class CarsModel extends BaseModel {
    public $tableName = 'cars';

// 1.教练车辆管理模块
    //根据驾校id获取驾校车辆列表
    public function getCarsList($school_id) {
        $list = array();
        $map = array();
        if ($school_id != 0) {
            $map['cars.school_id'] = $school_id;
        }
        $count = $this->table(C('DB_PREFIX').'cars cars')
                ->join(C('DB_PREFIX').'car_category car_category ON car_category.id = cars.car_cate_id', 'LEFT')
                ->join(C('DB_PREFIX').'school school ON school.l_school_id = cars.school_id', 'LEFT')
                ->where($map)
                ->count();
        $Page = new Page($count, 10);
        $page = $this->getPage($count, 10);
        $carlist =  $this->table(C('DB_PREFIX').'cars cars')
            ->field(
                'cars.*, 
                 car_category.subtype, 
                 car_category.name as category_name, 
                 l_school_id, 
                 s_school_name'
            )
            ->join(C('DB_PREFIX').'car_category car_category ON car_category.id = cars.car_cate_id', 'LEFT')
            ->join(C('DB_PREFIX').'school school ON school.l_school_id = cars.school_id', 'LEFT')
            ->where($map)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order('cars.id DESC, addtime desc')
            ->fetchSql(false)
            ->select();
        if (!empty($carlist)) {
            foreach ($carlist as $key => $value) {
                if ($value['imgurl'] != '') {
                    if ($value['imgurl'] != '') {
                        $imgurl = json_decode($value['imgurl'], true);
                        if (!is_array($imgurl)) {
                            $carlist[$key]['cars_imgurl'] = array();
                        }
                        if (!empty($imgurl)) {
                            foreach ($imgurl as $k => $v) {
                                $carlist[$key]['cars_imgurl'][$k]['original_imgurl_all'] = $this->buildUrl($v);
                                $carlist[$key]['cars_imgurl'][$k]['original_imgurl'] = $v;
                            }
                        } else {
                            $carlist[$key]['cars_imgurl'] = array();
                        }
                    } else {
                        $carlist[$key]['cars_imgurl'] = array();
                    }
                } else {
                    $original_imgurl = json_decode($value['original_imgurl'], true);
                    if (!is_array($original_imgurl)) {
                        $carlist[$key]['cars_imgurl'] = array();
                    }
                    if (!empty($original_imgurl)) {
                        foreach ($original_imgurl as $k => $v) {
                            $carlist[$key]['cars_imgurl'][$k]['original_imgurl_all'] = $this->buildUrl($v);
                            $carlist[$key]['cars_imgurl'][$k]['original_imgurl'] = $v;
                        }
                    } else {
                        $carlist[$key]['cars_imgurl'] = array();
                    }
                }

                if ($value['subtype'] == null) {
                    $carlist[$key]['subtype'] = '--';
                }

                if ($value['s_school_name'] == '') {
                    $carlist[$key]['school_name'] = '--';
                } else {
                    $carlist[$key]['school_name'] = $value['s_school_name'];
                }

                if ($value['car_no'] == '') {
                    $carlist[$key]['car_number'] = '--';
                } else {
                    $carlist[$key]['car_number'] = $value['car_no'];
                }

                if ($value['name'] == '') {
                    $carlist[$key]['name'] = '--';
                } 

                if ($value['addtime'] != 0) {
                    $carlist[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $carlist[$key]['addtime'] = '--';
                }

                if ($value['category_name'] == '') {
                    $carlist[$key]['category_name'] = '--';
                }

            }
        }
        $list = array('lists' => $carlist, 'page' => $page, 'count' => $count);
        return $list;
    }
    /**
     * 通过条件搜索车辆的相关信息
     *
     * @return 	void
     * @author 	wl
     * @date	August 11, 2016
     **/
    public function searchCarsInfo ($param, $school_id) {
        $map = array();
        $list = array();
        $complex = array();
        $s_keyword = '%' . $param['s_keyword'] . '%';
        if ($param['search_info'] == '') {
            $complex['cars.name'] = array('like', $s_keyword);
            $complex['car_no'] = array('like', $s_keyword);
            $complex['s_school_name'] = array('like', $s_keyword);
            $complex['_logic'] = 'OR';

        } else {
            if ($param['search_info'] == 'name') {
                $param['search_info'] = 'cars.name';
            }
            
            $complex[$param['search_info']] = array('like', $s_keyword);
        }

        $map['_complex'] = $complex;

        if ($param['car_type'] != '') {
            $map['car_type'] = array('EQ', $param['car_type']);
        }

        if ($school_id != 0) {
            $map['cars.school_id'] = $school_id;
        }

        $count = $this->table(C('DB_PREFIX').'cars cars')
                ->join(C('DB_PREFIX').'car_category car_category ON car_category.id = cars.car_cate_id', 'LEFT')
                ->join(C('DB_PREFIX').'school school ON school.l_school_id = cars.school_id', 'LEFT')
                ->where($map)
                ->count();
        $Page = new Page($count, 10, $param);
        $page = $this->getPage($count, 10, $param);
        $carlist =  $this->table(C('DB_PREFIX').'cars cars')
            ->field(
                'cars.*, 
                 car_category.subtype, 
                 car_category.name as category_name, 
                 l_school_id, 
                 s_school_name'
            )
            ->join(C('DB_PREFIX').'car_category car_category ON car_category.id = cars.car_cate_id', 'LEFT')
            ->join(C('DB_PREFIX').'school school ON school.l_school_id = cars.school_id', 'LEFT')
            ->where($map)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order('cars.id DESC, addtime desc')
            ->fetchSql(false)
            ->select();
        if (!empty($carlist)) {
            foreach ($carlist as $key => $value) {
                if ($value['imgurl'] != '') {
                    if ($value['imgurl'] != '') {
                        $imgurl = json_decode($value['imgurl'], true);
                        if (!is_array($imgurl)) {
                            $carlist[$key]['cars_imgurl'] = array();
                        }
                        if (!empty($imgurl)) {
                            foreach ($imgurl as $k => $v) {
                                $carlist[$key]['cars_imgurl'][$k]['original_imgurl_all'] = $this->buildUrl($v);
                                $carlist[$key]['cars_imgurl'][$k]['original_imgurl'] = $v;
                            }
                        } else {
                            $carlist[$key]['cars_imgurl'] = array();
                        }
                    } else {
                        $carlist[$key]['cars_imgurl'] = array();
                    }
                } else {
                    $original_imgurl = json_decode($value['original_imgurl'], true);
                    if (!is_array($original_imgurl)) {
                        $carlist[$key]['cars_imgurl'] = array();
                    }
                    if (!empty($original_imgurl)) {
                        foreach ($original_imgurl as $k => $v) {
                            $carlist[$key]['cars_imgurl'][$k]['original_imgurl_all'] = $this->buildUrl($v);
                            $carlist[$key]['cars_imgurl'][$k]['original_imgurl'] = $v;
                        }
                    } else {
                        $carlist[$key]['cars_imgurl'] = array();
                    }
                }

                if ($value['subtype'] == null) {
                    $carlist[$key]['subtype'] = '--';
                }

                if ($value['s_school_name'] == '') {
                    $carlist[$key]['school_name'] = '--';
                } else {
                    $carlist[$key]['school_name'] = $value['s_school_name'];
                }

                if ($value['car_no'] == '') {
                    $carlist[$key]['car_number'] = '--';
                } else {
                    $carlist[$key]['car_number'] = $value['car_no'];
                }

                if ($value['name'] == '') {
                    $carlist[$key]['name'] = '--';
                } 

                if ($value['addtime'] != 0) {
                    $carlist[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $carlist[$key]['addtime'] = '--';
                }

                if ($value['category_name'] == '') {
                    $carlist[$key]['category_name'] = '--';
                }

            }
        }
        $list = array('lists' => $carlist, 'page' => $page, 'count' => $count);
        return $list;
    }

    /**
     * 根据id删除车辆信息
     *
     * @return 	void
     * @author 	sun/wl
     * @date 	August 01, 2016
     **/
    public function delCarsList($ids) {
        $del = $this->where(array('id'=>$ids))
            ->fetchSql(false)
            ->delete();
        return $del;
    }

    /**
     * 获取单条教练车辆信息
     *
     * @return  void
     * @author  wl
     * @date    Dec 19, 2016
     **/
    public function getCarInfoById ($id) {
        if (!is_numeric($id)) {
            return false;
        }
        $carlist = $this->table(C('DB_PREFIX').'cars cars')
            ->field(
                'cars.id as id, 
                 cars.name  as name, 
                 cars.car_no as car_no, 
                 cars.car_type as car_type,
                 cars.imgurl as imgurl,
                 cars.car_cate_id as car_cate_id, 
                 cars.school_id, 
                 cars.addtime, 
                 car_category.id category_id, 
                 car_category.brand, 
                 car_category.name as category_name, 
                 car_category.subtype, 
                 school.l_school_id, 
                 school.s_school_name'
            )
            ->join(C('DB_PREFIX').'school school ON school.l_school_id = cars.school_id', 'LEFT')
            ->join(C('DB_PREFIX').'car_category car_category ON car_category.id = cars.car_cate_id', 'LEFT')
            ->where(array('cars.id' => $id))
            ->fetchSql(false)
            ->find();
        if (!empty($carlist)) {
            return $carlist;
        } else {
            return array();
        }
    }
    /**
     * 通过school_id获得车辆表中的信息
     *
     * @return 	void
     * @author 	wl
     * @date 	Sep 08, 2016
     **/
    public function getCarsByIds ($school_id) {
        $carslist = $this->table(C('DB_PREFIX').'cars c')
            ->where('school_id = :sid')
            ->bind(['sid' => $school_id])
            ->field('c.id, name, school_id, car_no, car_type')
            ->fetchSql(false)
            ->select();
        return $carslist;
    }

    /**
     * 通过school_id获得车辆型号表中的信息
     *
     * @return 	void
     * @author 	wl
     * @date 	August 18, 2016
     * @update 	August 19, 2016
     **/
    public function getCarsCategoryByIds ($school_id) {
        $carscategorylist = $this->table(C('DB_PREFIX').'car_category ca')
            ->where('school_id = :sid')
            ->bind(['sid' => $school_id])
            ->field('distinct brand, school_id')
            ->fetchSql(false)
            ->select();
        // var_dump($carscategorylist);exit;
        return $carscategorylist;
    }

    /**
     * 获取车辆型号
     *
     * @return  void
     * @author  wl
     * @date    Jan 06, 2016
     **/
    public function getCarCategory () {
        $carscategorylist = $this->table(C('DB_PREFIX').'car_category ca')
            ->field('id, name, brand, subtype')
            ->fetchSql(false)
            ->select();
        if (!empty($carscategorylist)) {
            return $carscategorylist;
        } else {
            return array();
        }
    }

    /**
     * 通过车辆型号id获取车辆名称
     *
     * @return  void
     * @author  wl
     * @date    Jan 06, 2016
     **/
    public function getCarsNameById ($cate_id) {
        $carsname = $this->table(C('DB_PREFIX').'car_category')
            ->where(array('id' => $cate_id))
            ->getField('name');
        if ($carsname != '') {
            return $carsname;
        } else {
            return '';
        }
    }

    /**
     * undocumented function
     *
     * @return  void
     * @author  wl
     * @date    Jan 06, 2017
     **/
    public function checkCars ($school_id, $name, $car_no) {
        $condition = array(
                'school_id' => $school_id,
                'name' => $name,
                'car_no' => $car_no,

            );
        $checkcars = $this->table(C('DB_PREFIX').'cars')
            ->where($condition)
            ->find();
        if (!empty($checkcars)) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * 通过brand获得车辆型号表中的信息
     *
     * @return 	void
     * @author 	wl
     * @date 	August 19, 2016
     **/
    public function getCarsCategoryByName ($name) {
        if (!$name) {
            return false;
        }
        $brandlist = $this->table(C('DB_PREFIX').'car_category ca')
            ->where('brand = :name')
            ->bind(['name' => $name])
            ->fetchSql(false)
            ->select();
        if (!empty($brandlist)) {
            return $brandlist;
        } else {
            return array();
        }
    }

    /**
     * 通过car_cate_id获得车辆型号表中的信息
     *
     * @return 	void
     * @author 	wl
     * @date 	August 18, 2016
     * @update 	August 19, 2016
     **/
    public function getCarsCategoryByCarId ($car_cate_id) {
        if (!is_numeric($car_cate_id)) {
            return false;
        }
        $carscategorylist = $this->table(C('DB_PREFIX').'car_category ca')
            ->where('id = :car_id')
            ->bind(['car_id' => $car_cate_id])
            ->fetchSql(false)
            ->find();
        if (!empty($carscategorylist)) {
            return $carscategorylist;
        } else {
            return array();
        }
    }

    /**
     * 获得车辆型号的列表
     *
     * @return 	void
     * @author 	wl
     * @date	August 09, 2016
     **/
    public function getCarsCategory ($school_id) {
        $lists= array();
        if ($school_id == 0) {
            $count = $this->table(C('DB_PREFIX').'car_category')
                ->fetchSql(false)
                ->count();
            $Page = new Page($count, 10);
            $page = $this->getPage($count, 10);
            $carscatelist = $this->table(C('DB_PREFIX').'car_category c')
                // ->join(C('DB_PREFIX').'school s ON s.l_school_id = c.school_id')
                // ->field('s.l_school_id, s_school_name, c.*')
                ->limit($Page->firstRow.','.$Page->listRows)
                ->order('c.id DESC')
                ->fetchSql(false)
                ->select();
        } else {
            $count = $this->table(C('DB_PREFIX').'car_category')
                ->where('school_id = :sid')
                ->bind(['sid' => $school_id])
                ->fetchSql(false)
                ->count();
            $Page = new Page($count, 10);
            $page = $this->getPage($count, 10);
            $carscatelist = $this->table(C('DB_PREFIX').'car_category c')
                ->where('school_id = :sid')
                ->bind(['sid' => $school_id])
                ->limit($Page->firstRow.','.$Page->listRows)
                ->order('c.id DESC')
                ->fetchSql(false)
                ->select();
        }
        if (!empty($carscatelist)) {
            foreach ($carscatelist as $key => $value) {
                if ($value['addtime'] != 0) {
                    $carscatelist[$key]['addtime'] = date('Y-m-d H:i:s',$value['addtime']);
                } else {
                    $carscatelist[$key]['addtime'] = '';
                }

                $carscatelist[$key]['point_text_url'] = $this->buildUrl($value['point_text_url']);

                if ($value['s_school_name'] == '') {
                    $carscatelist[$key]['s_school_name'] = '--';
                }

            }
        }
        $lists = array('carscatelist' => $carscatelist, 'page' =>$page, 'count' =>$count);
        return $lists;
    }
    /**
     * 搜索车辆型号
     *
     * @return 	void
     * @author 	wl
     * @date	August 09, 2016
     **/
    public function searchCarsCategory ($param, $school_id) {
        $lists= array();
        $map = array('name' => array('like', '%'.$param['s_keyword'].'%'));
        if ($school_id == 0) {
            $count = $this->table(C('DB_PREFIX').'car_category')
                ->where($map)
                ->fetchSql(false)
                ->count();
            $Page = new Page($count, 10, $param);
            $page = $this->getPage($count, 10, $param);
            $carscatelist = $this->table(C('DB_PREFIX').'car_category c')
                ->join(C('DB_PREFIX').'school s ON s.l_school_id = c.school_id')
                ->where($map)
                ->field('s.l_school_id, s_school_name, c.*')
                ->limit($Page->firstRow.','.$Page->listRows)
                ->order('c.id DESC')
                ->fetchSql(false)
                ->select();
        } else {
            $count = $this->table(C('DB_PREFIX').'car_category')
                ->where($map)
                ->where('school_id = :sid')
                ->bind(['sid' => $school_id])
                ->fetchSql(false)
                ->count();
            $Page = new Page($count, 10, $param);
            $page = $this->getPage($count, 10, $param);
            $carscatelist = $this->table(C('DB_PREFIX').'car_category c')
                ->where($map)
                ->where('school_id = :sid')
                ->bind(['sid' => $school_id])
                ->limit($Page->firstRow.','.$Page->listRows)
                ->order('c.id DESC')
                ->fetchSql(false)
                ->select();
        }
        if (!empty($carscatelist)) {
            foreach ($carscatelist as $key => $value) {
                if ($value['addtime'] != 0) {
                    $carscatelist[$key]['addtime'] = date('Y-m-d H:i:s',$value['addtime']);
                } else {
                    $carscatelist[$key]['addtime'] = '';
                }

                $carscatelist[$key]['point_text_url'] = $this->buildUrl($value['point_text_url']);

                if ($value['s_school_name'] == '') {
                    $carscatelist[$key]['s_school_name'] = '--';
                }

            }
        }
        $lists = array('carscatelist' => $carscatelist, 'page' =>$page, 'count' =>$count);
        return $lists;
    }

    /**
     * 删除车辆型号
     *
     * @return 	void
     * @author 	wl
     * @date 	August 09, 2016
     **/
    public function delCarsCategory ($cid) {
        if (!is_numeric($cid)) {
            return false;
        }
        $result = M('car_category')->where('id = :cid')
            ->bind(['cid' => $cid])
            ->fetchSql(false)
            ->delete();
        return $result;

    }

    /**
     * 根据id获得一条对应的信息
     *
     * @return void
     * @author 	wl
     * @date 	August 09, 2016
     **/
    public function getCarsCategoryById ($cid) {
        if (!is_numeric($cid)) {
            return false;
        }
        $cars_category_list = $this->table(C('DB_PREFIX').'car_category c')//M('car_category')
            ->join(C('DB_PREFIX').'school s ON s.l_school_id = c.school_id')
            ->where('id = :cid')
            ->bind(['cid' => $cid])
            ->field('s.l_school_id, s.s_school_name, c.*')
            ->fetchSql(false)
            ->find();
        if (!empty($cars_category_list)) {
            return $cars_category_list;
        } else {
            return array();
        }

    }

    /**
     * 根据条件判断车两型号是否存在
     *
     * @return 	void
     * @author 	wl
     * @date 	August 09, 2016
     **/
    public function carsListByCondition ($name, $school_id) {
        if (!$name && !$school_id) {
            return false;
        }
        $carscatelist = $this->table(C('DB_PREFIX').'car_category')
            ->where('name = :car_name AND school_id = :sid')
            ->bind(['car_name' => $name, 'sid' => $school_id])
            ->fetchSql(false)
            ->find();
        if (!empty($carscatelist)) {
            return true;
        } else {
            return false;
        }
    }

// 3.学车视频管理

    /**
     * 获取学车视频管理列表
     *
     * @return  void
     * @author  wl
     * @date    Oct 26, 2016
     **/
    public function getLearnVideoList () {
        $count = $this->table(C('DB_PREFIX').'learn_video')
            ->fetchSql(false)
            ->count();
        $Page = new Page($count, 10);
        $page = $this->getPage($count, 10);
        $learnvideolists = array();
        $learnvideolist = $this->table(C('DB_PREFIX').'learn_video')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order('id DESC')
            ->fetchSql(false)
            ->select();
        if (!empty($learnvideolist)) {
            foreach ($learnvideolist as $key => $value) {

                if ($value['stype'] == 1) {
                    $learnvideolist[$key]['stype_name'] = '科目一';

                } elseif ($value['stype'] == 2) {
                    $learnvideolist[$key]['stype_name'] = '科目二';

                } elseif ($value['stype'] == 3) {
                    $learnvideolist[$key]['stype_name'] = '科目三';

                } elseif ($value['stype'] == 4) {
                    $learnvideolist[$key]['stype_name'] = '科目四';

                }

                if ($value['addtime'] != 0) {
                    $learnvideolist[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $learnvideolist[$key]['addtime'] = '';
                }

                if ($value['updatetime'] != 0) {
                    $learnvideolist[$key]['updatetime'] = date('Y-m-d H:i:s', $value['updatetime']);
                } else {
                    $learnvideolist[$key]['updatetime'] = '';
                }

                $learnvideolist[$key]['video_url'] = $this->buildUrl($value['video_url']);

            }
        }
        $learnvideolists = array('learnvideolist' => $learnvideolist, 'count' => $count, 'page' => $page);
        return $learnvideolists;
    }

    /**
     * 学车视频管理列表的搜索
     *
     * @return  void
     * @author  wl
     * @date    Oct 26, 2016
     **/
    public function searchLearnVideo ($param) {
        $map = array();
        if ($param['s_keyword'] != '') {
            $map['title'] = array('like', '%'.$param['s_keyword'].'%');
        }
        $count = $this->table(C('DB_PREFIX').'learn_video')
            ->where($map)
            ->fetchSql(false)
            ->count();
        $Page = new Page($count, 10, $param);
        $page = $this->getPage($count, 10, $param);
        $learnvideolists = array();
        $learnvideolist = $this->table(C('DB_PREFIX').'learn_video')
            ->where($map)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order('id DESC')
            ->fetchSql(false)
            ->select();
        if (!empty($learnvideolist)) {
            foreach ($learnvideolist as $key => $value) {

                if ($value['stype'] == 1) {
                    $learnvideolist[$key]['stype_name'] = '科目一';

                } elseif ($value['stype'] == 2) {
                    $learnvideolist[$key]['stype_name'] = '科目二';

                } elseif ($value['stype'] == 3) {
                    $learnvideolist[$key]['stype_name'] = '科目三';

                } elseif ($value['stype'] == 4) {
                    $learnvideolist[$key]['stype_name'] = '科目四';

                }

                if ($value['addtime'] != 0) {
                    $learnvideolist[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $learnvideolist[$key]['addtime'] = '';
                }

                if ($value['updatetime'] != 0) {
                    $learnvideolist[$key]['updatetime'] = date('Y-m-d H:i:s', $value['updatetime']);
                } else {
                    $learnvideolist[$key]['updatetime'] = '';
                }

                $learnvideolist[$key]['video_url'] = $this->buildUrl($value['video_url']);
            }
        }
        $learnvideolists = array('learnvideolist' => $learnvideolist, 'count' => $count, 'page' => $page);
        return $learnvideolists;
    }

    /**
     * 检查添加的视频是否重复
     *
     * @return  void
     * @author  wl
     * @date    Dec 20, 2016
     **/
    public function checkLearnVideo ($title) {
        if (!trim($title)) {
            return false;
        }
        $checkLearnVideo = $this->table(C('DB_PREFIX').'learn_video')
            ->where('title = :video_title')
            ->bind(['video_title' => $title])
            ->find();
        if (!empty($checkLearnVideo)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 根据id获取单条学车视频
     *
     * @return 	void
     * @author 	wl
     * @date	Oct 27, 2016
     **/
    public function getLearnVideoById ($id) {
        if (!is_numeric($id)) {
            return false;
        }

        $learnvideolist = $this->table(C('DB_PREFIX').'learn_video')
            ->where(array('id' => $id))
            ->fetchSql(false)
            ->find();
        if (!empty($learnvideolist)) {
            return $learnvideolist;
        } else {
            return array();
        }
    }

    /**
     * 设置开启的状态
     *
     * @return 	void
     * @author 	wl
     * @date	Oct 27, 2016
     **/
    public function setOpenStatus ($id, $status) {
        if (!is_numeric($id) || !is_numeric($status)) {
            return false;
        }

        $list = array();
        $data = array('is_open' => $status);
        $result = M('learn_video')
            ->where(array('id' => $id))
            ->data($data)
            ->fetchSql(false)
            ->save();
        $list['id'] = $id;
        $list['res'] = $result;
        return $list;
    }

    /**
     * 删除学车视频
     *
     * @return  void
     * @author  wl
     * @date    Oct 27, 2016
     **/
    public function delLearnVideo ($id) {
        if (!is_numeric($id)) {
            return false;
        }

        $result = M('learn_video')->where(array('id' => $id))
            ->fetchSql(false)
            ->delete();
        return $result;
    }


}
?>
