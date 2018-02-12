CREATE TABLE `xihaxueche`.`cs_car_category` ( `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT , `name` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '车分类名' , `point_text_url` VARCHAR(200) NOT NULL DEFAULT '' COMMENT '车的打点图' , `addtime` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '添加时间' , PRIMARY KEY (`id`)) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_general_ci COMMENT = '车的种类表';

ALTER TABLE `cs_cars` ADD `car_cate_id` INT(11) UNSIGNED NOT NULL COMMENT '跟car_category车种表关联的id' AFTER `car_type`, ADD INDEX (`car_cate_id`);

ALTER TABLE `cs_car_category` ADD `school_id` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '驾校id' AFTER `point_text_url`;
