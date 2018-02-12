<?php

/**
 * 教练端计时设置
 *
 **/

namespace App\Http\Controllers\v2;

use Exception;
use Log;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\v1\AuthController;
use App\Http\Controllers\v1\OrderController;
use App\Http\Controllers\v1\UserController;
use Illuminate\Http\Request;

class CoachController extends Controller {
    
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

// 1、计时设置
    /**
     * 获取当前教练的时间模板
     * @param   string  token
     * @return  void
     **/
    public function getCoachTimeTemplate () {

        $user = (new AuthController)->getUserFromToken($this->request->input('token'));
        $user_id = $user['user_id'];
        $coach_id = $this->user->getCoachIdByUserId($user_id);
        if ( ! $coach_id) {
            $data = [
                'code'  =>  400,
                'msg'   =>  '当前教练账号异常',
                'data'  =>  new \stdClass
            ];
            Log::error('异常：【获取教练时间模板】当前的教练已下线/不存在 | 当前教练所在的驾校已下线/不存在');
            return response()->json($data);
        }

        $whereCondition = [
            'temp_owner_id' => $coach_id,
            'temp_type'     => 1, // 1:教练 2:驾校
            'is_deleted'    => 1, // 1:未删除 2:已删除
        ];

        $templatelist = DB::table('template_relationship as template')
            ->select('template.*')
            ->where($whereCondition)
            ->get();
       
        if ($templatelist) {
            foreach ($templatelist as $key => $value) {

                if ( 0 != $value->addtime ) {
                    $templatelist[$key]->addtime = date('Y-m-d H:i:s', $value->addtime);
                } else {
                    $templatelist[$key]->addtime = '';
                }

                if ( 0 != $value->updatetime ) {
                    $templatelist[$key]->updatetime = date('Y-m-d H:i:s', $value->updatetime);
                } else {
                    $templatelist[$key]->updatetime = '';
                }

                $templateconfig = [];
                $starthour = [];
                $endhour = [];
                $timeconfiglist = DB::table('time_config_template as config')
                    ->select('config.*')
                    ->where([
                        ['config.temp_id', '=', $value->id],
                        ['config.deleted', '=', 1], // 1:已删除 2:未删除
                    ])
                    ->get();
                
                if ($timeconfiglist) {
                    foreach ($timeconfiglist as $k => $v) {
                        $templateconfig[$k]['temp_config_id'] = $v->id;
                        $templateconfig[$k]['temp_id'] = $v->temp_id;
                        $templateconfig[$k]['start_time'] = $v->start_time;
                        $templateconfig[$k]['end_time'] = $v->end_time;
                        $templateconfig[$k]['start_hour'] = $v->start_hour;
                        $templateconfig[$k]['start_minute'] = $v->start_minute;
                        $templateconfig[$k]['end_hour'] = $v->end_hour;
                        $templateconfig[$k]['end_minute'] = $v->end_minute;
                        $templateconfig[$k]['lesson_time'] = $v->lesson_time;
                        $templateconfig[$k]['lesson_name'] = $v->lesson_name;
                        $templateconfig[$k]['lesson_id'] = $v->lesson_id;
                        $templateconfig[$k]['license_id'] = $v->license_id;
                        $templateconfig[$k]['license_name'] = $v->license_name;
                        $templateconfig[$k]['price'] = intval($v->price);
                        $templateconfig[$k]['max_user_num'] = $v->max_user_num;
                        $templateconfig[$k]['is_online'] = $v->is_online;
                        $starthour[] = $v->start_hour;
                        $endhour[] = $v->end_hour;
                    }
                }

                if (!empty($starthour) && !empty($endhour)) {
                    $start_work_time = min($starthour);
                    $end_work_time = max($endhour);
                } else {
                    $start_work_time = '8';
                    $end_work_time = '18';
                }

                $templatelist[$key]->start_work_time = $start_work_time;
                $templatelist[$key]->end_work_time = $end_work_time;
                $templatelist[$key]->temp_time_config = $templateconfig;
                
            }
        }

        $data = [
            'code'  => 200,
            'msg'   => '获取成功',
            'data'  => ['templatelist' => $templatelist]
        ];

        return response()->json($data);

    }

