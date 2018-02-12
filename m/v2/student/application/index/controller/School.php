<?php

//报名控制器

namespace app\index\controller;
use think\Controller;
use think\Request;
use think\Db;
use think\View;

class School extends Controller
{
	protected $request;
	protected $params;
	protected $device;
	protected $token;
	
	public function _initialize() {
		$this->request = Request::Instance();
		$this->params = $this->request->param();
		$this->device = $this->request->has('device') ? $this->params['device'] : '1'; // 1:web 2:ios 3:andriod
		$this->token = $this->request->has('token', 'get') ? $this->request->get('token') : null;
		$this->assign('token', $this->token);
		$this->assign('r', time());
		$this->assign('device', $this->device);
        $this->assign('title', '报名列表');
	}

    // 学车套餐
    public function packageshifts () {
        $this->assign('title', '学车套餐');
        $this->request = Request::Instance();
        $this->params = $this->request->param();
        $this->device = $this->request->has('device') ? $this->params['device'] : '1';
        $shifts_list = $this->getPackageShifts();
        $this->assign('shifts_list', $shifts_list);
        return $this->fetch('school/packageshifts');
    }

	// 驾校详情
	public function detail() {
        $this->assign('title', '驾校详情');
        $this->request = Request::Instance();
        $this->params = $this->request->param();
        $this->device = $this->request->has('device') ? $this->params['device'] : '1'; // 1:web 2:ios 3:andriod
        $school_id = $this->request->has('id') ? $this->params['id'] : '';
        $lat = $this->request->has('lat') ? $this->params['lat'] : '';
        $lng = $this->request->has('lng') ? $this->params['lng'] : '';

        // 获取驾校信息，最近驾校报名点
        $school_detail = $this->getSchoolDetail($school_id, $lat, $lng);
        $school_imgurl = $school_detail['school_imgurl'];

        // 获取驾校班制
        $shifts_info = $this->getSchoolShifts($school_id);
        $is_shifts_more = $shifts_info['is_shifts_more'];
        $shifts_list = $shifts_info['shifts_list'];
        $shifts_count = $shifts_info['_count'];
      
        // 获取驾校评价
        $comment_list = $this->getSchoolCommentList($school_id);
        $comment_info = $comment_list;

        $this->assign('school_detail', $school_detail);
        $this->assign('school_imgurl', $school_imgurl);
        $this->assign('shifts_count', $shifts_count);
        $this->assign('shifts_list', $shifts_list);
        $this->assign('comment_info', $comment_info);
        $this->assign('is_shifts_more', $is_shifts_more);
        return $this->fetch('school/detail');
	}
	

    // 获取套餐班制列表 
    public function getPackageShifts () {

        $whereCondition = [
            'shifts.deleted' => 1, // 1:正常 2:已删除
            'shifts.is_package' => 1, // 1:套餐 2:非套餐
        ];
        $shifts_list = Db::table('cs_school_shifts shifts')
            ->field(
                'shifts.*,
                 school.s_school_name,
                 school.s_thumb'
            )
            ->join('cs_school school', 'school.l_school_id = shifts.sh_school_id', 'LEFT')
            ->where($whereCondition)
            ->order('shifts.order', 'asc')
            ->select();
        if (!empty($shifts_list)) {
            foreach ($shifts_list as $index => $value) {
                $sh_imgurl = $this->buildUrl($value['sh_imgurl']);
                if ($sh_imgurl == '') {
                    $sh_imgurl = $this->buildUrl($value['s_thumb']);
                }
                $shifts_list[$index]['sh_imgurl'] = $sh_imgurl;
            }
        }
        return $shifts_list;

    }

