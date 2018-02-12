ALTER TABLE `cs_school_shifts` ADD `deleted` INT(2) NOT NULL DEFAULT '1' COMMENT '删除班制 1 正常 2 已删除' AFTER `sh_description_1`;
