<?php 
namespace Admin\Model;
use Think\Model;
use Think\Page;

/**
 * 优惠卷管理模型
 *
 * @author wl
 **/
class CoinModel extends BaseModel{
    public $tableName = 'coin_goods';

// 1、金币商城商品管理模块
    /**
     * 获取金币商城商品信息
     *
     * @return  void
     * @author  wl
     * @date    Oct 14, 2016
     **/
    public function getCoinGoodsList () {
        $count = $this->table(C('DB_PREFIX').'coin_goods c')
            ->join(C('DB_PREFIX').'coingoods_category cc ON cc.id = c.cate_id', 'LEFT')
            ->fetchSql(false)
            ->count();
        $Page = new Page($count, 10);
        $page = $this->getPage($count, 10);
        $coingoodslists = array();
        $coingoodslist = $this->table(C('DB_PREFIX').'coin_goods c')
            ->field(
                'c.*, 
                 cc.cate_name, 
                 cc.id as cateid'
            )
            ->join(C('DB_PREFIX').'coingoods_category cc ON cc.id = c.cate_id', 'LEFT')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order('c.order ASC, c.id DESC')
            ->fetchSql(false)
            ->select();
        if ($coingoodslist) {
            foreach ($coingoodslist as $key => $value) {

                $goods_desc = str_replace('&lt;', '<', $value['goods_desc']);
                $goods_desc = str_replace('&gt;', '>', $goods_desc);
                $goods_desc = strip_tags($goods_desc);
                $coingoodslist[$key]['goods_desc'] = $goods_desc;

                $goods_detail = str_replace('&lt;', '<', $value['goods_detail']);
                $goods_detail = str_replace('&gt;', '>', $goods_detail);
                $goods_detail = strip_tags($goods_detail);
                $coingoodslist[$key]['goods_detail'] = $goods_detail;

                if ($value['goods_expiretime'] != 0) {
                    $coingoodslist[$key]['goods_expiretime'] = date('Y-m-d H:i:s', $value['goods_expiretime']);
                } else {
                    $coingoodslist[$key]['goods_expiretime'] = '';
                }

                if ($value['addtime'] != 0) {
                    $coingoodslist[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $coingoodslist[$key]['addtime'] = '';
                }
                if ($value['updatetime'] != 0) {
                    $coingoodslist[$key]['updatetime'] = date('Y-m-d H:i:s', $value['updatetime']);
                } else {
                    $coingoodslist[$key]['updatetime'] = '';
                }

            }
        }
        $coingoodslists = array('coingoodslist' => $coingoodslist, 'page' => $page, 'count' => $count);
        return $coingoodslists;
    }
     /**
     * 搜索金币商城商品信息
     *
     * @return  void
     * @author  wl
     * @date    Oct 14, 2016
     **/
    public function searchCoinGoods ($param) {
        $map = array();
        $complex = array();
        $s_keyword = '%'.$param['s_keyword'].'%';
        if ($param['search_info'] == '') {
            $complex['goods_name'] = array('like', $s_keyword);
            $complex['cate_name'] = array('like', $s_keyword);
            $complex['_logic'] = 'OR';
        } else {
            $complex[$param['search_info']] = array('like', $s_keyword);
        }
        $map['_complex'] = $complex;

        if ($param['is_hot'] != '') {
            $map['is_hot'] = array('eq', $param['is_hot']);
        }

        if ($param['is_recommend'] != '') {
            $map['is_recommend'] = array('eq', $param['is_recommend']);
        }

        if ($param['is_promote'] != '') {
            $map['is_promote'] = array('eq', $param['is_promote']);
        }

        if ($param['is_publish'] != '') {
            $map['is_publish'] = array('eq', $param['is_publish']);
        }

        if ($param['is_deleted'] != '') {
            $map['is_deleted'] = array('eq', $param['is_deleted']);
        }

        $count = $this->table(C('DB_PREFIX').'coin_goods c')
            ->join(C('DB_PREFIX').'coingoods_category cc ON cc.id = c.cate_id', 'LEFT')
            ->where($map)
            ->fetchSql(false)
            ->count();
        $Page = new Page($count, 10);
        $page = $this->getPage($count, 10);
        $coingoodslists = array();
        $coingoodslist = $this->table(C('DB_PREFIX').'coin_goods c')
            ->field(
                'c.*, 
                 cc.cate_name, 
                 cc.id as cateid'
            )
            ->join(C('DB_PREFIX').'coingoods_category cc ON cc.id = c.cate_id', 'LEFT')
            ->where($map)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order('c.order ASC, c.id DESC')
            ->fetchSql(false)
            ->select();
        if ($coingoodslist) {
            foreach ($coingoodslist as $key => $value) {

                $goods_desc = str_replace('&lt;', '<', $value['goods_desc']);
                $goods_desc = str_replace('&gt;', '>', $goods_desc);
                $goods_desc = strip_tags($goods_desc);
                $coingoodslist[$key]['goods_desc'] = $goods_desc;

                $goods_detail = str_replace('&lt;', '<', $value['goods_detail']);
                $goods_detail = str_replace('&gt;', '>', $goods_detail);
                $goods_detail = strip_tags($goods_detail);
                $coingoodslist[$key]['goods_detail'] = $goods_detail;

                if ($value['goods_expiretime'] != 0) {
                    $coingoodslist[$key]['goods_expiretime'] = date('Y-m-d H:i:s', $value['goods_expiretime']);
                } else {
                    $coingoodslist[$key]['goods_expiretime'] = '';
                }
                if ($value['addtime'] != 0) {
                    $coingoodslist[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $coingoodslist[$key]['addtime'] = '';
                }
                if ($value['updatetime'] != 0) {
                    $coingoodslist[$key]['updatetime'] = date('Y-m-d H:i:s', $value['updatetime']);
                } else {
                    $coingoodslist[$key]['updatetime'] = '';
                }

            }
        }
        $coingoodslists = array('coingoodslist' => $coingoodslist, 'page' => $page, 'count' => $count);
        return $coingoodslists;
    }

    /**
     * 删除商品信息
     *
     * @return  void
     * @author  wl
     * @date    Oct 14, 2016
     **/
    public function delCoinGoods ($id) {
        if (!is_numeric($id)) {
            return false;
        }
        $coinCategory = M('coin_goods');
        $res = $coinCategory->where(array('id' => $id))
            ->fetchSql(false)
            ->delete(); 
        return $res;
    }

    /**
    * 设置商品的排序状态
    *
    * @return  void
    * @author  wl
    * @date    Oct 14, 2016
    **/
    public function updateCoinGoodsOrder ($post) {
        if (empty($post)) {
            return 101; // 参数错误
        }
        if (isset($post['order'])) {
            if (!is_numeric($post['order'])) {
                return 102; //参数类型错误
            } else {
                $old_num = $this->table(C('DB_PREFIX').'coin_goods')
                    ->where('id = :cid')
                    ->bind(['cid' => $post['id']])
                    ->getField('order');
                if ($post['order'] === $old_num) {
                    return 105; // 尚未做任何修改
                }
            }
        }

        $data['order'] = $post['order'];
        $coinGoodsCate = D('coin_goods');
        if ($res = $coinGoodsCate->create($data)) {
            $result = $coinGoodsCate->where('id = :cid')
                ->bind(['cid' => $post['id']])
                ->fetchSql(false)
                ->save($res);
            if ($result) {
                return 200;
            } else {
                return 400;
            }
        }
    }
    /**
     * 设置商品热销状态
     *
     * @return  void
     * @author  wl
     * @date    Oct 14, 2016
     **/
    public function setHotStatus ($id, $status) {
        if (!is_numeric($id) || !isset($status)) {
            return false;
        }

        $list = array();
        $data = array('is_hot'=>$status);
        $result = $this->table(C('DB_PREFIX').'coin_goods')
            ->where(array('id' => $id))
            ->save($data);
        $list['id']  = $id;
        $list['res'] = $result;
        return $list;
    }

    /**
     * 设置商品推荐的状态
     *
     * @return  void
     * @author  wl
     * @date    Oct 14, 2016
     **/
    public function setRecommendStatus ($id, $status) {
        if (!is_numeric($id) || !isset($status)) {
            return false;
        }

        $list = array();
        $data =array('is_recommend'=>$status);
        $result = $this->table(C('DB_PREFIX').'coin_goods')
            ->where(array('id' => $id))
            ->save($data);
        $list['id']  = $id;
        $list['res'] = $result;
        return $list;
    }
    /**
     * 设置商品促销的状态
     *
     * @return  void
     * @author  wl
     * @date    Oct 14, 2016
     **/
    public function setPromoteStatus ($id, $status) {
        if (!is_numeric($id) || !isset($status)) {
            return false;
        }

        $list = array();
        $data =array('is_promote'=>$status);
        $result = $this->table(C('DB_PREFIX').'coin_goods')
            ->where(array('id' => $id))
            ->save($data);
        $list['id']     = $id;
        $list['res'] = $result;
        return $list;
    }
    /**
     * 设置商品发布的状态
     *
     * @return  void
     * @author  wl
     * @date    Oct 14, 2016
     **/
    public function setPublishStatus ($id, $status) {
        if (!is_numeric($id) || !isset($status)) {
            return false;
        }
        $list = array();
        $data =array('is_publish'=>$status);
        $result = $this->table(C('DB_PREFIX').'coin_goods')
            ->where(array('id' => $id))
            ->save($data);
        $list['id']     = $id;
        $list['res'] = $result;
        return $list;
    }
    /**
     * 设置商品上下架状态
     *
     * @return  void
     * @author  wl
     * @date    2017-05-16
     **/
    public function setDeletedStatus ($id, $status) {
        if (!is_numeric($id) || !isset($status)) {
            return false;
        }

        $list = array();
        $data =array('is_deleted' => $status);
        $result = $this->table(C('DB_PREFIX').'coin_goods')
            ->where(array('id' => $id))
            ->save($data);
        $list['id']  = $id;
        $list['res'] = $result;
        return $list;
    }

    /**
     * 获取金币商城金币分类中的分类id和分类名称
     *
     * @return  void
     * @author  wl
     * @date    Oct 15, 2016
     **/
    public function getCoinCateName () {
        $coincategorylist = $this->table(C('DB_PREFIX').'coingoods_category ')
            ->field('id, cate_name')
            ->fetchSql(false)
            ->select();
        return $coincategorylist;

    }
    /**
     * 获取单条的金币商城商品信息
     *
     * @return  void
     * @author  wl
     * @date    Oct 15, 2016
     **/
    public function getCoinGoodsById ($id) {
        if (!is_numeric($id)) {
            return false;
        }

        $coingoodslist = $this->table(C('DB_PREFIX').'coin_goods c')
            ->join(C('DB_PREFIX').'coingoods_category cc ON cc.id = c.cate_id', 'LEFT')
            ->where(array('c.id' => $id))
            ->field('c.*, cc.cate_name, cc.id as cateid')
            ->fetchSql(false)
            ->find();
        if ($coingoodslist) {
            if ($coingoodslist['goods_expiretime'] != 0) {
                    $coingoodslist['goods_expiretime'] = date('Y-m-d H:i:s', $coingoodslist['goods_expiretime']);
            } else {
                $coingoodslist['goods_expiretime'] = '';
            }
            if ($coingoodslist['addtime'] != 0) {
                $coingoodslist['addtime'] = date('Y-m-d H:i:s', $coingoodslist['addtime']);
            } else {
                $coingoodslist['addtime'] = '';
            }
        }
        return $coingoodslist;

    }



// 2、金币商城商品分类管理模块
    /**
     * 获取金币商城商品分类管理
     *
     * @return  void
     * @author  wl
     * @date    Oct 14, 2016
     **/
    public function getCoinCategoryList () {
        $count = $this->table(C('DB_PREFIX').'coingoods_category cc')
            ->fetchSql(false)
            ->count();
        $Page = new Page($count, 10);
        $page = $this->getPage($count, 10);
        $coincategorylist = $this->table(C('DB_PREFIX').'coingoods_category cc')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order('cc.order ASC, cc.id DESC')
            ->fetchSql(false)
            ->select();

        $coincategorylists = array();
        if ($coincategorylist) {
            foreach ($coincategorylist as $key => $value) {

                if ($value['addtime'] != 0) {
                    $coincategorylist[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $coincategorylist[$key]['addtime'] = '--';
                }

                if ($value['updatetime'] != 0) {
                    $coincategorylist[$key]['updatetime'] = date('Y-m-d H:i:s', $value['updatetime']);
                } else {
                    $coincategorylist[$key]['updatetime'] = '--';
                }

            }

        }
        $coincategorylists = array('coincategorylist' => $coincategorylist, 'page' => $page, 'count' => $count);
        return $coincategorylists;
    }

    /**
     * 搜索商品分类信息
     *
     * @return  void
     * @author  wl
     * @date    Oct 14, 2016
     **/
    public function searchCoinCategory ($param) {
        $map = array();
        $complex = array();
        $s_keyword = '%'.$param['s_keyword'].'%';
        $map['cate_name'] = array('like', $s_keyword);
        /*
        if ($param['search_info'] == '') {
            $complex['order'] = array('like', $s_keyword);
            $complex['cate_name'] = array('like', $s_keyword);
            $complex['cate_desc'] = array('like', $s_keyword);
            $complex['id'] = array('like', $s_keyword);
            $complex['_logic'] = 'OR';
        } else {
            $complex[$param['search_info']] = array('like', $s_keyword);
        }
        $map['_complex'] = $complex;
        */
        
        $count = $this->table(C('DB_PREFIX').'coingoods_category cc')
            ->where($map)
            ->fetchSql(false)
            ->count();
        $Page = new Page($count, 10, $param);
        $page = $this->getPage($count, 10, $param);
        $coincategorylists = array();
        $coincategorylist = $this->table(C('DB_PREFIX').'coingoods_category cc')
            ->where($map)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order('cc.order ASC, cc.id DESC')
            ->fetchSql(false)
            ->select();
        if ($coincategorylist) {
            foreach ($coincategorylist as $key => $value) {

               if ($value['addtime'] != 0) {
                    $coincategorylist[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $coincategorylist[$key]['addtime'] = '--';
                }

                if ($value['updatetime'] != 0) {
                    $coincategorylist[$key]['updatetime'] = date('Y-m-d H:i:s', $value['updatetime']);
                } else {
                    $coincategorylist[$key]['updatetime'] = '--';
                }
                
            }
        }
        $coincategorylists = array('coincategorylist' => $coincategorylist, 'page' => $page, 'count' => $count);
        return $coincategorylists;
    }

    
    /**
     * 删除商品分类信息
     *
     * @return  void
     * @author  wl
     * @date    Oct 14, 2016
     **/
    public function delCoinGoodsCategory ($id) {
        if (!is_numeric($id)) {
            return false;
        }
        $coinCategory = M('coingoods_category');
        $res = $coinCategory->where(array('id' => $id))
            ->fetchSql(false)
            ->delete(); 
        return $res;
    }


    
    /**
    * 设置商品分类的排序状态
    *
    * @return  void
    * @author  wl
    * @date    Oct 14, 2016
    **/
    public function updateCoinCateOrder ($post) {
        if (empty($post)) {
            return 101; // 参数错误
        }
        if (isset($post['order'])) {
            if (!is_numeric($post['order'])) {
                return 102; //参数类型错误
            } else {
                $old_num = $this->table(C('DB_PREFIX').'coingoods_category')
                    ->where('id = :cid')
                    ->bind(['cid' => $post['id']])
                    ->getField('order');
                if ($post['order'] === $old_num) {
                    return 105; // 尚未做任何修改
                }
            }
        }

        $data['order'] = $post['order'];
        $coinGoodsCate = D('coingoods_category');
        if ($res = $coinGoodsCate->create($data)) {
            $result = $coinGoodsCate->where('id = :cid')
                ->bind(['cid' => $post['id']])
                ->fetchSql(false)
                ->save($res);
            if ($result) {
                return 200;
            } else {
                return 400;
            }
        }
    }
    /**
     * 获取单条商品分类信息
     *
     * @return  void
     * @author  wl
     * @date    Oct 14, 2016
     **/
    public function getCoinCateListById ($id) {
        if (!is_numeric($id)) {
            return false;
        }
        $coincategorylist = $this->table(C('DB_PREFIX').'coingoods_category')
            ->where('id = :cid')
            ->bind(['cid' => $id])
            ->fetchSql(false)
            ->find();
        if ($coincategorylist) {
            $coincategorylist['addtime'] = date('Y-m-d H:i:s', $coincategorylist['addtime']);
        }
        return $coincategorylist;

    }

// 3、金币商城规则管理

    /**
     * 获取金币商城规则管理的信息
     *
     * @return  void
     * @author  wl
     * @date    Oct 17, 2016
     **/
    public function getCoinRuleList () {
        $count = $this->table(C('DB_PREFIX').'coin_rule')
            ->fetchSql(false)
            ->count();
        // var_dump($count);exit;
        $Page = new Page($count, 10);
        $page = $this->getPage($count, 10);
        $coinrulelists = array();
        $coinrulelist = $this->table(C('DB_PREFIX').'coin_rule')
            ->order('id DESC')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->fetchSql(false)
            ->select();
        if ($coinrulelist) {
            foreach ($coinrulelist as $key => $value) {

                if ($value['rule_starttime'] != '' && $value['rule_starttime'] != 0) {
                    $coinrulelist[$key]['rule_starttime'] = date('Y-m-d H:i:s', $value['rule_starttime']);
                } else {
                    $coinrulelist[$key]['rule_starttime'] = '--';
                }

                if ($value['rule_endtime'] != '' && $value['rule_endtime'] != 0) {
                    $coinrulelist[$key]['rule_endtime'] = date('Y-m-d H:i:s', $value['rule_endtime']);
                } else {
                    $coinrulelist[$key]['rule_endtime'] = '--';
                }

                if ($value['addtime'] != '' && $value['addtime'] != 0) {
                    $coinrulelist[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $coinrulelist[$key]['addtime'] = '--';
                }
            }
        }
        $coinrulelists = array('coinrulelist' => $coinrulelist, 'page' => $page, 'count' => $count);
        return $coinrulelists;
    } 

    /**
    * 搜索商品规则信息
    *
    * @return  void
    * @author  wl
    * @date    Oct 17, 2016
    **/
    public function searchCoinRule ($param) {
        $map = array();
        $s_keyword = '%'.$param['s_keyword'].'%';
        if ($param['s_keyword'] != '') {
            $map['title'] = array('like', $s_keyword);
        }

        $count = $this->table(C('DB_PREFIX').'coin_rule')
            ->where($map)
            ->fetchSql(false)
            ->count();
        $Page = new Page($count, 10, $param);
        $page = $this->getPage($count, 10, $param);
        $coinrulelists = array();
        $coinrulelist = $this->table(C('DB_PREFIX').'coin_rule')
            ->where($map)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order('id DESC')
            ->fetchSql(false)
            ->select();
        if ($coinrulelist) {
            foreach ($coinrulelist as $key => $value) {
                if ($value['rule_starttime'] != '' && $value['rule_starttime'] != 0) {
                    $coinrulelist[$key]['rule_starttime'] = date('Y-m-d H:i:s', $value['rule_starttime']);
                } else {
                    $coinrulelist[$key]['rule_starttime'] = '--';
                }

                if ($value['rule_endtime'] != '' && $value['rule_endtime'] != 0) {
                    $coinrulelist[$key]['rule_endtime'] = date('Y-m-d H:i:s', $value['rule_endtime']);
                } else {
                    $coinrulelist[$key]['rule_endtime'] = '--';
                }

                if ($value['addtime'] != '' && $value['addtime'] != 0) {
                    $coinrulelist[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $coinrulelist[$key]['addtime'] = '--';
                }
            }
        }
        $coinrulelists = array('coinrulelist' => $coinrulelist, 'page' => $page, 'count' => $count);
        return $coinrulelists;

    }
    /**
     * 获取单条金币规则信息
     *
     * @return  void
     * @author  wl
     * @date    Oct 17, 2016
     **/
    public function getCoinRuleListById ($id) {
        if (!is_numeric($id)) {
            return false;
        }
        $coinrulelist = $this->table(C('DB_PREFIX').'coin_rule')
            ->where('id = :cid')
            ->bind(['cid' => $id])
            ->find();
        if ($coinrulelist) {
            if ($coinrulelist['rule_starttime'] != '') {
                $coinrulelist['rule_starttime'] = date('Y-m-d H:i:s', $coinrulelist['rule_starttime']);
            } else {
                $coinrulelist['rule_starttime'] = '';
            }

            if ($coinrulelist['rule_endtime'] != '') {
                $coinrulelist['rule_endtime'] = date('Y-m-d H:i:s', $coinrulelist['rule_endtime']);
            } else {
                $coinrulelist['rule_endtime'] = '';
            }

            if ($coinrulelist['addtime'] != '') {
                $coinrulelist['addtime'] = date('Y-m-d H:i:s', $coinrulelist['addtime']);
            } else {
                $coinrulelist['addtime'] = '';
            }
        }
        return $coinrulelist;
    }

    /**
     * 删除金币规则信息
     *
     * @return  void
     * @author  wl
     * @date    Oct 17, 2016
     **/
    public function delCoinRule ($id) {
        if (!is_numeric($id)) {
            return false;
        }
        $coinRule = M('coin_rule');
        $res = $coinRule->where(array('id' => $id))
            ->fetchSql(false)
            ->delete(); 
        return $res;
    }

// 金币兑换记录管理
    /**
     * 获取金币兑换记录列表的展示
     *
     * @return  void
     * @author  wl
     * @date    Oct 17, 2016
     **/
    public function getExchangeOrders () {
        $count = $this->table(C('DB_PREFIX').'exchange_orders e')
            ->count();
        $Page = new Page($count, 10);
        $page = $this->getPage($count, 10);
        $exchangeorders = array();
        $exchangeorder = $this->table(C('DB_PREFIX').'exchange_orders e')
            ->field('e.*')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order('e.id DESC')
            ->select();
        if ($exchangeorder) {
            foreach ($exchangeorder as $key => $value) {

                if ($value['addtime'] != '' && $value['addtime'] != 0) {
                    $exchangeorder[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']); 
                } else {
                    $exchangeorder[$key]['addtime'] = '--'; 
                }

            }
        }

        $exchangeorders = array('exchangeorder' => $exchangeorder, 'page' => $page, 'count' => $count);
        return $exchangeorders;
    }

    /**
     * 搜索金币兑换记录信息
     *
     * @return  void
     * @author  wl
     * @date    Oct 17, 2016
     **/
     public function searchExchangeOrders ($param) {
        $map = array();
        $complex = array();
         $s_keyword = '%'.$param['s_keyword'].'%';
        if ($param['search_info'] == '') {
            $complex['goods_name'] = array('like', $s_keyword);
            $complex['mch_name'] = array('like', $s_keyword);
            $complex['exchange_no'] = array('like', $s_keyword);
            $complex['_logic'] = 'OR';
        } else {
            $complex[$param['search_info']] = array('like', $s_keyword);
        }

        $map['_complex'] = $complex;

        if ($param['exchange_status'] != 0) {
            $map['exchange_status'] = array('EQ', $param['exchange_status']);
        }

        if ($param['pay_status'] != 0) {
            $map['pay_status'] = array('EQ', $param['pay_status']);
        }

        $count = $this->table(C('DB_PREFIX').'exchange_orders e')
            ->where($map)
            ->fetchSql(false)
            ->count();
        $Page = new Page($count, 10, $param);
        $page = $this->getPage($count, 10, $param);
        $exchangeorders = array();
        $exchangeorder = $this->table(C('DB_PREFIX').'exchange_orders e')
            ->where($map)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order('e.id DESC')
            ->select();
        if ($exchangeorder) {
            foreach ($exchangeorder as $key => $value) {

                if ($value['addtime'] != '' && $value['addtime'] != 0) {
                    $exchangeorder[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']); 
                } else {
                    $exchangeorder[$key]['addtime'] = '--'; 
                }

            }
        }

        $exchangeorders = array('exchangeorder' => $exchangeorder, 'page' => $page, 'count' => $count);
        return $exchangeorders;
     }

     /**
     * 删除金币兑换记录信息
     *
     * @return  void
     * @author  wl
     * @date    Oct 17, 2016
     **/
    public function delExchangeOrders ($id) {
        if (!is_numeric($id)) {
            return false;
        }
        $coinRule = M('exchange_orders');
        $res = $coinRule->where(array('id' => $id))
            ->fetchSql(false)
            ->delete(); 
        return $res;
    }

// 5、商品轮播图管理模块

    /**
     * 获取商品名称
     *
     * @return  void
     * @author  wl
     * @date    Oct 18, 2016
     **/
    public function getGoodsNameList () {
        $goodslist = $this->table(C('DB_PREFIX').'coin_goods')
            ->field('id, goods_name')
            ->fetchSql(false)
            ->select();
        return $goodslist;
    }

    /**
     * 商品轮播图展示
     *
     * @return  void
     * @author  wl
     * @date    Oct 18, 2016
     **/
    public function getBannerList ($goods_id) {
        if (!is_numeric($goods_id)) {
            return false;
        }
        $bannerlist = $this->table(C('DB_PREFIX').'coin_goods')
            ->where('id = :cid')
            ->bind(['cid' => $goods_id])
            ->getField('goods_images_url');
        if ($bannerlist != '' && $bannerlist != null) {
            $banner_list = array_values((array)json_decode($bannerlist, true));
            $bannerlists = array();
            if (is_array($banner_list)) {
                foreach ($banner_list as $key => $value) {
                    $bannerlists[$key]['goods_all_imgurl'] = C('HTTP_HOST').$value;
                    if (empty($bannerlists[$key]['goods_all_imgurl'])) {
                        $bannerlists[$key]['goods_all_imgurl'] = C('HTTP_SHOST').$value;
                    }
                    $bannerlists[$key]['goods_images_url'] = $value;
                }
            }
            return $bannerlists;
        } else {
            return array();
        }

    }

    /**
     * 获得添加的图片的路径
     *
     * @return  void
     * @author  wl
     * @date    August 06, 2016
     * @update  August 19, 2016
     **/
    public function getBannerUrl ($goods_id, $files) {
        if (!is_numeric($goods_id) || !$files) {
            return false;
        }

        // Upload: 1. create upload object
        $upload = new \Think\Upload();

        // Upload: 2. config upload options
        $upload->maxSize = 2 * 1024 * 1024; //2M
        $upload->exts = array('jpg', 'jpeg', 'png', 'gif');
        $upload->rootPath = '../upload/';
        $upload->savePath = 'goods/banner';
        $upload->subName = $goods_id . '/' . date('Y-m-d', time()); // Sub Directory
        $upload->saveName = array('uniqid', 'goodsbanner_');
        $upload->hash = false;

        // Upload: 3. upload start
        $bannerurl = $upload->upload();
        if (!$bannerurl) {
            return $upload->getError();
        }
        $bannerlist = array();
        foreach ($bannerurl as $key => $value) {
            $writePath      = $upload->rootPath . $value['savepath'] . $value['savename'];
            $bannerlist[]   = $writePath;
        }
        return $bannerlist;
    }

    /**
     * 添加图片（添加到数据库中）
     *
     * @return  void
     * @author  wl
     * @date    August 17, 2016
     **/
    public function saveBanner ($bannerurl, $goods_id) {
        if (!is_numeric($goods_id)) {
            return false;
        } 
        $list = array();
        $goodsbannerlist = $this->table(C('DB_PREFIX').'coin_goods')
            ->where('id = :cid')
            ->bind(['cid' => $goods_id])
            ->field('goods_images_url')
            ->fetchSql(false)
            ->find();
        if ($goodsbannerlist['goods_images_url'] != null && $goodsbannerlist['goods_images_url'] != '') {
            $bannerlist = json_decode($goodsbannerlist['goods_images_url'], true);
            $list = array_merge($bannerlist, $bannerurl);
        } else {
            $list = $bannerurl;
        }
        $goods_imgurl = json_encode($list);
        $data = array('goods_images_url' => $goods_imgurl);
        $goods_all_imgurls = M('coin_goods')
            ->where(array('id' => $goods_id))
            ->data($data)
            ->fetchSql(false)
            ->save();
        return $goods_all_imgurls;
    }

    /**
     * 删除商品轮播图
     *
     * @return  void
     * @author  wl
     * @date    Oct 18, 2016
     **/
    public function delbanner ($url, $goods_id) {
        $goods_imgurl = $this->table(C('DB_PREFIX').'coin_goods')
            ->where('id = :cid')
            ->bind(['cid' => $goods_id])
            ->field('goods_images_url')
            ->fetchSql(false)
            ->find();
        if ($goods_imgurl['goods_images_url']) {
            $banner_list = json_decode($goods_imgurl['goods_images_url'], true);
            if (!empty(is_array($banner_list))) {
                foreach ($banner_list as $key => $value) {
                    if ($url == $value) {
                        unset($banner_list[$key]);
                    }
                }
            }
            if (file_exists($url)) {
              unlink($url);
            }
            $imgurl = json_encode($banner_list);
            $data = array('goods_images_url' => $imgurl);
            $result = $this->where('id = :cid')
                ->bind(['cid' => $goods_id])
                ->data($data)
                ->fetchSql(false)
                ->save();
            return $result;
        } else {
          return false;
        }
    }










    
} /* class End */
