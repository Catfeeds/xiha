<?php
	namespace app\index\model;
	use think\Model;
	use think\Db;
	
	class Article extends Model {
		
		protected function initialize() {
			parent::initialize();
		}
		
		// 获取文章列表
		public function getArticleList($catid=2, $start=1, $limit=10) {
			$data = Db::table('xh_article')->field('id, title, message, has_attach, add_time')
											 ->where("category_id={$catid}")
											 ->order('sort DESC, add_time DESC, id DESC')
											 ->page("{$start},{$limit}")->select();
			if(!empty($data)) {
				foreach($data as $key => $value) {
					$data[$key]['add_format_time'] = date('Y-m-d', $value['add_time']);
					if($value['has_attach'] == 1) {
						$pattern = '/\[attach\]([0-9]+)\[\/attach]/';
						$data[$key]['message'] = preg_replace($pattern, '', $value['message']);
						$data[$key]['message'] = strip_tags($value['message']);
						$attach_arr = Article::getAttachList(Article::parse_attachs($value['message']));
						$data[$key]['article_thumb'] = empty($attach_arr) ? '' : $attach_arr[0];
					} else {
						$data[$key]['article_thumb'] = '';
					}
				}
			}
			return $data;
			
		}
		// 获取文章总数以及分页数
		public static function getArticlePage($catid=2, $limit=10) {
			$data = Db::table('xh_article')->field('count(1) as num')
											 ->where("category_id={$catid}")
											 ->find();
			$pageTotal = ceil($data['num'] / $limit);
			$data['pagetotal'] = $pageTotal;
			return $data;
		}
		
		// 获取文章详情
		public static function getArticleDetail($id) {
			$data = Db::table('xh_article')
				->field('title, uid, message, views, category_id, add_time')
				->where("id='{$id}'")
				->fetchSql(false)
				->find();
			if(empty($data)) {
				return false;
			}
			
			$pattern = '/\[attach\]([0-9]+)\[\/attach]/';
			$attach_arr = Article::getAttachList(Article::parse_attachs($data['message']));
			$replace_message = preg_replace($pattern, "%s", $data['message']);
			if(!empty($attach_arr)) {
				foreach($attach_arr as $key => $value) {
					$attach_arr[$key] = '<div style="width:100%; text-align:center;"><img style="padding:10px 0px; max-width:700px;" src="'.$value.'"></></div>';
				}
				$attach_unformat_arr = Article::parse_attachs($data['message'], false);
				$message = str_replace($attach_unformat_arr, $attach_arr, $data['message']);
			} else {
				$message = $data['message'];
			}
			
			$user_info = Db::table('xh_users')->field('user_name')->where("uid='{$data['uid']}'")->find();
			$data['user_name'] = $user_info['user_name'];
			$data['message'] = $message;
			$data['message'] = str_replace("\r", "<br/>", $data['message']);
			$data['message'] = str_replace("\n", "<br/>", $data['message']);
			$data['add_format_time'] = date('Y-m-d H:i:s', $data['add_time']);
			$category_id = $data['category_id'];
			//获取上一篇文章和下一篇文章id
			$prev_data = Db::table('xh_article')->field('max(id) as id, title')->where("id<'{$id}' AND category_id='{$category_id}'")->group('id DESC')->fetchSql(false)->find();
			$next_data = Db::table('xh_article')->field('min(id) as id, title')->where("id>'{$id}' AND category_id='{$category_id}'")->group('id DESC')->order('sort DESC, add_time DESC, id DESC')->fetchSql(false)->find();

			if(!empty($prev_data)) {
				$data['prev_id'] = $prev_data['id'];
				$data['prev_title'] = $prev_data['title'];
			} else {
				$data['prev_id'] = '';
				$data['prev_title'] = '';
			}
			if(!empty($next_data)) {
				$data['next_id'] = $next_data['id'];
				$data['next_title'] = $next_data['title'];
				
			} else {
				$data['next_id'] = '';
				$data['next_title'] = '';
			}
			return $data;
		}

		// 获取attach id
		public static function parse_attachs($str, $attach_type = true) {
			if($attach_type) {
				$pattern = '/\[attach\]([0-9]+)\[\/attach]/';
			} else {
				$pattern = '/(\[attach\][0-9]+\[\/attach])/';
			}
			if(preg_match_all($pattern, $str, $matches)) {
				return array_unique($matches[1]);
			} else {
				return [];
			}
		}

		// 根据attachID获取图片地址
		public static function getAttachList($attach_arr) {
			if(!is_array($attach_arr) || empty($attach_arr)) {
				return [];
			}
			$attach_str = implode(',', $attach_arr);
			$data = Db::table('xh_attach')->field('id, file_location, item_type, add_time')
											->where("id IN ({$attach_str}) AND is_image=1 AND item_type='article'")
											->order("find_in_set(id, '{$attach_str}')")
											->fetchSql(false)
											->select();
			$list = array();
			if(!empty($data)) {
				foreach($data as $key => $value) {
					$date_dir = gmdate('Ymd', $value['add_time']);
					$list[$key] = 'http://news.xihaxueche.com/uploads/'.$value['item_type'].'/'.$date_dir.'/'.$value['file_location'];
				}
			}
			return $list;
		}
	}	
?>