    // 获取驾校评价列表
    public function getSchoolCommentList($school_id, $type=1) {

        $_data = [];

        // 获取评论列表（3条最近）
        $comment_list = Db::table('cs_coach_comment coach_comment')
            ->field(
                'coach_comment.school_star,
                 coach_comment.school_content,
                 coach_comment.user_id,
                 coach_comment.addtime,
                 user.s_username as user_name,
                 users_info.photo_id,
                 users_info.user_photo'
            )
            ->join('cs_user user', 'user.l_user_id = coach_comment.user_id')
            ->join('cs_users_info users_info', 'coach_comment.user_id = users_info.user_id')
            ->where([
                'coach_comment.school_id'   => $school_id,
                'coach_comment.type'        => 2 // 1：预约学车 2：报名驾校
            ])
            ->limit(1, 3)
            ->fetchsql(false)
            ->select();
        
        if(!empty($comment_list)) {
            foreach ($comment_list as $key => $value) {
                if ($value['photo_id'] == '' || $value['photo_id'] == 0) {
                    $comment_list[$key]['photo_id'] = 1;
                }
                $comment_list[$key]['user_name'] = $comment_list[$key]['user_name'] ? $comment_list[$key]['user_name'] : '嘻哈学员';
                $comment_list[$key]['user_photo'] = $comment_list[$key]['user_photo'] ? 'http://60.173.247.68:50003/php/admin/'.$comment_list[$key]['user_photo'] : '';
                $comment_list[$key]['addtime'] = $comment_list[$key]['addtime'] ? date('Y-m-d H:i', $comment_list[$key]['addtime']) : date('Y-m-d H:i', time());
                $comment_list[$key]['school_content'] = $comment_list[$key]['school_content'] ? $comment_list[$key]['school_content'] : '默认好评';
            }
            // 获取评论总数
            $comment_count = DB::table('cs_coach_comment coach_comment')
                ->join('cs_user user', 'user.l_user_id = coach_comment.user_id')
                ->join('cs_users_info users_info', 'coach_comment.user_id = users_info.user_id')
                ->where([
                    'coach_comment.school_id'=>$school_id,
                    'coach_comment.type'=>2 // 1：预约学车 2：报名驾校
                ])
                ->count();
            if(3 >= $comment_count) {
                $_data['is_comment_more'] = 1; // 没有查看更多
            } else {
                $_data['is_comment_more'] = 2; // 有查看更多
            }
        } else {
            $_data['is_comment_more'] = 1; // 没有查看更多
        }
        
        $comment_count = DB::table('cs_coach_comment coach_comment')
            ->join('cs_user user', 'user.l_user_id = coach_comment.user_id')
            ->join('cs_users_info users_info', 'coach_comment.user_id = users_info.user_id')
            ->where([
                'coach_comment.school_id'=>$school_id,
                'coach_comment.type'=>2 // 1：预约学车 2：报名驾校
            ])
            ->count();
        $_data['comment_count'] = $comment_count;
        $star_sum = DB::table('cs_coach_comment coach_comment')
            ->where([
                'coach_comment.school_id' => $school_id,
                'coach_comment.type' => 2 // 1：预约学车 2：报名驾校
            ])
            ->sum('school_star');
        $_data['average_star'] = $_data['comment_count'] ? floor($star_sum / $_data['comment_count']) : 3;
        $_data['comment_list'] = $comment_list;
        return $_data;
    }


