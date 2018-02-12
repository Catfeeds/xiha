<?php

/**
 * 教练端首页中考试安排、模拟成绩接口
 *
 **/

namespace App\Http\Controllers\v1;

use Exception;
use Log;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\v1\AuthController;
use App\Http\Controllers\v1\OrderController;
use App\Http\Controllers\v1\UserController;
use Illuminate\Http\Request;

class CoachIndexController extends Controller {
    
    protected $request;
    protected $auth;
    protected $order;
    protected $user;

    public function __construct(Request $request) {
        $this->request = $request;
        $this->auth = new AuthController($this->request);
        $this->order = new OrderController($this->request);
        $this->user = new UserController($this->request);
    }

    /**
     * 获取考试安排列表
     * @param   string  token   用户登录标识
     * @param   number  year    年
     * @param   number  month   月
     * @return void
     **/
    public function getExamArrangementList () {

        if ( ! $this->request->has('year')
            OR ! $this->request->has('month')) 
        {
            $data = [
                'code'  =>  400,
                'msg'   =>  '参数错误',
                'data'  =>  new \stdClass
            ];
            Log::error('异常：【获取考试安排列表】缺少必须参数');
            return response()->json($data);
        }

        $year = intval($this->request->input('year'));
        $month = intval($this->request->input('month'));
        $user = (new AuthController)->getUserFromToken($this->request->input('token'));
        $user_id = $user['user_id'];
        $user_phone = $user['phone'];
        $coach_id = $this->user->getCoachIdByUserId($user_id);
        if ( ! $coach_id) {
            Log::error('异常：【获取考试安排列表】获取的教练不存在');
            return response()->json([
                'code' => 400,
                'msg' => '此教练的账号出现异常',
                'data' => new \stdClass,
            ]);
        }

        $condition = [
            'deleted'   => 1,
            'coach_id'  => $coach_id,
            'exam_year' => $year,
            'exam_month'=> $month,
        ];

        $arrangement_list = DB::table('coach_users_exam_arrangement as arrangement')
            ->select('arrangement.*')
            ->where($condition)
            ->get();
        
        $exam_list = [];
        $nowtime = time();
        if ($arrangement_list) {
            foreach ($arrangement_list as $key => $value) {

                $exam_list[$key]['id'] = $value->id;

                // coach_id
                $exam_list[$key]['coach_id'] = $value->coach_id;

                // lesson_id | lesson_name
                $exam_lesson = $value->exam_lesson;
                $exam_list[$key]['lesson_id'] = $exam_lesson;
                switch ($exam_lesson) {
                    case '1' :
                        $exam_list[$key]['lesson_name'] = '科目一';
                        break;
                    case '2' :
                        $exam_list[$key]['lesson_name'] = '科目二';
                        break;
                    case '3' :
                        $exam_list[$key]['lesson_name'] = '科目三';
                        break;
                    case '4' :
                        $exam_list[$key]['lesson_name'] = '科目四';
                        break;
                    default :
                        $exam_list[$key]['lesson_name'] = '科目二';
                        break;
                }

                // exam_year
                $exam_list[$key]['exam_year'] = $value->exam_year;

                // exam_month
                $exam_list[$key]['exam_month'] = $value->exam_month;

                // exam_day
                $exam_list[$key]['exam_day'] = $value->exam_day;

                $exam_timestamp = $value->exam_timestamp;
                if ( $exam_timestamp > $nowtime) {
                    $exam_list[$key]['is_overtime'] = 1; // 1:未超时 2:超时
                } else {
                    $exam_list[$key]['is_overtime'] = 2; // 1:未超时 2:超时
                }

                // exam_site
                $exam_list[$key]['exam_site'] = $value->exam_site;
                
                // exam_beizhu
                $exam_list[$key]['exam_beizhu'] = $value->exam_beizhu;
                
                // remind_time
                $remind_timestamp = $value->remind_timestamp;
                $remind_time = date('Y-m-d H:i', $remind_timestamp);
                $exam_list[$key]['remind_time'] = $remind_time;

                // exam_users_info
                $exam_users_info = [];
                if ( NULL != $value->coach_users_id
                     && NULL != $value->user_beizhu
                    ) 
                {

                    $coach_users_id = $value->coach_users_id;
                    $coach_users_id_arr = explode(',', $coach_users_id);
                    $user_beizhu_arr = json_decode($value->user_beizhu, true);
                    
                    if ( is_array($coach_users_id_arr)) {
                        foreach ($coach_users_id_arr as $index => $value) {
                            $coach_user = DB::table('coach_users_records')
                                ->select('user_name')
                                ->where([
                                    ['is_deleted', '=', 1],
                                    ['coach_id', '=', $coach_id],
                                    ['coach_users_id', '=', $value]
                                ])
                                ->first();
                            
                            $exam_users_info[$index]['user_id'] = $value;
                            $exam_users_info[$index]['user_name'] = $coach_user->user_name;
                            foreach ( $user_beizhu_arr as $k => $v) {
                                if ($k == $value) {
                                    $exam_users_info[$index]['user_beizhu'] = $v;
                                }
                            }
                            $exam_list[$key]['exam_users_info'] = $exam_users_info;
                        }
                    }
                }
                // end exam_users_info
            }
        }

        $data = [
            'code'  => 200,
            'msg'   => '获取成功',
            'data'  => $exam_list,
        ];

        return response()->json($data);
    }


