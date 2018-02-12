<div id="app" v-cloak>
    <div class="iframe-content" style="margin-top: 30px;">
        <template v-if="menuList.length > 0">
            <el-collapse v-model="activeNames">
                    <el-collapse-item v-for="(item, index) in menuList" :title="item.m_cname" :name="index">
                        <el-row v-for="(data, i) in item.children" style="margin: 15px 0px;">
                            <el-col :span="6" style="text-align:right;">{{ data.m_cname }}</el-col>
                            <el-col :span="18" style="text-align:right;">
                                <el-tag type="primary" style="margin-right: 10px;" v-for="val in data.menu_list">{{ val.m_cname }}</el-tag>
                            </el-col>
                        </el-row>
                    </el-collapse-item>
                </template>
            </el-collapse>
        </template>
        <template v-else>
            <el-alert v-if="seen"
                title="还未设置权限，请编辑权限"
                type="warning"
                :closable="false">
            </el-alert>
        </template>

    </div>

</div>

<script>
    var vm = new Vue({
        el: "#app",
        data: {
            seen: false,
            activeNames: [],
            menuList: [],
            base_url: "<?php echo base_url('admin/showrolemenuajax'); ?>",
            loading: false,
            roleId: "<?php echo $role_id; ?>",
            menuLevel: [],
        },
        created: function() {
            this.listAjax();
        },
        methods: {
            messageNotice: function(type, msg) {
				this.$message({
					type: type,
					message: msg
				});
			},
            listAjax: function() {
                $.ajax({
                    type: 'post',
                    url: this.base_url,
                    data: {'id': this.roleId},
                    dataType: 'json',
                    async: true,
                    success:function(data) {
                        if(data.code == 200) {
                            vm.menuList = data.data.list;
                            let active_arr = [];
                            if(data.data.list.length > 0) {
                                for(var n=0; n < data.data.list.length; n++) {
                                    active_arr.push(n);
                                }
                                vm.activeNames = active_arr;
                            } else {
                                vm.seen = true;
                            }
                        } else {
                            vm.messageNotice('warning', data.msg);
                        }
                    },
                    error: function() {
                        vm.messageNotice('warning', '网络错误，请检查网络');
                    }
                });
            },

        }
    });

</script>
