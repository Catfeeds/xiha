<?php

/**
 * 学员端-预约模块-教练信息
 * @return void
 * @author
 **/

namespace App\Http\Controllers\v3\student;

use Exception;
use App\Models\v3\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CoachController extends Controller {

    protected $request;
    protected $auth;
    protected $order;
    protected $user;

    public function __construct(Request $request) {
        $this->request = $request;
        $this->auth = new AuthController();
        $this->order = new OrderController($this->request);
        $this->user = new UserController($this->request);
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

    /**
     * 获取教练的热点标签
     * @param int $i_type
     */
    private function getHotParams($i_type) {
        $hot_config = array(
            '0' => array(
                array('color' => '#FF952B', 'text' => '金牌教练员')
            ),
            '1' => array(
                array('color' => '#11C0A3', 'text' => '普通教练员')
            ),
            '2' => array(
                array('color' => '#11C0A3', 'text' => '二级优秀教练员')
            ),
            '3' => array(
                array('color' => '#11C0A3', 'text' => '三级优秀教练员')
            ),
            '4' => array(
                array('color' => '#11C0A3', 'text' => '四级优秀教练员')
            ),
            '5' => array(
                array('color' => '#11C0A3', 'text' => '二级优秀教练员'),
                array('color' => '#FF952B', 'text' => '全国优秀教练员荣誉')
            ),
            '6' => array(
                array('color' => '#11C0A3', 'text' => '三级优秀教练员'),
                array('color' => '#FF952B', 'text' => '全国优秀教练员荣誉')
            ),
        );
        if (array_key_exists($i_type, $hot_config)) {
            return $hot_config[$i_type];
        } else {
            return $hot_config['1'];
        }
    }

    //  首页 新增字段is_elecoach(标识是否为电子教练)
    public function index()
    {
        // 组合条件
        $where = []; $isorderBy = 0;

        // 城市条件
        if ( ! $this->request->has('city_id') ) {
            $city_id = 340100; // 默认为合肥市(city_id=340100)
        } else {
            $city_id = intval($this->request->input('city_id'));
        }
        $where[] = ['coach.city_id', '=', $city_id];

        $where[] = ['user.i_user_type', '=', 1]; // 成员类型是教练
        $where[] = ['coach.order_receive_status', '=', 1]; // 教练打开在线状态，可以教学，状态码为1
        $where[] = ['user.i_status', '=', 0]; // 未删除状态
        if ( ! $this->request->has('school_id') ) {
            $where[] = ['school.l_school_id', '>', 0]; // 教练必须依托驾校
        } else {
            $where[] = ['school.l_school_id', '=', $this->request->input('school_id')]; // 教练必须依托驾校
        }
        $where[] = ['school.is_show', '=', 1]; // 驾校不存在 1：展示 2：不展示
        $where[] = ['coach.certification_status', [1, 3]]; // 未认证或已认证
        DB::connection()->enableQueryLog();
        $query = DB::table('coach')
            ->select(
                'coach.l_coach_id as coach_id',
                'coach.s_coach_name as coach_name',
                'coach.s_coach_phone as coach_phone',
                'coach.s_coach_imgurl as coach_imgurl',
                'coach.s_teach_age as teach_age',
                'coach.s_coach_sex as coach_sex',
                'coach.s_coach_lisence_id as license_id',
                'coach.i_coach_star as coach_star',
                'coach.i_type',
                'coach.must_bind',
                'coach.shift_min_price',
                'coach.shift_max_price',
                'coach.timetraining_supported',
                'coach.timetraining_min_price as price',
                'coach.is_elecoach',
                'coach.is_hot',
                'school.l_school_id as school_id',
                'school.s_school_name as school_name',
                'coach.certification_status'
            )
            ->leftJoin('user', 'coach.user_id', '=', 'user.l_user_id')
            ->leftJoin('school', 'coach.s_school_name_id', '=', 'school.l_school_id')
            ->where($where);

        //  按照选定关键词过滤  (综合/电子教练/学费低)
        if($this->request->has('assign_keyword')) {
            $assign_keyword = $this->request->input('assign_keyword');
            if ($assign_keyword == 'is_elecoach') {
                $query = $query->where(function ($query) use ($assign_keyword) {
                    $query->where('coach.is_elecoach', '=', '1');
                });
            }else if($assign_keyword == 'school_fee') {
                $isorderBy = 4;
            }
        }

        // 按牌照类型细化搜索
        if ( $this->request->has('license_id') && '0' != $this->request->input('license_id') ) {
            $license_id = $this->request->input('license_id');
            $query = $query->where(function ($query) use ($license_id) {
                $query->where('s_coach_lisence_id', '=', $license_id)
                    ->orWhere('s_coach_lisence_id', 'like', '%,'.$license_id.'%')
                    ->orWhere('s_coach_lisence_id', 'like', '%'.$license_id.',%')
                    ;
            });
        }

        // 按价格范围过滤
        if ( $this->request->has('price_range') ) {
            $price_range = $this->request->input('price_range');
            $price_range = explode(',', $price_range);
            if ( 2 == count($price_range) ) {
                $min_price = $price_range[0] * 1000;
                $max_price = ($price_range[1] > 0) ? $price_range[1] * 1000 : -1;
                if ($max_price == -1) {
                    $query = $query->where(function ($query) use ($min_price, $max_price) {
                        $query->where('shift_max_price', '>=', $min_price)
                            ;
                    });
                } elseif ($min_price < $max_price) {
                    $query = $query->where(function ($query) use ($min_price, $max_price) {
                        $query->where('shift_min_price', '<=', $max_price)
                            ->where('shift_max_price', '>=', $min_price)
                            ;
                    });
                }
            }
        }

        // 按关键词搜索
        if ( $this->request->has('keyword') ) {
            $keyword = trim($this->request->input('keyword'));
            $query = $query->where(function ($query) use ($keyword) {
                $query->where('s_coach_name', 'like', '%'.$keyword.'%')
                    ->orWhere('s_coach_phone', 'like', '%'.$keyword.'%')
                    ->orWhere('s_school_name', 'like', '%'.$keyword.'%')
                    ;
            });
        }

        // 排序方式
        if ( $this->request->has('order') ) {
            $order = $this->request->input('order');
            switch ($order) {
            case 0:
                // 智能排序
                $query = $query
                    ->orderBy('coach.i_order', 'desc')
                    ;
                break;
            case 1:
                // 已认证教练
                $query = $query
                    ->orderBy('certified', 'desc')
                    ->orderBy('coach.i_order', 'desc')
                    ;
                break;
            case 2:
                // 支持计时培训
                $query = $query
                    ->orderBy('timetraining_supported', 'desc')
                    ->orderBy('coach.i_order', 'desc')
                    ;
                break;
            case 3:
                // 支持优惠券
                $query = $query
                    ->orderBy('coupon_supported', 'desc')
                    ->orderBy('coach.i_order', 'desc')
                    ;
                break;
            default :
                $query = $query
                    ->orderBy('coach.i_order', 'desc')
                    ;
                break;
            }
        } else {
            if($isorderBy == 4) {
                 $query = $query->orderBy('coach.timetraining_min_price', 'asc');
            }else {
                $query = $query->orderBy('coach.i_order', 'desc');
            }
        }

        // 取得结果
        $coach_list = $query
            ->paginate(10); // 分页

        $coach_ids = array();
        foreach ($coach_list as $index => $coach) {
            $coach_list[$index]->coach_imgurl = $this->buildUrl($coach->coach_imgurl);
            $coach_list[$index]->student_count = 0;
            $student_count_sql = "select count(1) as cnt from `cs_study_orders` where `cs_study_orders`.`l_coach_id`= " . $coach->coach_id . " and `cs_study_orders`.`dt_zhifu_time`!=''";
            $student_count_rlt = DB::select($student_count_sql);
            $coach_list[$index]->student_count = $student_count_rlt[0]->cnt;
            // 优化热门标识
            if(2 == $coach->is_hot) {
                $coach_list[$index]->is_hot = 1;
            }else {
                $coach_list[$index]->is_hot = 0;
            }
            unset($coach_list[$index]->timetraining_supported);
            unset($coach_list[$index]->shift_min_price);
            unset($coach_list[$index]->shift_max_price);
            unset($coach_list[$index]->coach_phone);
            unset($coach_list[$index]->teach_age);
            unset($coach_list[$index]->coach_sex);
            unset($coach_list[$index]->license_id);
            unset($coach_list[$index]->i_type);
            unset($coach_list[$index]->must_bind);
        }

        $queries = DB::getQueryLog();
        $data = [
            'code' => 200,
            'msg'  => '成功',
            'data' => [
                'list' => $coach_list,
            ],
        ];
        return response()->json($data);
    }

    // 获取教练详情
    public function getCoachDetail() {
        if( !$this->request->has('id') || !$this->request->has('lng') || !$this->request->input('lat')) {
            return response()->json([
                'code'=>400,
                'msg'=>'参数错误',
                'data'=> new \stdClass,
            ]);
        }
        $coach_id = $this->request->input('id');
        $lng = $this->request->input('lng');
        $lat = $this->request->input('lat');
        $device = $this->request->has('device') ? $this->request->input('device') : 2; // 设备类型 1：web 2：苹果 3：安卓

        // 获取教练基本信息
        $_data = DB::table('user')
            ->select(
                'school.s_school_name as school_name',
                'coach.s_school_name_id as school_id',
                'coach.s_coach_name as coach_name',
                'coach.s_coach_phone as coach_phone',
                'coach.s_coach_imgurl as coach_imgurl',
                'coach.certification_status',
                'coach.i_coach_star as coach_star',
                'coach.is_elecoach',
                'coach.average_license_time',
                'coach.lesson2_pass_rate',
                'coach.lesson3_pass_rate'
            )
            ->leftJoin('coach', 'user.l_user_id', '=', 'coach.user_id')
            ->leftJoin('school', 'coach.s_school_name_id', '=', 'school.l_school_id')
            ->where(['coach.l_coach_id'=> $coach_id, 'coach.order_receive_status'=>1, 'user.i_status'=>0])
            ->first();
        if(empty($_data)) {
            $data = ['code'=>1002, 'msg'=>'教练不在线或不存在。', 'data'=> new \stdclass()];
            return response()->json($data);
        }
        $_data->average_license_time = $_data->average_license_time ? $_data->average_license_time . '天' : '60天';
        $_data->lesson2_pass_rate = $_data->lesson2_pass_rate ? $_data->lesson2_pass_rate.'%' : '50%';
        $_data->lesson3_pass_rate = $_data->lesson3_pass_rate ? $_data->lesson3_pass_rate.'%' : '50%';
        $_data->coach_imgurl = $this->buildUrl($_data->coach_imgurl);
        $student_count_sql = "select count(1) as cnt from `cs_school_orders` where `cs_school_orders`.`so_coach_id`= " . $coach_id . " and `cs_school_orders`.`dt_zhifu_time` <> '' limit 1";
        $student_count_rlt = DB::select($student_count_sql);
        $_data->student_count = $student_count_rlt[0]->cnt;

        // 是否查看更多 1：没有更多 2：查看更多
        $shifts_count = DB::table('school_shifts')
            ->select(DB::raw('count(1) as shifts_count'))
            ->where([
                ['deleted', '=', '1'],
                ['coach_id', '=', $coach_id]
            ])
            ->first();
        if ($shifts_count->shifts_count <= 3) {
            $is_shifts_more = 1; // 没有更多
        } else {
            $is_shifts_more = 2; // 查看更多
        }
        $_data->is_shifts_more = $is_shifts_more;

        // 获取评论列表（3条最近）
        $comment_list = DB::table('coach_comment')
            ->select(
                'coach_comment.coach_id',
                'coach_comment.coach_star',
                'coach_comment.coach_content',
                'coach_comment.user_id',
                'coach_comment.addtime',
                'user.s_username as user_name',
                'users_info.photo_id',
                'users_info.user_photo'
            )
            ->join('user', 'coach_comment.user_id', '=', 'user.l_user_id')
            ->join('users_info', 'coach_comment.user_id', '=', 'users_info.user_id')
            ->where([
                'coach_comment.coach_id'=>$coach_id,
                'coach_comment.type'=>1 // 1：预约学车 2：报名驾校
            ])
            ->orderBy('coach_comment.addtime', 'desc')
            ->take(3)
            ->get();
        if(!empty($comment_list->toArray())) {
            foreach ($comment_list->toArray() as $key => $value) {
                $comment_list[$key]->user_name = $comment_list[$key]->user_name ? $comment_list[$key]->user_name : '嘻哈学员';
                $comment_list[$key]->user_photo = $comment_list[$key]->user_photo ? env('APP_PATH').'admin/'.$comment_list[$key]->user_photo : '';
                $comment_list[$key]->addtime = $comment_list[$key]->addtime ? date('Y-m-d H:i', $comment_list[$key]->addtime) : date('Y-m-d H:i', time());
                $comment_list[$key]->coach_content = $comment_list[$key]->coach_content ? $comment_list[$key]->coach_content : '默认好评';
            }
            // 获取评论总数
            $comment_count = DB::table('coach_comment')
                ->select(DB::raw('count(1) as comment_count'))
                ->join('user', 'coach_comment.user_id', '=', 'user.l_user_id')
                ->join('users_info', 'coach_comment.user_id', '=', 'users_info.user_id')
                ->where([
                    'coach_comment.coach_id'=>$coach_id,
                    'coach_comment.type'=>1 // 1：预约学车 2：报名驾校
                ])
                ->first();
            if(3 >= $comment_count->comment_count) {
                $_data->is_comment_more = 1; // 没有查看更多
            } else {
                $_data->is_comment_more = 2; // 有查看更多
            }
        } else {
            $_data->is_comment_more = 1; // 没有查看更多
        }
        $_data->comment_list = $comment_list;
        $data = ['code'=>200, 'msg'=>'获取成功', 'data'=>['detail' => $_data]];
        return response()->json($data);
    }

    //  教练详情页－电子教练服务
    public function getElecoach()
    {
        if( !$this->request->has('id')) {
            return response()->json([
                'code'=>400,
                'msg'=>'参数错误',
                'data'=> new \stdClass,
            ]);
        }
        $coach_id = $this->request->input('id');
        $date = $this->request->input('date');
        if(!$date) {
            $date = date('Y-m-d');
        }
        // 获取教练基本信息  用户存在且教练在线
        $_data = DB::table('user')
            ->select(
                'school.s_school_name as school_name',
                'coach.s_school_name_id as school_id',
                'coach.s_coach_name as coach_name',
                'coach.s_coach_phone as coach_phone',
                'coach.s_coach_imgurl as coach_imgurl',
                'coach.certification_status',
                'coach.i_coach_star as coach_star',
                'coach.is_elecoach',
                'coach.average_license_time',
                'coach.lesson2_pass_rate',
                'coach.lesson3_pass_rate'
            )
            ->leftJoin('coach', 'user.l_user_id', '=', 'coach.user_id')
            ->leftJoin('school', 'coach.s_school_name_id', '=', 'school.l_school_id')
            ->where(['coach.l_coach_id'=> $coach_id, 'coach.order_receive_status'=>1, 'user.i_status'=>0])
            ->first();
        if(!$_data) {
            $json = ['code'=>1002,'msg'=>'教练不在线或不存在','data'=> new \stdClass()];
        }else {
             //  电子教练服务-教练时间表
            $elecoach_list = $this->getCoachTimeList($coach_id, $_data->school_id, $date);
            if(!$elecoach_list) {
                $json = ['code'=>200, 'msg'=>'获取成功', 'data'=> ['list'=>[]]];
            }else {
                $json = ['code'=>200, 'msg'=>'获取成功', 'data'=> ['list'=>$elecoach_list]];
            }
        }
        return response()->json($json);
    }

    //  教练详情页－驾考服务
    public function getShifts()
    {
        if(!$this->request->has('id')) {
            return response()->json(['code'=>400,'msg'=>'参数错误','data'=> new \stdClass()]);
        }
        $coach_id = $this->request->input('id');
        // 获取教练基本信息
        $_data = DB::table('user')
            ->select(
                'school.s_school_name as school_name',
                'coach.s_school_name_id as school_id',
                'coach.s_coach_name as coach_name',
                'coach.s_coach_phone as coach_phone',
                'coach.s_coach_imgurl as coach_imgurl',
                'coach.certification_status',
                'coach.i_coach_star as coach_star',
                'coach.is_elecoach',
                'coach.average_license_time',
                'coach.lesson2_pass_rate',
                'coach.lesson3_pass_rate'
            )
            ->leftJoin('coach', 'user.l_user_id', '=', 'coach.user_id')
            ->leftJoin('school', 'coach.s_school_name_id', '=', 'school.l_school_id')
            ->where(['coach.l_coach_id'=> $coach_id, 'coach.order_receive_status'=>1, 'user.i_status'=>0])
            ->first();
        if(!$_data) {
            return response()->json(['code'=>1002,'msg'=>'教练不在线或不存在','data'=> new \stdClass()]);
        }else {
            // 　驾考服务－报名班制列表
            $shifts_list = DB::table('school_shifts')
                ->select(
                    'id',
                    'sh_title',
                    'sh_money',
                    'sh_school_id',
                    'sh_original_money',
                    'sh_tag',
                    'sh_description_1 as sh_tag_two',
                    'sh_type',
                    'sh_description_2 as sh_description',
                    'is_promote',
                    'sh_license_id',
                    'sh_license_name',
                    'sh_imgurl'
                )
                ->where(['coach_id'=>$coach_id, 'deleted'=>1])
                ->orderBy('order', 'desc')
                // ->take(3)
                ->get();
            if(!empty($shifts_list->toArray())) {
                foreach ($shifts_list->toArray() as $key => $value) {
                    $shifts_list[$key]->sh_category = 2; // 教练设置的班制
                }
            }
            if(!empty($shifts_list->toArray())) {
                foreach ($shifts_list->toArray() as $key => $value) {
                    $shifts_list[$key]->sh_description = trim($value->sh_description);
                    $shifts_list[$key]->sh_title = trim($value->sh_title);
                    if($value->sh_imgurl) {
                        $shifts_list[$key]->sh_imgurl = env('APP_UPLOAD_PATH') . $value->sh_imgurl;
                    }else {
                        $shifts_list[$key]->sh_imgurl = '';
                    }
                    $shifts_list[$key]->student_count = 0;
                    $student_count_sql = "select count(1) as cnt from `cs_school_orders` where `cs_school_orders`.`so_shifts_id`= " . $value->id . " and `cs_school_orders`.`dt_zhifu_time` <> '' limit 1";
                    $student_count_rlt = DB::select($student_count_sql);
                    $shifts_list[$key]->student_count = $student_count_rlt[0]->cnt;
                    unset($shifts_list[$key]->is_promote);
                    unset($shifts_list[$key]->sh_school_id);
                    unset($shifts_list[$key]->sh_license_id);
                    unset($shifts_list[$key]->sh_type);
                    unset($shifts_list[$key]->sh_category);
                }
            }

            if(!empty($shifts_list->toArray())) {
                $json = ['code'=>200, 'msg'=>'获取成功', 'data'=> ['list'=>$shifts_list]];
            }else {
                $json = ['code'=>200, 'msg'=>'获取成功', 'data'=> ['list'=>[]]];
            }
            return response()->json($json);
        }
    }

    // 根据coach_id获取教练时间配置
    public function getCoachTimeList($coach_id, $school_id, $date) {
        $lesson_config = array('1' => '科目一', '2' => '科目二', '3' => '科目三', '4' => '科目四');
        $lisence_config = array('1' => 'C1', '2' => 'C2', '3' => 'C5', '4' => 'A1', '5' => 'A2', '6' => 'B1', '7' => 'B2', '8' => 'D', '9' => 'E', '10' => 'F');

        $coach_time_list = array();
        // $date_config = $this->getCoachDateTimeConfig();
        // $coach_time_list['date_time'] = $date_config;
        $date_format = array();
        $date_format = explode('-', $date);
        $year = $date_format[0];
        $month = $date_format[1];
        $day = $date_format[2];

        // 获取驾校的时间配置
        $s_time_list = array();
        $is_automatic = 1;

        $school_config = DB::table('school_config')
            ->select('s_time_list','is_automatic')
            ->where(['l_school_id'=>$school_id])
            ->first();

        if ($school_config) {
            $s_time_list = array_filter(explode(',', $school_config->s_time_list));
            $is_automatic = $school_config->is_automatic;
        }

        // 获取教练的时间配置
        $coach_config_info = DB::table('coach')
            ->select('s_am_subject','s_pm_subject','s_am_time_list','s_pm_time_list','s_coach_lisence_id','s_coach_lesson_id')
            ->where(['l_coach_id'=>$coach_id])
            ->first();

        $s_am_subject = 2;
        $s_pm_subject = 3;
        $s_am_time_list = array();
        $s_pm_time_list = array();
        $s_coach_lisence_id_list = array();
        $s_coach_lesson_id_list = array();
        if (!empty($coach_config_info)) {
            $s_am_subject = $coach_config_info->s_am_subject;
            $s_pm_subject = $coach_config_info->s_pm_subject;
            $s_am_time_list = isset($coach_config_info->s_am_time_list) ? array_filter(explode(',', $coach_config_info->s_am_time_list)) : array();
            $s_pm_time_list = isset($coach_config_info->s_pm_time_list) ? array_filter(explode(',', $coach_config_info->s_pm_time_list)) : array();
            $s_coach_lisence_id_list = isset($coach_config_info->s_coach_lisence_id) ? array_filter(explode(',', $coach_config_info->s_coach_lisence_id)) : array();
            $s_coach_lesson_id_list = isset($coach_config_info->s_coach_lesson_id) ? array_filter(explode(',', $coach_config_info->s_coach_lesson_id)) : array();
        }

        if (!empty($s_am_time_list) && !empty($s_pm_time_list)) {
            $time_config_ids_arr = array_merge($s_am_time_list, $s_pm_time_list);
        } else {
            $time_config_ids_arr = $s_time_list;
        }

        DB::connection()->enableQueryLog();
        $whereCondition1 = [
            ['coach_appoint_time.year' , '=',  $year],
            ['coach_appoint_time.month' , '=',  $month],
            ['coach_appoint_time.day' , '=',  $day],
            ['study_orders.l_coach_id' , '=',  $coach_id],
            ['coach_appoint_time.id', '<>', '0']
        ];
        $appoint_time_config_id = DB::table('study_orders')
            ->select('study_orders.time_config_id')
            ->leftJoin('coach_appoint_time', 'coach_appoint_time.id', '=', 'study_orders.appoint_time_id')
            ->where($whereCondition1)
            ->whereNotIn('study_orders.i_status', [3, 101])
            ->get();
        $queries = DB::getQueryLog();

        $time_config_ids = array();
        $time_config_id_arr = array();
        if ($appoint_time_config_id) {
            foreach ($appoint_time_config_id as $key => $value) {
                $time_config_ids = array_filter(explode(',', $value['time_config_id']));
                foreach ($time_config_ids as $k => $v) {
                    $time_config_id_arr[] = $v;
                }
            }
        }

        // 获取当前教练所设置的时间端配置
        $time_config_id = array();
        $time_lisence_config_id = array();
        $time_lesson_config_id = array();
        $time_config_money_id = array();
        $whereCondition2 = [
            ['coach_id' , '=',  $coach_id],
            ['year' , '=',  $year],
            ['month' , '=',  $month],
            ['day' , '=',  $day]
        ];


        $current_time_config = DB::table('current_coach_time_configuration')
            ->select()
            ->where($whereCondition2)
            ->first();

        if (!empty($current_time_config)) {
            $time_config_id = explode(',', $current_time_config->time_config_id);
            $time_lisence_config_id = json_decode($current_time_config->time_lisence_config_id, true);
            $time_lesson_config_id = json_decode($current_time_config->time_lesson_config_id, true);
            $time_config_money_id = json_decode($current_time_config->time_config_money_id, true);
        }
        // 在coach_time_config表中查询相关信息
        $map = array();
        $order = "id";
        $t_c_ids = [];
        $whereCondition3 = ['status'=>'1'];
        if (!empty($time_config_ids_arr) && empty($current_time_config)) {
            $t_c_ids = $time_config_ids_arr;
            $order = "start_time DESC";
        }
        if($order == 'id') {
            $coach_time_config = DB::table('coach_time_config')
            ->select()
            ->where($whereCondition3)
            ->whereIn('id', $t_c_ids)
            ->orderBy('id','asc')
            ->get();
        }else {
            $coach_time_config = DB::table('coach_time_config')
            ->select()
            ->where($whereCondition3)
            ->whereIn('id', $t_c_ids)
            ->orderBy('start_time','desc')
            ->get();
        }


        $_coach_time_config = array();

        if (!empty($coach_time_config)) {
            // 1.获取教练时间配置列表
            foreach ($coach_time_config as $key => $value) {
                $_coach_time_config[$key]['id'] = $value->id;
                $_coach_time_config[$key]['license_no'] = $value->license_no;
                $_coach_time_config[$key]['subjects'] = $value->subjects;
                $_coach_time_config[$key]['price'] = $value->price;
                $_coach_time_config[$key]['status'] = $value->status;
                $_coach_time_config[$key]['start_time'] = $value->start_time;
                $_coach_time_config[$key]['end_time'] = $value->end_time;
                $_coach_time_config[$key]['start_minute'] = $value->start_minute;
                $_coach_time_config[$key]['end_minute'] = $value->end_minute;

                if (count($s_coach_lisence_id_list) == 1 && is_array($s_coach_lisence_id_list)) {
                    $_coach_time_config[$key]['license_no'] = isset($lisence_config[$s_coach_lisence_id_list[0]]) ? $lisence_config[$s_coach_lisence_id_list[0]] : 'C1';
                }
                if (count($s_coach_lesson_id_list) == 1 && is_array($s_coach_lesson_id_list)) {
                    $_coach_time_config[$key]['subjects'] = isset($lesson_config[$s_coach_lesson_id_list[0]]) ? $lesson_config[$s_coach_lesson_id_list[0]] : 'C1';
                }

                if (!empty($current_time_config)) {
                    // 获取是否设置的状态
                    if (in_array($value->id, $time_config_id)) {
                        $_coach_time_config[$key]['is_set'] = 1; //教练设置时间配置
                        $_coach_time_config[$key]['price'] = $time_config_money_id[$value->id];
                        $_coach_time_config[$key]['license_no'] = $time_lisence_config_id[$value->id];
                        $_coach_time_config[$key]['subjects'] = $time_lesson_config_id[$value->id];
                    } else {
                        $_coach_time_config[$key]['is_set'] = 2; //教练未设置时间配置
                        $_coach_time_config[$key]['subjects'] = $value->subjects;
                    }

                    if ($value->addtime != 0) {
                        $_coach_time_config[$key]['addtime'] = date('Y-m-d H:i:s', $value->addtime);
                    } else {
                        $_coach_time_config[$key]['addtime'] = '';
                    }

                    // 设置是否预约的状态
                    if (!empty($time_config_id_arr)) {
                        if (in_array($value->id, $time_config_id_arr)) {
                            $_coach_time_config[$key]['is_appoint'] = 1;//被预约
                        } else {
                            $_coach_time_config[$key]['is_appoint'] = 2;//未被预约
                        }
                    } else {
                        $_coach_time_config[$key]['is_appoint'] = 2;//未被预约
                    }

                } else {

                    // 若教练设置了上午和下午的时间
                    if (!empty($s_am_time_list) && !empty($s_pm_time_list)) {
                        if (in_array($value->id, $s_am_time_list)) {
                            if ($s_am_subject == 1) {
                                $_coach_time_config[$key]['subjects'] = '科目一';

                            } elseif ($s_am_subject == 2) {
                                $_coach_time_config[$key]['subjects'] = '科目二';

                            } elseif ($s_am_subject == 3) {
                                $_coach_time_config[$key]['subjects'] = '科目三';

                            }elseif ($s_am_subject == 4) {
                                $_coach_time_config[$key]['subjects'] = '科目四';

                            }
                        }

                        if (in_array($value->id, $s_pm_time_list)) {
                            if ($s_pm_subject == 1) {
                                $_coach_time_config[$key]['subjects'] = '科目一';

                            } elseif ($s_pm_subject == 2) {
                                $_coach_time_config[$key]['subjects'] = '科目二';

                            } elseif ($s_pm_subject == 3) {
                                $_coach_time_config[$key]['subjects'] = '科目三';

                            }elseif ($s_pm_subject == 4) {
                                $_coach_time_config[$key]['subjects'] = '科目四';

                            }
                        }

                    // 教练未设置，驾校设置了
                    } else {

                        if ($value->end_time <= 12) {
                            if ($s_am_subject == 1) {
                                $_coach_time_config[$key]['subjects'] = '科目一';

                            } elseif ($s_am_subject == 2) {
                                $_coach_time_config[$key]['subjects'] = '科目二';

                            } elseif ($s_am_subject == 3) {
                                $_coach_time_config[$key]['subjects'] = '科目三';

                            }elseif ($s_am_subject == 4) {
                                $_coach_time_config[$key]['subjects'] = '科目四';

                            }

                        } else {

                            if ($s_pm_subject == 1) {
                                $_coach_time_config[$key]['subjects'] = '科目一';

                            } elseif ($s_pm_subject == 2) {
                                $_coach_time_config[$key]['subjects'] = '科目二';

                            } elseif ($s_pm_subject == 3) {
                                $_coach_time_config[$key]['subjects'] = '科目三';

                            }elseif ($s_pm_subject == 4) {
                                $_coach_time_config[$key]['subjects'] = '科目四';
                            }
                        }
                    }
                    $_coach_time_config[$key]['is_set'] = 1;
                    // 设置是否预约的状态
                    if (!empty($time_config_id_arr)) {
                        if (in_array($value->id, $time_config_id_arr)) {
                            $_coach_time_config[$key]['is_appoint'] = 1;//被预约
                        } else {
                            $_coach_time_config[$key]['is_appoint'] = 2;//未被预约
                        }
                    } else {
                        $_coach_time_config[$key]['is_appoint'] = 2;//未被预约
                    }
                }
            }
            // 2.获取教练的上午与下午的时间设置
            $coach_am_list = array();
            $coach_pm_list = array();

            foreach ($_coach_time_config as $key => $value) {
                $start_minute = $value['start_minute'] == 0 ? '00' : $value['start_minute'];
                $end_minute = $value['end_minute'] == 0 ? '00' : $value['end_minute'];
                $_coach_time_config[$key]['start_time'] = $value['start_time'].':'.$start_minute;
                $_coach_time_config[$key]['end_time'] = $value['end_time'].':'.$end_minute;
                unset($_coach_time_config[$key]['id']);
                unset($_coach_time_config[$key]['school_id']);
                unset($_coach_time_config[$key]['addtime']);
                unset($_coach_time_config[$key]['status']);
                unset($_coach_time_config[$key]['start_minute']);
                unset($_coach_time_config[$key]['end_minute']);
                unset($_coach_time_config[$key]['is_set']);
            }
        }

        //$coach_time_list['time_list'] = $coach_time_config;
        // $coach_time_list['am_time_list'] = $coach_am_list;
        // $coach_time_list['pm_time_list'] = $coach_pm_list;
        // $coach_time_list['date'] = $month.'-'.$day;
        return $_coach_time_config;
    }

    // 获取默认时间配置表中的时间 coach_time_config
    public function getDefaultTimeConfig($time_ids=[]) {
        $query = DB::table('coach_time_config')
            ->select(
                'id',
                'start_time',
                'end_time',
                'license_no',
                'subjects',
                'price',
                'start_minute',
                'end_minute'
            )
            ->where(['status'=>1]);
        if(!empty($time_ids)) {
            $query = $query->whereIn('id', $time_ids);
        }
        $time_list = $query->get();
        $list = [];
        foreach($time_list as $key => $value) {
            $list[$key]['id'] = $value->id;
            $list[$key]['license_no'] = $value->license_no;
            $list[$key]['subjects'] = $value->subjects;
            $list[$key]['price'] = $value->price;
            $list[$key]['start_time'] = $value->start_time;
            $list[$key]['end_time'] = $value->end_time;
            $list[$key]['start_minute'] = $value->start_minute == '0' ? '00' : $value->start_minute;
            $list[$key]['end_minute'] = $value->end_minute == '0' ? '00' : $value->end_minute;
            $list[$key]['start_time_format'] = $value->start_time.':'.$list[$key]['start_minute'];
            $list[$key]['end_time_format'] = $value->end_time.':'.$list[$key]['end_minute'];
            $list[$key]['is_coach_set'] = 2; // 1：是教练设置的时间段 2：驾校设置
        }
        return $list;
    }

    // 获取教练设置的时间 current_coach_time_configuration
    public function getCurrentCoachTimeConfig($default_time_config, $coach_id, $year, $month, $day) {
        $coach_info = $this->user->getCoachInfoById($coach_id);
        $day_time_list = DB::table('current_coach_time_configuration')
            ->select(
                'id',
                'current_time',
                'time_config_money_id',
                'time_config_id',
                'time_lisence_config_id',
                'time_lesson_config_id'
            )
            ->where([
                'coach_id'=>$coach_id,
                'year'=>$year,
                'month'=>$month,
                'day'=>$day
            ])
            ->first();

        $list = [];

        if ( $day_time_list ) {
            // 教练自定义设置了时间
            $time_config_id_arr         = isset($day_time_list->time_config_id) ? array_filter(explode(',', $day_time_list->time_config_id)) : [];
            $time_lisence_config_id_arr = isset($day_time_list->time_lisence_config_id) ? json_decode($day_time_list->time_lisence_config_id, true) : [];
            $time_lesson_config_id_arr  = isset($day_time_list->time_lesson_config_id) ? json_decode($day_time_list->time_lesson_config_id, true) : [];
            $time_money_config_id_arr   = isset($day_time_list->time_config_money_id) ? json_decode($day_time_list->time_config_money_id, true) : [];

            if(!empty($time_config_id_arr)) {
                // 教练有打开的时间段
                foreach($time_config_id_arr as $key => $value) {

                    foreach ($default_time_config as $k => $v) {
                        if($value == $v['id']) {
                            $list[$key]['id'] = $value;
                            $list[$key]['license_no'] = isset($time_lisence_config_id_arr[$value]) ? $time_lisence_config_id_arr[$value] : 'C1';
                            $list[$key]['subjects'] = isset($time_lesson_config_id_arr[$value]) ? $time_lesson_config_id_arr[$value] : '科目二';
                            $list[$key]['price'] = isset($time_money_config_id_arr[$value]) ? $time_money_config_id_arr[$value] : '130';
                            $list[$key]['start_time'] = $v['start_time'];
                            $list[$key]['end_time'] = $v['end_time'];
                            $list[$key]['start_minute'] = $v['start_minute'] == '0' ? '00' : $v['start_minute'];
                            $list[$key]['end_minute'] = $v['end_minute'] == '0' ? '00' : $v['end_minute'];
                            $list[$key]['start_time_format'] = $v['start_time']. ':'.$list[$key]['start_minute'];
                            $list[$key]['end_time_format'] = $v['end_time']. ':'.$list[$key]['end_minute'];
                            $list[$key]['is_coach_set'] = 1; // 1：是教练设置的时间段 2：驾校设置
                            $list[$key]['coach_license_id_list'] = array_filter(explode(',', $coach_info->license_id));
                            $list[$key]['coach_lesson_id_list'] = array_filter(explode(',', $coach_info->lesson_id));
                        }
                    }
                }
            } else {
                // 教练将所有的时间段关闭了
                return null;
            }
        } else {
            // 教练没有自定义设置时间
            // 获取教练设置的上午和下午科目及时间安排，在coach表中
            $stop = 0;
            if ($coach_info) {
                $am_subject = ($coach_info->s_am_subject == '2') ? '科目二' : ( ($coach_info->s_am_subject == '3') ? '科目三' : '科目二');
                $pm_subject = ($coach_info->s_pm_subject == '2') ? '科目二' : ( ($coach_info->s_pm_subject == '3') ? '科目三' : '科目三');
                $am_time_list = array_filter(explode(',', $coach_info->s_am_time_list));
                $pm_time_list = array_filter(explode(',', $coach_info->s_pm_time_list));
                if ( ! empty($am_time_list) ) {
                    foreach($am_time_list as $key => $value) {
                        foreach ($default_time_config as $k => $v) {
                            if($value == $v['id']) {
                                $list[$key]['id'] = $value;
                                $list[$key]['license_no'] = isset($time_lisence_config_id_arr[$value]) ? $time_lisence_config_id_arr[$value] : 'C1';
                                $list[$key]['subjects'] = isset($time_lesson_config_id_arr[$value]) ? $time_lesson_config_id_arr[$value] : '科目二';
                                $list[$key]['price'] = isset($time_money_config_id_arr[$value]) ? $time_money_config_id_arr[$value] : '130';
                                $list[$key]['start_time'] = $v['start_time'];
                                $list[$key]['end_time'] = $v['end_time'];
                                $list[$key]['start_minute'] = $v['start_minute'] == '0' ? '00' : $v['start_minute'];
                                $list[$key]['end_minute'] = $v['end_minute'] == '0' ? '00' : $v['end_minute'];
                                $list[$key]['start_time_format'] = $v['start_time']. ':'.$list[$key]['start_minute'];
                                $list[$key]['end_time_format'] = $v['end_time']. ':'.$list[$key]['end_minute'];
                                $list[$key]['is_coach_set'] = 1; // 1：是教练设置的时间段 2：驾校设置
                                $list[$key]['coach_license_id_list'] = array_filter(explode(',', $coach_info->license_id));
                                $list[$key]['coach_lesson_id_list'] = array_filter(explode(',', $coach_info->lesson_id));
                            }
                        }
                    }
                    $stop = $key+1;
                }

                if ( ! empty($pm_time_list) ) {
                    foreach($pm_time_list as $key_another => $value) {
                        $key = $key_another + $stop;
                        foreach ($default_time_config as $k => $v) {
                            if($value == $v['id']) {
                                $list[$key]['id'] = $value;
                                $list[$key]['license_no'] = isset($time_lisence_config_id_arr[$value]) ? $time_lisence_config_id_arr[$value] : 'C1';
                                $list[$key]['subjects'] = isset($time_lesson_config_id_arr[$value]) ? $time_lesson_config_id_arr[$value] : '科目二';
                                $list[$key]['price'] = isset($time_money_config_id_arr[$value]) ? $time_money_config_id_arr[$value] : '130';
                                $list[$key]['start_time'] = $v['start_time'];
                                $list[$key]['end_time'] = $v['end_time'];
                                $list[$key]['start_minute'] = $v['start_minute'] == '0' ? '00' : $v['start_minute'];
                                $list[$key]['end_minute'] = $v['end_minute'] == '0' ? '00' : $v['end_minute'];
                                $list[$key]['start_time_format'] = $v['start_time']. ':'.$list[$key]['start_minute'];
                                $list[$key]['end_time_format'] = $v['end_time']. ':'.$list[$key]['end_minute'];
                                $list[$key]['is_coach_set'] = 1; // 1：是教练设置的时间段 2：驾校设置
                                $list[$key]['coach_license_id_list'] = array_filter(explode(',', $coach_info->license_id));
                                $list[$key]['coach_lesson_id_list'] = array_filter(explode(',', $coach_info->lesson_id));
                            }
                        }
                    }
                }
            }
        }
        return $list;
    }

    // 获取日期时间配置
    public function getCoachDateList() {

        if(!$this->request->has('coach_id')) {
            return response()->json([
                'code' => 400,
                'msg'  => '参数错误',
                'data' => ''
            ]);
        }

        $coach_id = $this->request->input('coach_id');
        $school_id = DB::table('coach')->where('l_coach_id', $coach_id)->value('s_school_name_id');

        $_data = [];
        $tips = '';
        $user = (new AuthController())->getUserFromToken($this->request->input('token'));
        $user_id = $user['user_id'];
        $user_info = $this->user->getUserInfoById($user_id);
        $coach_info = $this->user->getCoachInfoById($coach_id);
        if (isset($coach_info->coach_name) && isset($user_info->license_name) && isset($user_info->lesson_name)) {
            $appoint_desc = '您选择的是: '.$coach_info->coach_name.' | '.$user_info->license_name.' | '.$user_info->lesson_name.' | ';
        }

        $lesson_arr = [
            '1' => '科目一',
            '2' => '科目二',
            '3' => '科目三',
            '4' => '科目四',
        ];

        $license_list = DB::table('license_config')
            ->select(
                'license_id',
                'license_name'
            )
            ->where([
                ['is_open', '=', 1],
            ])
            ->get();
        $license_arr = [];
        foreach ($license_list as $license) {
            $license_arr[$license->license_id] = $license->license_name;
        }

        $license_no = isset($user_info->license_name) && $user_info->license_name != '' ? $user_info->license_name : '';
        $lesson_no = isset($user_info->lesson_id) ? $user_info->lesson_id : 0;
        $coach_license_id_arr = explode(',', $coach_info->license_id);
        $coach_lesson_id_arr = explode(',', $coach_info->lesson_id);

        $coach_lesson_name_arr = [];
        foreach ($coach_lesson_id_arr as $lesson_id) {
            if (isset($lesson_arr[$lesson_id])) {
                $coach_lesson_name_arr[$lesson_id] = $lesson_arr[$lesson_id];
            }
        }
        $coach_lesson_name = implode(',', $coach_lesson_name_arr);

        $coach_license_name_arr = [];
        foreach($coach_license_id_arr as $license) {
            if (isset($license_arr[$license])) {
                $coach_license_name_arr[$license]= $license_arr[$license];
            }
        }

        $coach_license_name = implode(',', $coach_license_name_arr);

        // 获取当前用户报名班制的信息
        $order_info = $this->order->getUserShiftOrders($user_id);
        $_data['shifts_info'] = [
            'tips'=>$appoint_desc.$tips,
            'school_id'=> -1,
            'sh_type'=>-1,
            'sh_title'=>''
        ];

        if(!empty($order_info)) {
            // 有免费quota的时候提示语也顺应修改说明一下
            $quota = 0;
            $signed_license_name = '';
            $signup_order_info = DB::table('school_orders')
                ->select(
                    'id as order_id',
                    'so_licence as license_name',
                    'free_study_hour'
                )
                ->where([
                    ['school_orders.so_order_status', '<>', 101],
                    ['school_orders.so_user_id', '=', $user_id],
                ])
                ->where(function ($query) {
                    $query->where(function ($query) {
                        // 线下支付，已付款
                        $query->where('school_orders.so_pay_type', '=', 2)
                            ->where('school_orders.so_order_status', '=', 3);
                    })
                        ->orWhere(function ($query) {
                            // 线上支付(支付宝1, 微信3, 银联4)，已付款
                            $query->whereIn('school_orders.so_pay_type', [1, 3, 4])
                                ->where('school_orders.so_order_status', '=', 1);
                        });
                })
                ->orderBy('id', 'desc')
                ->first();

            if ($signup_order_info) {
                if (isset($signup_order_info->free_study_hour) && intval($signup_order_info->free_study_hour) >= 0) {
                    $free_study_hour = intval($signup_order_info->free_study_hour);
                    $appoint_order_info = DB::table('study_orders')
                        ->select(DB::raw('SUM(i_service_time) as total_study_hour'))
                        ->where([
                            ['study_orders.l_user_id', '=', $user_id],
                            ['study_orders.dc_money', '<=', 0],
                        ])
                        ->whereIn('study_orders.i_status', [1, 2, 1003, 1001]) // 已付款1 ，已完成2 ，未付款1003, 付款中1001
                        ->first();
                    if ($appoint_order_info && isset($appoint_order_info->total_study_hour)) {
                        $total_study_hour = intval($appoint_order_info->total_study_hour);
                    } else {
                        $total_study_hour = 0;
                    }

                    $quota = ($free_study_hour-$total_study_hour) > 0 ? ($free_study_hour-$total_study_hour) : 0;
                }

                if (isset($signup_order_info->license_name)) {
                    if (!empty($signup_order_info->license_name)) {
                        $signed_license_name = $signup_order_info->license_name;
                    } else {
                        Log::Info('报名的班制信息不全，license_name为空'.json_encode($user_info, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                    }
                }
            }
            // 有免费quota的时候提示语也顺应修改说明一下

            if($school_id == $order_info->sh_school_id) {
                if($order_info->sh_type == 1) {
                    $tips = $appoint_desc.'您已报名'.$order_info->sh_title.'('.$order_info->sh_license_name.')，预约练车计时收费';
                } else {
                    $tips = $appoint_desc.'您已报名'.$order_info->sh_title.'('.$order_info->sh_license_name.')，可免费预约练车';
                }
                if ($quota > 0) {
                    $tips .= '(还可免费预约学时:'.$quota.'个)';
                }
            } else {
                    $tips = '您未报名此驾校，将实行计时收费';
            }

            if (!empty($signed_license_name)) {
                if ($signed_license_name != $user_info->license_name) {
                    $tips = $appoint_desc.'您报名的班制'.$signed_license_name.'与您的考证类型'.$user_info->license_name.'不符，因此无法预约！';
                }
            }

            // 不支持计时的话，不能约
            if ($coach_info->order_receive_status == 0 || $coach_info->timetraining_supported == 0) {
                $tips = $appoint_desc.'暂时不支持预约计时';
            }

            try {
                // 科目不一致的情况
                if ( ! in_array($lesson_no, $coach_lesson_id_arr)) {
                    $tips = $appoint_desc.'您的科目'.$user_info->lesson_name.'与该教练设置的科目'.$coach_lesson_name.'不一致';
                }

                // 牌照不一致的情况
                if ( ! in_array($license_no, $coach_license_name_arr)) {
                    $tips = $appoint_desc.'您的牌照'.$user_info->license_name.'与该教练设置的牌照'.$coach_license_name.'不一致';
                }
            } catch(Exception $e){
                Log::Info('File:'.$e->getFile().'Line:'.$e->getLine().',Error:'.$e->getMessage());
            }

            $_data['shifts_info'] = [
                'tips'=>$tips,
                'school_id'=> $order_info->sh_school_id,
                'sh_type'=> $order_info->sh_type,
                'sh_title'=> $order_info->sh_title
            ];
        } else {
            try {
                // 科目不一致
                if ( ! in_array($lesson_no, $coach_lesson_id_arr)) {
                    $tips = $appoint_desc.'您的科目'.$user_info->lesson_name.'与该教练设置的科目'.$coach_lesson_name.'不一致';
                }

                // 牌照不一致
                Log::Info(json_encode($coach_info->license_id,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
                if ( ! in_array($license_no, $coach_license_name_arr)) {
                    $tips = $appoint_desc.'您的牌照'.$user_info->license_name.'与该教练设置的牌照'.$coach_license_name.'不一致';
                }
            } catch(Exception $e){
                Log::Info('File:'.$e->getFile().'Line:'.$e->getLine().',Error:'.$e->getMessage());
            }

            if ($tips) {
                $_data['shifts_info']['tips'] = $tips;

            } else {
                $_data['shifts_info']['tips'] = $appoint_desc.'您未报名此驾校，计时收费';

            }
        }

        $date_list = $this->getDateList(6);
        $_data['date_list'] = $date_list;
        $data = ['code'=>200, 'msg'=>'获取日期成功', 'data'=>$_data];
        return response()->json($data);
    }

    // 获取默认从今天开始的7天日期
    private function getDateList($limit=6) {
        $current_time = time();
        $year = date('Y', $current_time); //年
        $month = intval(date('m', $current_time)); //月
        $day = intval(date('d', $current_time)); //日

        // 构建一个时间
        $build_date_timestamp = mktime(0,0,0,$month,$day,$year);

        // 循环7天日期
        $date_config = array();
        for($i = 0; $i <= $limit; $i++) {
            $date_config[$i]['date'] = date('m-d', $build_date_timestamp + ( 24 * 3600 * $i));
            $date_config[$i]['year'] = intval(date('Y', $build_date_timestamp + ( 24 * 3600 * $i)));
            $date_config[$i]['month'] = intval(date('m', $build_date_timestamp + ( 24 * 3600 * $i)));
            $date_config[$i]['day'] = intval(date('d', $build_date_timestamp + ( 24 * 3600 * $i)));
            $date_config[$i]['date_format'] = intval(date('m', $build_date_timestamp + ( 24 * 3600 * $i))).'-'.intval(date('d', $build_date_timestamp + ( 24 * 3600 * $i)));

        }
        return $date_config;
    }
}

