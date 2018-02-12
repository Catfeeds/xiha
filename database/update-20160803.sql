ALTER TABLE `cs_cars` CHANGE `imgurl` `imgurl` VARCHAR(800) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '车辆图片';

ALTER TABLE `cs_coach_users_records` CHANGE `identity_img` `identity_img` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '身份证图片';
