<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <title><?php echo $title; ?></title>

    <!-- 引入css库 -->
    <link href="<?php echo base_url('static/themes/default/assets/css/reset.css'); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url('static/libs/element/index.css'); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url('static/themes/default/assets/css/common.css'); ?>" rel="stylesheet" type="text/css" />
<?php if(isset($css_list)): ?>
<?php foreach($css_list as $css): ?>
    <link href="<?php echo base_url($theme.$css.'.css'); ?>" rel="stylesheet" type="text/css" />
<?php endforeach;?>
<?php endif; ?>
    <style type="text/css">
      [v-cloak] {display: none;}
    </style>


