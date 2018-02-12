ALTER TABLE `cs_app_version` ADD `app_update_log` VARCHAR(1000) NOT NULL DEFAULT '' COMMENT '分号分隔每一条更新更新日志，即都更新了哪些内容' AFTER `version`, ADD `app_download_url` VARCHAR(500) NOT NULL DEFAULT '' COMMENT 'app下载地址' AFTER `app_update_log`;

ALTER TABLE `cs_app_version` CHANGE `version` `version` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '版本号，版本号必须是增加的';

ALTER TABLE `cs_app_version` CHANGE `app_client` `app_client` INT(11) NOT NULL DEFAULT '0' COMMENT 'app客户端id标识 1 学员端 2 教练端 3 校长端';
