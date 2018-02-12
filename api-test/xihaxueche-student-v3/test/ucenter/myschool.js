'use strict';

/**
 * 我的驾校
 */

const envs = require('envs');
const prefix = envs('prefix');
const assert = require('assert');
const qs = require('qs');

var chakram = require('chakram'),
    expect = chakram.expect;

describe('/student/ucenter/my_school', function () {
    const api = prefix + '/student/ucenter/my_school';

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
    	// 1.登录获取token 2.根据token获取学车报告
        var loginApi = prefix + '/student/ucenter/login';
        var token = "";
        before('request token first', function () {
            var loginArgs = {phone:'18656999023',pass:'123456'};
            var response = chakram.post(loginApi, loginArgs);
            return response.then(function (obj) {
               token = obj.body.data.token;
            });
        });

        it('login successful access a token should not be an empty string', function () {
            expect(token).to.be.an('string').that.is.not.empty;
        });

    	it('should match correct json schema', function () {
            var response = chakram.get(api + '?&token=' + token);
            return response.then(function (obj) {
            	if(!obj.body.data) {
            		//1.未报名
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
	                            "properties": {}
	                        }
	                    },
	                    "required": ["code", "msg", "data"]
	                });
            	}else if(obj.body.data.school_info && obj.body.data.shifts_info) {
            		//2.已报名
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
	                            "properties": {
	                            	"school_info":{
	                            		"type": "object"
	                            	},
	                            	"shifts_info":{
	                            		"type": "object"
	                            	},
	                            	"coach_info":{
	                            		"type": "object"
	                            	},
	                            	"comment_info":{
	                            		"type": "object"
	                            	}
	                            },
	                            "required":["school_info","shifts_info","coach_info","comment_info"]
	                        }
	                    },
	                    "required": ["code", "msg", "data"]
	                });
            	}else if(obj.body.data.school_list) {
            		//3.报名多个驾校
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
	                            "properties": {
	                            	"school_list":{
	                            		"type":"object"
	                            	}
	                            },
	                            "required":["school_list"]
	                        }
	                    },
	                    "required": ["code", "msg", "data"]
	                });
            	}
            });
        });
    });
});