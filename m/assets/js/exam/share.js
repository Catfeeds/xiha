(function($, doc) {

    var muiback = doc.getElementById('muiback');
    var exercise_again = doc.getElementById('exercise_again');
    var weixin = doc.getElementById('weixin');
    var qq = doc.getElementById('qq');

    var shareimg = root_path + 'assets/images/share/erweimaimg.png';
    var share_result = root_path + 'exam/shareresult-' + sid + "-" + ctype + "-" + stype + "-" + score + "-" + os + ".html";
    var share_json = { title: '嘻哈学车', content: '快来嘻哈学车和我一起pk吧', imgurl: shareimg, url: share_result };

    // muiback.addEventListener('tap', function(e) {
    //     location.href = root_path + 'exam/default-' + sid + "-" + ctype + "-" + stype + "-" + os + ".html";
    // })

    exercise_again.addEventListener('tap', function(e) {
        location.href = root_path + 'exam/default-' + sid + "-" + ctype + "-" + stype + "-" + os + ".html";
    })

    weixin.addEventListener('tap', function(e) {
        shareWeixin(share_json);
    })

    qq.addEventListener('tap', function(e) {
        shareQQ(share_json);
    })

})(mui, document);


function shareWeixin(share_json) {
    window.webkit.messageHandlers.shareToWeixin.postMessage(share_json);
}

function shareQQ(share_json) {
    window.webkit.messageHandlers.shareToQQ.postMessage(share_json);
}