    /**
     * 获取驾校班制信息(条数：有count决定[0:查询所有])
     **/
    public function getSchoolShifts ($school_id) {

        $shifts_info = DB::table('cs_school_shifts')
            ->field(
                'id,
                 sh_school_id,
                 sh_title,
                 sh_money,
                 sh_original_money,
                 sh_tag,
                 sh_description_1 as sh_tag_two,
                 is_package,
                 sh_type,
                 sh_description_2 as sh_description,
                 is_promote,
                 sh_license_id,
                 sh_license_name'
            )
            ->where([
                'deleted'       => '1',
                'sh_school_id'  => $school_id
            ])
            ->where("coach_id is NULL OR coach_id = 0 OR coach_id = ''")
            ->limit(0, 3)
            ->fetchsql(false)
            ->select();
        $shifts_list = [];
        if ($shifts_info) {
            foreach ($shifts_info as $key => $value) {
                
                if ( 1 == $value['is_package']) {
                    $shifts_info[$key]['package_intro'] = '套餐';
                } else {
                    $shifts_info[$key]['package_intro'] = '非套餐';
                }
            }

            // 获取班制总数
            $shifts_count = DB::table('cs_school_shifts')
                ->where([
                    'deleted'       => '1',
                    'sh_school_id'  => $school_id
                ])
                ->where("coach_id is NULL OR coach_id = 0 OR coach_id = ''")
                ->count();
            if ($shifts_count <= 3) {
                $is_shifts_more = 1; // 没有更多
            } else {
                $is_shifts_more = 2; // 查看更多
            }
        } else {
            $is_shifts_more = 1; // 没有更多
        }

        $_count = DB::table('cs_school_shifts')
            ->where([
                'deleted'       => '1',
                'sh_school_id'  => $school_id
            ])
            ->where("coach_id is NULL OR coach_id = 0 OR coach_id = ''")
            ->count();

        $shifts_list['is_shifts_more'] = $is_shifts_more;
        $shifts_list['_count'] = $_count;
        $shifts_list['shifts_list'] = $shifts_info;
        return $shifts_list;
    }

    // 获取驾校详情
    public function getSchoolDetail ($school_id, $lat, $lng) {
        $school_detail = DB::table('cs_school')
            ->field(
                'l_school_id as school_id,
                 s_school_name as school_name,
                 s_imgurl as school_imgurl,
                 is_show,
                 s_location_x as location_x,
                 s_location_y as location_y,
                 s_frdb_tel as school_phone,
                 s_shuoming as school_desc,
                 s_address as school_address,
                 brand,
                 s_thumb as thumb_imgurl'
            )
            ->where(['l_school_id' => $school_id])
            ->find();
        if  ($school_detail) {
            $school_detail['thumb_imgurl'] = $this->buildUrl($school_detail['thumb_imgurl']);
        }
        // return $school_detail;
        $_school_imgurl_arr = [];
        $school_imgurl_arr = isset($school_detail['school_imgurl']) ? json_decode($school_detail['school_imgurl'], true) : [];
        if(is_array($school_imgurl_arr) && !empty($school_imgurl_arr)) {
            foreach ($school_imgurl_arr as $key => $value) {
                $url = $this->buildUrl($value);
                if (! empty($url)) {
                    $_school_imgurl_arr[] = $url;
                }
            }
        }
        if ($_school_imgurl_arr) {
            $school_detail['school_imgurl'] = $_school_imgurl_arr;
        } else {
            $school_detail['school_imgurl'] = '';
        }
        $school_detail['signup_num'] = $this->getSchoolOrdersNum($school_id);
        $school_desc = $school_detail['school_desc'];
        $school_desc = str_replace('&amp;nbsp;','&nbsp;', $school_desc);
        $school_desc = str_replace('&lt;','<', $school_desc);
        $school_desc = str_replace('&gt;','>', $school_desc);
        $school_desc = str_replace('&quot;','"', $school_desc);
        $school_detail['school_desc'] = $school_desc;
        $train_list = Db::table('cs_school_train_location')
            ->field(
                'id,
                 tl_train_address,
                 tl_location_x,
                 tl_location_y,
                 tl_phone,
                 tl_imgurl'
            )
            ->fetchsql(false)
            ->where('tl_school_id', $school_id)
            ->select();
        $distance = array();
        if (!empty($train_list)) {
            foreach ($train_list as $index => $value) {
                
                $train_list[$index]['distance'] = round($this->getDistance($lng, $lat, $value['tl_location_x'], $value['tl_location_y'])/1000, 1);
                $distance[] = round($this->getDistance($lng, $lat, $value['tl_location_x'], $value['tl_location_y'])/1000, 1);
            }

            $school_distance = round($this->getDistance($lng, $lat, $school_detail['location_x'], $school_detail['location_y'])/1000, 1);
            $train_min_distance = !empty($distance) ? min($distance) : 0;
            if ($train_min_distance >= $school_distance) {
                $distance[] = round($this->getDistance($lng, $lat, $school_detail['location_x'], $school_detail['location_y'])/1000, 1);
            }
            
        } else {
            $distance[] = round($this->getDistance($lng, $lat, $school_detail['location_x'], $school_detail['location_y'])/1000, 1);
            
        }
        $min_distance = !empty($distance) ? min($distance) : 0;
        $school_detail['min_distance'] = $min_distance;
        return $school_detail;
    }
    

