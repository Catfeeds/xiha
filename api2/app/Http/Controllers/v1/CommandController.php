<?php

namespace App\Http\Controllers\v1;

/**
 * 命令控制器
 *  1 batchCoach 生成批量的教练以备测试之用
 */

use Exception;
use InvalidArgumentException;
use Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;

class CommandController extends Controller {

    public function batchCoach() {
        Log::Info('工具开始执行');

        $phone_list = $this->buildPhoneList();
        $reg_counter = 0;
        $init_coach_param = $this->initCoachInfo();

        foreach ( $phone_list as $phone ) {
            $coach = DB::table('user')
                ->select('user.l_user_id as user_id')
                ->leftJoin('coach', 'coach.user_id', '=', 'user.l_user_id')
                ->where([
                    ['user.s_phone', '=', $phone],
                    ['user.i_user_type', '=', 1],
                    ['user.i_status', '=', 0],
                ])
                ->first();
            if ( $coach ) {
                Log::Info(sprintf("coach %s already exist", $phone));
            } else {
                Log::Info(sprintf("coach %s not exist", $phone));
                $new_coach = $this->regCoachWithPhone($phone, $init_coach_param);
                if ( $new_coach ) {
                    $reg_counter++;
                    Log::Info(sprintf("coach phone: %s regester ok coach_id: %d success count: %d", $phone, $new_coach, $reg_counter));
                } else {
                    Log::Info(sprintf("coach %s regester fail", $phone));
                }
            }
        }
        return ['new_coach' => $reg_counter];
    }

    private function buildPhoneList() {
        $phone_list = [];
        $prefix = '623511';
        $i = 0;
        while ( $i < 10000) {
            $phone = sprintf( "%04d", $i );
            $phone_list[] = $prefix.$phone;
            $i++;
        }
        return $phone_list;
    }

    private function initCoachInfo() {
        $coach_info = [
            'user_name' => '高教练', // 用户名前辍
            'password' => 'e10adc3949ba59abbe56e057f20f883e', // 教练登陆密码
            'i_user_type' => 1, // 教练
            'i_status' => 0, // 正常状态
            'time' => time(),
            'must_bind' => 2, // 不绑定也可预约此教练
            'teach_age' => 3, // 教龄
            'sex' => 1,
            'coach_imgurl' => '../upload/coach/4694/20170104/coachimg_586c5105070bb.png', // 教练头像
            'certification_status' => 3, // 教练已认证
            'timetraining_min_price' => 130,
            'school_id' => 5426, // 嘻哈驾校蜀山分校
            'lesson_id' => '2,3', // 科目二、三
            'license_id' => '1,10,11,12,13,14,15,16,2,3,5,6,7,8,9', // 牌照类型
            'average_license_time' => 70,
            'lesson2_pass_rate' => 86,
            'lesson3_pass_rate' => 88,
            'order_receive_status' => 1, // 在线接单
            'coach_star' => 4,
            'good_coach_star' => 35,
            'service_count' => 20,
            'success_count' => 40,
            'car_id' => 877,
            'address' => '安徽省合肥市蜀山区',
            'i_type' => 5,
            'province_id' => 340000,
            'city_id' => 340100,
            'area_id' => 340104,
            'total_price' => 150,
            'coach_content' => '太阳依旧升起，明天加倍努力',
            'am_subject' => '2',
            'pm_subject' => '2',
            'am_time_list' => '22,23,24,1,2,3,4',
            'pm_time_list' => '5,6,7,8,9,10,11,12,13,14,15,16',
            'is_hot' => 1, // 非热门教练
            'i_order' => 0, // 排名最后
            'timetraining_supported' => 1,
            'coupon_supported' => 1,
            'shift_min_price' => 1000,
            'shift_max_price' => 5000,
        ];

        return $coach_info;
    }

    private function regCoachWithPhone($phone, $params = []) {
        $uid = DB::table('user')
            ->insertGetId([
                's_username' => $params['user_name'].$phone,
                's_password' => $params['password'],
                'i_user_type' => $params['i_user_type'],
                'i_status' => $params['i_status'],
                's_real_name' => $params['user_name'].$phone,
                's_phone' => $phone,
                'addtime' => $params['time'],
                'updatetime' => $params['time'],
            ]);
        if ( $uid ) {
            Log::Info($uid);
            $coach_id = DB::table('coach')
                ->insertGetId([
                    'must_bind' => $params['must_bind'],
                    's_coach_name' => $params['user_name'].$phone,
                    's_teach_age' => $params['teach_age'],
                    's_coach_sex' => $params['sex'],
                    's_coach_imgurl' => $params['coach_imgurl'],
                    'certification_status' => $params['certification_status'],
                    'timetraining_supported' => $params['timetraining_supported'],
                    'timetraining_min_price' => $params['timetraining_min_price'],
                    's_coach_phone' => $phone,
                    's_school_name_id' => $params['school_id'],
                    's_coach_lesson_id' => $params['lesson_id'],
                    's_coach_lisence_id' => $params['license_id'],
                    'average_license_time' => $params['average_license_time'],
                    'lesson2_pass_rate' => $params['lesson2_pass_rate'],
                    'lesson3_pass_rate' => $params['lesson3_pass_rate'],
                    's_coach_car_id' => $params['car_id'],
                    'i_coach_star' => $params['coach_star'],
                    'good_coach_star' => $params['good_coach_star'],
                    'i_service_count' => $params['service_count'],
                    'i_success_count' => $params['success_count'],
                    's_coach_address' => $params['address'],
                    'i_type' => $params['i_type'],
                    'order_receive_status' => $params['order_receive_status'],
                    'province_id' => $params['province_id'],
                    'city_id' => $params['city_id'],
                    'area_id' => $params['area_id'],
                    'addtime' => $params['time'],
                    'updatetime' => $params['time'],
                    's_coach_content' => $params['coach_content'],
                    'user_id' => $uid,
                    's_am_subject' => $params['am_subject'],
                    's_pm_subject' => $params['pm_subject'],
                    's_am_time_list' => $params['am_time_list'],
                    's_pm_time_list' => $params['pm_time_list'],
                    'is_hot' => $params['is_hot'],
                    'i_order' => $params['i_order'],
                    'coupon_supported' => $params['coupon_supported'],
                    'shift_min_price' => $params['shift_min_price'],
                    'shift_max_price' => $params['shift_max_price'],
                ]);
            if ( $coach_id ) {
                return $coach_id;
            } else {
                return false;
            }
        }
    }
}

?>
