'use strict';

/**
 * 获取科目列表
 */

const envs = require('envs');
const prefix = envs('prefix');
const assert = require('assert');
const qs = require('qs');

var chakram = require('chakram'),
    expect = chakram.expect;


describe('/student/ucenter/lessonItems', function () {
    const api = prefix + '/student/ucenter/lessonItems';

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


    	it('should match correct json schema', function () {
            var response = chakram.get(api);
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
                            "properties": {
                                "list":{
                                    "title":"列表",
                                    "type":"array",
                                    "items": {
                                        "type":"object",
                                            "properties":{
                                                "lesson_id":{
                                                "title":"科目id",
                                                "type":"number"
                                            },
                                            "lesson_name":{
                                                "title":"科目名称",
                                                "type":"string"
                                            }
                                        },
                                        "required":["lesson_id","lesson_name"]
                                    }
                                }
                            }
                        }
                    },
                    "required": ["code", "msg", "data"]
                });
            });
        });
    });
});