    // 获取多少人报名驾校
    public function getSchoolOrdersNum($school_id) {

        $commonwhereCondition = [
            'so_school_id'      => $school_id,
            'so_order_status'   => array('neq', '101'),
        ];

        $where = [];
        $Condition = [];
        $where = [
            'so_pay_type'       => array('in', [1, 3, 4]),
            'so_order_status'   => '1',
        ];

        $condition = [
            'so_pay_type'       => '2',
            'so_order_status'   => '3',
        ];

        $order_num = 0;
        $online_orders_num = Db::table('cs_school_orders')
            ->where($commonwhereCondition)
            ->where($where)
            ->fetchsql(false)
            ->count();
        $offline_orders_num = Db::table('cs_school_orders')
            ->where($commonwhereCondition)
            ->where($condition)
            ->fetchsql(false)
            ->count();
        $order_num = intval($online_orders_num) + intval($offline_orders_num);
        return $order_num;
    }

    // 计算距离
    public function getDistance($lng1, $lat1, $lng2, $lat2) {
        $earthRadius = 6367000; //approximate radius of earth in meters

        $lat1 = ($lat1 * pi() ) / 180;
        $lng1 = ($lng1 * pi() ) / 180;

        $lat2 = ($lat2 * pi() ) / 180;
        $lng2 = ($lng2 * pi() ) / 180;

        $calcLongitude = $lng2 - $lng1;
        $calcLatitude = $lat2 - $lat1;
        $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);
        $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
        $calculatedDistance = $earthRadius * $stepTwo;

        return round($calculatedDistance);
    }

    // 文件路径
    public function buildUrl ($url) {
        $upload_path = realpath('../../../../').'/upload/';
        // $http_host = 'http://60.173.247.68:50003/php/upload/';
        $http_host = 'http://w.xihaxueche.com:8001/service/upload/';
        if (empty($url)) {
            return '';
        }
        if (substr($url, 0, 10) == '../upload/') {
            $url = str_replace('../upload/', '', $url);
        }
        if (substr($url, 0, 7) == 'upload/') {
            $url = str_replace('upload/', '', $url);
        }

        //windows specific
        if ( 'WINNT' === PHP_OS) {
            // you are under windows os
            // path separator
            if (trim(file_exists(str_replace('/', '\\', $upload_path.$url)))) {
                return $http_host.$url;
            }
            if (trim(file_exists(str_replace('/', '\\', $upload_path.'../admin/upload/'.$url)))) {
                return $http_host.'../admin/upload/'.$url;
            }
            if (trim(file_exists(str_replace('/', '\\', $upload_path.'../sadmin/upload/'.$url)))) {
                return $http_host.'../sadmin/upload/'.$url;
            }

        } else {
            // other os

            if (trim(file_exists($upload_path.$url))) {
                return $http_host.$url;
            }
            if (trim(file_exists($upload_path.'../admin/upload/'.$url))) {
                return $http_host.'../admin/upload/'.$url;
            }
            if (trim(file_exists($upload_path.'../sadmin/upload/'.$url))) {
                return $http_host.'../sadmin/upload/'.$url;
            }
        }

        return '';
    }


}

?>