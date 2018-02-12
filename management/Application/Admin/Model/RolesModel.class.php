<?php
namespace Admin\Model;
use Think\Model;
use Think\Model\RelationModel;
use Think\Page;
/**
 * 关联模型的建立
 *
 * @return void
 * @author wl
 **/
class RolesModel extends RelationModel{
       protected $tableName = 'roles';//以roles表为基准表
       protected $_link = array(
        'rolepermission'=>array(
          // 1)要关联的模型类名
            'mapping_type'      => self::HAS_ONE,
            'class_name'        => 'rolepermission',
            'mapping_name'      => 'rolepermission',
          // 2)外键 ，即 rolepermission中的字段
            'foreign_key'       => 'l_role_id',
          // 3)关联要查询的状态
            'mapping_fields'    => 'l_rolepress_incode,l_role_id,module_id'
            ),
        );

    

}

?>