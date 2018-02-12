'use strict';

/**
 * 获取教练评价
 */

const envs = require('envs');
const prefix = envs('prefix');
const assert = require('assert');
const qs = require('qs');
var chakram = require('chakram'),
    expect = chakram.expect;

describe('/student/coach/coachshiftslist', function () {
    const api = prefix + '/student/coach/coachshiftslist';

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

        // 1.登录获取token
        var token = "";
        var loginApi = prefix + '/student/ucenter/login';
        var loginArgs = {phone:'18656999023',pass:'123456'};
        var coachList = [];
        var coachApi = prefix + '/student/coach/index';

        it('should match correct json schema', function () {
            return chakram.post(loginApi, loginArgs) //返回tokenResponse
            .then(function(tokenResponse) {
                token = tokenResponse.body.data.token;
                return chakram.get(coachApi); //返回coachResponse
            })
            .then(function(coachResponse) {
                coachList = coachResponse.body.data.list.data;
                return coachList; //返回list
            })
            .then(function(list) {
                list.forEach(function (item) {
                    var coach_id = item.coach_id;
                    var response = chakram.get(api + '?&token=' + token + '&coach_id' + coach_id);
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
                                        "list":{
                                            "type":"array",
                                            'items':{
                                                "properties":{
                                                    "sh_id":{
                                                        "type":"integer"
                                                    }
                                                },
                                                "required":["sh_id"]
                                            }
                                        }
                                    },
                                    "required":["list"]
                                }
                            },
                             "required": ["code", "msg", "data"]
                        });
                    });
                });
            });
        });
    });
});