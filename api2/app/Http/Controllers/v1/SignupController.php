<?php
/**
 * 报名模块
 *
 * @return void
 * @author cx
 * lumen database doc link: https://laravel.com/docs/5.3/queries
 **/

namespace App\Http\Controllers\v1;

use Log;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\v1\AuthController;
use Illuminate\Http\Request;

class SignupController extends Controller {

    protected $request;
    protected $user;
    protected $auth;

    public function __construct(Request $request) {
        $this->request = $request;
        $this->user = new UserController($this->request);
        $this->auth = new AuthController($this->request);
    }
    
    /**
     * 获取首页推荐驾校列表
     * @param   number  city_id     城市ID
     * @param   string  lat         纬度
     * @param   string  lng         经度
     * @return void
     **/
    public function getIndexRecommendSchoolList () {

        if( ! $this->request->has('city_id')
            OR ! $this->request->has('lat')
            OR ! $this->request->has('lng'))
        {
            Log::error('异常：【获取首页推荐驾校列表】缺少必须参数');
            return response()->json([
                'code'=>400,
                'msg'=>'参数错误',
                'data'=>'',
            ]);
        }

        $city_id = $this->request->input('city_id');
        $lat = $this->request->input('lat');
        $lng = $this->request->input('lng');

        $where[] = ['school.is_show', '=', 1]; // 1:展示 2:不展示
        $where[] = ['school.city_id', '=', $city_id];
        $condition = ['school.is_hot', '=', 1]; // 1:热门 2:非热门

        $school_list = DB::table('school')
            ->select(
                'l_school_id as school_id',
                's_school_name as school_name',
                'province_id as province_id',
                'city_id as city_id',
                's_thumb as thumb_imgurl'
            )
            ->where([
                ['school.is_show', '=', 1], // 1:展示 2:不展示
                ['school.city_id', '=', $city_id],
                ['school.is_hot', '=', 1], // 1:热门 2:非热门
            ])
            ->orderBy('s_order', 'asc')
            ->take(2)
            ->get();
        
        if ( empty($school_list->toArray())) {
            $school_list = DB::table('school')
            ->select(
                'l_school_id as school_id',
                's_school_name as school_name',
                'province_id as province_id',
                'city_id as city_id',
                's_thumb as thumb_imgurl'
            )
            ->where([
                ['school.is_show', '=', 1], // 1:展示 2:不展示
                ['school.city_id', '=', $city_id],
            ])
            ->orderBy('s_order', 'asc')
            ->take(2)
            ->get();
        }

        // $shifts_list = [];
        if ($school_list) {
            foreach ($school_list as $key => $value) {

                // 驾校头像
                $school_list[$key]->thumb_imgurl = $this->buildUrl($value->thumb_imgurl);

                $school_id = $value->school_id;
                
                // 驾校星级
                $comment_list = $this->getSchoolCommentList($school_id);
                if ($comment_list && $comment_list->average_star) {
                    $school_list[$key]->school_star = $comment_list->average_star;
                } else {
                    $school_list[$key]->school_star = 3;
                }
                
                // 已报名人数
                $school_list[$key]->signup_num = $this->getSchoolOrdersNum($school_id);

                // 驾校班制
                $shifts_list = $this->getSchoolShiftsByCount($school_id, 2);
                $school_list[$key]->shifts_info = $shifts_list;

            }
        }

        $data = [
            'code'  => 200,
            'msg'   => '获取成功',
            'data'  => ['school_list' => $school_list],
        ];

        return response()->json($data);

    }

    /**
     * 获取驾校班制信息(条数：有count决定[0:查询所有])
     **/
    private function getSchoolShiftsByCount ($school_id, $count = 0) {
         $where = function ($query) {
            $query->whereNull('coach_id')
            ->orWhere('coach_id', '=', 0)
            ->orWhere('coach_id', '=', '');
        };
        $shifts_info = DB::table('school_shifts')
            ->select(
                'id',
                'sh_school_id',
                'sh_title',
                'sh_money',
                'sh_original_money',
                'sh_tag',
                'sh_description_1 as sh_tag_two',
                'is_package',
                'sh_type',
                'sh_description_2 as sh_description',
                'is_promote',
                'sh_license_id',
                'sh_license_name'
            )
            ->where([
                ['deleted', '=', '1'],
                ['sh_school_id', '=', $school_id]
            ])
            ->where($where);
            if ($count > 0) {
                $shifts_info = $shifts_info
                    ->take($count)
                    ->get();
            } else {
                $shifts_info = $shifts_info
                    ->get();
            }

        $shifts_list = [];
        if ($shifts_info) {
            foreach ($shifts_info as $key => $value) {
                
                $shifts_info[$key]->sh_money = $value->sh_money;
                $shifts_info[$key]->sh_original_money = intval($value->sh_original_money);
                if ( 1 == $value->is_package) {
                    $shifts_info[$key]->package_intro = '套餐';
                } else {
                    $shifts_info[$key]->package_intro = '非套餐';
                }
            }

            // 获取班制总数
            $shifts_count = DB::table('school_shifts')
                ->select(DB::raw('count(1) as shifts_count'))
                ->where([
                    ['deleted', '=', '1'],
                    ['sh_school_id', '=', $school_id]
                ])
                ->where($where)
                ->first();
            if ($shifts_count->shifts_count <= 3) {
                $is_shifts_more = 1; // 没有更多
            } else {
                $is_shifts_more = 2; // 查看更多
            }
        } else {
            $is_shifts_more = 1; // 没有更多
        }

        $shifts_list['is_shifts_more'] = $is_shifts_more;
        $shifts_list['shifts_list'] = $shifts_info;

        return $shifts_list;
    }

    /**
     * 分享报名驾校班制接口
     * @param   id  number  驾校ID
     * @param   lat string  纬度
     * @param   lng string  经度
     * @param   device number  设备类型（1：ios | 2：android）
     * @return  void
     **/
    public function shareShifts () {

        if ( ! $this->request->has('id')
            OR ! $this->request->has('lat')
            OR ! $this->request->has('lng')) 
        {
            $data = [
                'code'  => 400,
                'msg'   => '参数错误',
                'data'  => new \stdClass,
            ];
            log::eror('异常：【分享报名驾校班制】缺少必须参数');
            return response()->json($data);
        }

        $school_id = $this->request->input('id');
        $lat = $this->request->input('lat');
        $lng = $this->request->input('lng');

        if ( $this->request->has('device')) {
            $device = $this->request->input('device');
        } else {
            $device = 1;
        }
        
        if ( ! in_array($device, [1, 2])) {
            log::error('异常：【分享报名驾校班制】设备类型'.$device.'不在[1,2]的范围内');
            return response()->json([
                'code'  => 400,
                'msg'   => '参数错误',
                'data'  => new \stdClass,
            ]);
        }

        $school_list = DB::table('school')
            ->select('l_school_id')
            ->where([
                ['l_school_id', '=', $school_id],
                ['is_show', '=', 1],
            ])
            ->first();

        if ( ! $school_list) {
            log::error('异常：【分享报名驾校班制】当前驾校已下线');
            return response()->json([
                'code'  => 400,
                'msg'   => '当前驾校已下线',
                'data'  => new \stdClass,
            ]);
        }

        $url = env('APP_PATH').'m/v2/student/public/school/detail?id='.$school_id.'&device='.$device.'&lat='.$lat.'&lng='.$lng;
        $share_content = '这里有个不错的学车软件，快来和我一起学车吧！';
        $to_share = [
            'share_title'   => '嘻哈学车',
            'share_content' => $share_content,
            'share_link'    => $url,
            'share_pic'     => '',
        ];

        $data = [
            'code' => 200,
            'msg'  => 'OK',
            'data' => $to_share,
        ];

        return response()->json($data);

    }

    /**
     * 学车套餐分享
     * @param   device number 设备类型（1：ios | 2：android）
     * @return  void
     **/
    public function sharePackageShifts () {

        if ($this->request->has('device')) {
            $device = $this->request->input('device');
        } else {
            $device = 1;
        }

        if ( ! in_array($device, [1, 2])) {
            log::error('异常：【分享报名驾校班制】设备类型'.$device.'不在[1,2]的范围内');
            return response()->json([
                'code'  => 400,
                'msg'   => '参数错误',
                'data'  => new \stdClass,
            ]);
        }

        $school_list = DB::table('school')
            ->select(
                'l_school_id',
                'shifts.id'
            )
            ->leftJoin('school_shifts as shifts', 'shifts.sh_school_id', '=', 'school.l_school_id')
            ->where([
                ['is_show', '=', 1],
                ['shifts.deleted', '=', 1],
                ['shifts.is_package', '=', 1],
            ])
            ->first();

        if ( ! $school_list) {
            log::error('异常：【分享报名驾校班制】暂无学车套餐');
            return response()->json([
                'code'  => 400,
                'msg'   => '暂无学车套餐',
                'data'  => new \stdClass,
            ]);
        }

        $url = env('APP_PATH').'m/v2/student/public/school/packageshifts?device='.$device;
        $share_content = '这里有个不错的学车软件，快来和我一起学车吧！';
        $to_share = [
            'share_title'   => '嘻哈学车',
            'share_content' => $share_content,
            'share_link'    => $url,
            'share_pic'     => '',
        ];

        $data = [
            'code' => 200,
            'msg'  => 'OK',
            'data' => $to_share,
        ];

        return response()->json($data);

    }


