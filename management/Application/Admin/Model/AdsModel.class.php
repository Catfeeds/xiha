<?php 
namespace Admin\Model;
use Think\Model;
use Think\Page;
use Think\Upload;
class AdsModel extends BaseModel{

// 1.广告订单模块
    /**
     * 获取广告订单列表
     *
     * @return  void
     * @author  wl
     * @date    Nov 21, 2016
     **/
    public function getAdsOrders () {
        $map = array(
            'a.ads_status' => array('neq', 3),
            'o.order_status' => array('neq', 1010)
        );
        $count = $this->table(C('DB_PREFIX').'ads_order o')
            ->join(C('DB_PREFIX').'ads a ON a.id = o.ads_id', 'LEFT')
            ->count();
        $Page = new Page($count, 10);
        $page = $this->getPage($count, 10);
        $adsorders = array();
        $adsorder = $this->table(C('DB_PREFIX').'ads_order o')
            ->join(C('DB_PREFIX').'ads a ON a.id = o.ads_id', 'LEFT')
            ->field('o.*, a.id as sid, a.title, a.intro')
            ->where($map)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->fetchSql(false)
            ->order('id DESC')
            ->select();
        if (!empty($adsorder)) {
            foreach ($adsorder as $key => $value) {
                if ($value['buyer_type'] == 2) {
                    if ($value['buyer_id'] == 0) {
                        $adsorder[$key]['buyer_name'] = '嘻哈平台';
                        $adsorder[$key]['buyer_phone'] = '0551-65653272';
                    }
                }
                if ($value['addtime'] != 0) {
                    $adsorder[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);   
                } else {
                    $adsorder[$key]['addtime'] = '';   
                }
                if ($value['over_time'] != 0) {
                    $adsorder[$key]['over_time'] = date('Y-m-d H:i:s', $value['over_time']);   
                } else {
                    $adsorder[$key]['over_time'] = '';   
                }

                $adsorder[$key]['img_url'] = $this->buildUrl($value['resource_url']);
                
            }
        }
        $adsorders = array('adsorders' => $adsorder, 'count' => $count, 'page' => $page);
        return $adsorders;
    }

    /**
     * 搜索广告订单
     *
     * @return  void
     * @author  wl
     * @date    Nov21, 2106
     **/
    public function searchAdsOrders ($param) {
        $map = array();
        $complex = array();
        $s_keyword = '%'.$param['s_keyword'].'%';
        if ($param['search_info'] == '') {
            $complex['o.id'] = array('EQ', $param['s_keyword']);
            $complex['order_no'] = array('LIKE', $s_keyword);
            $complex['unique_trade_no'] = array('LIKE', $s_keyword);
            $complex['buyer_name'] = array('LIKE', $s_keyword);
            $complex['_logic'] = 'OR';
        } else {
            if ($param['search_info'] == 'id') {
                $param['search_info'] = 'o.id';
                $complex[$param['search_info']] = array('EQ', $param['s_keyword']);

            }
            $complex[$param['search_info']] = array('LIKE', $s_keyword);
        }

        $map['_complex'] = $complex;
        if ($param['device'] != '') {
            $map['device'] = array('EQ', $param['device']);
        }

        if ($param['pay_type'] != '') {
            $map['pay_type'] = array('EQ', $param['pay_type']);
        }

        if ($param['order_status'] != '') {
            $map['o.order_status'] = array('EQ', $param['order_status']);
        } else {
            $map['o.order_status'] = array('neq', 1010);
        }

        if ($param['ads_id'] != '') {
            $map['a.id'] = array('EQ', $param['ads_id']);
        }

        $map['a.ads_status'] = array('neq', 3);
        // if ($param['is_promote'] != '') {
        //     $map['is_promote'] = array('EQ', $param['is_promote']);
        // }
        $count = $this->table(C('DB_PREFIX').'ads_order o')
            ->join(C('DB_PREFIX').'ads a ON a.id = o.ads_id', 'LEFT')
            ->where($map)
            ->count();
        $Page = new Page($count, 10, $param);
        $page = $this->getPage($count, 10, $param);
        $adsorders = array();
        $adsorder = $this->table(C('DB_PREFIX').'ads_order o')
            ->join(C('DB_PREFIX').'ads a ON a.id = o.ads_id', 'LEFT')
            ->where($map)
            ->field('o.*, a.id as sid, a.title, a.intro')
            ->where('ads_status != 1010')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->fetchSql(false)
            ->order('id DESC')
            ->select();
        if (!empty($adsorder)) {
            foreach ($adsorder as $key => $value) {
                if ($value['buyer_type'] == 2) {
                    if ($value['buyer_id'] == 0) {
                        $adsorder[$key]['buyer_name'] = '嘻哈平台';
                        $adsorder[$key]['buyer_phone'] = '0551-65653272';
                    }
                }
                if ($value['addtime'] != 0) {
                    $adsorder[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);   
                } else {
                    $adsorder[$key]['addtime'] = '';   
                }
                if ($value['over_time'] != 0) {
                    $adsorder[$key]['over_time'] = date('Y-m-d H:i:s', $value['over_time']);   
                } else {
                    $adsorder[$key]['over_time'] = '';   
                }
                $adsorder[$key]['img_url'] = $this->buildUrl($value['resource_url']);
            }
        }
        $adsorders = array('adsorders' => $adsorder, 'count' => $count, 'page' => $page);
        return $adsorders;
    }

