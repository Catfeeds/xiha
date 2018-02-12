(function($, doc) {
    // 轮播图
     var mySwiper = new Swiper('.swiper-container',{
       loop: true,
       autoplay: 3000,
       pagination: '.swiper-pagination',
     });
    
    var t = [];
    t['C11'] = '小车科目一';
    t['C14'] = '小车科目四';
    t['A21'] = '货车科目一';
    t['A24'] = '货车科目四';
    t['A11'] = '客车科目一';
    t['A14'] = '客车科目四';
    t['D1'] = '摩托车科目一';
    t['D4'] = '摩托车科目四';

    var k = ctype+stype;
    if(os == 'web') {
	    doc.getElementById('index_title').innerHTML = !t[k] ? '驾考题库' : t[k];
    }
    	
    //练习
    $('#practise').on('tap', '.mui-table-view-cell', function(e) {
        var data_id = this.getAttribute('data-id');
        switch(data_id) {
            case "1":
            case "2":
            case "4":
                location.href = root_path+"exam/exercise-"+sid+"-"+ctype+"-"+stype+"-"+data_id+"-1"+"-"+os+".html";
                break;
            case "3":
                location.href = root_path+"exam/chapter-"+sid+"-"+ctype+"-"+stype+"-"+data_id+"-"+os+".html";
                break;
            default:
                location.href = root_path+"exam/index-"+sid+"-"+os+".html";
                break;
        }
    });
    //错题和收藏
    $('#practise-album').on('tap', '.mui-table-view-cell', function(e) {
        var data_id = this.getAttribute('data-id');
        switch(data_id) {
            case "5":
                location.href = root_path+"exam/myerr-"+sid+"-"+ctype+"-"+stype+"-"+data_id+"-"+os+".html";
                break;
            case "7":
                location.href = root_path+"exam/exercise-"+sid+"-"+ctype+"-"+stype+"-"+data_id+"-1"+"-"+os+".html";
                break;
            default:
                location.href = root_path+"exam/index-"+sid+"-"+os+".html";
                break;
        }
    });
    
})(mui,document);