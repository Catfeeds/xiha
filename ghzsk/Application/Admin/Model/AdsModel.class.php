<?php 
namespace Admin\Model;
use Think\Model;
use Think\Page; 

/**
 * 广告发布管理模型类
 *
 * @author sun
 **/
class AdsModel extends BaseModel{

    //根据传入的登录者id获取该登录者发布的“没有完全失效”广告信息
    public function getAds($publisher_id) {

       $condition['publisher_id'] = array('eq', $publisher_id);
       $condition['ads_status'] = array('neq', 3);
       $count = $this->join('cs_ads_info ON cs_ads.id = cs_ads_info.ads_id')->where($condition)->count();
       $Page = new Page($count, 10);
       $page = $this->getPage($count, 10);
       $ads_info = $this->join('cs_ads_info ON cs_ads.id = cs_ads_info.ads_id')->where($condition)->order('cs_ads.addtime desc')->limit($Page->firstRow.','.$Page->listRows)->select();
       foreach ($ads_info as $key => $value) {
            $scene_id = $value['scene_id'];//场景id
            $level_id = $value['level_id'];//等级id
            $resource_type = $value['resource_type'];//展示类型
            $device = $value['device'];//设备类型
            $province_id = $value['province_id'];//省id
            $city_id = $value['city_id'];//城市id
            $area_id = $value['area_id'];//区域id
            $expire_time = $value['expire_time'];//有效期
            //查询广告场景
            $ads_info[$key]['scene'] = $this->table('cs_ads_position')->where(array('scene' => $scene_id))->getField('title');
            //查询广告等级
            $ads_info[$key]['level'] = $this->table('cs_ads_level')->where(array('level_id' => $level_id))->getField('level_money');
            //展示类型处理
            switch ($resource_type) {
              case 1:
                $ads_info[$key]['resource_type'] = '图片';
                break;
              
              case 2:
                $ads_info[$key]['resource_type'] = '视频';
                break;
              default:
                $ads_info[$key]['resource_type'] = '图片';
                break;
            }
            //设备类型处理
            switch ($device) {
              case '1':
                 $ads_info[$key]['device'] = '苹果';
                break;
              case '2':
                 $ads_info[$key]['device'] = '安卓';
                break;
              case '1,2':
                 $ads_info[$key]['device'] = '苹果和安卓';
                break;
              default:
                $ads_info[$key]['device'] = '苹果和安卓';
                break;
            }
            //查询发布的广告省，市，区
            $ads_info[$key]['province'] = $this->table('cs_province')->where(array('provinceid' => $province_id))->getField('province');
            $ads_info[$key]['city'] = $this->table('cs_city')->where(array('cityid' => $city_id))->getField('city');
            $ads_info[$key]['area'] = $this->table('cs_area')->where(array('areaid' => $area_id))->getField('area');
            $ads_info[$key]['address'] =  $ads_info[$key]['province'].$ads_info[$key]['city'].$ads_info[$key]['area'];
            //处理发布日期
            $ads_info[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
            //处理有效期
            $day = floor($expire_time / (60*60*24));
            $hour = floor(($expire_time - $day * 24 * 60 * 60) / 3600);
            $minute = floor(($expire_time - $day * 24 * 60 * 60 - $hour * 3600) / 60 );
            $second = $expire_time - $day * 24 * 60 * 60 - $hour * 3600 - $minute * 60;
            $ads_info[$key]['expire_time'] = $day.'天'.$hour.'时'.$minute.'分'.$second.'秒';
            //查询该广告有多少订单
            $ads_info[$key]['order_number'] = $this->table('cs_ads_order')->where(array('ads_id' => $value['id']))->count();

       }
      $res = array($ads_info, $count, $page);
      return $res;

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