    /**
     * 设置广告订单的状态
     *
     * @return  void
     * @author  wl
     * @date    Mar 14, 2017
     **/
    public function setAdsOrdersStatus ($id, $status) {
        if (!is_numeric($id) && !is_numeric($status)) {
            return false;
        }
        $where = array('id' => $id);
        $data = array('order_status' => $status);
        $result = M('ads_order')
            ->where($where)
            ->fetchSql(false)
            ->save($data);
        return $result;
    }   

    /**
     * 判断添加的广告订单是否重复
     *
     * @return  void
     * @author  wl
     * @date    Nov 24, 2016
     **/
    public function checkAdsOrders ($ads_id, $buyer_id, $buyer_type, $device) {
        if (!ads_id && !$buyer_id && !$buyer_type && !$device) {
            return false;
        }
        $checkmessage = $this->table(C('DB_PREFIX').'ads_order')
            ->where(array(
                'ads_id' => $ads_id,
                'buyer_id' => $buyer_id,
                'buyer_type' => $buyer_type,
                'device' => $device,
                'order_status' => array('neq', 101)
            ))
            ->fetchSql(false)
            ->find();
        if (!empty($checkmessage)) {
            return $checkmessage;
        } else {
            return array();
        }
    }
    
    
    /**
     *获取单条广告订单信息
     *
     * @return  void
     * @author  wl
     * @date    Nov 22, 2016
     **/
    public function getAdsOrdersById ($id) {
        if (!is_numeric($id)) {
            return false;
        }
        $adsorderslist = $this->table(C('DB_PREFIX').'ads_order o')
            ->join(C('DB_PREFIX').'ads a ON a.id = o.ads_id', 'LEFT')
            ->field('o.*, a.id as sid, a.title, a.intro')
            ->where('ads_status != 3')
            ->where(array('o.id' => $id))
            ->find();
        if (!empty($adsorderslist)) {
            if ($adsorderslist['addtime'] != 0) {
                $adsorderslist['addtime'] = date('Y-m-d H:i:s', $adsorderslist['addtime']);
            } else {
                $adsorderslist['addtime'] = '';
            }

            if ($adsorderslist['over_time'] != 0) {
                $adsorderslist['over_time'] = date('Y-m-d H:i:s', $adsorderslist['over_time']);
            } else {
                $adsorderslist['over_time'] = '';
            }
            if ($adsorderslist['buyer_type'] == 2) {
                if ($adsorderslist['buyer_id'] == 0) {
                    $adsorderslist['buyer_name'] = '嘻哈平台';
                    $adsorderslist['buyer_phone'] = '0551-65653272';
                }
            }
            if ($adsorderslist['resource_url'] == '') {
                $adsorderslist['resource_url'] = '';
            }

        }
        return $adsorderslist;
    }