    // 获取首页推荐教练列表
    public function getIndexRecommendCoachList() {

        if(!$this->request->has('city_id')) {
            return response()->json([
                'code'=>400,
                'msg'=>'参数错误',
                'data'=>'',
            ]);
        }

        $city_id = $this->request->input('city_id');

        // 获取教练基本信息
        $query = DB::table('coach')
            ->select(
                'coach.s_school_name_id as school_id',
                'school.s_school_name as school_name',
                'coach.l_coach_id as coach_id',
                'coach.s_coach_name as coach_name',
                'coach.s_coach_phone as coach_phone',
                'coach.s_coach_imgurl as coach_imgurl',
                'coach.i_type',
                'coach.certification_status',
                'coach.timetraining_min_price',
                'coach.i_coach_star as coach_star',
                'coach.timetraining_supported',
                'coach.coupon_supported'
            )
            ->leftJoin('school', 'coach.s_school_name_id', '=', 'school.l_school_id');

        $whereCondition = [
            ['coach.city_id', '=', $city_id],
            ['school.city_id', '=', $city_id],
            ['coach.order_receive_status', '=', '1'],
            ['coach.is_hot', '=', '2'],
        ];

        $coach_list = $query->where($whereCondition)
            ->orderBy('coach.i_order', 'desc')
            ->take(4)
            ->get();

        if(empty($coach_list->toArray())) {
            $query = DB::table('coach')
            ->select(
                'coach.s_school_name_id as school_id',
                'school.s_school_name as school_name',
                'coach.l_coach_id as coach_id',
                'coach.s_coach_name as coach_name',
                'coach.s_coach_phone as coach_phone',
                'coach.s_coach_imgurl as coach_imgurl',
                'coach.i_type',
                'coach.certification_status',
                'coach.timetraining_min_price',
                'coach.i_coach_star as coach_star',
                'coach.coupon_supported'

            )
            ->leftJoin('school', 'coach.s_school_name_id', '=', 'school.l_school_id');

            $_whereCondition = [
                ['coach.city_id', '=', $city_id],
                ['school.city_id', '=', $city_id],
                ['coach.order_receive_status', '=', '1'],
            ];
            $coach_list = $query->where($_whereCondition)
                ->orderBy('coach.i_order', 'desc')
                ->take(3)
                ->get();

            if(empty($coach_list->toArray())) {
                return response()->json([
                    'code'=>200,
                    'msg'=>'不存在热门教练',
                    'data'=>[],
                ]);
            }

        }

        $coach_ids = array();
        foreach ($coach_list->toArray() as $key => $value) {
            $coach_list[$key]->coach_imgurl = $this->buildUrl($value->coach_imgurl);
            $coach_list[$key]->hot_params = $this->getHotParams($value->i_type); // 教练的类型标签:二级、三级、四级教练员等
            $coach_ids[] = $value->coach_id;
        }
        // 是否有学车优惠券
        $current_time = time();
        $ticket_list = DB::table('coupon')
            ->select('id as coupon_id', 'owner_id', 'coupon_value', 'coupon_name')
            ->where([
                ['owner_type', '=', '1'], // 1：教练，2：驾校
                ['coupon_category_id', '=', '1'], // 1、现金券，2、打折券
                ['scene' ,'=', '1'], // 1:报名班制 2:预约学车
                ['expiretime', '>', $current_time],
                ['is_open', '=', '1'],
                ['is_show', '=', '1'], // 打开展示功能
            ])
            ->whereIn('owner_id', array_values($coach_ids))
            ->get();

        if(!empty($ticket_list->toArray())) {
            foreach ($coach_list->toArray() as $key => $value) {
                $coach_list[$key]->ticket_info = [];
                foreach ($ticket_list->toArray() as $k => $v) {
                    if($value->coach_id == $v->owner_id) {
                        $coach_list[$key]->ticket_info[] = $ticket_list[$k];
                    }
                }
            }
        } else {
            foreach ($coach_list->toArray() as $k => $v) {
                $coach_list[$k]->ticket_info = [];
            }
        }
        $shifts_list = [];
        foreach ($coach_list->toArray() as $key => $value) {

            // 从教练班制表中获取教练设置班制
            $coach_shifts_list = DB::table('school_shifts')
                ->select(
                    'id',
                    'sh_school_id',
                    'sh_title',
                    'sh_money',
                    'sh_original_money',
                    'sh_tag',
                    'sh_type',
                    'sh_description_2 as sh_description',
                    'is_promote',
                    'sh_license_id',
                    'sh_license_name'
                )
                ->where([
                    ['coach_id', '=', $value->coach_id],
                    ['deleted', '=', '1'],
                ])
                ->orderBy('order', 'desc')
                ->take(2)
                ->get();

            if(!empty($coach_shifts_list->toArray())) {
                foreach ($coach_shifts_list->toArray() as $k => $val) {
                    $coach_shifts_list[$k]->sh_category = 2;  // 教练设置的班制
                }
                $shifts_list = $coach_shifts_list;
            } else {
                $school_shifts_list = DB::table('school_shifts')
                    ->select(
                        'id',
                        'sh_school_id',
                        'sh_title',
                        'sh_money',
                        'sh_original_money',
                        'sh_tag',
                        'sh_type',
                        'sh_description_2 as sh_description',
                        'is_promote',
                        'sh_license_id',
                        'sh_license_name'
                    )
                    ->where([
                        ['deleted', '=', '1'],
                        ['sh_school_id', '=', $value->school_id],
                    ])
                    ->orderBy('order', 'desc')
                    ->take(2)
                    ->get();
                if(!empty($school_shifts_list->toArray())) {
                    foreach ($school_shifts_list->toArray() as $k => $val) {
                        $school_shifts_list[$k]->sh_category = 1; // 驾校设置的班制
                    }
                }
                $shifts_list = $school_shifts_list;
            }

            if(!empty($shifts_list->toArray())) {
                foreach ($shifts_list->toArray() as $k => $val) {
                    $shifts_list[$k]->sh_description = trim($val->sh_description);
                    $shifts_list[$k]->sh_title = trim($val->sh_title);
                }
            }
            $coach_list[$key]->shifts_list = $shifts_list;
        }
        $data = ['code'=>200, 'msg'=>'获取推荐教练成功', 'data'=>$coach_list];
        return response()->json($data);

    }


    // 获取首页学车套餐信息
    public function getIndexShiftsList () {

        if(!$this->request->has('city_id')) {
            return response()->json([
                'code'=>400,
                'msg'=>'参数错误',
                'data'=>'',
            ]);
        }

        $city_id = $this->request->input('city_id');
        
        // 获取(班制)学车套餐信息
        $shifts_info = [];
        $school_shifts = [];
        $package_shifts = DB::table('school_shifts')
            ->select(
                'school_shifts.id as id',
                'school_shifts.sh_school_id as sh_school_id',
                'school_shifts.coach_id as coach_id',
                'school_shifts.sh_title as sh_title',
                'school_shifts.sh_money as sh_money',
                'school_shifts.sh_original_money as sh_original_money',
                'school_shifts.sh_tag as sh_tag',
                'school_shifts.sh_description_1 as sh_tag_two',
                'school_shifts.sh_type as sh_type',
                'school_shifts.sh_info as sh_info',
                'school_shifts.is_promote as is_promote',
                'school_shifts.sh_license_id as sh_license_id',
                'school_shifts.sh_license_name as sh_license_name',
                'school_shifts.sh_tag_id as tag_id',
                'school_shifts.sh_imgurl as sh_imgurl',
                'school.s_school_name as school_name',
                'school.s_thumb as thumb_img'
            )
            ->where([
                ['is_package', '=', 1], // 1:套餐 2:非套餐
                ['school.city_id', '=', $city_id],
                ['school.is_show', '=', 1],
                ['school_shifts.deleted', '=', 1] 
            ]) 
            ->leftJoin('school', 'school.l_school_id', '=', 'school_shifts.sh_school_id')
            ->orderBy('school_shifts.order', 'asc')
            ->take(2)
            ->get();
        if ($package_shifts) {
            foreach ($package_shifts as $index => $value) {
                // id
                $school_shifts[$index]['id'] = $value->id;

                // sh_school_id
                $school_shifts[$index]['sh_school_id'] = $value->sh_school_id;

                // sh_title
                $school_shifts[$index]['sh_title'] = $value->sh_title;

                // sh_money
                $school_shifts[$index]['sh_money'] = $value->sh_money;

                // sh_original_money
                $school_shifts[$index]['sh_original_money'] = $value->sh_original_money;
                
                // sh_tag
                $school_shifts[$index]['sh_tag'] = $value->sh_tag;
                
                // sh_tag_two
                $school_shifts[$index]['sh_tag_two'] = $value->sh_tag_two;

                // sh_type
                $school_shifts[$index]['sh_type'] = $value->sh_type;
                
                // sh_description
                $school_shifts[$index]['sh_description'] = $value->sh_info;

                // is_promote
                $school_shifts[$index]['is_promote'] = $value->is_promote;
                
                // sh_license_id
                $school_shifts[$index]['sh_license_id'] = $value->sh_license_id;
                
                // sh_license_name
                $school_shifts[$index]['sh_license_name'] = $value->sh_license_name;
                
                if ($value->coach_id != ''
                    && $value->coach_id != NULL
                    && $value->coach_id != 0) 
                {
                    $coach_info = DB::table('coach')
                        ->select('l_coach_id')
                        ->leftJoin('user', 'user.l_user_id', '=', 'coach.user_id')
                        ->where([
                            ['user.i_user_type', '=', 1],
                            ['user.i_status', '=', 0],
                            ['l_coach_id', '=', $value->coach_id],
                            ['order_receive_status', '=', 1] // 1：在线 0：不在线
                        ]) 
                        ->first();
                    if ($coach_info) {
                        $school_shifts[$index]['sh_category'] = 2;
                    } else {
                        $school_shifts[$index]['sh_category'] = 1;
                    }
                } else {
                    $school_shifts[$index]['sh_category'] = 1;
                }

                // school_name
                if ($value->school_name == '') {
                    $school_shifts[$index]['school_name'] = '嘻哈平台';
                } else {
                    $school_shifts[$index]['school_name'] = $value->school_name;
                }

                // school_imgurl
                // $school_shifts[$index]['school_imgurl'] = $this->buildUrl($value->thumb_img);
                $sh_imgurl = $this->buildUrl($value->sh_imgurl);
                if ($sh_imgurl == '') {
                    $sh_imgurl = $this->buildUrl($value->thumb_img);
                }
                $school_shifts[$index]['school_imgurl'] = $sh_imgurl;

                // price_unit
                $school_shifts[$index]['price_unit'] = '￥';
                
            }
        } 

        if ($school_shifts) {
            $path = env('APP_UPLOAD_PATH');
            $index_headimgurl = $path.'background/index_headbg.png';
            $index_bgimgurl = $path.'background/index_bg.png';
            $shifts_info = [
                'index_headimgurl'  => $path.'background/index_headbg.png',
                'index_bgimgurl'    => $path.'background/index_bg.png',
                'shifts_info'       => $school_shifts,
            ];
        } else {
            $shifts_info = new \stdClass;
        }

        $data = [
            'code'  => 200, 
            'msg'   => '获取成功', 
            'data'  => $shifts_info,
        ];
        return response()->json($data);
    }

