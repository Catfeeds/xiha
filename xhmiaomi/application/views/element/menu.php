<style>
    .el-tabs__content {height: 90%;}
    .el-tabs__header {margin: 0px;}
</style>
<div id="app" v-cloak>
    <div class="gx-nav-top" style="">
        <el-menu class="el-menu-demo" mode="horizontal">
            <el-menu-item @click="addTab('首页', home_url)" style="width: 200px; color: #fff; background: #3595d1; margin-right: 20px;">喵咪鼠标管理平台</el-menu-item>

            <!--顶部导航-->
             <el-submenu :index="item.index" v-for="item in top_menu_list">
                <template slot="title"><i class="iconfont" :class="item.iconclass" style="padding-right: 10px;"></i> {{ item.mname }}</template>
                <el-menu-item v-for="val in item.secondmenu" :index="val.index" @click="addTab(val.mname, val.url)">{{ val.mname }}</el-menu-item>
            </el-submenu> 

            <el-menu-item :index="admin_info.index" style="float:right" v-popover:popover2>
                <a href="javascript:;">{{ admin_info.name }}（{{ admin_info.content }}）</a>

                <!--弹出框-->
                <el-popover
                    popper-class="xiha-menu-admininfo"
                    ref="popover2"
                    placement="bottom-start"
                    title=""
                    width="480"
                    trigger="hover"
                    offset=100
                    transition="fade-in-linear"
                    >
                    <div class="bottom clearfix" style="text-align:center; border-top: 1px solid #d1dbe5; height: 50px; line-height: 50px;">
                        <el-button @click="handleLogout" type="text" class="button">退出当前账户</el-button>
                    </div>
                </el-popover>
            </el-menu-item>
            <!-- <el-menu-item index="7" style="float:right">
                <el-badge :value="200" :max="99" class="item">
                    <i class="el-icon-message"></i>消息
                </el-badge>
            </el-menu-item> -->
        </el-menu>
    </div>
    <div class="gx-nav-left">
        <el-row class="tac">
            <el-col :span="8" style="width:200px;">
                <el-menu default-active="2" class="el-menu-vertical-demo" @open="handleOpen" @close="handleClose" unique-opened>
                    <el-submenu :index="item.index" v-for="item in menu_list">
                        <template slot="title"><i class="iconfont" :class="item.iconclass" style="padding-right: 10px;"></i> {{ item.mname }}</template>
                        <el-menu-item-group>
                            <el-menu-item v-for="val in item.secondmenu" :index="val.mname" @click="addTab(val.mname, val.url)">{{ val.mname }}</el-menu-item>
                        </el-menu-item-group>
                        </el-submenu>
                    </el-submenu>

                </el-menu>
            </el-col>
        </el-row>
    </div>
    <div id="iframe_box" class="gx-content">
        <el-tabs style="height:100%; left: 0px; position: absolute; top: 5px; width: 100%; " type="card" v-model="editableTabsValue2"  closable @tab-remove="removeTab">
            <el-tab-pane
                v-for="(item, index) in editableTabs2"
                :key="item.name"
                :label="item.title"
                :name="item.name"
            >
            <div class="show_iframe">
                <iframe scrolling="yes" frameborder="0" :src="item.content"></iframe>
            </div>

            </el-tab-pane>
        </el-tabs>

    </div>

    <!--<div class="show_iframe" style="margin-top: 100px;">
        <div style="display:none" class="loading"></div>
        <iframe scrolling="yes" frameborder="0" :src="iframe_url"></iframe>
    </div>-->
</div>
<script>
    "use strict";
    var role_id = "<?php echo $role_id;?>";
    var vm = new Vue({
        el: '#app',
        data: {
            editableTabsValue2: '1',
            editableTabs2: [
                {
                    title: '首页',
                    name: '1',
                    content: "<?php echo base_url('admin/home'); ?>"
                },
            ],
            tabIndex: 1,
            logout_url: "<?php echo base_url('admin/logout'); ?>",
            home_url: "<?php echo base_url('admin/home'); ?>",
            admin_info: {
                index: '6',
                name: "<?php echo $admin_name; ?>",
                content: "<?php echo $content; ?>",
            },
            menu_list: [],
            top_menu_list: [],
            iframe_url: "<?php echo base_url('admin/home'); ?>",
            menu_url: "<?php echo base_url('admin/showMenuAjax'); ?>",
            top_menu_url: "<?php echo base_url('admin/topMenuAjax'); ?>",
        },
        created: function() {
            this.showMenuAjax();
            this.topMenuAjax();
		},
        methods: {
            showMenuAjax: function() {
                $.ajax({
                    url: this.menu_url,
                    dataType: "json",
                    async: true,
                    success: function (res) {
                        if ( res.code == 200) {
                            vm.menu_list = res.data;
                        }
                    },
                    error: function (e) {
                        vm.messageNotice('error', '加载出错！');
                    } 
                });
            },
            topMenuAjax: function() {
                $.ajax({
                    url: this.top_menu_url,
                    dataType: "json",
                    async: true,
                    success: function (res) {
                        if ( res.code == 200) {
                            vm.top_menu_list = res.data;
                        }
                    },
                    error: function (e) {
                        vm.messageNotice('error', '加载出错！');
                    } 
                });
            },
            handleSelect: function(key, keyPath) {
                // console.log(key, keyPath);
            },
            handleOpen: function(key, keyPath) {
                // console.log(key, keyPath);
            },
            handleClose: function(key, keyPath) {
                // console.log(key, keyPath);
            },
            showiframe: function(url) {
                this.iframe_url = url;
            },
            handleLogout: function() {
                $.ajax({
                    type: 'post',
                    url: this.logout_url,
                    dataType: 'json',
                    success: function(data) {
                        if(data.code == 200) {
                            vm.messageNotice('success', data.msg);
                            location.reload();
                        } else {
                            vm.messageNotice('warning', data.msg);
                        }
                    },
                    error: function() {
                        vm.messageNotice('warning', '网络错误，请检查网络');
                    }
                })
            },
            messageNotice: function(type, msg) {
                this.$message({
                    type: type,
                    message: msg
                });
            },
            notification: function(data) {
				this.$notify({
					title: '成功',
					message: data,
					type: 'success',
                    offset: 0
				});
			},
            addTab: function(mname, url) {
                let tabs = this.editableTabs2;
                for(var index in tabs) {
                    if(tabs[index].title == mname) {
                        this.editableTabsValue2 = tabs[index].name;
                        return false;
                    } else {
                        continue;
                    }
                }
                let newTabName = ++this.tabIndex + '';
                this.editableTabs2.push({
                    title: mname,
                    name: newTabName,
                    content: url,
                });
                this.editableTabsValue2 = newTabName;
            },
            removeTab: function(targetName) {
                let tabs = this.editableTabs2;
                let activeName = this.editableTabsValue2;
                if(activeName == 1) {
                    return false;
                }
                if (activeName === targetName) {
                    for(var index in tabs) {
                        if(tabs[index].name === targetName) {
                            let nextTab = tabs[index + 1] || tabs[index - 1];
                            if (nextTab) {
                                activeName = nextTab.name;
                            }
                        }
                    }
                }
                this.editableTabsValue2 = activeName;
                this.editableTabs2 = tabs.filter(function(tab){ return tab.name !== targetName});
            },

        }
    });
</script>