    /**
     * 上传图片或者视频(添加中用到)
     *
     * @return  void
     * @author  wl
     * @date    Nov 22, 2016
     **/
    public function uploadFiles ($ads_id, $files) {
        if (!is_numeric($ads_id) && !$files) {
            return false;
        }
        $upload = new Upload();
        $upload->maxSize = 10 * 1024 * 1024;
        $upload->exts = array('jpg', 'jpeg', 'gif', 'png', 'mp4', 'mkv', 'wmv', 'mpg', 'rm', 'rmvb', 'mpeg', 'vob');
        $upload->rootPath = '../upload/'; // 设置附件上传根目录
        $upload->savePath = "ads/$ads_id/"; // 设置附件上传（子）目录
        $upload->subName = date('Y-m-d', time()); // Sub Directory
        $upload->saveName = array('uniqid', 'resourceurl_');
        $upload->hash = false;
        $adsurl = $upload->upload();
        if(!$adsurl){
           return $upload->getError(); //获得上传附件产生的错误信息
        }else {
            $url = array();
            foreach ($adsurl as $k => $v) {
                $orgurlsingle = $upload->rootPath.$v['savepath'].$v['savename'];
                $url[]   =  $orgurlsingle; 
            }
            $resource_url = json_encode($url, JSON_UNESCAPED_SLASHES);
            return $resource_url;
        }
    }

    /**
     * 上传图片或者视频(编辑中用到)
     *
     * @return  void
     * @author  wl
     * @date    Nov 22, 2016
     **/
    public function upResourceUrl ($id, $files) {
        if (!is_numeric($id) || !$files) {
            return false;
        }
        $upload = new \Think\Upload();
        // $upload = new Upload();
        $upload->maxSize = 10 * 1024 * 1024;
        $upload->exts = array('jpg', 'jpeg', 'gif', 'png', 'mp4', 'mkv', 'wmv', 'mpg', 'rm', 'rmvb', 'mpeg', 'vob');
        $upload->rootPath = '../upload/'; // 设置附件上传根目录
        $upload->savePath = "ads/$id/"; // 设置附件上传（子）目录
        $upload->subName = date('Y-m-d', time()); // Sub Directory
        $upload->saveName = array('uniqid', 'resourceurl_');
        $upload->hash = false;
        $adsurl = $upload->upload();
        if(!$adsurl){
           return array();
           // return $upload->getError(); //获得上传附件产生的错误信息
        }
        $url = array();
        foreach ($adsurl as $k => $v) {
            $orgurlsingle = $upload->rootPath.$v['savepath'].$v['savename'];
            $url[]   =  $orgurlsingle; 
        }
        return $url;
    }

    /**
     * 更新图片或者视频(编辑中用到)
     *
     * @return  void
     * @author  wl
     * @date    Nov 22, 2016
     **/
    public function updateUrl ($resourceurl, $id) {
        if (!is_numeric($id)) {
            return false;
        }
        $list = array();
        $resourceurllist = $this->table(C('DB_PREFIX').'ads_order')
            ->where(array('id' => $id))
            ->field('resource_url')
            ->find();
        if ($resourceurllist['resource_url'] != null && $resourceurllist['resource_url'] != '' ) {
            $urllist = json_decode($resourceurllist['resource_url'], true);
            $list = array_merge($urllist, $resourceurl);
        } else {
            $list = $resourceurl;
        }
        $url = json_encode($list);
        $data = array(
            'resource_url' => $url,
        );
        $result = M('ads_order')
            ->where(array('id' => $id))
            ->fetchSql(false)
            ->data($data)
            ->save();
        return $result;
    }

    /**
     * 通过买家的id获取买家的电话
     *
     * @return  void
     * @author  wl
     * @date    Nov 22, 2016
     **/
    public function getPhoneByBuyId ($id) {
        if (!is_numeric($id)) {
            return false;
        }
        if ($id == 0) {
            $buyerphone['s_frdb_tel'] = '0551-65653272';
        } else {
          $buyerphone = $this->table(C('DB_PREFIX').'school')
              ->fetchSql(false)
              ->where(array('l_school_id' => $id))
              ->field('s_frdb_tel')
              ->find();
            // ->getField('s_frdb_tel');
        }
        if ($buyerphone) {
            return $buyerphone;
        } else {
          return array();
        }
    }
    /**
     * 通过buyer_id获取buyer_name
     *
     * @return  void
     * @author  wl
     * @date    Nov 22, 2016
     **/
    public function getNameByBuyerId ($id) {
        if (!is_numeric($id)) {
            return false;
        }
        $buyername = $this->table(C('DB_PREFIX').'school')
            ->where(array('l_school_id' => $id))
            ->fetchSql(false)
            ->getfield('s_school_name');
        return $buyername;
    }

