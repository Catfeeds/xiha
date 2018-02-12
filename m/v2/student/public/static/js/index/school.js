$(function() {
    'use strict';

    $(document).on('pageInit', '#schooldetail', function(e, id, page) {

        var $content = $(page).find('.content');
        $content.on('click', function() {
            //初始化
            MobLink.init({
                path: "demo/a" //对应客户端路径
            });
        });


    });


})