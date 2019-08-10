<extend name="../../Admin/View/Common/base_layout"/>

<block name="content">
    <div id="app" style="height: 100%;" v-cloak>
        <div style="
                -webkit-box-shadow: 0px 3px 39px -15px rgba(0,0,0,0.75);
                -moz-box-shadow: 0px 3px 39px -15px rgba(0,0,0,0.75);
                box-shadow: 0px 3px 39px -15px rgba(0,0,0,0.75);
                position: fixed;top: 0;right: 0;left: 0;background: white;    height: 60px;">
            <div style="position: relative;padding: 4px;height: 60px;width: 25%;float: left;">
                <h3 style="display: inline-block;">目录</h3>
                <button @click="addConstantCategory" class="btn btn-success" style="right: 10px;margin-top;top: 15px;position: absolute;float: right">添加文档</button>
            </div>
            <div style="position: relative;padding: 4px;height: 60px;width: 75%;float: left;">
                <template v-if="selectedItem">
                    <h3 style="display: inline-block;">{{selectedItem.name}}</h3>
                    <button @click="delConstantCategory(selectedItem.id)" class="btn btn-danger" style="float: right;margin-left: 15px;margin-top: 10px;">删除</button>
                    <button @click="editConstantCategory(selectedItem.id)" class="btn btn-primary" style="float: right;margin-left: 15px;margin-top: 10px;">编辑</button>
                </template>
            </div>
        </div>
        <div style="margin-top: 60px;height: 100%;">
            <div class="table_list" style="width: 25%;height: 100%;float: left;overflow-x: scroll;background: white;">

                <table class="table table-bordered table-hover">
                    <thead>
                    </thead>
                    <tbody>
                    <tr v-for="item in items">
                        <td width="400" align="left" style="white-space: nowrap;font-size: 12px;"  @click="editContent(item.id, item)" :style="{'background': selectedItem && item.id == selectedItem.id ? '#f5f5f5':''}">
                            <a :style="'margin-left: '+item.level*15+'px;'" href="javascript:;">{{item.name}} | {{item.key}}</a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="table_list" style="width: 75%;height: 100%;float: left;">
                <iframe :src="iframe_src" width="100%" height="100%" style="border: 1px solid gainsboro;"></iframe>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            new Vue({
                el: '#app',
                data: {
                    id: '{:I("get.id")}',
                    items: [],
                    selectedItem: null,
                    iframe_src: ''
                },
                methods: {
                    getList: function () {
                        var that = this;
                        var where = {
                            project_id: that.id
                        };
                        $.ajax({
                            url: "{:U('Translate/ConstantCategory/getConstantCategoryBySort')}",
                            data: where,
                            dataType: 'json',
                            type: 'get',
                            success: function (res) {
                                that.items = res.data;
                                if(that.items.length > 0){
                                    if(that.selectedItem == null){
                                        that.editContent(that.items[0].id, that.items[0]);
                                    }
                                }
                            }
                        })
                    },
                    addConstantCategory: function(){
                        var that = this;
                        layer.open({
                            type: 2,
                            title: '添加',
                            content: '{:U("Translate/ConstantCategory/editConstantCategory")}&project_id='+that.id,
                            area: ['60%', '70%'],
                            end: function(){
                                that.getList();
                            }
                        });
                    },
                    editConstantCategory: function(id){
                        var that = this;
                        layer.open({
                            type: 2,
                            title: '修改',
                            content: '{:U("Translate/ConstantCategory/editConstantCategory")}&id='+id+'&project_id='+that.id,
                            area: ['60%', '70%'],
                            end: function(){
                                that.getList();
                            }
                        });
                    },
                    editContent: function(id, item){
                        var that = this;

                        that.iframe_src = '{:U("Translate/Constant/index")}&category_id='+id;
                        console.log(that.iframe_src)
                        this.selectedItem = item
                    },
                    delConstantCategory: function(id){
                        var that = this;
                        layer.confirm('是否确定删除', {title: '删除'}, function(){
                            $.ajax({
                                url: "{:U('Translate/ConstantCategory/delCatalog')}",
                                data: {id: id},
                                dataType: 'json',
                                type: 'post',
                                success: function (res) {
                                    if(res.status){
                                        layer.msg('删除成功', {time: 1000}, function(){
                                            that.getList();
                                        })
                                    }else{
                                        layer.msg(res.msg, {time: 1000})
                                    }
                                }
                            });
                        });
                    }
                },
                mounted: function () {
                    this.getList();
                }
            })
        })
    </script>
</block>
