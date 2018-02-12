<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Crypt;        //引用加密解密类

class PublicController extends BaseController {

    //登录
    public function login() {
        if (session('loginauth')) {
            //已登录
            $this->redirect('Index/index');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD']=="POST") {
            if (!empty(I('post.captcha'))) {
                //验证码校验
                $vry = new \Think\Verify();
                $vry->reset = false;
                if(!$vry->check(I('post.captcha'))){    //如果验证码错误
                    $this->error('验证码错误');
                }else {                                 //如果验证码正确
                    //提交登录数据
                    $data['name']=I('post.username');             //将用户名存入数组
                    $data['password']=md5(I('post.password'));    //将密码存入数组
                    $user = M('admin')->where($data)->find();       //将用户名和密码组成的数组作为条件在数据库中查找

                    if ($user==null) {                            //如果$user为空
                        $this->error('用户不存在或密码错误');     //提示用户
                    }else{                                        //如果$user不为空
                        if($user['is_close'] != 1) {
                            $this->error('您已被限制登录，请联系嘻哈客服说明原因');     //提示用户
                            exit();
                        }
                        $crypt = new \Think\Crypt(); //实例化加密解密类             
                        //将用户登陆信息赋给变量
                        $session = $user['name'].'\t'.$user['id'].'\t'.$user['password'].'\t'.$user['role_id'].'\t'.$user['school_id'].'\t'.$user['content'].'\t'.$user['is_close'];
                        $loginauth = $crypt->encrypt($session, CRYPT_KEY);//用Crypt加密类加密方法加密
                        //将加密后的用户登陆信息保存在session中
                        session(array('expire' => 3600 * 24));
                        session('loginauth', $loginauth);
                        //记录行为
                        action_log('user_login', 'admin', $user['id'], $user['id']);
                        $this->redirect('Index/index'); //代理商首页    
                    }
                }
            }
        }else{
            //普通访问
            $this->display(CONTROLLER_NAME.'/'.__FUNCTION__);
        } 

    }

    public function logout(){
        session('loginauth',null);     
        $this->success('注销成功',U('Admin/Public/login'));
    }

    //生成验证码
    public function verifyImg() {
        // clean output buffer
        ob_clean();
        $config = array(
            'imageH'    => 30,  //验证码图片高度
            'imageW'    => 120, 
            'fontSize'  =>14,   //验证码字体大小
            'fontttf'   =>'4.ttf',  //验证码字体，不设置随机获取
            'length'    =>4,    //验证码位数
            'useCurve'  =>  false,            // 是否画混淆曲线
        );
        $verify = new \Think\Verify($config);
        // 输出验证码并把验证码的值保存到session中
        // 验证码保存到session的格式为： array('verify_code' => '验证码值', 'verify_time' => '验证码创建时间');
        $verify->entry();
    }

}
