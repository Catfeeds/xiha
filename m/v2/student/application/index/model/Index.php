<?php  
    namespace app\index\model;
    use think\Model;
    use think\Db;
	
    class Index extends Model {

//      protected $table = "cs_admin";
        //自定义初始化
        protected function initialize() {
            //需要调用`Model`的`initialize`方法
            parent::initialize();
            //TODO:自定义的初始化
        }

        public function getCitiesList() {
            $data = Db::name('city')->select();
            return $data;
        }


   } 
?>