    /**
     * 获取广告id和其对应的名称
     *
     * @return  void
     * @author  wl
     * @date    Nov 21, 2016
     **/
    public function getAdsName () {
        $adslist = $this->table(C('DB_PREFIX').'ads')
            ->where('ads_status != 3')
            ->field('id, title, intro')
            ->select();
        if ($adslist) {
            return $adslist;
        } else {
          return array();
        }
    }

    /**
     * 获取买家的id和其对应的名称(buyer_type固定为2)
     *
     * @return  void
     * @author  wl
     * @date    Nov 22, 2016
     **/
    public function getBuyerName ($school_id) {
        if ($school_id == 0) {
            $buyernamelist = $this->table(C('DB_PREFIX').'school s')
                ->fetchSql(false)
                ->field('l_school_id, s_school_name')
                ->select();
            if ($buyernamelist) {
                return $buyernamelist;
            } else {
                array();
            }
        } else {
            $buyernamelist = $this->table(C('DB_PREFIX').'school s')
                ->fetchSql(false)
                ->where(array('l_school_id' => $school_id))
                ->field('l_school_id, s_school_name')
                ->select();
            if ($buyernamelist) {
                return $buyernamelist;
            } else {
                array();
            }
        }
    }

    /**
     * 删除广告订单
     *
     * @return  void
     * @author  wl
     * @date    Nov 21, 2016
     **/
    public function delAdsOrders ($id) {
        if (!is_numeric($id)) {
            return false;
        }

        $result = M('ads_order')->where(array('id' => $id))
            ->fetchSql(false)
            ->save(array('order_status' => 1010));
            // ->delete();
        return $result;
    }


