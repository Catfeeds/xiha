ALTER TABLE `cs_exam_history` CHANGE `identity_no` `identity_id` VARCHAR(28) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '身份证号码';

ALTER TABLE `cs_exam_history`
  DROP `year`,
  DROP `month`,
  DROP `day`;

ALTER TABLE `cs_coach` ADD `is_first` INT NOT NULL DEFAULT '0' COMMENT '是否第一次登陆' AFTER `user_id`;