    /**
     * 添加 | 编辑学员考试安排信息
     * @param   string    token             用户登录标识
     * @param   number    id                考试安排表的ID(编辑必须传)
     * @param   number    year              年(考试时间)
     * @param   number    month             月(考试时间)
     * @param   number    day               日(考试时间)
     * @param   number    lesson            考试科目
     * @param   string    site              考试场地
     * @param   string    remindtime        提醒时间
     * @param   string    exam_beizhu       考试备注         
     * @param   obj       user_info         学员信息（user_phone, user_name, user_beizhu）
     * @return void
     **/
    public function handleStudentExam () {
        
        if ( ! $this->request->has('year') 
            OR ! $this->request->has('month')
            OR ! $this->request->has('day')
            OR ! $this->request->has('lesson')
            OR ! $this->request->has('site')) 
        {
            $data = [
                'code'  => 400,
                'msg'   => '参数错误',
                'data'  => new \stdClass,
            ];
            Log::error('异常：【添加|编辑学员考试安排信息】缺少必须参数');
            return response()->json($data);
        }
        
        $user = (new AuthController)->getUserFromToken($this->request->input('token'));
        $user_id = $user['user_id'];
        $coach_id = $this->user->getCoachIdByUserId($user_id);
        if ( ! $coach_id) {
            Log::error('异常：【获取学员模拟成绩】获取的教练不存在');
            return response()->json([
                'code' => 400,
                'msg' => '此教练的账号出现异常',
                'data' => new \stdClass,
            ]);
        }

        $year = $this->request->input('year');
        $month = $this->request->input('month');
        $day = $this->request->input('day');
        $lesson = intval($this->request->input('lesson')) ? intval($this->request->input('lesson')) : 2; // 默认科目二
        $site = (string)($this->request->input('site'));
        $date = $year.'-'.$month.'-'.$day;
        $exam_timestamp = strtotime($date);
        
        $lesson_arr = [
            '1' => '科目一',
            '2' => '科目二',
            '3' => '科目三',
            '4' => '科目四',
        ];

        if ( ! in_array($lesson, array_keys($lesson_arr)) ) {
            $data = [
                'code'  => 400,
                'msg'   => '科目错误',
                'data'  => new \stdClass,
            ];
            Log::error('异常：【添加|编辑学员考试安排信息】不存在的科目');
            return response()->json($data);
        }

        if ( mb_strlen($site) > 20) {
            $data = [
                'code'  => 400,
                'msg'   => '考试场地不能超过20个字',
                'data'  => new \stdClass
            ];
            Log::error('异常：【添加|编辑学员考试安排信息】考试的场地超过了20个字');
            return response()->json($data);
        }

        // 考试备注
        $exam_beizhu = '';
        if ( $this->request->has('exam_beizhu') ) {
            $exam_beizhu = $this->request->input('exam_beizhu');
            if ( mb_strlen($exam_beizhu) > 50 ) {
                $data = [
                    'code'  => 400,
                    'msg'   => '考试备注不能超过50个字',
                    'data'  => new \stdClass
                ];
                Log::error('异常：【添加|编辑学员考试安排信息】考试备注超过了50个字');
                return response()->json($data);
            }
        } 

        // 提醒时间
        $remind_time = 0;
        if ( $this->request->has('remind_time') ) {
            $remind_time = $this->request->input('remind_time');
            $remind_time = strtotime($remind_time);
        } 

        // 用户信息
        $coach_users_id = '';
        $user_name = '';
        $user_phone = '';
        $user_beizhu = '';
        $user_beizhu_arr = [];
        $coach_users_id_arr = [];
        $coach_users_lesson = [];
        if ( $this->request->has('user_json') ) {
            $user_json = $this->request->input('user_json');            
            $user_json_arr = json_decode($user_json);
            return response()->json($user_json);
            if ( ! is_array($user_json_arr)) {
                $data = [
                    'code'  => 400,
                    'msg'   => '学员格式出错',
                    'data'  => new \stdClass
                ];
                Log::error('异常：【添加|编辑学员考试安排信息】格式错误：添加学员信息不是json格式的');
                return response()->json($data);
            }

            if (count($user_json_arr) > 30) {
                $data = [
                    'code'  => 400,
                    'msg'   => '学员格式出错',
                    'data'  => new \stdClass
                ];
                Log::error('异常：【添加|编辑学员考试安排信息】格式错误：添加学员信息不是json格式的');
                return response()->json($data);
            }

            if ($user_json_arr) {
                foreach ($user_json_arr as $key => $value) {

                    $user_beizhu = $value->user_beizhu;
                    $user_phone = $value->user_phone;
                    $coach_users = $this->getCoachUsersId($user_phone, $coach_id); 
                    // coach_users_id_arr
                    if (NULL != $coach_users && NULL != $coach_users->id) {

                        $coach_users_id = $coach_users->id;
                        $coach_users_id_arr[] = $coach_users_id;
                        if ('' != $user_beizhu) {
                            $user_beizhu_arr[$coach_users_id] = $user_beizhu;
                        }
                    }

                    // coach_users_lesson
                    if (NULL != $coach_users && NULL != $coach_users->i_stage) {
                        $coach_users_lesson[] = intval($coach_users->i_stage);
                    }

                }

            }
            
        } // end user_json

        // 学员科目是否匹配
        if ($coach_users_lesson) {
            foreach ($coach_users_lesson as $index => $value) {
                if ($lesson != $value) {
                    $data = [
                        'code'  => 400,
                        'msg'   => '学员信息异常,请重新添加学员',
                        'data'  => new \stdClass
                    ];
                    Log::error('异常：【添加|编辑学员考试安排信息】学员的科目与教练设置的考试安排的科目不匹配 | 该科目下教练还未添加此学员');
                    return response()->json($data);
                }
            }
        }

        $nowtime = time();
        $coach_users_ids = NULL;
        $user_beizhu_json = NULL;
        if ( !empty($coach_users_id_arr)) {
            $coach_users_ids = implode(',', $coach_users_id_arr);
        }

        if ( !empty($user_beizhu_arr)) {
            $user_beizhu_json = $this->JSON($user_beizhu_arr);
        }

        // 操作
        if ( ! $this->request->has('id')) { // 新增
            $record_id = DB::table('coach_users_exam_arrangement')
                ->insertGetId([
                    'coach_users_id'    => $coach_users_ids,
                    'coach_id'          => $coach_id,
                    'exam_lesson'       => $lesson,
                    'exam_year'         => $year,
                    'exam_month'        => $month,
                    'exam_day'          => $day,
                    'exam_timestamp'    => $exam_timestamp,
                    'exam_site'         => $site,
                    'exam_beizhu'       => $exam_beizhu,
                    'user_beizhu'       => $user_beizhu_json,
                    'remind_timestamp'  => $remind_time,
                    'deleted'           => 1,
                    'addtime'           => $nowtime,
                    'updatetime'        => 0,
                ]);
            if ($record_id) {
                $data = [
                    'code'  => 200,
                    'msg'   => '添加成功',
                    'data'  => 'ok'
                ];
            } else {
                $data = [
                    'code'  => 400,
                    'msg'   => '添加失败',
                    'data'  => ''
                ];
            }

            // 新增结束
        } else { // 更新
            $record_id = $this->request->input('id');
            $check_arrangement = $this->getUserArrangement($record_id, $coach_id);
            if (NULL == $check_arrangement) {
                $data = [
                    'code'  => 400,
                    'msg'   => '服务器繁忙',
                    'data'  => new \stdClass
                ];
                Log::error('异常：【更新学员考试安排信息】安排ID->'.$record_id.'不存在或不是当前教练所设置的');
                return response()->json($data);
            }

            $update_data = [
                'coach_users_id'    => $coach_users_ids,
                'coach_id'          => $coach_id,
                'exam_lesson'       => $lesson,
                'exam_year'         => $year,
                'exam_month'        => $month,
                'exam_day'          => $day,
                'exam_timestamp'    => $exam_timestamp,
                'exam_site'         => $site,
                'exam_beizhu'       => $exam_beizhu,
                'user_beizhu'       => $user_beizhu_json,
                'remind_timestamp'  => $remind_time,
                'deleted'           => 1,
                'updatetime'        => $nowtime,
            ];
            $update_ok = DB::table('coach_users_exam_arrangement')
                ->where('id', '=', $record_id)
                ->update($update_data);
            if ($update_ok >= 1) {
                $data = [
                    'code'  => 200,
                    'msg'   => '更新成功',
                    'data'  => 'ok'
                ];
            } else {
                $data = [
                    'code'  => 400,
                    'msg'   => '更新成功',
                    'data'  => ''
                ];
            }
            // 结束更新
        }

        return response()->json($data);

    }

