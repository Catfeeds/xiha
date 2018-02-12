<div id="app" v-cloak>
    <div class="iframe-content" style="margin-top: 30px;">

        <el-collapse v-model="activeNames" @change="handleChange">

            <el-collapse-item v-for="(item, index) in menuList" :title="item.m_cname" :name="index">
                <el-row v-for="(data, i) in item.children" style="margin: 15px 0px;">
                    <el-col :span="4" style="text-align:right;">{{ data.m_cname }}</el-col>
                    
                    <el-col :span="14" style="text-align:right;">
                        <el-checkbox-group v-model="data.checkedMenu" @change="handleCheckedMenuChange($event, index, i)">
                            <el-checkbox v-for="val in data.menu_list" :label="val.moduleid" :key="val.moduleid">{{ val.m_cname }}</el-checkbox>
                        </el-checkbox-group>
                    </el-col>

                    <el-col :span="6" style="text-align:center;">
                        <el-checkbox style="margin-left: 20px;" :indeterminate="data.isIndeterminate" v-model="data.checkAll" @change="handleCheckAllChange($event, index, i)">全选</el-checkbox>
                    </el-col>
                </el-row>
            </el-collapse-item>

        </el-collapse>

        <div style="margin-top: 20px; float：right;">
            <el-button type="primary" @click="submitForm">编辑保存角色菜单</el-button>
        </div>
    </div>

</div>

