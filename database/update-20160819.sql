ALTER TABLE `cs_car_category` ADD `brand` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '车品牌' AFTER `name`, ADD `subtype` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '车子型号' AFTER `brand`;

ALTER TABLE `cs_car_category` ADD UNIQUE( `brand`, `subtype`, `school_id`);