    /**
     * 删除考试安排
     * @param   string  token   用户登录标识 
     * @param   number  id      考试安排ID
     * @return void
     **/
    public function deleteStuExamArrangment () {

        if ( ! $this->request->has('id')) {
            $data = [
                'code'  => 400,
                'msg'   => '参数错误',
                'data'  => new \stdClass
            ];
            Log::error('异常：【删除考试安排】缺少必须参数');
            return response()->json($data);
        }

        $id = $this->request->input('id');
        $user = (new AuthController)->getUserFromToken($this->request->input('token'));
        $user_id = $user['user_id'];
        $user_phone = $user['phone'];
        $coach_id = $this->user->getCoachIdByUserId($user_id);
        if ( ! $coach_id) {
            Log::error('异常：【删除考试安排】获取的教练不存在');
            return response()->json([
                'code' => 400,
                'msg' => '此教练的账号出现异常',
                'data' => new \stdClass,
            ]);
        }

        $check_arrangement = $this->getUserArrangement($id, $coach_id);
        if (NULL == $check_arrangement) {
            $data = [
                'code'  => 400,
                'msg'   => '服务器繁忙',
                'data'  => new \stdClass
            ];
            Log::error('异常：【删除考试安排】考试安排ID->'.$id.'不存在 | 不是当前教练所添加的考试安排');
            return response()->json($data);
        }

        $update_data = [
            'deleted'   => 2,
        ];

        $whereCondition = [
            'deleted'   => 1,
            'id'        => $id,
            'coach_id'  => $coach_id
        ];

        $update_ok = DB::table('coach_users_exam_arrangement')
            ->where($whereCondition)
            ->update($update_data);
        
        if ($update_ok >= 1) {
            $data = [
                'code'  => 200,
                'msg'   => '删除成功',
                'data'  => 'ok'
            ];
        } else {
             $data = [
                'code'  => 400,
                'msg'   => '删除失败',
                'data'  => ''
            ];
        }

        return response()->json($data);

    }

