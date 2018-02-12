
<?php
    header("Content-Type:text/html; charset=UTF-8");
    error_reporting(0);
    require_once '../include/config.php';
    require_once '../include/phpqrcode/qrlib.php';
    $PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'../uploads'.DIRECTORY_SEPARATOR;
    if (!file_exists($PNG_TEMP_DIR))
        mkdir($PNG_TEMP_DIR);
    $filename = $PNG_TEMP_DIR.$user_info['qrcode'].'.png';
    $errorCorrectionLevel = 'H';
    $matrixPointSize = 5;
    $PNG_WEB_DIR = 'uploads/';
    QRcode::png(M_HOST.'u/'.$user_info['qrcode'], $filename, $errorCorrectionLevel, $matrixPointSize, 2);    
    $imgurl = $PNG_WEB_DIR . basename($filename);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1, user-scalable=no">
    <link rel="stylesheet" href="<?php echo HOST; ?>../m/assets/css/framework7.ios.min.css">
    <meta name="format-detection" content="telephone=yes">
    <!-- <link rel="stylesheet" href="<?php echo HOST; ?>../m/assets/css/framework7.ios.colors.min.css"> -->
    <title>app下载</title>
</head>
<body  style="background: #2472b4;">
    <div id="particles-js" style="margin:0px auto; width:100%; height: 100%; position:absolute; text-align:center; ">
    </div>
    <div style="position:relative; width:80%; box-shadow: 0px 1px 2px #225e90; height: 460px; margin:0px auto; top:50%; transform: translateY(-50%); background:rgba(255, 255, 255, 1); border-radius:8px; padding:5px;">
        <p style="text-align:center; margin: 0px;">
            <br><span style="color: #999;">工作人员</span><br><a class = "external" href="tel:<?php echo $user_info['phone']; ?>"><span style="color: #c90;"><?php echo $user_info['content']; ?></span></a>
        </p>
        <?php if($user_info['phone']) { ?>
        <a style="top: 23px; right: 25px; position:absolute;" class = "external" href="tel:<?php echo $user_info['phone']; ?>">
            <img style="width: 40px; height:40px;" src="<?php echo HOST?>../m/assets/images/u/phone.png" alt="">
        </a>
        <?php } ?>
        
        <form method="post" class="ajax-submit">
          <div class="list-block">
            <ul>
              <li>
                <div class="item-content">
                  <div class="item-inner"> 
                    <div class="item-title label">号码</div>
                    <div class="item-input">
                      <input type="text" id="phone" name="phone" value="" placeholder="填写您的号码"/>
                    </div>
                  </div>
                </div>
              </li>
            </ul>
            <div class="list-block" style="margin:20px 15px;">
             <ul>
                <li> 
                    <input type="hidden" name="name" id="name" value="<?php echo $user_info['name']; ?>">
                    <input type="hidden" name="qrcode" id="qrcode" value="<?php echo $user_info['qrcode']; ?>">
                    <input type="button" value="下载" id="ajax-submit" class="button button-big button-fill"/>
                </li>
             </ul>
            </div>
          </div>
        </form>

        <div id="showqrcode" style="text-align:center">
            <div class="col-25" style="color: #333;">
                <span style="width:42px; height: 42px;" class="preloader preloader-black preloader-big"></span>
                <br><br>正在生成二维码
            </div>
        </div>
    </div>
    <div style="position: absolute; bottom: 20px; color: #fff; text-align: center; width: 100%;">
        &copy;2015 安徽嘻哈网络技术有限公司
    </div>
    <script src="<?php echo HOST; ?>../m/assets/js/framework7.min.js"></script>

    <script>
        var myApp = new Framework7();
        var $$ = Framework7.$;
        var imghtml = '<img style="height: 185px; width: 185px;" src="<?php echo $imgurl; ?>" ><br><span style="color:#999">扫描二维码下载APP</span>';
        $$('#showqrcode').html(imghtml);
        // 提交
        $$('#ajax-submit').on('click', function(e) {
            var phone = $$('#phone').val();
            var name = $$('#name').val();
            var qrcode = $$('#qrcode').val();
            if(phone.trim() == '') {
                myApp.alert('请填写手机号', '警告！');
                return false;
            }
            if(!(/^1(3|4|5|7|8)\d{9}$/.test(phone))) {
                myApp.alert('请填写正确的手机号码', '警告！');
                return false;
            }
            $$.ajax({
                type: 'post',
                url: "<?php echo M_HOST; ?>u/app/ajax",
                data: {'phone': phone, 'name': name, 'qrcode': qrcode},
                dataType:"json",
                async: false,
                beforeSend: function() {
                    myApp.showPreloader('获取下载链接中...');
                },
                success: function(data) {
                    myApp.hidePreloader();
                    if(data.code == 400) {
                        myApp.alert(data.msg,'通知');
                    }
                    myApp.alert(data.msg,'通知', function() {
                        location.href=data.data.url;
                    });

                },
                error: function() {
                    myApp.hidePreloader();
                    location.href="http://m.xihaxueche.com:8001/html_h5/index.html";
                }

            })
        });
    </script>
</body>
</html>