<?php 
namespace Common\Model;
use Think\Model;
//这种模型类基本是直接操作数据库的，所以在命名规范上和数据表是对应的
class IndexModel extends Model {	

	public function __construct() {
		parent::__construct();
		echo "\common";
	}

}