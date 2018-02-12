'use strict';

/**
 * 提交预约计时订单
 */

const envs = require('envs');
const prefix = envs('prefix');
const assert = require('assert');
const qs = require('qs');
var chakram = require('chakram'),
    expect = chakram.expect;

describe('/student/order/appoint/submit', function () {
    const api = prefix + '/student/order/appoint/submit';

    //  测试是否为正常的接口
    describe('should be an callable api', function () {
        it('should return status 200 in headers', function () {
            var response = chakram.get(api);
            expect(response).to.have.status(200);
            return chakram.wait();
        });

        it('should return correct JSON response', function () {
            var response = chakram.get(api);
            expect(response).to.have.header("content-type", "application/json");
            expect(response).to.have.schema({
                "type": "object",
                "properties": {  //此关键字确定子实例如何验证对象，并且不直接验证直接实例本身。
                    "code": {
                        "title": "状态码",
                        "type": "number"
                    },
                    "msg": {
                        "title": "提示信息",
                        "type": "string"
                    },
                    "data": {
                        "title": "数据对象",
                        "type": "object",
                    }
                },
                "required": ["code", "msg", "data"]
            });
            return chakram.wait();
        });

        it('should not return 404 code in JSON response', function () {
            var response = chakram.get(api);
            expect(response).to.not.have.json("code", 404);
            return chakram.wait();
        });
    });


    describe('business here', function () {

        var loginApi = prefix + '/student/ucenter/login';
        var profileApi = prefix + '/student/ucenter/profile';
        var coachApi = prefix + '/student/coach/detail';
        var loginArgs = {phone:'18656999023',pass:'123456'};
        var token = "";
        var coach_id = 11333;
        var id = 11333;
        var lng = 117.231221;
        var lat = 31.323333;
        var device = 3;
        var time_configs = JSON.stringify([{"id":1, "is_coach_set":2}, {"id":2, "is_coach_set":2}]);
        var date_obj = new Date();
        var date = date_obj.getFullYear() + "-" + (date_obj.getMonth()+1) + "-" + (date_obj.getDate()+1);
        var money = '3100';
        var phone = '';
        var real_name = '';
        var identity_id = '';
        var coach_name = '';
        var coach_phone = '';

        it('should match correct json schema', function () {
            return chakram.post(loginApi, loginArgs)
            .then(function(tokenRes){
                token = tokenRes.body.data.token;
                return chakram.get(profileApi + '?&token=' + token);
            })
            .then(function(profileRes){
                phone = profileRes.body.data.phone;
                real_name = profileRes.body.data.real_name;
                identity_id = profileRes.body.data.identity_id;
                return chakram.get(coachApi+'?&id='+id+'&lng='+lng+'&lat='+lat+'&device='+device);
            })
            .then(function(coachRes){
                coach_name = coachRes.body.data.detail.coach_name;
                coach_phone = coachRes.body.data.detail.coach_phone;
                var response = chakram.get(api + '?&token=' + token
                    + '&coach_id=' + coach_id
                    + '&time_configs=' + time_configs
                    + '&date=' + date
                    + '&money=' + money
                    + '&phone=' + phone
                    + '&real_name=' + real_name
                    + '&identity_id=' + identity_id
                    + '&coach_name=' + coach_name
                    + '&coach_phone=' + coach_phone);
                return response.then(function (obj) {
                    expect(obj).to.have.schema({
                        "type": "object",
                        "properties": {
                            "code": {
                                "title": "状态码",
                                "type": "number"
                            },
                            "msg": {
                                "title": "提示信息",
                                "type": "string"
                            },
                            "data": {
                                "title": "数据对象",
                                "type": "object",
                                "properties":{
                                    "order_info":{
                                        "type":"object",
                                        "properties":{
                                            "order_id":{
                                                "type":"integer"
                                            },
                                            "order_no":{
                                                "type":"string"
                                            },
                                            "user_id":{
                                                "type":"integer"
                                            },
                                            "user_name":{
                                                "type":"string"
                                            },
                                            "coach_name":{
                                                "type":"string"
                                            },
                                            "license_name":{
                                                "type":"string"
                                            },
                                            "lesson_name":{
                                                "type":"string"
                                            },
                                            "money":{
                                                "type":"string"
                                            },
                                            "transaction_no":{
                                                "type":"string"
                                            },
                                            "pay_time":{
                                                "type":"string"
                                            },
                                            "addtime":{
                                                "type":"integer"
                                            },
                                            "pay_type":{
                                                "type":"integer"
                                            },
                                            "order_status":{
                                                "type":"integer"
                                            },
                                            "addtime_format":{
                                                "type":"string"
                                            }
                                        },
                                        "required":["order_id","order_no","user_id","user_name","coach_id","coach_name","license_name","lesson_name","school_name","money","transaction_no","pay_time","addtime","pay_type","order_status","addtime_format"]
                                    }
                                }
                            }
                        },
                        "required": ["code", "msg", "data"]
                    });
                })
            })
        });
    });
});
