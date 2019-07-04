<extend name="../../Admin/View/Common/base_layout"/>

<block name="content">
    <div id="app" style="padding: 8px;height: 100%;" v-cloak>
        <div class="table_list">
            <div style="position: relative;padding: 4px;height: 60px;">
                <h3 style="display: inline-block;">翻译对照表</h3>
                <button @click="exportContent" class="btn btn-success" style="float: right;margin-left: 15px;margin-top: 10px;">导出翻译</button>
                <button @click="addContent" class="btn btn-primary" style="float: right;margin-left: 15px;margin-top: 10px;">添加翻译</button>
            </div>
            <table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <td align="center">Key</td>
                    <td align="center">翻译</td>
                    <td align="center">
                      操作
                    </td>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(item,index) in items">
                    <td align="center">
                        <span></span><input class="form-control" type="text" v-model="item.path" @change="editContent(index)">
                    </td>
                    <td align="center">
                        <volist name="langList" id="vo">
                            <div style="padding: 10px;">
                                <span style="width: 10%;display: inline-block;text-align: right;padding-right: 8px;">{$vo.lang}  </span><input class="form-control" type="text" v-model="item.data.{$vo.lang}" @change="editContent(index)" style="display: inline-block;width: 80%" >
                            </div>
                        </volist>
                    </td>
                    <td align="center">
                        <a @click="delContent(item.id)" class="btn btn-danger">删除</a>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            new Vue({
                el: '#app',
                data: {
                    id: '{:I("get.catalog_id")}',
                    items: []
                },
                methods: {
                    getList: function () {
                        var that = this;
                        var where = {
                            catalog_id: that.id
                        };
                        $.ajax({
                            url: "{:U('Translate/Content/getList')}",
                            data: where,
                            dataType: 'json',
                            type: 'get',
                            success: function (res) {
                                var data = res.data;
                                that.items = data.items;
                            }
                        })
                    },
                    addContent: function(){
                        var that = this;
                        var data = {
                            catalog_id: that.id
                        };
                        $.ajax({
                            url: '{:U("Translate/Content/addContent")}',
                            data: data,
                            dataType: 'json',
                            type: 'post',
                            success: function (res) {
                                if(res.status){
                                    that.getList();
                                }else{
                                    layer.msg(res.msg, {time: 1000});
                                }
                            }
                        })
                    },
                    editContent: function(index){
                        var that = this;
                        var data = {
                            id: that.items[index].id,
                            path: that.items[index].path,
                            data: that.items[index].data
                        };
                        $.ajax({
                            url: '{:U("Translate/Content/editContent")}',
                            data: data,
                            dataType: 'json',
                            type: 'post',
                            success: function (res) {
                                if(res.status){
                                    layer.msg('OK', {time: 1000});
                                }else{
                                    layer.msg(res.msg, {time: 1000});
                                }
                            }
                        })
                    },
                    delContent: function(id){
                        var that = this;
                        $.ajax({
                            url: '{:U("Translate/Content/delContent")}',
                            data: {id: id},
                            dataType: 'json',
                            type: 'post',
                            success: function (res) {
                                if(res.status){
                                    layer.msg(res.msg, {time: 1000}, function(){
                                        that.getList();
                                    });
                                }else{
                                    layer.msg(res.msg, {time: 1000});
                                }
                            }
                        });
                    },
                    exportContent: function(){
                        var that = this;
                        var url = '{:U("Translate/Catalog/exportContent")}&id='+that.id;
                        window.open(url);
                    }
                },
                mounted: function () {
                    this.getList();
                }
            })
        })
    </script>
</block>