    /**
     * 添加 | 编辑教练时间模板
     * @param   string      token           用户登录标识
     * @param   number      id              模板ID[编辑时必传]
     * @param   string      temp_name       模板名称
     * @param   string      lesson_name     科目名称
     * @param   string      license_name    牌照名称
     * @param   string      price           价格
     * @param   array[obj]  temp_time_config时间段设置
     * @param   number      time_config_id  时间段设置ID[编辑时必传]
     * @param   number      temp_id         模板ID
     * @param   string      start_time      开始时间
     * @param   string      end_time        结束时间
     * @param   string      lesson_name     科目名称
     * @param   string      license_name    牌照名称
     * @param   string      lesson_name     科目名称
     * @param   string      price           价格
     * @param   string      is_online       是否在线(1：在线 | 2：不在线)
     * @return void
     **/
    public function handleCoachTimeTemplate () {

        if ( ! $this->request->has('temp_name')
            OR ! $this->request->has('lesson_name')
            OR ! $this->request->has('license_name')
            OR ! $this->request->has('price')
            OR ! $this->request->has('is_default')
            OR ! $this->request->has('temp_time_config')) 
        {
            $data = [
                'code'  => 400,
                'msg'   => '参数错误',
                'data'  => new \stdClass,
            ];
            Log::error('异常：【添加|编辑教练时间模板】缺少必须参数');
            return response()->json($data);
        }

        $user = $this->auth->getUserFromToken($this->request->input('token'));
        $user_id = $user['user_id'];
        $coach_id = $this->user->getCoachIdByUserId($user_id);
        if ( ! $coach_id) {
            Log::error('异常：【添加|编辑教练时间模板】当前教练可能已下线(不存在)');
            return response()->json([
                'code'  => 400,
                'msg'   => '账号出现异常',
                'data'  => new \stdClass,
            ]);
        }

        $coach_info = $this->user->getCoachInfoById($coach_id);
        $coach_name = $coach_info->coach_name;
        $temp_time_config = $this->request->input('temp_time_config');
        $is_default = $this->request->input('is_default');
        $time_config_arr = json_decode($temp_time_config);
        if ( ! is_array($time_config_arr)) {
            $data = [
                'code'  => 400,
                'msg'   => '格式错误',
                'data'  => new \stdClass,
            ];
            Log::error('异常：【添加|编辑教练时间模板】提交的时间段格式不是json格式的');
            return response()->json($data);
        }

        $price = $this->request->input('price');
        $temp_name = $this->request->input('temp_name');
        $lesson_name = $this->request->input('lesson_name');
        $license_name = $this->request->input('license_name');
        $license_info = $this->getLicenseIdByName($license_name);
        $license_id = $license_info->license_id;

        switch ($lesson_name) {
            case '科目一':
                $lesson_id = '1';
                break;
            case '科目二':
                $lesson_id = '2';
                break;
            case '科目三':
                $lesson_id = '3';
                break;
            case '科目四':
                $lesson_id = '4';
                break;
            default :
                $lesson_id = 2;
        }
        
        if ( $is_default == 1) { // 默认模板
            // 获取默认的时间模板
            $defaultTemp = $this->getDefaultTemp($coach_id, 1);
            if ( ! empty($defaultTemp)) { 
                // 将更新过的模板设置成
                $update_ok = $this->chanageTempDefault($defaultTemp, $coach_id, 1, 2);
            }
        }

        $nowtime = time();
        if ( ! $this->request->input('id')) { // add template

            $temp_id = DB::table('template_relationship')
                ->insertGetId([
                    'temp_name'         => $temp_name,
                    'temp_owner_id'     => $coach_id,
                    'temp_owner_name'   => $coach_name,
                    'temp_type'         => 1, // 1:coach 2:school
                    'price'             => $price, 
                    'lesson_id'         => $lesson_id,
                    'lesson_name'       => $lesson_name,
                    'license_id'        => $license_id,
                    'license_name'      => $license_name,
                    'weekday'           => '',
                    'is_default'        => $is_default,
                    'is_online'         => 1, // 1:在线 | 2:不在线
                    'is_deleted'        => 1, // 1:未删除 | 2:已删除
                    'addtime'           => $nowtime,
                ]);

            if ($temp_id) {
                    $data = [
                        'code'  => 200,
                        'msg'   => '添加成功',
                        'data'  => 'ok',
                    ];
                } else {
                    $data = [
                        'code'  => 400,
                        'msg'   => '添加失败',
                        'data'  => '',
                    ];
                }

        } else { // update template

            $temp_id = $this->request->input('id');
            $temp_info = $this->getTemplateById($temp_id, $coach_id);
            if ( ! $temp_info) {
                $data = [
                    'code'  => 400,
                    'msg'   => '服务器异常',
                    'data'  => new \stdClass,
                ];
                Log::error('异常：【更新时间模板】当前模板已下线');
                return response()->json($data);
            }

            $update_data = [
                'temp_name'         => $temp_name,
                'temp_owner_id'     => $coach_id,
                'temp_owner_name'   => $coach_name,
                'temp_type'         => 1, // 1:coach 2:school
                'price'             => $price, 
                'lesson_id'         => $lesson_id,
                'lesson_name'       => $lesson_name,
                'license_id'        => $license_id,
                'license_name'      => $license_name,
                'weekday'           => '',
                'is_default'        => $is_default,
                'is_online'         => 1, // 1:在线 | 2:不在线
                'is_deleted'        => 1, // 1:未删除 | 2:已删除
                'updatetime'        => $nowtime,
            ];

            $update_ok = DB::table('template_relationship as temp')
                ->where([
                    ['temp.id', '=', $temp_id],
                    ['temp_owner_id', '=', $coach_id],
                    ['temp_type', '=', 1],
                    ['is_deleted', '=', 1],
                ])
                ->update($update_data);

            if ($update_ok >= 1) {
                $data = [
                    'code'  => 200,
                    'msg'   => '更新成功',
                    'data'  => 'ok',
                ];
            } else {
                $data = [
                    'code'  => 400,
                    'msg'   => '更新失败',
                    'data'  => '',
                ];
            }

        }

        // 处理模板时间配置
        foreach ($time_config_arr as $key => $value) {

            $start_time = $value->start_time;
            $start_time_arr = explode(':', $start_time);
            $start_hour = intval($start_time_arr[0]);
            $start_minute = intval($start_time_arr[1]);

            $end_time = $value->end_time;
            $end_time_arr = explode(':', $end_time);
            $end_hour = intval($end_time_arr[0]);
            $end_minute = intval($end_time_arr[1]);
            
            $lesson_time = round((strtotime($end_time) - strtotime($start_time))/3600, 1);

            $license_name = $value->license_name;
            $lesson_info = $this->getLessonIdByName($lesson_name);
            $lesson_id = $lesson_info->lesson_id;

            $license_info = $this->getLicenseIdByName($license_name);
            $license_id = $license_info->license_id;

            $price = $value->price;

            if ( '' == $value->time_config_id ) 
                // && '' == $value->temp_id) 
            { // add new time_config_template
                $time_config_id = DB::table('time_config_template')
                    ->insertGetId([
                        'temp_id'       => $temp_id,
                        'start_time'    => $start_time,
                        'end_time'      => $end_time,
                        'start_hour'    => $start_hour, // 1:coach 2:school
                        'start_minute'  => $start_minute, 
                        'end_hour'      => $end_hour,
                        'end_minute'    => $end_minute,
                        'lesson_time'   => $lesson_time,
                        'lesson_name'   => $lesson_name,
                        'lesson_id'     => $lesson_id,
                        'license_id'    => $license_id,
                        'license_name'  => $license_name,
                        'price'         => $price,
                        'is_online'     => 1, // 1:在线 | 2:不在线
                        'deleted'       => 1, // 1:未删除 | 2:已删除
                        'addtime'       => $nowtime,
                    ]);
                
            } else { // update time_config_template

                $time_config_id = $value->time_config_id;
                if ($temp_id != $value->temp_id) {
                    $template_id = $temp_id;
                }

                $update_data = [
                    'id'            => $time_config_id,
                    'temp_id'       => $template_id,
                    'start_time'    => $start_time,
                    'end_time'      => $end_time,
                    'start_hour'    => $start_hour, // 1:coach 2:school
                    'start_minute'  => $start_minute, 
                    'end_hour'      => $end_hour,
                    'end_minute'    => $end_minute,
                    'lesson_time'   => $lesson_time,
                    'lesson_name'   => $lesson_name,
                    'lesson_id'     => $lesson_id,
                    'license_id'    => $license_id,
                    'license_name'  => $license_name,
                    'price'         => $price,
                    'is_online'     => 1, // 1:在线 | 2:不在线
                    'deleted'       => 1, // 1:未删除 | 2:已删除
                    'updatetime'    => $nowtime,
                ];

                $update_ok = DB::table('time_config_template as config')
                    ->where([
                        ['id', '=', $time_config_id],
                        ['deleted', '=', 1],
                    ])
                    ->update($update_data);
            }
        }

        return response()->json($data);

    }

