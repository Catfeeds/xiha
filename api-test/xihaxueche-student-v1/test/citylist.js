'use strict';

const envs = require('envs');
const prefix = envs('prefix');
const assert = require('assert');

var chakram = require('chakram'),
    expect = chakram.expect;

describe('/citylist', function () {
    var api = prefix + '/citylist';

    before(function () {
    });

    it('should return 200 status code upon GET request', function () {
        // this.skip();
        var response = chakram.get(api);
        expect(response).to.have.status(200);
        return chakram.wait();
    });

    it('should return 405 status code upon POST request', function () {
        // this.skip();
        var response = chakram.post(api);
        expect(response).to.have.status(405);
        return chakram.wait();
    });

    it('should match json schema', function () {
        // this.skip();
        var response = chakram.get(api);
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
                    "title": "城市列表",
                    "type": "array",
                    "items": {
                        "type": "object",
                        "properties": {
                            "cityid": {
                                "title": "城市id",
                                "type": "number"
                            },
                            "city": {
                                "title": "城市名称",
                                "type": "string"
                            },
                            "fatherid": {
                                "title": "城市的上级id",
                                "type": "number"
                            },
                            "leter": {
                                "title": "城市拼音的单字母简拼",
                                "type": "string"
                            },
                            "spelling": {
                                "title": "城市名称的全拼",
                                "type": "string"
                            },
                            "acronym": {
                                "title": "城市名称的简拼",
                                "type": "string"
                            },
                            "is_hot": {
                                "title": "是热门城市吗",
                                "type": "number",
                                "enum": [0,1]
                            }
                        },
                        "required": ["cityid", "city", "fatherid", "leter", "spelling", "acronym", "is_hot"]
                    }
                }
            },
            "required": ["code", "msg", "data"]
        });
        return chakram.wait();
    });
});
