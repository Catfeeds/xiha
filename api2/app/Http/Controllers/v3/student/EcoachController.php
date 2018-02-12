<?php

/**
 * 电子教练模块
 * lumen查询构造器 https://laravel-china.org/docs/5.3/queries
 **/

namespace App\Http\Controllers\v3\student;

use Exception;
use Log;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\v3\student\AuthController as Auth;
use App\Http\Controllers\v3\student\UserController as User;
use Illuminate\Http\Request;

class EcoachController extends Controller {

    protected $request;
    protected $auth;
    protected $order;
    protected $user;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->auth = new AuthController();
        $this->user = new UserController($this->request);
    }

    /**
     * 电子教练上传学员模拟考试记录
     */
    public function uploadexam()
    {
        Log::info('########## uploadexam ##########');
        Log::info(json_encode(['form_data' => $this->request->input()], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        Log::info(json_encode(['_FILES' => $_FILES], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        Log::info(json_encode(['exam_record' => file_get_contents($_FILES['exam_record']['tmp_name'])], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        if ( !$this->request->has('user_idcard')
            OR !$this->request->has('school_id')
            OR !$this->request->has('train_date')
            OR !$this->request->has('train_starttime')
            OR !$this->request->has('train_endtime')
            OR !isset($_FILES['exam_record'])
        ) {
            Log::error('# uploadexam # 参数错误');
            return response()->json(['code' => 400, 'msg' => '参数错误', 'data' => new \stdClass]);
        }

        $idcard = $this->request->input('user_idcard');
        $school_id = $this->request->input('school_id');
        $date = strtotime($this->request->input('train_date')); // 日期转时间戳
        $date_fmt = date('Y-m-d', $date); // 日期
        $begin = date('H:i:s', strtotime($this->request->input('train_date') . (int)$this->request->input('train_starttime')));
        $end = date('H:i:s', strtotime($this->request->input('train_date') . (int)$this->request->input('train_endtime')));
        $exam_records_str = file_get_contents($_FILES['exam_record']['tmp_name']);

        // $exam_records_str = "#165341.2*165435.2*1*1*0*0*0*0*7*100*0*0*0*0*700*车身出线扣100分*****不按规定路线行驶扣100分本项目结束不按规定路线行驶扣100分本项目结束不按规定路线行驶扣100分本项目结束不按规定路线行驶扣100分本项目结束不按规定路线行驶扣100分本项目结束不按规定路线行驶扣100分本项目结束没有定点停车扣100分本项目结束";
        $exam_records_ar = array_values(array_filter(explode('#', $exam_records_str)));
        $exam_records = [];
        if (count($exam_records_ar) > 0) {
            foreach ($exam_records_ar as $i => $exam_record) {
                $exam_records[] = explode('*', $exam_record);
            }
        } else {
            Log::error('# uploadexam # 数据为空');
            return response()->json(['code' => 400, 'msg' => '参数错误', 'data' => new \stdClass]);
        }

        /**
         * 数据结构
         * 注：1-倒车入库 2-侧方停车 3-直角转弯 4-曲线行驶  5-坡道定点
         "exam_records": [
             [
                 "165341.2",
                 "165435.2",
                 "1",
                 "1",
                 "0",
                 "0",
                 "0",
                 "0",
                 "7",
                 "100",
                 "0",
                 "0",
                 "0",
                 "0",
                 "700",
                 "车身出线扣100分",
                 "",
                 "",
                 "",
                 "",
                 "不按规定路线行驶扣100分本项目结束不按规定路线行驶扣100分本项目结束不按规定路线行驶扣100分本项目结束不按规定路线行驶扣100分本项目结束不按规定路线行驶扣100分本项目结束不按规定路线行驶扣100分本项目结束没有定点停车扣100分本项目结束"
             ],
             [
                 "165341.2",
                 "165435.2",
                 "1",
                 "1",
                 "0",
                 "0",
                 "0",
                 "0",
                 "7",
                 "100",
                 "0",
                 "0",
                 "0",
                 "0",
                 "700",
                 "车身出线扣100分",
                 "",
                 "",
                 "",
                 "",
                 "不按规定路线行驶扣100分本项目结束不按规定路线行驶扣100分本项目结束不按规定路线行驶扣100分本项目结束不按规定路线行驶扣100分本项目结束不按规定路线行驶扣100分本项目结束不按规定路线行驶扣100分本项目结束没有定点停车扣100分本项目结束"
             ]
         ]
         *
         */

        // 统计总次数和合格率
        $total = 0;
        $pass = 0;
        // 出错统计
        $failstat = [
            'label' => ['倒车入库', '侧方停车', '直角转弯', '曲线行驶', '坡道定点', '其它项目'],
            'value' => [0, 0, 0, 0, 0, 0]
        ];
        $pure_list = [];
        $pure_item = [];
        foreach ($exam_records as $i => $exam_record) {
            // 如果$exam_record不是21个字段，则直接pass这条记录，进行下一条
            if (count($exam_record) === 21) {
                $total += 1;
            } else {
                continue;
            }

            // 开始时间
            $pure_item['begin'] = date('H:i:s', strtotime($this->request->input('train_date') . (int)$exam_record[0]));
            // 结束时间
            $pure_item['end'] = date('H:i:s', strtotime($this->request->input('train_date') . (int)$exam_record[1]));
            // 学习时长
            $total_time_in_second = strtotime($this->request->input('train_date') . (int)$exam_record[1]) - strtotime($this->request->input('train_date') . (int)$exam_record[0]);
            $total_time = '';
            $total_time_second = $total_time_in_second % 60; // 秒
            $total_time = $total_time_second . '秒' . $total_time;
            if ($total_time_in_second > 60) {
                // 超过60秒钟
                $total_time_in_minute = floor($total_time_in_second / 60);
                $total_time_minute = $total_time_in_minute % 60; // 分
                $total_time = $total_time_minute . '分' . $total_time;
                if ($total_time_in_minute > 60) {
                    // 超过60分钟
                    $total_time_in_hour = floor($total_time_in_minute / 60);
                    $total_time_hour = $total_time_in_hour % 24;
                    $total_time = $total_time_hour . '时' . $total_time;
                    if ($total_time_in_hour > 24 ) {
                        // 超过24小时
                        $total_time_in_day = floor($total_time_in_hour / 24);
                        $total_time_day = $total_time_in_day;
                        $total_time = $total_time_day. '天' . $total_time;
                    }
                }
            }
            $pure_item['total_time'] = $total_time;
            // 平均速度 km/h
            $pure_item['avspeed'] = floatval($exam_record[2]);
            // 扣分总值
            $pure_item['lostscore'] = $exam_record[9] + $exam_record[10] + $exam_record[11] + $exam_record[12] + $exam_record[13] + $exam_record[14];
            // 扣分原因列表
            $pure_item['lostreasons'] = array_values(array_filter([$exam_record[15], $exam_record[16], $exam_record[17], $exam_record[18], $exam_record[19], $exam_record[20]]));
            if ($pure_item['lostscore'] >= 100) {
                // 扣分超过100分，得分0
                $score = 0;
            } else {
                // 扣分不超过100分，用满分100减去扣分
                $score = 100 - $pure_item['lostscore'];
            }

            // 80分合格
            if ($score >= 80) {
                $pass += 1;
            }

            // 各项目出错统计
            $failstat['value'][0] += $exam_record[3];
            $failstat['value'][1] += $exam_record[4];
            $failstat['value'][2] += $exam_record[5];
            $failstat['value'][3] += $exam_record[6];
            $failstat['value'][4] += $exam_record[7];
            $failstat['value'][5] += $exam_record[8];

            $pure_item['score'] = $score;

            // 单条训练统计完毕
            $pure_list[] = $pure_item;;
        }

        $where = [
            ['idcard', '=', $idcard],
            ['date', '=', $date],
        ];

        if (DB::table('ecoach_exam_record')->where($where)->first()) {
            // update
            $res_ok = DB::table('ecoach_exam_record')
                ->where($where)
                ->update([
                    'idcard' => $idcard,
                    'date' => $date,
                    'date_fmt' => $date_fmt,
                    'begin' => $begin,
                    'end' => $end,
                    'total' => $total,
                    'pass' => $pass,
                    'failstat' => json_encode($failstat, JSON_UNESCAPED_UNICODE),
                    'traindetail' => json_encode($pure_list, JSON_UNESCAPED_UNICODE),
                    'uptime' => time(),
                ]);
        } else {
            // 插入一条记录
            $res_ok = DB::table('ecoach_exam_record')
                ->insert([
                    'idcard' => $idcard,
                    'date' => $date,
                    'date_fmt' => $date_fmt,
                    'begin' => $begin,
                    'end' => $end,
                    'total' => $total,
                    'pass' => $pass,
                    'failstat' => json_encode($failstat, JSON_UNESCAPED_UNICODE),
                    'traindetail' => json_encode($pure_list, JSON_UNESCAPED_UNICODE),
                    'addtime' => time(),
                ]);
        }

        if ($res_ok) {
            $data = ['code' => 200, 'msg' => 'OK', 'data' => new \stdClass];
        } else {
            $data = ['code' => 500, 'msg' => 'Fail', 'data' => new \stdClass];
        }

        return response()->json($data);
    }

    /**
     * 电子教练按身份证号获取学员某天的模拟考试记录
     */
    public function examresults()
    {
        /**
         * user 内含
         *
         * user_id
         * i_user_type
         * phone
         */
        $user = (new Auth())->getUserFromToken($this->request->input('token'));
        $User = new User($this->request);
        $user_info = $User->getUserInfoById($user['user_id']);
        $user_name = $user_info->user_name;
        $idcard = $user_info->identity_id;
        $photo_id = $user_info->photo_id;
        $photo_list = [
            '',
            'http://w.xihaxueche.com:8001/service/upload/default/1.png',
            'http://w.xihaxueche.com:8001/service/upload/default/2.png',
            'http://w.xihaxueche.com:8001/service/upload/default/3.png',
            'http://w.xihaxueche.com:8001/service/upload/default/4.png',
            'http://w.xihaxueche.com:8001/service/upload/default/5.png',
            'http://w.xihaxueche.com:8001/service/upload/default/6.png',
            'http://w.xihaxueche.com:8001/service/upload/default/7.png',
            'http://w.xihaxueche.com:8001/service/upload/default/8.png',
            'http://w.xihaxueche.com:8001/service/upload/default/9.png',
            'http://w.xihaxueche.com:8001/service/upload/default/10.png',
            'http://w.xihaxueche.com:8001/service/upload/default/11.png',
            'http://w.xihaxueche.com:8001/service/upload/default/12.png',
            'http://w.xihaxueche.com:8001/service/upload/default/13.png',
            'http://w.xihaxueche.com:8001/service/upload/default/14.png',
            'http://w.xihaxueche.com:8001/service/upload/default/15.png',
            'http://w.xihaxueche.com:8001/service/upload/default/16.png',
        ];

        Log::info('########## examresults ##########');
        Log::info(json_encode(['form_data' => $this->request->input()], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        Log::info(json_encode(['_FILES' => $_FILES], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        /*
        // mock data
        $data = file_get_contents(__DIR__ . '/document.json');
        $data = json_decode($data, true);
        $response = ['code' => 200, 'msg' => 'OK', 'data' => $data];
        // return response()->json($response);
        // mock over

        // todo: 根据token获取身份证编号
        // done

        // mock idcard
        $idcard = '342222199205119510';
        */

        // 参数日期
        if ($this->request->has('datetime')) {
            $date = $this->request->input('datetime');
        } else {
            $date = time();
        }
        $date_fmt = date('Y-m-d', $date);
        // 需要计算出这天的开始与结束时间戳
        $date_min = strtotime($date_fmt . ' 0:0:0');
        $date_max = strtotime($date_fmt . ' 23:59:59');

        $result = DB::table('ecoach_exam_record')
            ->select('*')
            ->where([
                ['idcard', '=', $idcard],
                ['date', '>=', $date_min],
                ['date', '<=', $date_max],
            ])
            ->first();

        // 空结果
        if (null == $result) {
            return response()->json(['code' => 200, 'msg' => 'OK', 'data' => new \stdClass()]);
        }

        if (null == $result->failstat) {
            // $result->failstat = ['label' => [], 'value' => []];
        } else {
            $result->failstat = json_decode($result->failstat, true);
        }

        $result->name = $user_name;
        $result->avatar = $photo_list[$photo_id];
        // 日期格式化 YYYY-MM-DD
        $result->date = date('Y-m-d', $result->date);
        // 模拟考试通关百分比计算
        $result->passrate = ( $result->total > 0 ) ? round($result->pass / $result->total * 100) : 0;
        $result->traindetail = json_decode($result->traindetail);

        if ( count($result->traindetail) > 0 ) {
            foreach ($result->traindetail as $i => $train) {
                $result->traindetail[$i]->nums = $i+1;
            }
        }

        $data = [
            'code' => 200,
            'msg'  => 'OK',
            'data' => $result,
        ];
        return response()->json($data);

    }

    /**
     * 上传训练结果
     */
    public function uploadtrain ()
    {
        Log::info('########## uploadexam ##########');
        Log::info(json_encode(['form_data' => $this->request->input()], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        Log::info(json_encode(['_FILES' => $_FILES], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        if (isset($_FILES['train_record'])) {
            Log::info(json_encode(['train_record' => file_get_contents($_FILES['train_record']['tmp_name'])], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        }

        $data =  ['code' => 200, 'msg' => 'ok', 'data' => new \stdClass()];
        return response()->json($data);
    }

    /**
     * 学车报告
     */
    public function reportDev ()
    {
        $raw = file_get_contents('./report.json');
        $data = json_decode($raw, true);

        return response()->json($data);
    }

    /**
     * 学车报告
     */
    public function report ()
    {
        /**
         * user 内含
         *
         * user_id
         * i_user_type
         * phone
         */
        $user = (new Auth())->getUserFromToken($this->request->input('token'));
        $User = new User($this->request);
        $user_info = $User->getUserInfoById($user['user_id']);
        $user_name = $user_info->user_name;
        $idcard = $user_info->identity_id;
        $photo_id = $user_info->photo_id;
        $photo_list = [
            '',
            'http://w.xihaxueche.com:8001/service/upload/default/1.png',
            'http://w.xihaxueche.com:8001/service/upload/default/2.png',
            'http://w.xihaxueche.com:8001/service/upload/default/3.png',
            'http://w.xihaxueche.com:8001/service/upload/default/4.png',
            'http://w.xihaxueche.com:8001/service/upload/default/5.png',
            'http://w.xihaxueche.com:8001/service/upload/default/6.png',
            'http://w.xihaxueche.com:8001/service/upload/default/7.png',
            'http://w.xihaxueche.com:8001/service/upload/default/8.png',
            'http://w.xihaxueche.com:8001/service/upload/default/9.png',
            'http://w.xihaxueche.com:8001/service/upload/default/10.png',
            'http://w.xihaxueche.com:8001/service/upload/default/11.png',
            'http://w.xihaxueche.com:8001/service/upload/default/12.png',
            'http://w.xihaxueche.com:8001/service/upload/default/13.png',
            'http://w.xihaxueche.com:8001/service/upload/default/14.png',
            'http://w.xihaxueche.com:8001/service/upload/default/15.png',
            'http://w.xihaxueche.com:8001/service/upload/default/16.png',
        ];

        Log::info('########## report ##########');
        Log::info(json_encode(['form_data' => $this->request->input()], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        // 参数日期
        if ($this->request->has('datetime')) {
            $date = $this->request->input('datetime');
        } else {
            $date = time();
        }
        $date_fmt = date('Y-m-d', $date);
        // 需要计算出这天的开始与结束时间戳
        $date_min = strtotime($date_fmt . ' 0:0:0');
        $date_max = strtotime($date_fmt . ' 23:59:59');

        $result = DB::table('ecoach_exam_record')
            ->select('*')
            ->where([
                ['idcard', '=', $idcard],
                ['date', '>=', $date_min],
                ['date', '<=', $date_max],
            ])
            ->first();

        // 空结果
        if (null == $result) {
            return response()->json(['code' => 200, 'msg' => 'OK', 'data' => new \stdClass()]);
        }

        if (null == $result->failstat) {
            // $result->failstat = ['label' => [], 'value' => []];
        } else {
            $result->failstat = json_decode($result->failstat, true);
        }

        if (null == $result->trainstat) {
            // $result->failstat = ['label' => [], 'value' => []];
        } else {
            $result->trainstat = json_decode($result->trainstat, true);
        }

        $result->name = $user_name;
        $result->avatar = $photo_list[$photo_id];
        // 日期格式化 YYYY-MM-DD
        $result->date = date('Y-m-d', $result->date);
        // 模拟考试通关百分比计算
        $result->passrate = ( $result->total > 0 ) ? round($result->pass / $result->total * 100) : 0;
        $result->traindetail = json_decode($result->traindetail);
        // 速度字符串转为浮点型
        $result->avspeed = floatval($result->avspeed);
        // 里程字符串转为浮点型
        $result->distance = floatval($result->distance);
        if (isset($result->begin) && isset($result->end)) {
            $begin_ts =  strtotime(date('Y-m-d ').$result->begin);
            $end_ts =  strtotime(date('Y-m-d ').$result->end);
            $time_sub = $end_ts - $begin_ts;
            $result->fact_time = round($time_sub / 3600, 1);
            $result->appoint_time = $result->fact_time;
            $result->valid_time = $result->fact_time;
        }

        if ( count($result->traindetail) > 0 ) {
            foreach ($result->traindetail as $i => $train) {
                $result->traindetail[$i]->nums = $i+1;
            }
        }

        $data = [
            'code' => 200,
            'msg'  => 'OK',
            'data' => $result,
        ];
        return response()->json($data);
    }

}
?>