    /**
     * 获取学车套餐列表
     *
     * @param   int     $city_id
     * @return  void
     */
    public function getPackageShiftsList () {

        if(!$this->request->has('city_id')) {
            return response()->json([
                'code'=>400,
                'msg'=>'参数错误',
                'data'=>'',
            ]);
        }

        $city_id = $this->request->input('city_id');

        // 获取(班制)学车套餐信息
        $shifts_info = [];
        $school_shifts = [];
        $package_shifts = DB::table('school_shifts')
            ->select(
                'school_shifts.id as id',
                'school_shifts.sh_school_id as sh_school_id',
                'school_shifts.coach_id as coach_id',
                'school_shifts.sh_title as sh_title',
                'school_shifts.sh_money as sh_money',
                'school_shifts.sh_original_money as sh_original_money',
                'school_shifts.sh_tag as sh_tag',
                'school_shifts.sh_description_1 as sh_tag_two',
                'school_shifts.sh_type as sh_type',
                'school_shifts.sh_info as sh_info',
                'school_shifts.is_promote as is_promote',
                'school_shifts.sh_license_id as sh_license_id',
                'school_shifts.sh_license_name as sh_license_name',
                'school_shifts.sh_tag_id as tag_id',
                'school_shifts.sh_imgurl as sh_imgurl',
                'school.s_school_name as school_name',
                'school.s_thumb as thumb_img'
            )
            ->where([
                ['is_package', '=', 1], // 1:套餐 2:非套餐
                ['school.city_id', '=', $city_id],
                ['school.is_show', '=', 1],
                ['school_shifts.deleted', '=', 1] 
            ]) 
            ->leftJoin('school', 'school.l_school_id', '=', 'school_shifts.sh_school_id')
            ->orderBy('school_shifts.order', 'asc')
            ->paginate(10);

        if ($package_shifts) {
            foreach ($package_shifts as $index => $value) {
                // id
                $school_shifts[$index]['id'] = $value->id;

                // sh_school_id
                $school_shifts[$index]['sh_school_id'] = $value->sh_school_id;

                // sh_title
                $school_shifts[$index]['sh_title'] = $value->sh_title;

                // sh_money
                $school_shifts[$index]['sh_money'] = $value->sh_money;

                // sh_original_money
                $school_shifts[$index]['sh_original_money'] = $value->sh_original_money;
                
                // sh_tag
                $school_shifts[$index]['sh_tag'] = $value->sh_tag;
                
                // sh_tag_two
                $school_shifts[$index]['sh_tag_two'] = $value->sh_tag_two;

                // sh_type
                $school_shifts[$index]['sh_type'] = $value->sh_type;
                
                // sh_description
                $school_shifts[$index]['sh_description'] = $value->sh_info;

                // is_promote
                $school_shifts[$index]['is_promote'] = $value->is_promote;
                
                // sh_license_id
                $school_shifts[$index]['sh_license_id'] = $value->sh_license_id;
                
                // sh_license_name
                $school_shifts[$index]['sh_license_name'] = $value->sh_license_name;
                
                if ($value->coach_id != ''
                    && $value->coach_id != NULL
                    && $value->coach_id != 0) 
                {
                    $coach_info = DB::table('coach')
                        ->select('l_coach_id')
                        ->leftJoin('user', 'user.l_user_id', '=', 'coach.user_id')
                        ->where([
                            ['user.i_user_type', '=', 1],
                            ['user.i_status', '=', 0],
                            ['l_coach_id', '=', $value->coach_id],
                            ['order_receive_status', '=', 1] // 1：在线 0：不在线
                        ]) 
                        ->first();
                    if ($coach_info) {
                        $school_shifts[$index]['sh_category'] = 2;
                    } else {
                        $school_shifts[$index]['sh_category'] = 1;
                    }
                } else {
                    $school_shifts[$index]['sh_category'] = 1;
                }

                // school_name
                if ($value->school_name == '') {
                    $school_shifts[$index]['school_name'] = '嘻哈平台';
                } else {
                    $school_shifts[$index]['school_name'] = $value->school_name;
                }

                // school_imgurl
                // $school_shifts[$index]['school_imgurl'] = $this->buildUrl($value->thumb_img);
                $sh_imgurl = $this->buildUrl($value->sh_imgurl);
                if ($sh_imgurl == '') {
                    $sh_imgurl = $this->buildUrl($value->thumb_img);
                }
                $school_shifts[$index]['school_imgurl'] = $sh_imgurl;

                // price_unit
                $school_shifts[$index]['price_unit'] = '￥';

                
            }
        }
        $activity_description = '1、报名计时班或单独绑定教练的学员预约学车按小时收费，若报名驾校班制的学员可免费预约学车。;2、预约前请先绑定教练，绑定后将为您显示教练可预约时间段，否则无法预约学车。;3、学车套餐，优惠多，更方便';
        if ($school_shifts) {
            $path = env('APP_UPLOAD_PATH');
            $body_headimg = $path.'background/body_headimg.png';
            $body_bgimg = $path.'background/body_bgimg.png';
            $head_bgimg = $path.'background/head_bgimg.png';
            $color = '#bf3730';
            $shifts_info = [
                'body_headimg'  => $body_headimg,
                'body_bgimg'    => $body_bgimg,
                'head_bgimg'    => $head_bgimg,
                'color'         => $color,
                'delimiter'     => ';',
                'activity_description' => $activity_description,
                'shifts_info'   => $school_shifts,
            ];
        } else {
            $shifts_info = new \stdClass;
        }
        
        $data = [
            'code'  => 200,
            'msg'   => '获取成功',
            'data'  => $shifts_info
        ];
        return response()->json($data);
    }

    /**
     * 获取学车套餐详情
     *
     * @param   int        $id     套餐的班制ID
     * @param   number     $lat    纬度
     * @param   number     $lng    经度
     * @return void
     */
    public function getPackageShiftsDetail() {
        if (!$this->request->has('id')
            || !$this->request->has('lat')
            || !$this->request->has('lng'))
        {
            Log::Info('异常：获取套餐详情请求的参数错误');
            return response()->json([
                'code'  => 400,
                'msg'   => '参数错误',
                'data'  => new \stdClass,
            ]);
        }
        $sh_id = $this->request->input('id');
        $lat = $this->request->input('lat');
        $lng = $this->request->input('lng');

        // 获取套餐信息
        $whereCondition = [
            ['school.is_show', '=', 1], // 1:展示 2:不展示
            ['school.l_school_id', '>=', 0], 
            ['school_shifts.id', '=', $sh_id], 
            ['school_shifts.deleted', '=', 1], // 1:正常 2:已删除
            ['school_shifts.is_package', '=', 1], // 1:是套餐 2:不是套餐
        ];
        $package_info = DB::table('school_shifts')
            ->select(
                'school_shifts.id as id',
                'school_shifts.sh_school_id as sh_school_id',
                'school_shifts.sh_title as sh_title',
                'school_shifts.sh_money as sh_money',
                'school_shifts.sh_original_money as sh_original_money',
                'school_shifts.sh_tag as sh_tag',
                'school_shifts.sh_description_1 as sh_tag_two',
                'school_shifts.sh_type as sh_type',
                'school_shifts.sh_info as sh_description',
                'school_shifts.is_promote as is_promote',
                'school_shifts.sh_license_id as sh_license_id',
                'school_shifts.sh_license_name as sh_license_name',
                'school_shifts.is_package as is_package',
                'school_shifts.sh_imgurl',
                'school_shifts.coach_id as coach_id',
                'school.s_school_name as school_name',
                'school.s_address as school_address',
                'school.s_location_x as location_x',
                'school.s_location_y as location_y',
                'school.s_thumb as school_imgurl'
            )
            ->join('school', 'school.l_school_id', '=', 'school_shifts.sh_school_id')
            ->where($whereCondition)
            ->first();
        if ( ! $package_info) {
            $data = [
                'code'  => 400,
                'msg'   => '服务器繁忙',
                'data'  => new \stdClass,
            ];
            Log::error('异常：班制ID'.$sh_id.'不存在或不是套餐系列,也可能是该班制所对应的驾校不存在');
            return response()->json($data);
        }
        $sh_description = $package_info->sh_description;
        $sh_description = str_replace('&amp;nbsp;','&nbsp;', $sh_description);
        $sh_description = str_replace('&lt;','<', $sh_description);
        $sh_description = str_replace('&gt;','>', $sh_description);
        $sh_description = str_replace('&quot;','"', $sh_description);
        $package_info->sh_description = $sh_description;
        if ($package_info->coach_id != ''
            && $package_info->coach_id != NULL
            && $package_info->coach_id != 0) 
        {
            $coach_info = DB::table('coach')
                ->select('l_coach_id')
                ->where([
                    ['user.i_user_type', '=', 1],
                    ['user.i_status', '=', 0],
                    ['l_coach_id', '=', $package_info->coach_id],
                    ['order_receive_status', '=', 1], // 1：在线 0：不在线
                ]) 
                ->first();
            if ($coach_info) {
                $package_info->sh_category = 2;
            } else {
                $package_info->sh_category = 1;
            }
        } else {
            $package_info->sh_category = 1;
        }
        
        $location_x = $package_info->location_x; // 经度
        $location_y = $package_info->location_y; // 纬度
        $school_id = $package_info->sh_school_id;
        $comment_list = $this->getSchoolCommentList($school_id);
        if ($comment_list && $comment_list->average_star) {
            $package_info->school_star = $comment_list->average_star;
        } else {
            $package_info->school_star = 3;
        }

        $distance = [];
        // 获取驾校附近的报名点
        $train_list = DB::table('school_train_location')
            ->select(
                'id',
                'tl_school_id',
                'tl_location_x',
                'tl_location_y',
                'tl_phone',
                'tl_imgurl'
            )
            ->where('tl_school_id', '=', $school_id)
            ->get();
        $train_list = $train_list->toArray();
        if ($train_list) {
            foreach ($train_list as $index => $value) {
                $train_list[$index]->distance = round($this->getDistance($lng, $lat, $value->tl_location_x, $value->tl_location_y)/1000, 1);
                $distance[] = round($this->getDistance($lng, $lat, $value->tl_location_x, $value->tl_location_y)/1000, 1);
            }
            // $school_distance = round($this->getDistance($lng, $lat, $location_x, $location_y)/1000, 1);
            // array_unshift($distance, $school_distance);
        } else {
            $distance[] = round($this->getDistance($lng, $lat, $location_x, $location_y)/1000, 1);
        }

        $min_distance = !empty($distance) ? min($distance) : 0;
        $package_info->min_distance = $min_distance;
        $package_info->distance_unit = 'km';  // 距离单位
        $package_info->price_unit = '￥';  // 价格
        // $package_info->school_imgurl = $this->buildUrl($package_info->school_imgurl);
        // school_imgurl
        $sh_imgurl = $this->buildUrl($package_info->sh_imgurl);
        if ($sh_imgurl == '') {
            $sh_imgurl = $this->buildUrl($package_info->school_imgurl);
        }
        $package_info->school_imgurl = $sh_imgurl;

        if ($train_list) {
            foreach ($train_list as $k => $v) {
                if ($min_distance == $v->distance) {
                    $package_info->tl_location_x = $v->tl_location_x;
                    $package_info->tl_location_y = $v->tl_location_y;
                }
            }
        }
        $data = [
            'code'  => 200,
            'msg'   => '获取成功',
            'data'  => $package_info
        ];
        return response()->json($data);
    }

