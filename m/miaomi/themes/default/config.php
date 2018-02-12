<?php
return array(
    array('field' => 'indexFineShow', 'caption' => '首页精品是否展示', 'type' => 'select', 'info' => '商品id用逗号分开。', 'options' => '0:关闭,1:开启'),
    array('field' => 'indexFineIds', 'caption' => '首页精品推荐', 'type' => 'text', 'info' => '商品id用逗号分开。', 'pattern' => '(\d+,)*\d+'),
    array('field' => 'indexRecommend', 'caption' => '首页大幅力推', 'type' => 'text', 'info' => '格式(片地址::连接地址)', 'pattern' => '.+::.+'),
);
