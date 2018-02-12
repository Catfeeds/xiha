<?php
header("Content-type: text/html; charset=utf-8");
!defined('IN_FILE') && exit('Access Denied');
$op = in_array($op, array('index','school','coach','schooledit','coachedit')) ? $op : 'index';

if(!isset($_SESSION['school_loginauth'])) {
    echo "<script>location.href='index.php?action=admin&op=login';</script>";
    exit();
    }
    $loginauth_str = authcode($_SESSION['school_loginauth'], 'DECODE');
    $loginauth_arr = explode('\t', $loginauth_str);
    $school_id = $loginauth_arr[2];

    $mcoach = new mcoach($db);
    $mschool = new mschool($db);

    if($op == 'index') {
        $school_sysconfig = $mschool->getSchoolSysconfig($school_id);
        $smarty->assign('school_sysconfig', $school_sysconfig);
        $smarty->display('sysconfig/school.html');

    } else if($op == 'school') {
        $school_sysconfig = $mschool->getSchoolSysconfig($school_id);
        $smarty->assign('school_sysconfig', $school_sysconfig);
        $smarty->display('sysconfig/school.html');

    } else if($op == 'coach') {
        $coachlist = $mcoach->getSysCoachlist($school_id);
        $time_list = $mcoach->getAnPmTimeConfig();
        // echo "<pre>";
        // print_r($coachlist);
        // print_r($time_list);
        // exit();
        $smarty->assign('time_list', $time_list);
        $smarty->assign('coachlist', $coachlist);
        $smarty->display('sysconfig/coach.html');

    } else if($op == 'schooledit') {
        $order_limit_time = isset($_POST['order_limit_time']) ? trim($_POST['order_limit_time']) : '2';
        $appoint_limit_time = isset($_POST['appoint_limit_time']) ? trim($_POST['appoint_limit_time']) : '2';
        $is_automatic = isset($_POST['is_automatic']) ? trim($_POST['is_automatic']) : '1';
        $time_config_id = isset($_POST['time_config_id']) ? $_POST['time_config_id'] : array();
        $free_time = isset($_POST['free_time']) ? trim($_POST['free_time']) : 0;

        $arr['l_school_id'] = $school_id;
        $arr['order_limit_time'] = $order_limit_time;
        $arr['appoint_limit_time'] = $appoint_limit_time;
        $arr['is_automatic'] = $is_automatic;
        $arr['free_time'] = $free_time;
        $arr['s_time_list'] = implode(',', $time_config_id);

        if($arr['s_time_list'] == '' || $arr['appoint_limit_time'] == '' || $arr['is_automatic'] == '') {
            echo "<script>alert('请将信息选择完全'); location.href='index.php?action=sysconfig&op=school'</script>";
            exit();
        }
        // print_r($arr);
        // exit();

        $res = $mschool->updateSchoolTimeLimit($arr);
        if($res) {
            echo "<script>alert('设置成功！'); location.href='index.php?action=sysconfig&op=school'</script>";
            exit();
        } else {
            echo "<script>alert('设置失败！'); location.href='index.php?action=sysconfig&op=school'</script>";
            exit();
        }

    } else if($op == 'coachedit') {

        $morning_subjects = isset($_POST['morning_subjects']) ? trim($_POST['morning_subjects']) : 2;
        $afternoon_subjects = isset($_POST['afternoon_subjects']) ? trim($_POST['afternoon_subjects']) : 2;
        $coach_id = isset($_POST['coach_id']) ? trim($_POST['coach_id']) : 0;
        $am_time_config_id = isset($_POST['am_time_config_id']) ? $_POST['am_time_config_id'] : array();
        $pm_time_config_id = isset($_POST['pm_time_config_id']) ? $_POST['pm_time_config_id'] : array();

        if(empty($pm_time_config_id) && empty($am_time_config_id)) { // || -> && 两者不可以同时为空，上午为空或下午为空是允许的
            echo "<script>alert('请选择可预约时间'); location.href='index.php?action=sysconfig&op=coach'</script>";
            exit();
        }

        if($morning_subjects == 0 || $afternoon_subjects == 0) {
            echo "<script>alert('请选择培训科目'); location.href='index.php?action=sysconfig&op=coach'</script>";
            exit();
        }

        if($coach_id == 0) {
            echo "<script>alert('请选择教练'); location.href='index.php?action=sysconfig&op=coach'</script>";
            exit();
        }

        $arr['l_school_id'] = $school_id;
        $arr['coach_id'] = $coach_id;
        $arr['morning_subjects'] = $morning_subjects;
        $arr['afternoon_subjects'] = $afternoon_subjects;
        $arr['am_time_config_id'] = implode(',', $am_time_config_id);
        $arr['pm_time_config_id'] = implode(',', $pm_time_config_id);
        // echo "<pre>";
        // print_r($arr);
        // exit();
        $res = $mcoach->updateCoachTimeLimit($arr);
        if($res) {
            echo "<script>alert('设置成功！'); location.href='index.php?action=sysconfig&op=coach'</script>";
            exit();
        } else {
            echo "<script>alert('设置失败！'); location.href='index.php?action=sysconfig&op=coach'</script>";
            exit();
        }
    }
?>