    /**
     * 通过ID获取考试安排的信息
     * @param   number  record_id   考试安排ID
     * @return  void
     **/
    private function getUserArrangement ($record_id, $coach_id) {

        $arrangement = DB::table('coach_users_exam_arrangement')
            ->select('coach_users_exam_arrangement.*')
            ->where([
                ['id', '=', $record_id],
                ['coach_id', '=', $coach_id],
                ['deleted', '=', 1]
            ])
            ->first();

        return $arrangement;

    }

    /**
     * 获取教练学员表中的学员ID
     * @param   number  user_phone  用户手机号
     * @return  void
     **/
    private function getCoachUsersId ($user_phone, $coach_id) {

        $whereCondition = [
            'records.user_phone'        => $user_phone,
            'records.coach_id'          => $coach_id,
            'coach_users.user_phone'    => $user_phone,
        ];

        $coach_users = DB::table('coach_users')
            ->select(
                'coach_users.id',
                'records.i_stage'
            )
            ->leftJoin('coach_users_records as records', 'records.coach_users_id', '=', 'coach_users.id')
            ->where($whereCondition)
            ->first();

        return $coach_users;

    }
    


    /**
     * 获取学员模拟成绩（与教练绑定关系的学员）
     * @param string token 用户登录标识
     * @param number lesson_id 科目ID (1：科目一 | 2：科目二 | 3：科目三 | 4：科目四)
     * @return void
     **/
    public function getStudentExamRecords () {

        if ( ! $this->request->has('lesson_id')) {
            $data = [
                'code'  => 400,
                'msg'   => '参数错误',
                'data'  => new \stdClass,
            ];
            Log::Info('异常：【获取学员模拟成绩】缺少参数科目ID');
            return response()->json($data);
        }

        $lesson_id = $this->request->input('lesson_id') ? $this->request->input('lesson_id') : 1;
        $user = (new AuthController)->getUserFromToken($this->request->input('token'));
        $user_id = $user['user_id'];
        $user_phone = $user['phone'];
        $coach_id = $this->user->getCoachIdByUserId($user_id);
        if ( ! $coach_id) {
            Log::error('异常：【获取学员模拟成绩】获取的教练不存在');
            return response()->json([
                'code' => 400,
                'msg' => '此教练的账号出现异常',
                'data' => new \stdClass,
            ]);
        }

        $user_ids_arr = $this->getBindUserByCoachId($coach_id);
        if ( empty($user_ids_arr)) {
            $data = [
                'code'  => 200,
                'msg'   => '获取成功',
                'data'  => [],
            ];
            Log::Info('异常：【获取学员模拟成绩】目前还没有学员与当前教练绑定');
            return response()->json($data);
        }

        $examrecords = [];
        foreach ($user_ids_arr as $key => $value) {

            $user_info = $this->user->getUserInfoById($value);

            if ($user_info) {
                
                $exam_license_name = $user_info->exam_license_name;
                
                if ('' != $exam_license_name) {
                    $whereCon = [
                        'stype' => $lesson_id, 
                        'ctype' => $exam_license_name, 
                        'user_id' => $value
                    ];
                } else {
                    $whereCon = [
                        'stype' => $lesson_id, 
                        'user_id' => $value
                    ];
                }

                $record_list = DB::table('user_exam_records')
                    ->select(
                        'user_id',
                        'realname',
                        'phone_num',
                        'identify_id',
                        'score',
                        'exam_total_time',
                        'os',
                        'stype',
                        'ctype',
                        'addtime'
                    )
                    ->where($whereCon)
                    ->orderBy('addtime', 'DESC')
                    ->take(1)
                    ->first();
                
                if ( $record_list) {
                    $record_list->user_id = $record_list->user_id ? $record_list->user_id : $user_info->user_id;
                    $record_list->realname = $record_list->realname ? $record_list->realname : ($user_info->real_name ? $user_info->real_name : $user_info->user_name);
                    $record_list->phone_num = $record_list->phone_num ? $record_list->phone_num : $user_info->phone;
                    $record_list->photo_id = $user_info->photo_id;
                    $record_list->user_photo = $this->buildUrl($user_info->user_photo);
                    $record_list->exam_license_name = $exam_license_name;
                    $record_list->identify_id = $record_list->identify_id ? $record_list->identify_id : $user_info->identity_id;
                    $record_list->score = $record_list->score;
                    $record_list->exam_total_time = $record_list->exam_total_time;
                    $record_list->stype = $record_list->stype;
                    $record_list->ctype = $record_list->ctype;
                    $addtime = $record_list->addtime;
                    $record_list->year = date('Y', $addtime);
                    $record_list->month = date('m', $addtime);
                    $record_list->day = date('d', $addtime);
                    $record_list->bind_status = 1; // 1:已绑定
                    $record_list->is_register = 1; // 1:已注册
                    $examrecords[] = $record_list;
                } else {
                    $examrecords = [];
                }
            }
        }

        $data = [
            'code'  => 200,
            'msg'   => '获取成功',
            'data'  => $examrecords
        ];

        return response()->json($data);

    }


    /**
     * 获取绑定教练的学员ID
     * @param string $user_ids 学员ID
     * @return void
     **/
    private function getBindUserByCoachId ($coach_id) {

        $bind_user_id = DB::table('coach_user_relation')
            ->select('coach_user_relation.user_id')
            ->leftJoin('user', 'l_user_id', '=', 'coach_user_relation.user_id')
            ->where([
                ['coach_user_relation.coach_id', '=', $coach_id],
                ['bind_status', '=', 1], // 1：已绑定
                ['i_user_type', '=', 0], 
                ['i_status', '=', 0]
            ])
            ->get();
        $user_id_arr = [];
        if ($bind_user_id) {
            foreach ($bind_user_id as $key => $value) {
                $user_id_arr[] = $value->user_id;
            }
        }
        if ($user_id_arr) {
            return $user_id_arr;
        } else {
            return [];
        }
        
    }

    














}