ALTER TABLE `cs_users_info` ADD `lesson_id` INT NOT NULL DEFAULT '0' COMMENT '学车阶段：未报名，科目1/2/3/4,毕业。' AFTER `school_id`;
ALTER TABLE `cs_city` ADD `is_hot` INT NOT NULL DEFAULT '2' COMMENT '是否为热门城市 1 热门 2 非热门' AFTER `city`;