    /**
     * 获取驾校列表
     * @param   int     $city_id    城市ID
     * @param   int     $lat        纬度
     * @param   int     $lng        经度
     * @param   int     $order      排序
     * @param   string  $keyword    关键词
     * @param   int     $lng        经度
     * @param   int     $lng        经度
     * @return  void
     **/
    public function getSchoolList () {
        
        if ( ! $this->request->has('city_id')
            OR ! $this->request->has('lat')
            OR ! $this->request->has('lng')) 
        {
            $data = [
                'code'  => 400,
                'msg'   => '参数错误',
                'data'  => new \stdClass,
            ];
            Log::error('异常：【获取驾校列表】缺少必须参数');
            return response()->json($data);
        }

        $lat = $this->request->input('lat');
        $lng = $this->request->input('lng');
        $page = $this->request->input('page') ? $this->request->input('page') : 1;
        $limit = 10;
        $start = ($page - 1) * $limit;

        // 组合条件
        $where = [];

        // 城市条件
        if ( ! $this->request->has('city_id') ) {
            $city_id = 340100; // 默认合肥市
        } else {
            $city_id = intval($this->request->input('city_id'));
        }

        $where[] = ['school.is_show', '=', 1]; // 1:展示 2:不展示
        $where[] = ['school.city_id', '=', $city_id];

        $query = DB::table('school')
            ->select(
                'l_school_id as school_id',
                's_school_name as school_name',
                'i_dwxz as nature',
                'dc_base_je as base_money',
                's_address as school_address',
                'province_id as province_id',
                'city_id as city_id',
                's_location_x as location_x',
                's_location_y as location_y',
                's_thumb as thumb_imgurl',
                'brand as brand',
                's_order',
                'support_coupon'
            )
            ->where($where)
            ->orderBy('s_order', 'asc');

        // 按搜索关键词
        if ( $this->request->has('keyword')) {
            $keyword = trim((string)$this->request->input('keyword'));
            $query = $query->where('s_school_name', 'like', '%'.$keyword.'%');
        }

        // 按品牌标志 1:普通驾校 2:品牌驾校
        if ( $this->request->has('brand')) {
            $brand = intval($this->request->input('brand')); 
            $query = $query->where('school.brand', '=', $brand);
        }

        // 按驾校性质 1:一类 2:二类 3:三类
        if ( $this->request->has('nature')) {
            $nature = intval($this->request->input('nature')); 
            $query = $query->where('school.i_dwxz', '=', $nature);
        }

        if ( $this->request->has('order')) {
            $order = intval($this->request->input('order')); 
            if ($order == 3) {
                $query = $query->where('school.support_coupon', '=', 1);
            }
        }

        // 获取结果
        $school_list = $query->get();
        
        $final_money_arr = [];
        if ($school_list) {
            foreach ($school_list as $key => $value) {
                
                // 驾校头像
                $school_list[$key]->thumb_imgurl = $this->buildUrl($value->thumb_imgurl);

                // 驾校品牌
                $brand = $value->brand;
                switch ($brand) {
                    case 1 : // 普通
                        $school_list[$key]->brand_text = '普通驾校';
                        break;
                    case 2 : // 品牌
                        $school_list[$key]->brand_text = '品牌驾校';
                        break;
                    default :
                        $school_list[$key]->brand_text = '普通驾校';
                        break;
                }

                $school_id = $value->school_id;
                
                // 驾校班制最低价格
                $min_money = $this->getMinShiftsMoney($school_id, $value->base_money);
                $school_list[$key]->min_money = $min_money;
                
                // 驾校星级
                $comment_list = $this->getSchoolCommentList($school_id);
                if ($comment_list && $comment_list->average_star) {
                    $school_list[$key]->school_star = $comment_list->average_star;
                } else {
                    $school_list[$key]->school_star = 3;
                }

                // 与驾校的距离
                $min_distance = 0;
                $school_distance = $this->getMinDistanceToSchool($school_id, $lat, $lng, $value->location_x, $value->location_y);
                if ($school_distance) {
                    $min_distance = $school_distance['min_distance'];
                }
                $school_list[$key]->min_distance = (string)$min_distance;
                $school_list[$key]->distance_unit = 'km';

            } // 结束组装
        }

        $school_list = $school_list->toArray();
        
        if ( ! empty($school_list)) {
            // 排序 
            if ( ! $this->request->has('order')) {
                $field = 's_order';
            } else {
                $order = intval($this->request->input('order'));
                switch ($order) {
                    case 0 : // 智能
                        $field = 's_order';
                        break;
                    case 1 : // 离我最近
                        $field = 'min_distance';
                        break;
                    case 2 : // 价格最优
                        $field = 'min_money';
                        break;
                    default : 
                        $field = 's_order';
                        break;
                }
            }

            $school_list = $this->multiArraySort($school_list, $field);
            $school_list = array_slice($school_list, $start, $limit);
        }

        $data = [
            'code' => 200,
            'msg'   => '获取成功',
            'data'  => ['school_list' => $school_list]
        ];

        return response()->json($data);

    }

    // 获取驾校下的最低班制价格
    private function getMinShiftsMoney ($school_id, $base_money) {

        $school_shifts = $this->getSchoolShiftsList($school_id);
        if ($school_shifts) {
            foreach ($school_shifts as $k => $v) {
                $final_money_arr[] = $v->sh_money;
            }
        }
        $min_money = ! empty($final_money_arr) ? min($final_money_arr) : $base_money;
        return $min_money;
    }

    
    /**
     * 获取驾校及其报名点中最优距离
     * @param int   $school_id    驾校ID
     * @param int   $lat          纬度
     * @param int   $lng          经度
     * @param int   $location_x   经度
     * @param int   $location_y   纬度
     *
     **/
    public function getMinDistanceToSchool ($school_id, $lat, $lng, $location_x, $location_y) {

        // 获取驾校附近的报名点
        $train_list = DB::table('school_train_location')
            ->select(
                'id',
                'tl_school_id',
                'tl_location_x',
                'tl_location_y',
                'tl_phone',
                'tl_imgurl'
            )
            ->where('tl_school_id', '=', $school_id)
            ->get();
        $train_location = [];
        $train_list = $train_list->toArray();
        if ($train_list) {
            foreach ($train_list as $index => $value) {
                $train_list[$index]->distance = round($this->getDistance($lng, $lat, $value->tl_location_x, $value->tl_location_y)/1000, 1);
                $distance[] = round($this->getDistance($lng, $lat, $value->tl_location_x, $value->tl_location_y)/1000, 1);
            }

            $school_distance = round($this->getDistance($lng, $lat, $location_x, $location_y)/1000, 1);
            $train_min_distance = !empty($distance) ? min($distance) : 0;
            if ($train_min_distance >= $school_distance) {
                $train_list = [];
                $distance[] = round($this->getDistance($lng, $lat, $location_x, $location_y)/1000, 1);
            }
            
        } else {
            $distance[] = round($this->getDistance($lng, $lat, $location_x, $location_y)/1000, 1);
            
        }
        
        $min_distance = !empty($distance) ? min($distance) : 0;
        if ($train_list) {
            foreach ($train_list as $k => $v) {
                if ( $v->distance == $min_distance ) {
                    $train_location['tl_location_x'] = $v->tl_location_x;
                    $train_location['tl_location_y'] = $v->tl_location_y;
                    $train_location['min_distance'] = $v->distance;
                }
            }
        } else {
            $train_location['tl_location_x'] = $location_x;
            $train_location['tl_location_y'] = $location_y;
            $train_location['min_distance'] = $min_distance;
        }
        return $train_location;
    }
    
