<?php

namespace App\Http\Controllers\v1;

use Exception;
use App\Http\Controllers\Controller;
use App\Http\Controllers\v1\AuthController;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use phpQuery;

/**
 * @module 题库
 */

class ExamController extends Controller {

    protected $request;

    public function __construct(Request $request) {
        $this->request = $request;
    }

    /**
     * 获取题目总数
     *
     * @param void
     * @return array
     */
    public function sum() {
        $raw_license = [
            'C1',  // 小车
            'A2',  // 货车
            'A1',  // 客车
            'D' ,  // 摩托车
        ];
        $list = [];
        foreach ($raw_license as $index => $license_name) {
            $license_info = DB::table('license_config')
                ->select('license_id', 'license_name as exam_license_name', 'license_title', 'license_class')
                ->where([
                    ['license_name', '=', $license_name],
                    ['is_open', '=', 1], // 1-open 2-not
                ])
                ->first();
            if (! is_null($license_info)) {
                $exam_total = DB::table('exams')
                    ->select(DB::raw('stype, count(id) as total_num'))
                    ->where([
                        ['ctype', '=', $license_name],
                    ])
                    ->groupBy('ctype', 'stype')
                    ->get();
                if (! is_null($exam_total)) {
                    $license_info->exam_total = $exam_total;
                } else {
                    $license_info->exam_total = [];
                }
                $license_info->thumbnail = '';
            } else {
                continue;
            }
            $list[] = $license_info;
        }
        if (! $list) {
            $data = [
                'code' => 400,
                'msg'  => '暂无题目信息',
                'data' => [
                    'updated_to' => date('Y-m-d', time()),
                        'list'       => [],
                    ],
                ];
        } else {
            $data = [
                'code' => 200,
                'msg'  => 'OK',
                'data' => [
                    'updated_to' => date('Y-m-d', time()),
                        'list'       => $list,
                    ],
                ];
        }
        return response()->json($data);
    }

    /**
     * 获取所有牌照
     *
     * @param void
     * @return array
     */
    public function licenseList() {
        $license_list = DB::table('license_config')
            ->select('license_id', 'license_name')
            ->where('is_open', '=', '1') // 1-开启 2-不开启
            ->orderBy('order', 'desc')
            ->get();
        if ($license_list) {
            $data = [
                'code' => 200,
                'msg'  => 'OK',
                'data' => $license_list,
            ];
        } else {
            $data = [
                'code' => 200,
                'msg'  => 'OK',
                'data' => [],
            ];
        }
        return response()->json($data);
    }

    // 根据科目获取内容
    public function getSubjectsInfo() {
        if(!$this->request->has('lesson_id') || !$this->request->has('license_id')) {
            return response()->json([
                'code'=>400,
                'msg'=>'参数错误',
                'data'=>'',
            ]);
        }
        $license_id = $this->request->input('license_id');
        $lesson_id = $this->request->input('lesson_id');

        $_data = [];
        $video_list = $this->getVideoList('', $lesson_id);

        $_data['video_list'] = array_values($video_list);
        $data = ['code'=>200, 'msg'=>'获取科目数据成功', 'data'=>$_data];
        return response()->json($data);
    }

