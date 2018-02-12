
    </div> <!-- #app ends -->

    <div style="display:none;">
      <!-- 引入javascript库 -->
      <script src="<?php echo base_url('static/libs/vue-2.2.6/dist/vue.min.js'); ?>"></script>
      <script src="<?php echo base_url('static/libs/element/index.js'); ?>"></script>
      <script src="<?php echo base_url('static/libs/axios-0.16.1/dist/axios.min.js'); ?>"></script>
      <script src="<?php echo base_url('static/libs/lodash-4.17.4/dist/lodash.min.js'); ?>"></script>
      <script src="<?php echo base_url('static/libs/jquery-3.2.1/dist/jquery.min.js'); ?>"></script>
      <script src="<?php echo base_url('static/libs/layer/layer.js'); ?>"></script>
      <script src="<?php echo base_url('static/themes/default/assets/js/config.js'); ?>"></script>
      <script>
        Vue.config.devtools = true;
      </script>
<?php if(isset($js_list)): ?>

      <!-- 引入本页面js -->
  <?php foreach($js_list as $js): ?>
    <script src="<?php echo base_url($theme.$js.'.js').'?_t='.time(); ?>"></script>
  <?php endforeach ?>
<?php endif; ?>
    </div>
  </body>
</html>
