<?php  
    namespace app\index\model;
    user think\Model;
    user think\Db;

    class Admin extends Model {

        protected $table = "cs_admin";

        protected function initialize() {
            parent::initialize();
        }

        public function getAdminList() {
            $data = Db::name('admin')->select();
            return $data;
        }
    } 
?>