    // 将默认的时间模板改成非默认状态下的
    private function chanageTempDefault ($temp_id_arr, $owner_id, $temp_type, $default) {

        $update_ok = DB::table('template_relationship as temp')
            ->where([
                'temp_owner_id' => $owner_id,
                'temp_type'     => $temp_type,
                'is_deleted'    => 1,
            ])
            ->whereIn('temp.id', $temp_id_arr)
            ->update(['is_default' => $default]);

        return $update_ok;

    }

    // 获取用户的默认时间模板
    private function getDefaultTemp ($owner_id, $owner_type) {

        $tempdefault = DB::table('template_relationship as temp')
            ->select('temp.id')
            ->where([
                ['is_deleted', '=', 1],
                ['temp_owner_id', '=', $owner_id],
                ['temp_type', '=', $owner_type], // 1:教练 | 2:驾校
                ['is_default', '=', 1], // 1:默认 | 2:不默认
            ])
            ->get();
        $timetempdefault = [];

        if ($tempdefault) {
            foreach ($tempdefault as $key => $value) {
                $timetempdefault[$key] = $value->id;
            }
        }

        return $timetempdefault;

    }


    /**
     * 删除时间模板
     * @param   string  token   用户登录标识
     * @param   number  id      模板ID
     * @return  void
     **/
    public function deleteCoachTimeTemplate () {

        if ( ! $this->request->has('id')) {
            $data = [
                'code'  => 400,
                'msg'   => '参数错误',
                'data'  => new \stdClass
            ];
            Log::error('异常：【删除时间模板】缺少必须参数');
            return response()->json($data);
        }

        $temp_id = $this->request->input('id');
        $temp_id_arr = explode(',', $temp_id);
        $user = (new AuthController)->getUserFromToken($this->request->input('token'));
        $user_id = $user['user_id'];
        $coach_id = $this->user->getCoachIdByUserId($user_id);
        if ( ! $coach_id) {
            $data = [
                'code'  =>  400,
                'msg'   =>  '当前教练账号异常',
                'data'  =>  new \stdClass
            ];
            Log::error('异常：【删除时间模板】当前教练已下线/不存在 | 当前教练所在的驾校已下线/不存在');
            return response()->json($data);
        }

        $checkTemp = $this->getTemplateById($temp_id, $coach_id);
        if (null == $checkTemp) {
            $data = [
                'code'  => 400,
                'msg'   => '服务器异常',
                'data'  => new \stdClass
            ];
            Log::error('异常：【删除时间模板】当前模板可能已被删除 | 当前模板补属于当前教练');
            return response()->json($data);
        }

        $whereCon = [
            'temp_owner_id' => $coach_id,
            'temp_type'     => 1, // 1:教练 2:驾校
            'is_deleted'    => 1, // 1:未删除 2:已删除
        ];

        $update_ok = DB::table('template_relationship as template')
            ->where($whereCon)
            ->whereIn('template.id', $temp_id_arr)
            ->update(['is_deleted' => 2]);

        $temptimeconf = $this->getTempTimeConf($temp_id_arr);
        if ( ! $temptimeconf) {
            $update_ok = DB::table('time_config_template as config')
                ->where('deleted', '=', 1)
                ->whereIn('template.id', $temp_id_arr)
                ->update(['deleted' => 2]);
        } 
        
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

    // 通过temp_id获取模板时间配置列表
    private function getTempTimeConf ($temp_id) {
        
        $temptimeconf = DB::table('time_config_template as config')
            ->select('config.*')
            ->where('deleted', '=', 1)
            ->whereIn('config.id', $temp_id)
            ->get();

        return $temptimeconf;

    }


    // 获取教练的
    private function getTemplateById ($id, $coach_id) {

        $temp_id_arr = explode(',', $id);

        $whereCondition = [
            // 'id'            => $id,
            'temp_owner_id' => $coach_id,
            'temp_type'     => 1, // 1:教练 2:驾校
            'is_deleted'    => 1, // 1:未删除 2:已删除
        ];

        $coachtemplate = DB::table('template_relationship as template')
            ->select('template.*')
            ->where($whereCondition)
            ->whereIn('template.id', $temp_id_arr)
            ->first();
        
        return $coachtemplate;

    }

    // 根据license_name获取license_id
    public function getLicenseIdByName ($license_name) {

        $license_arr = $this->getLicenseName();
        
        if ( '' == $license_name  
            OR !in_array($license_name, $license_arr)) 
        {
            $license_name = 'C1';
        }

        $license_info = DB::table('license_config')
            ->select('license_id')
            ->where('license_name', '=', $license_name)
            ->first();

        return $license_info;

    }

    // 根据license_name获取license_id
    public function getLessonIdByName ($lesson_name) {

        $lesson_arr = $this->getLessonName();
        if ( '' == $lesson_name
            OR ! in_array($lesson_name, $lesson_arr)) 
        {
            $lesson_name = '科目二';
        }

        $lesson_info = DB::table('lesson_config')
            ->select('lesson_id')
            ->where([
                ['lesson_name', '=', $lesson_name],
                ['is_open', '=', 1],
            ])
            ->first();

        return $lesson_info;

    }

    // 获取所有牌照名称
    private function getLicenseName () {

        $license_list = DB::table('license_config')
            ->select(
                'license_id', 
                'license_name'
            )
            ->where('is_open', '=', 1)
            ->get();

        $license_arr = [];
        if ($license_list) {
            foreach ($license_list as $index => $license) {
                $license_arr[] = $license->license_name;
            }
        }

        return $license_arr;
    }

    // 获取所有科目名称
    private function getLessonName () {

        $lesson_arr = [
            '1' => '科目一',
            '2' => '科目二',
            '3' => '科目三',
            '4' => '科目四',
        ];

        return $lesson_arr;
    }




}