<script>
    // const menuOptions = [
    //     {
    //         m_cname: '驾校管理',
    //         children: [
    //             {
    //                 m_cname: '驾校列表',
    //                 menu_list: [
    //                     {m_cname: '新增', moduleid: '1'},
    //                     {m_cname: '查看', moduleid: '2'},
    //                     {m_cname: '编辑', moduleid: '3'},
    //                     {m_cname: '删除', moduleid: '4'},
    //                 ],
    //                 checkAll: false,
    //                 checkedMenu: [],
    //                 isIndeterminate: false,
    //             },
    //             {
    //                 m_cname: '场地管理',
    //                 menu_list: [
    //                     {m_cname: '新增', moduleid: '1'},
    //                     {m_cname: '查看', moduleid: '2'},
    //                     {m_cname: '编辑', moduleid: '3'},
    //                     {m_cname: '删除', moduleid: '4'},
    //                 ],
    //                 checkAll: false,
    //                 checkedMenu: [],
    //                 isIndeterminate: false,
    //             },
    //         ]
    //     },
    //     {
    //         m_cname: '车辆管理',
    //         children: [
    //             {
    //                 m_cname: '驾校列表',
    //                 menu_list: [
    //                     {m_cname: '新增', moduleid: '1'},
    //                     {m_cname: '查看', moduleid: '2'},
    //                     {m_cname: '编辑', moduleid: '3'},
    //                     {m_cname: '删除', moduleid: '4'},
    //                 ],
    //                 checkAll: false,
    //                 checkedMenu: [],
    //                 isIndeterminate: false,
    //             },
    //             {
    //                 m_cname: '场地管理',
    //                 menu_list: [
    //                     {m_cname: '新增', moduleid: '1'},
    //                     {m_cname: '查看', moduleid: '2'},
    //                     {m_cname: '编辑', moduleid: '3'},
    //                     {m_cname: '删除', moduleid: '4'},
    //                 ],
    //                 checkAll: false,
    //                 checkedMenu: [],
    //                 isIndeterminate: false,
    //             },
    //         ]
    //     },
    // ];
    var vm = new Vue({
        el: "#app",
        data: {
            activeNames: [0, 1],
            menuList: [],
            base_url: "<?php echo base_url('admin/editrolemenuajax'); ?>",
            menu_url: "<?php echo base_url('admin/menuajax'); ?>",
            loading: false,
            menuFormList: [],
            menuChecked: false,
            roleId: "<?php echo $role_id; ?>",
            menuLevel: [],
        },
        created: function() {
            this.listAjax();
        },
        methods: {
            handleChange: function(val) {
                console.log(val)
            },
            messageNotice: function(type, msg) {
				this.$message({
					type: type,
					message: msg
				});
			},
            listAjax: function() {
                $.ajax({
                    type: 'post',
                    url: this.menu_url,
                    dataType: 'json',
                    success:function(data) {
                        if(data.code == 200) {
                            vm.menuList = data.data;
                            let active_arr = [];
                            for(var n=0; n < data.data.length; n++) {
                                active_arr.push(n);
                            }
                            vm.activeNames = active_arr;
                        } else {
                            vm.messageNotice('warning', data.msg);
                        }
                    },
                    error: function() {
                        vm.messageNotice('warning', '网络错误，请检查网络');
                    }
                });
            },
            editAjax: function() {
                this.menuFormList = [];
                this.menuList.forEach(function(val, key) {
                    val.children.forEach(function(v, k) {
                        if(val.children[k].checkedMenu.length > 0) {
                            vm.menuFormList.push(val.children[k].checkedMenu);
                        }
                    })
                }, this);
                $.ajax({
                    type: 'post',
                    url: this.base_url,
                    data: {'moduleid':this.menuFormList, 'roleid': this.roleId},
                    dataType: 'json',
                    success:function(data) {
                        if(data.code == 200) {
                            parent.layer.closeAll();
                            parent.vm.messageNotice('success', data.msg);
                            parent.vm.listAjax();
                        } else {
                            vm.messageNotice('warning', data.msg);
                        }
                    },
                    error: function() {
                        vm.messageNotice('warning', '网络错误，请检查网络');
                    }
                });
            },
            handleCheckAllChange: function(event, index, i) {
                let menu_list = this.menuList[index].children[i].menu_list;
                // let menulevel1_moduleid = this.menuList[index].moduleid;
                // let menulevel2_moduleid = this.menuList[index].children[i].moduleid;
                let menu_value_list = [];
                if(menu_list.length > 0) {
                    for(var key in menu_list) {
                        menu_value_list.push(menu_list[key].moduleid);
                    }
                }
                // if(event.target.checked) {
                //     menu_value_list.push(menulevel1_moduleid);
                //     menu_value_list.push(menulevel2_moduleid);
                // }
                this.menuList[index].children[i].checkedMenu = event.target.checked ? menu_value_list : [];
                this.menuList[index].children[i].isIndeterminate = false;
                this.menuFormList = [];
                this.menuList.forEach(function(val, key) {
                    val.children.forEach(function(v, k) {
                        if(val.children[k].checkedMenu.length > 0) {
                            vm.menuFormList.push(val.children[k].checkedMenu);
                        }
                    })
                }, this);
                if(this.menuFormList.length > 0) {
                    this.menuChecked = true;
                } else {
                    this.menuChecked = false;
                }
                // console.log(this.menuFormList)
            },
            handleCheckedMenuChange: function(event, index, i) {
                let checkedCount = event.length;
                let menulevel1_moduleid = this.menuList[index].moduleid;
                let menulevel2_moduleid = this.menuList[index].children[i].moduleid;

                this.menuList[index].children[i].checkAll = checkedCount === this.menuList[index].children[i].menu_list.length;
                this.menuList[index].children[i].isIndeterminate = checkedCount > 0 && checkedCount < this.menuList[index].children[i].menu_list.length;
                this.menuFormList = [];
                this.menuList.forEach(function(val, key) {
                    val.children.forEach(function(v, k) {
                        if(val.children[k].checkedMenu.length > 0) {
                            vm.menuFormList.push(val.children[k].checkedMenu);
                        }
                    })
                }, this);
                if(this.menuFormList.length > 0) {
                    this.menuChecked = true;
                } else {
                    this.menuChecked = false;
                }
            },
            submitForm: function() {
                if (this.menuChecked) {
                    vm.editAjax();
                } else {
                    vm.messageNotice('warning', '请选择菜单');
                    return false;
                }
            },
        }
    });

</script>
