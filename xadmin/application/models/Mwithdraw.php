<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 提现Model
 */
class Mwithdraw extends CI_Model {

    public $withdraw_tbl;

    public function __construct()
    {
        parent::__construct();

        $this->withdraw_tbl = $this->db->dbprefix('withdraw');

        $this->load->database();
        $this->load->model('mbase');
        $this->load->model('mbalance');
        $this->load->model('mschool');
    }

    /**
     * 我的提现申请列表
     */
    public function myRequestList($where = [], $start = 0, $limit = 10)
    {
        $count = $this->db->from($this->withdraw_tbl)
            ->where($where)
            ->count_all_results();
        $page = (int) ceil( $count/$limit );

        $items = $this->db->from($this->withdraw_tbl)
            ->where($where)
            ->order_by('created_at', 'DESC')
            ->limit($limit, $start)
            ->get()
            ->result_array();

        if ($items && count($items) > 0) {
            foreach ($items as $index => $item) {
                // 提现进度
                $process_active = 0;
                $process_fields = ['created', 'reviewed', 'transferred', 'completed'];
                $ts_fields = ['created_at', 'reviewed_at', 'transferred_at', 'completed_at'];
                $process = [
                    ['title' => '已创建'],
                    ['title' => '已审核'],
                    ['title' => '已打款'],
                    ['title' => '已完成']
                ];
                $process_description = [
                    '您已提交申请 %s',
                    '财务人员已通过您的申请，准备打款 %s',
                    '财务人员已打款，到账有延迟 %s',
                    '关闭申请 %s',
                ];
                foreach ($process_fields as $i => $f) {
                    if (isset($item[$f]) && $item[$f] == 1) {
                        $process_active = $process_active + 1;
                        $process[$i]['description'] = sprintf($process_description[$i], date('m-d H:i', $item[$ts_fields[$i]]));
                    }
                }
                $items[$index]['process'] = $process;
                $items[$index]['process_active'] = $process_active;
                $items[$index]['process_active_description'] = $process[$process_active - 1]['title'];

                // 时间格式化
                foreach ($ts_fields as $i => $f) {
                    if (isset($item[$f]) && ($item[$f]) > 0) {
                        $items[$index][$f] = date('m-d H:i', $item[$f]);
                    }
                }
            }
        }

        return
            [
                'items' => $items,
                'pn'    => $page,
                'count' => $count
            ];
    }

    /**
     * 统计条目
     */
    public function count ($where)
    {
        return $this->db->from($this->withdraw_tbl)->where($where)->count_all_results();
    }

    /**
     * 统计求和
     */
    public function sum ($field, $where = [])
    {
        $sum = $this->db->select_sum($field)->where($where)->get($this->withdraw_tbl)->result_array();
        if (isset($sum[0]) && isset($sum[0][$field])) {
            return $sum[0][$field];
        }
        return 0;
    }

} /* class ends */
?>
