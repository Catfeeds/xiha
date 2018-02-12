// 嘻哈券领取
$(function() {
    'use strict';
    var params;
    var _params;
    var xmlhttp;
    var data;

    // 1、获取
    $(document).on('pageInit', '#getcoupon', function(e, id, page) {
        var $content = $(page).find('.content');
        var phoneFormat = /^1(3[0-9]|4[579]|5[0-35-9]|7[0135678]|8[0-9])\d{8}$/;
        // 提交信息
        $('#coupon_btn').on('touchend', function(e) {
            e.preventDefault();
            $(":focus").blur(); // 避免弹出键盘输入法
            var _name = $('.name').val();
            var _phone = $('.phone').val();
            var _code = $('.code').val();
            var _coach_id = $('#coach_id').val();
            var _coupon_id = $('#coupon_id').val();
            var _province_id = $('#province_id').val();
            var _city_id = $('#city_id').val();
            var _area_id = $('#area_id').val();
            if (_name.trim() == '') {
                $.toast('请填写您的姓名');
                return false;
            }

            if (_phone.trim() == '') {
                $.toast('请填写您的手机号');
                return false;
            }

            if (!phoneFormat.test(_phone)) {
                $.toast('请输入正确的手机号');
                return false;
            }

            if (_code.trim() == '') {
                $.toast('请填写验证码');
                return false;
            }

            var _params = 'coach_id=' + _coach_id + '&coupon_id=' + _coupon_id + '&province_id=' + _province_id + '&city_id=' + _city_id + '&area_id=' + _area_id + '&name=' + _name + '&phone=' + _phone + '&code=' + _code;
            ajaxReturn('POST', api_url+'v1/coupon/exchange?'+_params, function() {
                if (xmlhttp.readyState == 4) {
                    if (xmlhttp.status == 200) {
                        data = $.parseJSON(xmlhttp.responseText);
                        if (data.code == 200) {
                            // 获取优惠券成功
                            $.toast('领取成功');
                            setTimeout(function() {
                                location.reload();
                            }, 5000);
                        } else {
                            $.toast(data.msg);
                        }
                    } else {
                        $.toast('网络错误，请检查网络');
                    }
                }
            });
        })

        // 获取验证码
        $('#getCode').on('touchstart', function(e) {
            e.preventDefault();
            var timestamp = Date.now() / 1000 | 0;
            var phone = $('.phone').val();
            if (phone.trim() == '') {
                $.toast('请填写您的手机号');
                return false;
            }

            if (!phoneFormat.test(phone)) {
                $.toast('请输入正确的手机号');
                return false;
            }
            // 点击获取后验证码的
            $('#getCode').hide();
            $('#resetCode').show();
            $('#code_second').html('59s');
            var second = 59;
            var timer = null;
            timer = setInterval(function() {
                second -= 1;
                if (second > 0) {
                    $('#code_second').html(second + 's');
                } else {
                    clearInterval(timer);
                    $('#getCode').show();
                    $('#resetCode').hide();
                }
            }, 1000);

            var params = 'phone=' + phone + '&timestamp=' + timestamp;
            ajaxReturn('GET', site_url + 'api/couponcode?' + params, function() {
                if (xmlhttp.readyState == 4) {
                    if (xmlhttp.status == 200) {
                        data = $.parseJSON(xmlhttp.responseText);
                        if (data.code == 200) {

                            // 获取验证码错误
                        } else {
                            $.toast(data.msg);
                        }
                    } else {
                        $.toast('网络错误，请检查网络');
                    }
                }
            });
        })

    });

    //ajax获取数据
    function ajaxReturn(method, url, _func) {
        xmlhttp = null;
        if (window.XMLHttpRequest) { // code for IE7, Firefox, Opera, etc.
            xmlhttp = new XMLHttpRequest();
        } else if (window.ActiveXObject) { // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        if (xmlhttp != null) {
            xmlhttp.onreadystatechange = _func;
            xmlhttp.open(method, url, false);
            xmlhttp.send(null);
        } else {
            alert("Your browser does not support XMLHTTP.");
        }
    }
    // $.init();
});