    /**
    * 设置是否在线的状态设置
    *
    * @return  void
    * @author  wl
    * @date    Nov 21, 2016
    **/
    public function setPromoteStatus ($id, $status) {
        if (!$id) {
            return false;
        }
        $list = array();
        $data = array('is_promote' => $status);
        $result = M('ads_order')->where('id = :cid')
                            ->bind(['cid' => $id])
                            ->fetchSql(false)
                            ->data($data)
                            ->save();
        $list['is_promote']  = $result;
        $list['id']         = $id;
        return $list;
    }



// 广告管理模块
    /**
     * 获取广告管理
     *
     * @return  void
     * @author  wl
     * @data    Sep 21, 2016
     **/
    public function getAdsList ($publisher_id) {
        $map = array();
        $map['ads.ads_status'] = array('neq', 3);
        if ($publisher_id != 0) {
            $map['ads.ads_status'] = array('neq', 3);
            $map['ads.publisher_id'] = $publisher_id;
        }
        $count = $this->table(C('DB_PREFIX').'ads ads')
            ->join(C('DB_PREFIX').'ads_info ads_info ON ads_info.ads_id = ads.id', 'LEFT')
            ->join(C('DB_PREFIX').'ads_position ads_position ON ads_position.scene =  ads.scene_id ', 'LEFT')
            ->join(C('DB_PREFIX').'ads_level ads_level ON ads_level.level_id = ads.level_id', 'LEFT')
            ->join(C('DB_PREFIX').'school school ON school.l_school_id = ads.publisher_id', 'LEFT')
            ->where($map)
            ->fetchSql(false)
            ->count();
        $Page = new Page($count, 10);
        $page = $this->getPage($count, 10);
        $ads_infos = array();
        $ads_info = $this->table(C('DB_PREFIX').'ads ads')
            ->field(
                'ads.id as id,
                 ads.scene_id as ads_scene_id,
                 ads.level_id as ads_level_id,
                 ads.title as ads_title,
                 ads.intro as ads_intro,
                 ads.publisher_id as ads_publisher_id,
                 ads.province_id as province_id,
                 ads.city_id as city_id,
                 ads.area_id as area_id,
                 ads.limit_time as limit_time,
                 ads.ads_status as ads_status,
                 ads.sort_order as sort_order,
                 ads.limit_num as limit_num,
                 ads.addtime as ads_addtime,
                 ads_info.device as device,
                 ads_info.resource_type as resource_type,
                 ads_position.description as description,
                 ads_position.scene as scene,
                 ads_position.title as position_title,
                 ads_level.level_id as level_id,
                 ads_level.level_title as level_title,
                 ads_level.level_intro as level_intro,
                 ads_level.level_money as level_money,
                 ads_level.loop_time as loop_time,
                 school.s_school_name as s_school_name'
            )
            ->join(C('DB_PREFIX').'ads_info ads_info ON ads_info.ads_id = ads.id', 'LEFT')
            ->join(C('DB_PREFIX').'ads_position ads_position ON ads_position.scene =  ads.scene_id ', 'LEFT')
            ->join(C('DB_PREFIX').'ads_level ads_level ON ads_level.level_id = ads.level_id', 'LEFT')
            ->join(C('DB_PREFIX').'school school ON school.l_school_id = ads.publisher_id', 'LEFT')
            ->where($map)
            ->order('sort_order ASC, ads.id DESC')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->fetchSql(false)
            ->select();
        if (!empty($ads_info)) {
            $orderCondition = array();
            foreach ($ads_info as $key => $value) {
                $ads_info[$key]['id'] = $value['id'];
                $orderCondition = array(
                    'ads_id' => $value['id'],
                    'order_status' => array('neq', 101)
                );
                $ads_info[$key]['order_number'] = $this->table(C('DB_PREFIX').'ads_order')
                    ->where( $orderCondition )
                    ->fetchSql(false)
                    ->count();
                if ($value['province_id'] != '') {
                    $province = $this->table(C('DB_PREFIX').'province province')
                        ->where(array('province.provinceid' => $value['province_id']))
                        ->fetchSql(false)
                        ->getField('province');
                    if ( !empty( $province ) ) {
                        $ads_info[$key]['province'] = $province;
                    } else {
                        $ads_info[$key]['province'] = '';
                    }
                } else {
                    $ads_info[$key]['province'] = '';
                }

                if ($value['city_id'] != '') {
                    $city = $this->table(C('DB_PREFIX').'city city')
                        ->where(array('city.cityid' => $value['city_id']))
                        ->fetchSql(false)
                        ->getField('city');
                    if ( !empty( $city ) ) {
                        $ads_info[$key]['city'] = $city;
                    } else {
                        $ads_info[$key]['city'] = '';
                    }
                } else {
                    $ads_info[$key]['city'] = '';
                }

                if ($value['area_id'] != '') {
                    $area = $this->table(C('DB_PREFIX').'area area')
                        ->where(array('area.areaid' => $value['area_id']))
                        ->fetchSql(false)
                        ->getField('area');
                    if ( !empty( $area ) ) {
                        $ads_info[$key]['area'] = $area;
                    } else {
                        $ads_info[$key]['area'] = '';
                    }
                } else {
                    $ads_info[$key]['area'] = '';
                }

                if ($value['ads_addtime'] != 0) {
                    $ads_info[$key]['add_time'] = date('Y-m-d H:i:s', $value['ads_addtime']);
                } else {
                    $ads_info[$key]['add_time'] = '';
                }

                switch ($value['resource_type'] != 0) {
                    case 1:
                      $ads_info[$key]['resource_type_name'] = '图片';
                      break;
                    
                    case 2:
                      $ads_info[$key]['resource_type_name'] = '视频';
                      break;
                    default:
                      $ads_info[$key]['resource_type_name'] = '图片';
                      break;
                }
                if ($value['s_school_name'] == '') {
                    $ads_info[$key]['s_school_name'] = '嘻哈平台';
                }
                if ($value['title'] == '') {
                    $ads_info[$key]['title'] = '--';
                }
              
            }
            $ads_infos = array('ads_info' => $ads_info, 'page' => $page, 'count' => $count);
            return $ads_infos;
        }
    }

