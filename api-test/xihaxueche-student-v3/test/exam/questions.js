'use strict';

const envs = require('envs');
const prefix = envs('prefix');
const assert = require('assert');
const qs = require('qs');

var chakram = require('chakram'),
    expect = chakram.expect;

describe('/student/exam/questions', function () {

    const api = prefix + '/student/exam/questions';

    describe('should be an callable api', function () {
        it('should return status 200 in headers', function () {
            // this.skip();
            var response = chakram.get(api);
            expect(response).to.have.status(200);
            return chakram.wait();
        });

        it('should return correct JSON response', function () {
            // this.skip();
            var response = chakram.get(api);
            expect(response).to.have.header("content-type", "application/json");
            expect(response).to.have.schema({
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
                    }
                },
                "required": ["code", "msg", "data"]
            });
            return chakram.wait();
        });

        it('should not return 404 code in JSON response', function () {
            // this.skip();
            var response = chakram.get(api);
            expect(response).to.not.have.json("code", 404);
            return chakram.wait();
        });
    });

    describe('business here', function () {

        var questionIds;

        before('request questionIds first', function () {
            var args = {car_type: 'car', course: 'kemu1'};
            var response = chakram.get(prefix + '/student/exam/questionIds?' + qs.stringify(args));
            return response.then(function (obj) {
                questionIds = obj.body.data.list;
            });
        });

        it('quesitonIds should not be an empty array list', function () {
            // this.skip();
            expect(questionIds).to.be.an('array').that.is.not.empty;
        });

        it('should return 400 code without arg: question_ids', function () {
            // this.skip();
            var response = chakram.post(api);
            return response.then(function (obj) {
                expect(obj.body.code).to.be.equal(400);
            });
        });

        it('should match correct json schema', function () {
            // this.skip();
            var args = {question_ids: questionIds.join(',')};
            var response = chakram.post(api, args);
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
                                "count": {
                                    "type": "number"
                                },
                                "list": {
                                    "type": "array",
                                    "items": {
                                        "type": "object",
                                        "properties": {
                                            "question": {
                                                "title": "题干",
                                                "type": "string"
                                            },
                                            "answer": {
                                                "title": "答案",
                                                "type": "string"
                                            },
                                            "explain": {
                                                "title": "答案解析",
                                                "type": "string"
                                            },
                                            "option_type": {
                                                "title": "题目答案类型，单选，多选，判断",
                                                "type": "number"
                                            },
                                            "options": {
                                                "title": "答案选项",
                                                "type": "array",
                                                "items": {
                                                    "type": "object",
                                                    "properties": {
                                                        "tag": {
                                                            "title": "选项名称 A B C D",
                                                            "type": "string"
                                                        },
                                                        "content": {
                                                            "title": "选项内容",
                                                            "type": "string"
                                                        }
                                                    },
                                                    "required": ["tag", "content"]
                                                }
                                            },
                                            "media_content": {
                                                "title": "多媒体内容，图片或视频地址",
                                                "type": "string"
                                            },
                                            "wrong_rate": {
                                                "title": "错误率",
                                                "type": "number"
                                            }
                                        },
                                        "required": [
                                            "question",
                                            "answer",
                                            "explain",
                                            "option_type",
                                            "options",
                                            "media_content",
                                            "wrong_rate"
                                        ]
                                    }
                                }
                            },
                            "required": ["count", "list"]
                        }
                    },
                    "required": ["code", "msg", "data"]
                });
            });
        });

    });

});