    // 获取视频列表
    private function getVideoList($video_id='', $lesson_id=2) {
        $video_arr = array(
            '2'=>array(//科目二
                '1'=>array(
                    'pic_url'=>env('APP_UPLOAD_PATH').'video/subjects/lesson_2/i6223561274278543874.png',
                    'video_url'=>'http://toutiao.com/i6223561274278543874/',
                    'download_url'=>env('APP_UPLOAD_PATH').'video/subjects/lesson_2/download/i6223561274278543874.mp4',
                    'video_title'=>'倒车入库',
                    'video_time'=>'4:42'
                ),
                '2'=>array(
                    'pic_url'=>env('APP_UPLOAD_PATH').'video/subjects/lesson_2/i6223609442529706498.png',
                    'video_url'=>'http://toutiao.com/i6223609442529706498/',
                    'download_url'=>env('APP_UPLOAD_PATH').'video/subjects/lesson_2/download/i6223609442529706498.mp4',
                    'video_title'=>'曲线行驶',
                    'video_time'=>'2:54'
                ),
                '3'=>array(
                    'pic_url'=>env('APP_UPLOAD_PATH').'video/subjects/lesson_2/i6223610324461814273.png',
                    'video_url'=>'http://toutiao.com/i6223610324461814273/',
                    'download_url'=>env('APP_UPLOAD_PATH').'video/subjects/lesson_2/download/i6223610324461814273.mp4',
                    'video_title'=>'直角转弯',
                    'video_time'=>'2:33'
                ),
                '4'=>array(
                    'pic_url'=>env('APP_UPLOAD_PATH').'video/subjects/lesson_2/i6223604730757644801.png',
                    'video_url'=>'http://toutiao.com/i6223604730757644801/',
                    'download_url'=>env('APP_UPLOAD_PATH').'video/subjects/lesson_2/download/i6223604730757644801.mp4',
                    'video_title'=>'坡道定点停车和起步',
                    'video_time'=>'3:09'
                ),
                '5'=>array(
                    'pic_url'=>env('APP_UPLOAD_PATH').'video/subjects/lesson_2/i6223554707994968577.png',
                    'video_url'=>'http://toutiao.com/i6223554707994968577/',
                    'download_url'=>env('APP_UPLOAD_PATH').'video/subjects/lesson_2/download/i6223554707994968577.mp4',
                    'video_title'=>'侧方停车',
                    'video_time'=>'3:45'
                ),
            ),
            '3'=>array( // 科目三
                '1'=>array(
                    'pic_url'=>env('APP_UPLOAD_PATH').'video/subjects/lesson_3/i6224298883833397761.png',
                    'video_url'=>'http://toutiao.com/i6224298883833397761/',
                    'download_url'=>env('APP_UPLOAD_PATH').'video/subjects/lesson_3/download/i6224298883833397761.mp4',
                    'video_title'=>'跟车+超车+会车',
                    'video_time'=>'2:34'
                ),
                '2'=>array(
                    'pic_url'=>env('APP_UPLOAD_PATH').'video/subjects/lesson_3/i6222939658863510017.png',
                    'video_url'=>'http://toutiao.com/i6222939658863510017/',
                    'download_url'=>env('APP_UPLOAD_PATH').'video/subjects/lesson_3/download/i6222939658863510017.mp4',
                    'video_title'=>'起步夜间行驶',
                    'video_time'=>'3:24'
                ),
                '3'=>array(
                    'pic_url'=>env('APP_UPLOAD_PATH').'video/subjects/lesson_3/i6224312620556485121.png',
                    'video_url'=>'http://toutiao.com/i6224312620556485121/',
                    'download_url'=>env('APP_UPLOAD_PATH').'video/subjects/lesson_3/download/i6224312620556485121.mp4',
                    'video_title'=>'通过路口+通过人行横道+通过学校公交站',
                    'video_time'=>'3:15'
                ),
                '4'=>array(
                    'pic_url'=>env('APP_UPLOAD_PATH').'video/subjects/lesson_3/i6224300591913370114.png',
                    'video_url'=>'http://toutiao.com/i6224300591913370114/',
                    'download_url'=>env('APP_UPLOAD_PATH').'video/subjects/lesson_3/download/i6224300591913370114.mp4',
                    'video_title'=>'变更车道+掉头+靠边停车',
                    'video_time'=>'2:56'
                ),
            )
        );
        if(!isset($video_arr[$lesson_id])) {
            return [];
        }
        if(trim($video_id) == '') {
            return $video_arr[$lesson_id];
        } else {
            if(!isset($video_arr[$lesson_id][$video_id])) {
                return [];
            }
        }
        return $video_arr[$lesson_id][$video_id];
    }

    // 点击视频获取内容
    public function getVideoInfo() {
        if(!$this->request->has('lesson_id') || !$this->request->has('video_id')) {
            return response()->json([
                'code'=>400,
                'msg'=>'参数错误',
                'data'=>'',
            ]);
        }
        $video_id = $this->request->input('video_id');
        $lesson_id = $this->request->input('lesson_id');

        $video_info = $this->getVideoInfoByLessonVideo($video_id, $lesson_id);
        $data = ['code'=>200, 'msg'=>'获取视频成功', 'data'=>$video_info];
        return response()->json($data);
    }

    // 获取科目二，科目三的视频内容
    private function getVideoInfoByLessonVideo($video_id, $lesson_id) {
        $video_info = $this->getVideoList($video_id, $lesson_id);

        $data = ['code'=>200, 'msg'=>'获取视频成功', 'data'=>$video_info];
        return response()->json($data);
    }

    // 获取灯光模拟
    public function getLightsList() {
        if(!$this->request->has('lesson_id') || !$this->request->has('license_id')) {
            return response()->json([
                'code'=>400,
                'msg'=>'参数错误',
                'data'=>'',
            ]);
        }
        $light_list = $this->_getLightsList();
        $data = ['code'=>200, 'msg'=>'获取灯光列表成功', 'data'=>array_values($light_list)];
        return response()->json($data);
    }

    // 获取灯光列表
    private function _getLightsList() {

        $light_arr = array(
            '1'=>array(
                'light_url'=>env('APP_PATH').'assets/voice/light1.mp3',
                'light_title'=>'灯光1'
            ),
            '2'=>array(
                'light_url'=>env('APP_PATH').'assets/voice/light2.mp3',
                'light_title'=>'灯光2',
            ),
            '3'=>array(
                'light_url'=>env('APP_PATH').'assets/voice/light3.mp3',
                'light_title'=>'灯光3',
            ),
            '4'=>array(
                'light_url'=>env('APP_PATH').'assets/voice/light4.mp3',
                'light_title'=>'灯光4',
            ),
            '5'=>array(
                'light_url'=>env('APP_PATH').'assets/voice/light5.mp3',
                'light_title'=>'灯光5',
            ),
            '6'=>array(
                'light_url'=>env('APP_PATH').'assets/voice/light6.mp3',
                'light_title'=>'灯光6',
            ),
            '7'=>array(
                'light_url'=>env('APP_PATH').'assets/voice/light7.mp3',
                'light_title'=>'灯光7',
            ),
            '8'=>array(
                'light_url'=>env('APP_PATH').'assets/voice/light8.mp3',
                'light_title'=>'灯光8',
            ),
        );
        return $light_arr;
    }

}

?>