    /**
     * 获取单条广告信息
     *
     * @return  void
     * @author  wl
     * @date    Sep 23, 2016
     **/
    public function getAdsListById ($id) {
        if (!$id) {
            return false;
        }
        $ads_info = $this->table(C('DB_PREFIX').'ads ads')
            ->field(
                'ads.id as ads_id,
                 ads.scene_id as ads_scene_id,
                 ads.level_id as ads_level_id,
                 ads.title as ads_title,
                 ads.intro as ads_intro,
                 ads.publisher_id as ads_publisher_id,
                 ads.province_id as province_id,
                 ads.city_id as city_id,
                 ads.area_id as area_id,
                 ads.limit_time as limit_time,
                 ads.ads_status as ads_status,
                 ads.sort_order as sort_order,
                 ads.limit_num as limit_num,
                 ads.addtime as ads_addtime,
                 ads_info.id as ads_info_id,
                 ads_info.device as device,
                 ads_info.resource_type as resource_type,
                 ads_position.id as position_id,
                 ads_position.description as description,
                 ads_position.scene as scene,
                 ads_position.title as position_title,
                 ads_level.level_id as level_id,
                 ads_level.level_title as level_title,
                 ads_level.level_intro as level_intro,
                 ads_level.level_money as level_money,
                 ads_level.loop_time as loop_time'
            )
            ->join(C('DB_PREFIX').'ads_info ads_info ON ads_info.ads_id = ads.id', 'LEFT')
            ->join(C('DB_PREFIX').'ads_position ads_position ON ads_position.scene =  ads.scene_id ', 'LEFT')
            ->join(C('DB_PREFIX').'ads_level ads_level ON ads_level.level_id = ads.level_id', 'LEFT')
            ->where(array('ads.id' => $id))
            ->find();
        if (!empty($ads_info)) {
            if ($ads_info['province_id'] != '') {
                $province = $this->table(C('DB_PREFIX').'province province')
                    ->where(array('province.provinceid' => $ads_info['province_id']))
                    ->fetchSql(false)
                    ->getField('province');
                if ( !empty( $province ) ) {
                    $ads_info['province'] = $province;
                } else {
                    $ads_info['province'] = '';
                }
            } else {
                $ads_info['province'] = '';
            }

            if ($ads_info['city_id'] != '') {
                $city = $this->table(C('DB_PREFIX').'city city')
                    ->where(array('city.cityid' => $ads_info['city_id']))
                    ->fetchSql(false)
                    ->getField('city');
                if ( !empty( $city ) ) {
                    $ads_info['city'] = $city;
                } else {
                    $ads_info['city'] = '';
                }
            } else {
                $ads_info['city'] = '';
            }

            if ($ads_info['area_id'] != '') {
                $area = $this->table(C('DB_PREFIX').'area area')
                    ->where(array('area.areaid' => $ads_info['area_id']))
                    ->fetchSql(false)
                    ->getField('area');
                if ( !empty( $area ) ) {
                    $ads_info['area'] = $area;
                } else {
                    $ads_info['area'] = '';
                }
            } else {
                $ads_info['area'] = '';
            }
            return $ads_info;  
        } else {
            return array();
        }
    }

    /**
     * 检查ads_info中的ads_id是否重复
     *
     * @return  void
     * @author  wl
     * @date    Mar 14, 2017
     **/
    public function checkAdsId ($ads_id) {
        if (!is_numeric($ads_id)) {
            return false;
        }
        $where = array(
            'ads_id' => $ads_id,
            'ads_status' => array('neq', 3)
        );
        $checkAdsId = $this->table(C('DB_PREFIX').'ads_info')
            ->where($where)
            ->find();
        if (!empty($checkAdsId)) {
            return false;
        } else {
            return true;
        }

    }


    /**
     * 删除广告管理单条
     *
     * @return  void
     * @author  wl
     * @date    Sep 23, 2016
     **/
    public function delAdsManage ($id) {
        if (!id) {
            return false;
        }
        $result = M('ads')->where(array('id' => $id))
            ->fetchSql(false)
            ->save(array('ads_status' => 3));
            // ->delete();
        return $result;
    }
    
    /**
     * 更新排序的内容
     *
     * @return  void
     * @author  wl
     * @date    Sep 24, 2016
     **/
    public function updateAdsOrder ($post) {
      if (empty($post)) {
        return 101;// 参数错误
      }
      if (isset($post['sort_order'])) {
          if (!is_numeric($post['sort_order'])) {
            return 102; // 参数类型不符合
          } else {
              $old_num = $this->table(C('DB_PREFIX').'ads')
                              ->where('id = :aid')
                              ->bind(['aid' => $post['id']])
                              ->getField('sort_order');
              if ($post['sort_order'] == $old_num) {
                return 105; // 未做任何修改
              } 
          }
      }
      $data['sort_order'] = $post['sort_order'];
      $ads = D('ads');
      if ($res = $ads->create($data)) {
          $result = $ads->where(array('id' => $post['id']))
                        ->fetchSql(false)
                        ->save($res);
          // var_dump($result);exit;
          if ($result) {
              return 200;
          } else {
              return 400;
          }
      }

    }



    //改变广告状态(逻辑删除)
    public function changeAdsStatus($id, $status) {
      $data['ads_status'] = $status;
      $del = $this->where(array('id'=>$id))->save($data);
      if ($del) {
        return true;
      } else {
        return false;
      }
    }

} /* class End */