    /**
     * 获取教练列表
     *
     * @param int $city_id
     * @return void
     */
    public function getCoachList() {
        // 组合条件
        $where = [];

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
                'coach.coupon_supported',
                'coach.timetraining_min_price',
                'school.l_school_id as school_id',
                'school.s_school_name as school_name'
            )
            ->selectRaw('certification_status = 3 as certified') // 3-已成功认证教练的状态值
            ->leftJoin('user', 'coach.user_id', '=', 'user.l_user_id')
            ->leftJoin('school', 'coach.s_school_name_id', '=', 'school.l_school_id')
            ->where($where);

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
            $query = $query
                ->orderBy('coach.i_order', 'desc')
                ;
        }

        // 取得结果
        $coach_list = $query
            ->paginate(10); // 分页

        $coach_ids = array();
        foreach ($coach_list as $index => $coach) {
            $coach_list[$index]->hot_params = $this->getHotParams($coach->i_type); // 教练的类型标签:二级、三级、四级教练员等
            $coach_list[$index]->signup_num = 0; // 报名人数
            $coach_list[$index]->coach_imgurl = $this->buildUrl($coach->coach_imgurl);
            $coach_ids[] = $coach->coach_id;
        }
        // 是否有学车优惠券
        $current_time = time();
        $ticket_list = DB::table('coupon')
            ->select('id as coupon_id', 'owner_id', 'coupon_value', 'coupon_name')
            ->where([
                ['owner_type', '=', '1'], // 1：教练，2：驾校
                ['coupon_category_id', '=', '1'], // 1、现金券，2、打折券
                ['scene' ,'=', '1'], // 1:报名班制 2:预约学车
                ['expiretime', '>', $current_time],
                ['is_open', '=', '1'], // 开启优惠券功能
                ['is_show', '=', '1'], // 打开展示功能
            ])
            ->whereIn('owner_id', array_values($coach_ids))
            ->get();

        if(!empty($ticket_list->toArray())) {
            foreach ($coach_list as $key => $value) {
                $coach_list[$key]->ticket_info = [];
                foreach ($ticket_list as $k => $v) {
                    if($value->coach_id == $v->owner_id) {
                        $coach_list[$key]->ticket_info[] = $ticket_list[$k];
                    }
                }
            }
        } else {
            foreach ($coach_list as $k => $v) {
                $coach_list[$k]->ticket_info = [];
            }
        }

        // $coach_list = $coach_list->toArray();
        // if (empty($coach_list['data'])) {
        //     $data = [
        //         'code' => 1002,
        //         'msg'   => '当前驾校已下线，当前驾校教练不在线',
        //         'data'  => new \stdClass,
        //     ];
        //     Log::Info('异常：该教练所在的驾校已下线或者教练不在线');
        //     return response()->json($data);
        // }
        
        $data = [
            'code' => 200,
            'msg'  => '成功',
            'data' => [
                'coach_list' => $coach_list,
            ],
        ];
        return response()->json($data);
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

    // 获取教练名片
    public function getCoachDetail() {
        if( !$this->request->has('id') || !$this->request->has('lng') || !$this->request->input('lat')) {
            return response()->json([
                'code'=>400,
                'msg'=>'参数错误',
                'data'=>'',
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
                'school.s_address as school_address',
                'school.s_frdb_tel as school_phone',
                'school.s_location_x as location_x',
                'school.s_location_y as location_y',
                'school.s_imgurl as school_imgurl',
                'coach.s_school_name_id as school_id',
                'coach.s_coach_name as coach_name',
                'coach.s_coach_phone as coach_phone',
                'coach.s_coach_imgurl as coach_imgurl',
                'coach.certification_status',
                'coach.timetraining_supported',
                'coach.timetraining_min_price',
                'coach.i_coach_star as coach_star',
                'coach.must_bind',
                'coach.average_license_time',
                'coach.lesson2_pass_rate',
                'coach.lesson3_pass_rate',
                'coach.coupon_supported'
            )
            ->leftJoin('coach', 'user.l_user_id', '=', 'coach.user_id')
            ->leftJoin('school', 'coach.s_school_name_id', '=', 'school.l_school_id')
            ->where(['coach.l_coach_id'=> $coach_id, 'coach.order_receive_status'=>1, 'user.i_status'=>0])
            ->first();
        if(empty($_data)) {
            $data = ['code'=>1002, 'msg'=>'教练不在线或不存在。', 'data'=> new \stdclass()];
            return response()->json($data);
        }
        $_data->school_imgurl = json_decode($_data->school_imgurl, true);
        $school_imgurl_arr = array();
        $_data->average_license_time = $_data->average_license_time ? $_data->average_license_time.'天' : '60天';
        $_data->lesson2_pass_rate = $_data->lesson2_pass_rate ? $_data->lesson2_pass_rate.'%' : '50%';
        $_data->lesson3_pass_rate = $_data->lesson3_pass_rate ? $_data->lesson3_pass_rate.'%' : '50%';

        if(is_array($_data->school_imgurl) && !empty($_data->school_imgurl)) {
            foreach ($_data->school_imgurl as $key => $value) {
                $url = $this->buildUrl($value);
                if (! empty($url)) {
                    $school_imgurl_arr[] = $url;
                }
            }
        }
        $_data->school_imgurl = $school_imgurl_arr;
        $_data->coach_imgurl = $this->buildUrl($_data->coach_imgurl);
        // 获取最近报名点信息
        $train_list = DB::table('school_train_location')
            ->select(
                'id',
                'tl_train_address',
                'tl_location_x',
                'tl_location_y',
                'tl_phone',
                'tl_imgurl'
            )
            ->where('tl_school_id', $_data->school_id)
            ->orderBy('order', 'desc')
            ->get();
        $list = array();
        $distance = array();

        if(!empty($train_list->toArray())) {
            foreach ($train_list as $key => $value) {
                // 获取所有距离
                $list[$key]['tl_train_address'] = $value->tl_train_address;
                $list[$key]['tl_phone']         = $value->tl_phone;
                $tl_imgurl                      = json_decode($value->tl_imgurl, true) ? json_decode($value->tl_imgurl, true) : [];
                $list[$key]['tl_imgurl']        = $tl_imgurl;
                $list[$key]['tl_location_x']    = $value->tl_location_x;
                $list[$key]['tl_location_y']    = $value->tl_location_y;
                $list[$key]['distance'] = round($this->getDistance($lng, $lat, $value->tl_location_x, $value->tl_location_y)/1000, 1);
                $distance[] = round($this->getDistance($lng, $lat, $value->tl_location_x, $value->tl_location_y)/1000, 1);
            }
            $school_distance = round($this->getDistance($lng, $lat, $_data->location_x, $_data->location_y)/1000, 1);
            $train_min_distance = !empty($distance) ? min($distance) : 0;
            if ($train_min_distance >= $school_distance) {
                $list = [];
                $distance[] = round($this->getDistance($lng, $lat, $_data->location_x, $_data->location_y)/1000, 1);
            }
        } else {
            $distance[] = round($this->getDistance($lng, $lat, $_data->location_x, $_data->location_y)/1000, 1);
        }
        $min_distance = !empty($distance) ? min($distance) : 0;

        $tl_imgurl_arr = array();

        if(is_array($list) && !empty($list)) {
            foreach ($list as $k => $v) {
                if($min_distance == $v['distance']) {
                    $_data->tl_train_address = $v['tl_train_address'];
                    $_data->tl_phone = $v['tl_phone'];
                    if(is_array($v['tl_imgurl']) && !empty($v['tl_imgurl'])) {
                        foreach ($v['tl_imgurl'] as $key => $value) {
                            $url = $this->buildUrl($value);
                            if (! empty($url) ) {
                                $tl_imgurl_arr[] = $url;
                            }
                        }
                    }
                    $_data->tl_imgurl = $tl_imgurl_arr;
                    $_data->tl_location_x = $v['tl_location_x'];
                    $_data->tl_location_y = $v['tl_location_y'];
                } else {
                    continue;
                }
            }
            if(!$_data->tl_imgurl) {
                $_data->tl_train_address = trim($_data->school_address);
                $_data->tl_phone = $_data->school_phone;
                $_data->tl_imgurl = $_data->school_imgurl;
                $_data->tl_location_x = $_data->location_x;
                $_data->tl_location_y = $_data->location_y;
            }
        } else {
            $_data->tl_train_address = trim($_data->school_address);
            $_data->tl_phone = $_data->school_phone;
            $_data->tl_imgurl = $_data->school_imgurl;
            $_data->tl_location_x = $_data->location_x;
            $_data->tl_location_y = $_data->location_y;
        }

        $_data->min_distance = round($min_distance, 1);
        $_data->distance_unit = 'km'; // 距离单位 m km

        $current_time = time();
        // 是否有学车优惠券
        $ticket_info = DB::table('coupon')
            ->select('id', 'coupon_value', 'coupon_name', 'is_open')
            ->where([
                ['owner_type', '=', '1'], // 1：教练，2：驾校
                ['owner_id', '=', $coach_id],
                ['coupon_category_id', '=', '1'], // 1、现金券，2、打折券
                ['scene' ,'=', '1'], // 1:报名班制 2:预约计时
                ['expiretime', '>', $current_time],
                ['is_open', '=', '1'], // 1 开启券功能
                ['is_show', '=', '1'], // 1 展示券到教练详情页面　0　不展示
            ])
            ->first();

        if(empty($ticket_info)) {
            $_data->coupon_id = 0; //
            $_data->coupon_name = '';
            $_data->coupon_value = '';
            $_data->is_open = 2; // 1：开启 2：不开启
        } else {
            $_data->coupon_id = $ticket_info->id; // 券ID
            $_data->coupon_name = $ticket_info->coupon_name;
            $_data->coupon_value = $ticket_info->coupon_value;
            $_data->is_open = $ticket_info->is_open;
        }
        // 券的URL
        $_data->coupon_url = 'http://m.xihaxueche.com:8001/v2/student/public/signup/getcoupon?device='.$device.'&id='.$_data->coupon_id.'&coach_id='.$coach_id;
        // 报名班制列表
        $shifts_list = DB::table('school_shifts')
            ->select(
                'id',
                'sh_title',
                'sh_money',
                'sh_school_id',
                'sh_original_money',
                'sh_tag',
                'sh_type',
                'sh_description_2 as sh_description',
                'is_promote',
                'sh_license_id',
                'sh_license_name'
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
        // if(empty($shifts_list->toArray())) {
        //     $where = function ($query) {
        //         $query->whereNull('coach_id')
        //         ->orWhere('coach_id', '=', 0)
        //         ->orWhere('coach_id', '=', '');
        //     };
        //     $shifts_list = DB::table('school_shifts')
        //         ->select(
        //             'id',
        //             'sh_school_id',
        //             'sh_title',
        //             'sh_money',
        //             'sh_original_money',
        //             'sh_tag',
        //             'sh_type',
        //             'sh_description_2 as sh_description',
        //             'is_promote',
        //             'sh_license_id',
        //             'sh_license_name'
        //         )
        //         ->where(['sh_school_id'=>$_data->school_id, 'deleted'=>1])
        //         // ->whereNull('coach_id')
        //         ->where($where)
        //         ->orderBy('order', 'desc')
        //         ->get();
        //     if(!empty($shifts_list->toArray())) {
        //         foreach ($shifts_list->toArray() as $key => $value) {
        //             $shifts_list[$key]->sh_category = 1; // 驾校设置的班制
        //         }
        //     }
        // }
        if(!empty($shifts_list->toArray())) {
            foreach ($shifts_list->toArray() as $key => $value) {
                $shifts_list[$key]->sh_description = trim($value->sh_description);
                $shifts_list[$key]->sh_title = trim($value->sh_title);
            }
        }
        $_data->shifts_list = $shifts_list;

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
        $data = ['code'=>200, 'msg'=>'获取成功', 'data'=>['coach_detail' => $_data]];
        return response()->json($data);

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

    // 获取报名点列表
    public function getTrainList() {
        if(!$this->request->has('id') || !$this->request->has('sid') || !$this->request->has('lat') || !$this->request->has('lng')) {
            return response()->json([
                'code'=>400,
                'msg'=>'参数错误',
                'data'=>'',
            ]);
        }

        $coach_id = $this->request->input('id'); // 教练ID
        $school_id = $this->request->input('sid'); // 驾校ID
        $lng = $this->request->input('lng'); // 经度
        $lat = $this->request->input('lat'); // 纬度

        $train_list = $this->_getTrainList($coach_id, $school_id, $lat, $lng);
        return $train_list;
    }

    // _获取报名点列表
    public function _getTrainList($coach_id, $school_id, $lat, $lng) {
        $train_list = DB::table('school_train_location')
            ->select(
                'id',
                'tl_train_address',
                'tl_location_x',
                'tl_location_y',
                'tl_phone',
                'tl_imgurl'
            )
            ->where('tl_school_id', $school_id)
            ->orderBy('order', 'desc')
            ->get();
        $list = array();
        if(!empty($train_list->toArray())) {
            foreach ($train_list as $key => $value) {
                // 获取所有距离
                $list[$key]['tl_train_address'] = $value->tl_train_address;
                $list[$key]['tl_phone']         = $value->tl_phone;
                $tl_imgurl                      = json_decode($value->tl_imgurl, true) ? json_decode($value->tl_imgurl, true) : [];
                if(!empty($tl_imgurl)) {
                    foreach ($tl_imgurl as $k => $v) {
                        $tl_imgurl[$k] = env('APP_PATH').'admin/'.$v;
                    }
                }
                $list[$key]['tl_imgurl']        = !empty($tl_imgurl) ? array_values($tl_imgurl) : [];
                $list[$key]['tl_location_x']    = $value->tl_location_x;
                $list[$key]['tl_location_y']    = $value->tl_location_y;
                $list[$key]['distance'] = round($this->getDistance($lng, $lat, $value->tl_location_x, $value->tl_location_y)/1000, 1);
                $list[$key]['distance_unit'] = 'km';

            }
            $data = ['code'=>200, 'msg'=>'获取成功', 'data'=>$list];
        } else {
            // 获取驾校地址
            $school_detail = DB::table('school')
                ->select(
                    's_address',
                    's_frdb_mobile',
                    's_imgurl',
                    's_location_x',
                    's_location_y'
                )
                ->where([
                    'l_school_id'=>$school_id,
                    'is_show'=>1
                ])
                ->first();

            if(!empty($school_detail)) {
                $school_detail->s_imgurl = json_decode($school_detail->s_imgurl, true);
                $s_imgurl_arr = array();

                if(is_array($school_detail->s_imgurl) && !empty($school_detail->s_imgurl)) {
                    foreach ($school_detail->s_imgurl as $key => $value) {
                        $s_imgurl_arr[] = env('APP_PATH').'sadmin/'.$value;
                    }
                }
                $list['tl_train_address']   = $school_detail->s_address;
                $list['tl_phone']           = $school_detail->s_frdb_mobile;
                $list['tl_imgurl']          = $s_imgurl_arr;
                $list['tl_location_x']      = $school_detail->s_location_x;
                $list['tl_location_y']      = $school_detail->s_location_y;
                $list['distance'] = round($this->getDistance($lng, $lat, $school_detail->s_location_x, $school_detail->s_location_y)/1000, 1);
                $list['distance_unit'] = 'km';

                $data = ['code'=>200, 'msg'=>'获取成功', 'data'=>$list];

            } else {
                $data = ['code'=>100, 'msg'=>'没有更多报名点', 'data'=>$list];
            }
        }
        return response()->json($data);

    }

    // 获取教练设置的班制列表
    public function getCoachShiftsList($id, $cid) {
        $shifts_info = DB::table('school_shifts')
            ->select(
                'id',
                'sh_school_id as school_id',
                'sh_title',
                'sh_type',
                'sh_tag',
                'sh_tag_id',
                'sh_description_2 as sh_description',
                'is_promote',
                'coupon_id',
                'sh_license_id',
                'sh_license_name'
            )
            ->where([
                ['id', '=', $id],
                ['coach_id', '=', $cid],
                ['deleted', '=', '1'],
            ])
            ->first();
        return $shifts_info;
    }

    // 根据驾校ID和班制id获取驾校设置的且存在的班制
    public function getSchoolShiftsListById($id, $sid) {
        $shifts_info = DB::table('school_shifts')
            ->select(
                'id',
                'sh_school_id',
                'sh_title',
                'sh_tag',
                'sh_tag_id',
                'sh_description_2 as sh_description',
                'is_promote',
                'coupon_id',
                'sh_license_id',
                'sh_license_name'
            )
            ->where([
                ['id', '=', $id],
                ['sh_school_id', '=', $sid],
                ['deleted', '=', 1] // 1：正常 | 2：已删除
            ])
            ->first();
        return $shifts_info;
    }

    // 根据驾校ID获取驾校设置的班制
    public function getSchoolShiftsList($sid) {
         $where = function ($query) {
            $query->whereNull('coach_id')
            ->orWhere('coach_id', '=', 0)
            ->orWhere('coach_id', '=', '');
        };
        $shifts_info = DB::table('school_shifts')
            ->select(
                'id',
                'sh_school_id',
                'sh_title',
                'sh_money',
                'sh_original_money',
                'sh_tag',
                'sh_description_1 as sh_tag_two',
                'is_package',
                'sh_type',
                'sh_description_2 as sh_description',
                'is_promote',
                'sh_license_id',
                'sh_license_name'
            )
            ->where([
                ['deleted', '=', '1'],
                ['sh_school_id', '=', $sid]
            ])
            // ->whereNull('coach_id')
            ->where($where)
            ->get();
        if ($shifts_info) {
            foreach ($shifts_info as $key => $value) {
                if ( 1 == $value->is_package) {
                    $shifts_info[$key]->package_intro = '套餐';
                } else {
                    $shifts_info[$key]->package_intro = '非套餐';
                }
            }
        }
        return $shifts_info;
    }

    // 获取驾校详情
    public function getSchoolInfo() {
        if(!$this->request->has('sid') || !$this->request->has('lat') || !$this->request->has('lng')) {
            return response()->json([
                'code' => 400,
                'msg'  => '参数错误',
                'data' => ''
            ]);
        }
        //$coach_id = $this->request->input('id'); // 教练ID
        $school_id = $this->request->input('sid'); // 驾校ID
        $lng = $this->request->input('lng'); // 经度
        $lat = $this->request->input('lat'); // 纬度
                   
        $_data = [];
        // 获取驾校详情
        // $school_detail = $this->getSchoolDetail($school_id);
        // $_data['school_info'] = $school_detail;

        // 获取驾校信息，最近驾校报名点
        $train_list = $this->_getRecentTrainList($school_id, $lat, $lng);
        $_data['school_info'] = $train_list;
        // 获取驾校班制
        $shifts_list = $this->getSchoolShiftsList($school_id);
        $_data['shifts_list'] = $shifts_list;

        // 获取驾校评价
        $comment_list = $this->getSchoolCommentList($school_id);
        $_data['comment_info'] = $comment_list;
        $data = ['code'=>200, 'msg'=>'获取驾校评价成功', 'data'=>$_data];
        return response()->json($data);
    }

    /**
     * 获取我报名的驾校
     * @param   string  token   用户登录标识
     * @param   string  lat     纬度
     * @param   string  lng     经度
     * @return void
     **/
    public function getMySchool () {
        
        if ( ! $this->request->has('lat')
            OR ! $this->request->has('lng'))
        {
            $data = [
                'code'  => 400,
                'msg'   => '参数错误',
                'data'  => new \stdClass,
            ];
            Log::error('异常：【获取我报名的驾校】缺少必须参数');
            return response()->json($data);
        }

        $lat = $this->request->input('lat');
        $lng = $this->request->input('lng');
        $user = $this->auth->getUserFromToken($this->request->input('token'));
        $user_id = $user['user_id'];
        $user_phone = $user['phone'];

        // 获取报名的驾校
        $school_count = 0;
        $school_id_arr = $this->getMySchoolId($user_id, $user_phone);
        if ( ! empty($school_id_arr)) {
            $school_count = count($school_id_arr);
        }

        if ( 0 == $school_count) { // 未报名驾校
            $data = [
                'code'  => 200,
                'msg'   => '获取成功',
                'count' => $school_count,
                'data'  => new \stdClass,
            ];

        } elseif ( 1 == $school_count) { // 报名一个驾校

            $school_id = $school_id_arr[0];

            $_data = [];

            // 获取驾校信息，最近驾校报名点
            $train_list = $this->_getRecentTrainList($school_id, $lat, $lng);
            $_data['school_info'] = $train_list;

            // 获取驾校班制
            $shifts_list = $this->getSchoolShiftsByCount($school_id, 3);
            $_data['shifts_info'] = $shifts_list;
            
            // 获取驾校的教练
            $coach_list = $this->getCountCoachInSchool($school_id, 4);
            $_data['coach_info'] = $coach_list;

            // 获取驾校评价
            $comment_list = $this->getSchoolCommentList($school_id);
            $_data['comment_info'] = $comment_list;

            $data = [
                'code'  =>  200, 
                'msg'   =>  '获取成功', 
                'count' => $school_count,
                'data'  =>  $_data,
            ];

        } elseif ( 1 < $school_count) { // 报名多个驾校

            $school_list = DB::table('school')
                ->select(
                    'l_school_id as school_id',
                    's_school_name as school_name',
                    'i_dwxz as nature',
                    'dc_base_je as base_money',
                    's_address as school_address',
                    'province_id as province_id',
                    'city_id as city_id',
                    's_location_x as location_x',
                    's_location_y as location_y',
                    's_thumb as thumb_imgurl',
                    'brand as brand',
                    's_order',
                    'support_coupon'
                )
                ->whereIn('l_school_id', $school_id_arr)
                ->where('is_show', '=', 1)
                ->get();

            if ($school_list) {
                foreach ($school_list as $key => $value) {
                    
                    // 驾校头像
                    $school_list[$key]->thumb_imgurl = $this->buildUrl($value->thumb_imgurl);

                    // 驾校品牌
                    $brand = $value->brand;
                    switch ($brand) {
                        case 1 : // 普通
                            $school_list[$key]->brand_text = '普通驾校';
                            break;
                        case 2 : // 品牌
                            $school_list[$key]->brand_text = '品牌驾校';
                            break;
                        default :
                            $school_list[$key]->brand_text = '普通驾校';
                            break;
                    }

                    $school_id = $value->school_id;
                    
                    // 驾校班制最低价格
                    $min_money = $this->getMinShiftsMoney($school_id, $value->base_money);
                    $school_list[$key]->min_money = $min_money;
                    
                    // 驾校星级
                    $comment_list = $this->getSchoolCommentList($school_id);
                    if ($comment_list && $comment_list->average_star) {
                        $school_list[$key]->school_star = $comment_list->average_star;
                    } else {
                        $school_list[$key]->school_star = 3;
                    }

                    // 与驾校的距离
                    $min_distance = 0;
                    $school_distance = $this->getMinDistanceToSchool($school_id, $lat, $lng, $value->location_x, $value->location_y);
                    if ($school_distance) {
                        $min_distance = $school_distance['min_distance'];
                    }
                    $school_list[$key]->min_distance = (string)$min_distance;
                    $school_list[$key]->distance_unit = 'km';

                } 
            }

            $data = [
                'code'  => 200,
                'msg'   => '获取成功',
                'count' => $school_count,
                'data'  => ['school_list' => $school_list]
            ];

        }

        return response()->json($data);
        
    }

    // 获取用户报名驾校的个数
    public function getMySchoolId ($user_id, $user_phone) {

        $where = [
            'so_user_id'        => $user_id,
            'so_phone'          => $user_phone,
            'school.is_show'    => 1,
        ];

        $whereCondition = function ($query) {
            $query->whereIn('so_pay_type', [1, 3, 4])
                ->where('so_order_status', '=', 1)
                ->orWhere(function ($_query) {
                    $_query->where('so_pay_type', '=', 2)
                        ->where('so_order_status', '=', 3);
                });
        };

        $school_ids = DB::table('school_orders as orders')
            ->select('so_school_id')
            ->leftJoin('school', 'school.l_school_id', '=', 'orders.so_school_id')
            ->where($where)
            ->where($whereCondition)
            ->distinct()
            ->orderBy('dt_zhifu_time', 'desc')
            ->get();

        $school_id_arr = [];
        if ($school_ids) {
            foreach ($school_ids as $key => $value) {
                $school_id_arr[] = $value->so_school_id;
            }
        }

        return $school_id_arr;

    }


    /**
     * 获取驾校的详情（新）
     * @param   number  $id  教练ID（非必须）
     * @param   number  $school_id  驾校ID
     * @param   string  $lat    纬度
     * @param   string  $lng    经度
     * @return void
     **/
    public function getSchoolMessage () {

        if( ! $this->request->has('school_id') 
            OR ! $this->request->has('lat') 
            OR ! $this->request->has('lng')) 
        {
            Log::error('异常：【获取驾校详情(新)】缺少必须的参数');
            return response()->json([
                'code' => 400,
                'msg'  => '参数错误',
                'data' => ''
            ]);
        }
        //$coach_id = $this->request->input('id'); // 教练ID
        $school_id = $this->request->input('school_id'); // 驾校ID
        $lng = $this->request->input('lng'); // 经度
        $lat = $this->request->input('lat'); // 纬度
                   
        $_data = [];

        // 获取驾校信息，最近驾校报名点
        $train_list = $this->_getRecentTrainList($school_id, $lat, $lng);
        $_data['school_info'] = $train_list;

        // 获取驾校班制
        $shifts_list = $this->getSchoolShiftsByCount($school_id, 3);
        $_data['shifts_info'] = $shifts_list;

        // 获取驾校的教练
        $coach_list = $this->getCountCoachInSchool($school_id, 4);
        $_data['coach_info'] = $coach_list;

        // 获取驾校评价
        $comment_list = $this->getSchoolCommentList($school_id);
        $_data['comment_info'] = $comment_list;

        $data = [
            'code'  =>200, 
            'msg'   =>'获取驾校详情成功', 
            'data'  =>$_data
        ];
        return response()->json($data);

    }

    /**
     * 获取驾校下的班制列表
     * @param   number  $school_id  驾校ID
     * @param   number  $coach_id   教练ID
     * @param   string  $lat        纬度
     * @param   string  $lng        经度
     * @return void
     **/
    public function getShiftsList () {

        if ( ! $this->request->has('school_id')
            OR ! $this->request->has('lat') 
            OR ! $this->request->has('lng')) 
        {
            $data = [
                'code'  => 400, 
                'msg'   => '参数错误', 
                'data'  => new \stdClass,
            ];
            Log::error('异常：【获取驾校下的班制列表】缺少参数');
            return response()->json($data);
        }

        $school_id = $this->request->input('school_id');
        $lat = $this->request->input('lat');
        $lng = $this->request->input('lng');

        if ( $this->request->has('coach_id')) {
            $coach_id = $this->request->input('coach_id');
            $where = [
                'coach_id' => $coach_id,
            ];
        } else {
            $where = function ($query) {
                $query->whereNull('coach_id')
                ->orWhere('coach_id', '=', 0)
                ->orWhere('coach_id', '=', '');
            };
        }

        $shifts_info = DB::table('school_shifts')
            ->select(
                'id',
                'sh_school_id',
                'coach_id',
                'sh_title',
                'sh_money',
                'sh_original_money',
                'sh_tag',
                'sh_description_1 as sh_tag_two',
                'is_package',
                'sh_type',
                'sh_description_2 as sh_description',
                'is_promote',
                'sh_license_id',
                'sh_license_name'
            )
            ->where([
                ['deleted', '=', '1'],
                ['sh_school_id', '=', $school_id]
            ])
            ->where($where)
            ->paginate();
        if ($shifts_info) {
            foreach ($shifts_info as $key => $value) {
                if ( 1 == $value->is_package) {
                    $shifts_info[$key]->package_intro = '套餐';
                } else {
                    $shifts_info[$key]->package_intro = '非套餐';
                }
            }
        }

        $data = [
            'code'  => 200,
            'msg'   => '获取成功',
            'data'  => ['shifts_list' => $shifts_info]
        ];
        return response()->json($data);

    }

    /**
     * 获取驾校班制详情
     * @param   number  $id         班制ID
     * @param   number  $school_id  驾校ID (必须)
     * @param   number  $coach_id   教练ID (非必须)
     * @param   string  $lat        纬度
     * @param   string  $lng        经度
     * @return void
     **/
    public function getSchoolShiftsDetail () {

        if ( ! $this->request->has('id')
            OR ! $this->request->has('lat') 
            OR ! $this->request->has('lng')
            OR ! $this->request->has('school_id'))
        {
            $data = [
                'code'  => 400, 
                'msg'   => '参数错误', 
                'data'  => new \stdClass,
            ];
            Log::error('异常：【获取驾校班制详情】缺少必须参数');
            return response()->json($data);
        }
        
        $sh_id = $this->request->input('id');
        $school_id = $this->request->input('school_id');
        $lat = $this->request->input('lat');
        $lng = $this->request->input('lng');

        $where = [];
        $where['sh_school_id'] = $school_id;

        $where['school_shifts.id'] = $sh_id; 
        $where['school.is_show'] = 1; // 1:展示 2:不展示
        $where['school_shifts.deleted'] = 1; // 1:正常 2:已删除

        $shifts_info = DB::table('school_shifts')
            ->select(
                'school_shifts.id as id',
                'sh_school_id',
                'coach_id',
                'sh_title',
                'sh_money',
                'sh_original_money',
                'sh_tag',
                'sh_description_1 as sh_tag_two',
                'sh_type',
                'sh_description_2 as sh_description_2',
                'sh_info as sh_info',
                'is_promote',
                'sh_license_id',
                'sh_license_name',
                'is_package',
                'sh_imgurl',
                'school.s_school_name as school_name',
                'school.s_address as school_address',
                'school.s_location_x as location_x',
                'school.s_location_y as location_y',
                'school.s_thumb as thumb_imgurl'
            )
            ->leftJoin('school', 'school.l_school_id', '=', 'school_shifts.sh_school_id')
            ->where($where)
            ->where('school.l_school_id', '>', 0)
            ->first();
        
        if ( ! $shifts_info) {
            $data = [
                'code'  => 400,
                'msg'   => '服务器繁忙',
                'data'  => new \stdClass,
            ];
            Log::error('异常：班制ID'.$sh_id.'不存在或不是当前驾校/教练的班制 | 当前班制的驾校/教练已下线');
            return response()->json($data);
        }
        
        $is_package = $shifts_info->is_package;
        if ( 1 == $is_package) { // 1:套餐 2:非套餐
            $shifts_info->sh_description = $shifts_info->sh_info;
        } else {
            $shifts_info->sh_description = $shifts_info->sh_description_2;
        }

        $shifts_info->sh_category = 1; // 班制分类 1：驾校设置的 2：教练设置的

        // thumb_imgurl
        // $shifts_info->thumb_imgurl = $this->buildUrl($shifts_info->thumb_imgurl);
        $sh_imgurl = $this->buildUrl($shifts_info->sh_imgurl);
        if ($sh_imgurl == '') {
            $sh_imgurl = $this->buildUrl($shifts_info->thumb_imgurl);
        }
        $shifts_info->school_imgurl = $sh_imgurl;

        $school_id = $shifts_info->sh_school_id;

        // 驾校星级
        $comment_list = $this->getSchoolCommentList($school_id);
        if ($comment_list && $comment_list->average_star) {
            $shifts_info->star = $comment_list->average_star;
        } else {
            $shifts_info->star = 3;
        }

        if ( $this->request->has('coach_id')) {
            $coach_id = $this->request->input('coach_id');
            $coach_info = $this->user->getCoachInfoById($coach_id);
            if (!$coach_info) {
                $data = [
                    'code'  => 400,
                    'msg'   => '服务器异常',
                    'data'  => new \stdClass
                ];
                Log::error('异常：【获取班制详情】当前教练的信息出现异常，可能已下线');
                return response()->json($data);
            }

            $coach_imgurl = $coach_info->coach_imgurl;
            // $thumb_imgurl = $this->buildUrl($coach_imgurl);
            // $shifts_info->school_imgurl = $thumb_imgurl;
            $sh_imgurl = $this->buildUrl($shifts_info->sh_imgurl);
            if ($sh_imgurl == '') {
                $sh_imgurl = $this->buildUrl($coach_imgurl);
            }
            $shifts_info->school_imgurl = $sh_imgurl;
            $shifts_info->sh_category = 2; // 班制分类 1：驾校设置的 2：教练设置的
            $shifts_info->star = $coach_info->coach_star; // 教练星级
        }

        $sh_description = $shifts_info->sh_description;
        $sh_description = str_replace('&amp;nbsp;','&nbsp;', $sh_description);
        $sh_description = str_replace('&lt;','<', $sh_description);
        $sh_description = str_replace('&gt;','>', $sh_description);
        $sh_description = str_replace('&quot;','"', $sh_description);
        $shifts_info->sh_description = $sh_description;


        $location_x = $shifts_info->location_x; // 经度
        $location_y = $shifts_info->location_y; // 纬度

        // 驾校附近报名点
        $train_list = $this->getMinDistanceToSchool($school_id, $lat, $lng, $location_x, $location_y);
        if ($train_list) {
            $shifts_info->tl_location_x = $train_list['tl_location_x'];
            $shifts_info->tl_location_y = $train_list['tl_location_y'];
            $shifts_info->min_distance = (string)$train_list['min_distance'];
        } else {
            $shifts_info->tl_location_x = $location_x;
            $shifts_info->tl_location_y = $location_y;
            $min_distance = round($this->getDistance($lng, $lat, $location_x, $location_y)/1000, 1);
            $shifts_info->min_distance = (string)$min_distance;
        }
        $shifts_info->distance_unit = 'km';
        $shifts_info->price_unit = '￥';

        $data = [
            'code'  => 200,
            'msg'   => '获取成功',
            'data'  => $shifts_info
        ];
        
        return response()->json($data);
    }

    /**
     * 获取驾校下的教练列表
     * @param   number  $school_id  驾校ID
     * @param   string  $lat        纬度
     * @param   string  $lng        经度
     * @return void
     **/
    public function getCoachListInSchool () {

        if ( ! $this->request->has('school_id')
            OR ! $this->request->has('lat')
            OR ! $this->request->has('lng')) 
        {
            $data = [
                'code'  => 400,
                'msg'   => '参数错误',
                'data'  => new \stdClass
            ];
            Log::error('异常：【获取驾校下的教练列表】缺少必须参数');
            return response()->json($data);
        }

        $school_id = $this->request->input('school_id');
        $lat = $this->request->input('lat');
        $lng = $this->request->input('lng');

        $whereCondition = [
            'user.i_status'         => 0,
            'user.i_user_type'      => 1,
            's_school_name_id'      => $school_id,
            'school.l_school_id'    => $school_id,
            'school.is_show'        => 1, // 1:展示 2：不展示
            'order_receive_status'  => 1, // 1:接单 2：不接单
        ];

        $coach_list = DB::table('coach')
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
                'coach.coupon_supported',
                'coach.timetraining_min_price',
                'school.l_school_id as school_id',
                'school.s_school_name as school_name'
            )
            ->leftJoin('school', 'coach.s_school_name_id', '=', 'school.l_school_id')
            ->leftJoin('user', 'user.l_user_id', '=', 'coach.user_id')
            ->where($whereCondition)
            ->paginate(10);
       
        if ($coach_list) {

            $coach_ids = array();
            foreach ($coach_list as $index => $coach) {
                $coach_list[$index]->hot_params = $this->getHotParams($coach->i_type); // 教练的类型标签:二级、三级、四级教练员等
                $coach_list[$index]->signup_num = 0; // 报名人数
                $coach_list[$index]->coach_imgurl = $this->buildUrl($coach->coach_imgurl);
                $coach_ids[] = $coach->coach_id;
            }

            // 是否有学车优惠券
            $current_time = time();
            $ticket_list = DB::table('coupon')
                ->select('id as coupon_id', 'owner_id', 'coupon_value', 'coupon_name')
                ->where([
                    ['owner_type', '=', '1'], // 1：教练，2：驾校
                    ['coupon_category_id', '=', '1'], // 1、现金券，2、打折券
                    ['scene' ,'=', '1'], // 1:报名班制 2:预约学车
                    ['expiretime', '>', $current_time],
                    ['is_open', '=', '1'], // 开启优惠券功能
                    ['is_show', '=', '1'], // 打开展示功能
                ])
                ->whereIn('owner_id', array_values($coach_ids))
                ->get();

            if(!empty($ticket_list->toArray())) {
                foreach ($coach_list as $key => $value) {
                    $coach_list[$key]->ticket_info = [];
                    foreach ($ticket_list as $k => $v) {
                        if($value->coach_id == $v->owner_id) {
                            $coach_list[$key]->ticket_info[] = $ticket_list[$k];
                        }
                    }
                }
            } else {
                foreach ($coach_list as $k => $v) {
                    $coach_list[$k]->ticket_info = [];
                }
            }

        }

        $data = [
            'code'  => 200,
            'msg'   => '获取成功',
            'data'  => ['coach_list' => $coach_list]
        ];

        return response()->json($data);

    }



    /**
     * 获取驾校下的教练
     **/
    private function getCountCoachInSchool ($school_id, $count = 0) {

        $whereCondition = [
            's_school_name_id'              => $school_id,
            'user.i_user_type'              => 1,
            'user.i_status'                 => 0,
            'coach.order_receive_status'    => 1, // 1：在线 0：不在线
        ];

        $coach_list = DB::table('coach')
            ->select(
                'l_coach_id as coach_id',
                's_coach_name as coach_name',
                's_coach_phone as coach_phone',
                'i_coach_star as coach_star',
                's_coach_imgurl as coach_imgurl'
            )
            ->leftJoin('user', 'user.l_user_id', '=', 'coach.user_id')
            ->where($whereCondition);
        if ($count >= 0) {
            $coach_list = $coach_list
                ->take($count)
                ->orderBy('is_hot', 'desc')
                ->orderBy('i_order', 'asc')
                ->get();
        } else {
            $coach_list = $coach_list
                ->orderBy('is_hot', 'desc')
                ->orderBy('i_order', 'asc')
                ->get();
        }
            
        $school_coach_list = [];
        if ($coach_list) {
            foreach ($coach_list as $key => $value) {
                $coach_list[$key]->coach_imgurl = $this->buildUrl($value->coach_imgurl);
            }

            $coach_count = DB::table('coach')
                ->select(DB::raw('count(1) as coach_count'))
                ->leftJoin('user', 'user.l_user_id', '=', 'coach.user_id')
                ->where($whereCondition)
                ->first();
            if ($coach_count->coach_count >= 3) {
                $is_coach_more = 2; // 查看更多
            } else {
                $is_coach_more = 1; // 没有更多
            }
        } else {
            $is_coach_more = 1; // 没有更多
        }

        $school_coach_list['is_coach_more'] = $is_coach_more;
        $school_coach_list['coach_list'] = $coach_list;
        return $school_coach_list;
    }

    // 获取驾校详情
    public function getSchoolDetail($school_id) {
        $school_detail = DB::table('school')
            ->select(
                'l_school_id as school_id',
                's_school_name as school_name',
                's_imgurl as school_imgurl',
                'is_show',
                's_location_x as location_x',
                's_location_y as location_y',
                's_frdb_tel as school_phone',
                's_shuoming as school_desc',
                's_address as school_address',
                'brand',
                's_thumb as thumb_imgurl'
            )
            ->where(['l_school_id' => $school_id])
            ->first();
        if  ($school_detail) {
            $school_detail->thumb_imgurl = $this->buildUrl($school_detail->thumb_imgurl);
        }
        // return $school_detail;
        $_school_imgurl_arr = [];
        $school_imgurl_arr = isset($school_detail->school_imgurl) ? json_decode($school_detail->school_imgurl, true) : [];
        if(is_array($school_imgurl_arr) && !empty($school_imgurl_arr)) {
            foreach ($school_imgurl_arr as $key => $value) {
                $url = $this->buildUrl($value);
                if (! empty($url)) {
                    $_school_imgurl_arr[] = $url;
                }
            }
        }
        if ($_school_imgurl_arr) {
            $school_detail->school_imgurl = $_school_imgurl_arr;
        } else {
            $school_detail->school_imgurl = [];
        }
        $school_detail->signup_num = $this->getSchoolOrdersNum($school_id);
        $school_desc = $school_detail->school_desc;
        $school_desc = str_replace('&amp;nbsp;','&nbsp;', $school_desc);
        $school_desc = str_replace('&lt;','<', $school_desc);
        $school_desc = str_replace('&gt;','>', $school_desc);
        $school_desc = str_replace('&quot;','"', $school_desc);
        $school_detail->school_desc = $school_desc;
        return $school_detail;
    }

    // 获取最近报名点的详情
    public function _getRecentTrainList($school_id, $lat, $lng) {
        // 获取最近报名点信息
        $_data = $this->getSchoolDetail($school_id);
        $train_list = DB::table('school_train_location')
            ->select(
                'id',
                'tl_train_address',
                'tl_location_x',
                'tl_location_y',
                'tl_phone',
                'tl_imgurl'
            )
            ->where('tl_school_id', $school_id)
            ->orderBy('order', 'desc')
            ->get();
        $list = array();
        $distance = array();
        if(!empty($train_list->toArray())) {
            foreach ($train_list as $key => $value) {
                // 获取所有距离
                $list[$key]['tl_train_address'] = $value->tl_train_address;
                $list[$key]['tl_phone']         = $value->tl_phone;
                $tl_imgurl                      = json_decode($value->tl_imgurl, true) ? json_decode($value->tl_imgurl, true) : [];
                $list[$key]['tl_imgurl']        = $tl_imgurl;
                $list[$key]['tl_location_x']    = $value->tl_location_x;
                $list[$key]['tl_location_y']    = $value->tl_location_y;
                $list[$key]['distance'] = round($this->getDistance($lng, $lat, $value->tl_location_x, $value->tl_location_y)/1000, 1);
                $distance[] = round($this->getDistance($lng, $lat, $value->tl_location_x, $value->tl_location_y)/1000, 1);
            }
            $school_distance = round($this->getDistance($lng, $lat, $_data->location_x, $_data->location_y)/1000, 1);
            $train_min_distance = !empty($distance) ? min($distance) : 0;
            if ($train_min_distance >= $school_distance) {
                $list = [];
                $distance[] = round($this->getDistance($lng, $lat, $_data->location_x, $_data->location_y)/1000, 1);
            }
        } else {
            $distance[] = round($this->getDistance($lng, $lat, $_data->location_x, $_data->location_y)/1000, 1);
        }
        $min_distance = !empty($distance) ? min($distance) : 0;
        $tl_imgurl_arr = array();

        if(is_array($list) && !empty($list)) {
            foreach ($list as $k => $v) {
                if($min_distance == $v['distance']) {
                    $_data->tl_train_address = $v['tl_train_address'];
                    $_data->tl_phone = $v['tl_phone'];
                    if(is_array($v['tl_imgurl']) && !empty($v['tl_imgurl'])) {
                        foreach ($v['tl_imgurl'] as $key => $value) {
                            $url = $this->buildUrl($value);
                            if (! empty($url) ) {
                                $tl_imgurl_arr[] = $url;
                            }
                        }
                    }
                    $_data->tl_imgurl = $tl_imgurl_arr;
                    $_data->tl_location_x = $v['tl_location_x'];
                    $_data->tl_location_y = $v['tl_location_y'];
                } else {
                    continue;
                }
            }
            if(!$_data->tl_imgurl) {
                $_data->tl_train_address = trim($_data->school_address);
                $_data->tl_phone = $_data->school_phone;
                $_data->tl_imgurl = $_data->school_imgurl;
                $_data->tl_location_x = $_data->location_x;
                $_data->tl_location_y = $_data->location_y;
            }
        } else {
            $_data->tl_train_address = trim($_data->school_address);
            $_data->tl_phone = $_data->school_phone;
            $_data->tl_imgurl = $_data->school_imgurl;
            $_data->tl_location_x = $_data->location_x;
            $_data->tl_location_y = $_data->location_y;
        }

        $_data->min_distance = round($min_distance, 1);
        $_data->distance_unit = 'km'; // 距离单位 m km

        return $_data;
    }

    // 获取驾校评价列表
    public function getSchoolCommentList($school_id, $type=1) {

        // $_data = [];
        $_data = new \Stdclass();

        // 获取评论列表（3条最近）
        $comment_list = DB::table('coach_comment')
            ->select(
                'coach_comment.school_star',
                'coach_comment.school_content',
                'coach_comment.user_id',
                'coach_comment.addtime',
                'user.s_username as user_name',
                'users_info.photo_id',
                'users_info.user_photo'
            )
            ->join('user', 'coach_comment.user_id', '=', 'user.l_user_id')
            ->join('users_info', 'coach_comment.user_id', '=', 'users_info.user_id')
            ->where([
                'coach_comment.school_id'=>$school_id,
                'coach_comment.type'=>2 // 1：预约学车 2：报名驾校
            ])
            ->orderBy('coach_comment.addtime', 'desc')
            ->take(3)
            ->get();
        if(!empty($comment_list->toArray())) {
            foreach ($comment_list->toArray() as $key => $value) {
                $comment_list[$key]->user_name = $comment_list[$key]->user_name ? $comment_list[$key]->user_name : '嘻哈学员';
                $comment_list[$key]->user_photo = $comment_list[$key]->user_photo ? env('APP_PATH').'admin/'.$comment_list[$key]->user_photo : '';
                $comment_list[$key]->addtime = $comment_list[$key]->addtime ? date('Y-m-d H:i', $comment_list[$key]->addtime) : date('Y-m-d H:i', time());
                $comment_list[$key]->school_content = $comment_list[$key]->school_content ? $comment_list[$key]->school_content : '默认好评';
            }
            // 获取评论总数
            $comment_count = DB::table('coach_comment')
                ->select(DB::raw('count(1) as comment_count'))
                ->join('user', 'coach_comment.user_id', '=', 'user.l_user_id')
                ->join('users_info', 'coach_comment.user_id', '=', 'users_info.user_id')
                ->where([
                    'coach_comment.school_id'=>$school_id,
                    'coach_comment.type'=>2 // 1：预约学车 2：报名驾校
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
        $_data->comment_count = DB::table('coach_comment')
            ->join('user', 'coach_comment.user_id', '=', 'user.l_user_id')
            ->join('users_info', 'coach_comment.user_id', '=', 'users_info.user_id')
            ->where([
                'coach_comment.school_id' => $school_id,
                'coach_comment.type' => 2 // 1：预约学车 2：报名驾校
            ])
            ->count();
        $star_sum = DB::table('coach_comment')->where([
                'coach_comment.school_id' => $school_id,
                'coach_comment.type' => 2 // 1：预约学车 2：报名驾校
            ])
            ->sum('school_star');
        $_data->average_star = $_data->comment_count ? floor($star_sum / $_data->comment_count) : 3;

        return $_data;
    }

    // 获取多少人报名驾校
    public function getSchoolOrdersNum($school_id) {
        $commonwhereCondition = [
            ['so_school_id', '=', $school_id],
            ['so_order_status', '<>', '101']
        ];
        $whereCondition = function($query) {
            $query->where('so_order_status', '=', '1')
                ->whereIn('so_pay_type', [1, 3, 4])
                ->orWhere(function($_query) {
                    $_query->where('so_order_status', '=', '3')
                        ->where('so_pay_type', '=', '2');
                });
        };
        $order_num = DB::table('school_orders')
                            ->where($commonwhereCondition)
                            ->where($whereCondition)
                            ->count();
        return $order_num;
    }


}
?>
