<?php
namespace app\index\controller;
use think\Controller;
use think\Request;
use think\Db;
use app\index\model\Coach as CoachModel;
use app\index\model\Article as ArticleModel;

class Index extends Controller
{   
    public function _initialize() {
        $this->assign('title', '嘻哈学车-官网');
    }
    // 首页
    public function index() {
		$coach = new CoachModel();
		$coach_list = $coach->getRandomCoachList();
		$this->assign('coach_list', $coach_list);
        return $this->fetch('index/index');
    }
	
	// 教练端
	public function coach() {
		$this->assign('title', '嘻哈学车|教练端');
        return $this->fetch('index/products_coach');
	}
	
	// 学员端
	public function student() {
		$this->assign('title', '嘻哈学车|学员端');
        return $this->fetch('index/products_students');
	}

    // 关于我们
    public function aboutus() {
        $this->assign('title', '嘻哈学车|关于我们');
        return $this->fetch('index/aboutus');
    }

    // 加入我们
    public function join() {
        $this->assign('title', '嘻哈学车|加入我们');
        return $this->fetch('index/join');
    }

    // 常见问题
    public function help() {
        $this->assign('title', '嘻哈学车|常见问题');
        return $this->fetch('index/help');
    }
	
	// 新闻中心-嘻哈动态
    public function news($page=1) {
        $this->assign('title', '嘻哈学车|新闻中心');
		$article = new ArticleModel();
		$article_list = $article->getArticleList(19, $page, 6);
		$article_page = ArticleModel::getArticlePage(19, 6);
		$this->assign('article_list', $article_list);
		$this->assign('article_page', $article_page);
		$this->assign('page', $page);
        return $this->fetch('index/news');
    }
	
	// 新闻中心-行业新闻
    public function industry($page=1) {
    	if(!is_numeric($page)) {
    		$page = 1;
    	}
        $this->assign('title', '嘻哈学车|行业新闻');
		$article = new ArticleModel();
		$article_list = $article->getArticleList(20, $page, 6);
		$article_page = ArticleModel::getArticlePage(20, 6);
		$this->assign('article_list', $article_list);
		$this->assign('article_page', $article_page);
		$this->assign('page', $page);
        return $this->fetch('index/industry');
    }
	
	// 新闻详情
	public function detail($id=1) {
		if(!is_numeric($id)) {
			$id = 0;
		}
        $article_detail = ArticleModel::getArticleDetail($id);
        $this->assign('title', '嘻哈学车-'.$article_detail['title']);
		$this->assign('article_detail', $article_detail);
		return $this->fetch('index/detail');
	}

    // 电子教练
    public function ecoach() {
        $this->assign('title', '嘻哈学车|电子教练');
        return $this->fetch('index/products_ecoach');
    }

    // 电子教练详情
    public function ecoachdetail() {
       $this->assign('title', '嘻哈学车|电子教练');
        return $this->fetch('index/products_ecoachdetail'); 
    }


    // 喵咪鼠标
    public function mouse() {
        $this->assign('title', '嘻哈学车|喵咪鼠标');
        return $this->fetch('index/products_mouse');
    }

}
