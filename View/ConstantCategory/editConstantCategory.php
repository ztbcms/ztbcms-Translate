<extend name="../../Admin/View/Common/base_layout"/>

<block name="content">
    <div id="app" style="padding: 8px;height: 100%;" v-cloak>
        <div class="table_list">
            <table class="table table-bordered table-hover">
                <tr>
                    <td>名称</td>
                    <td>
                        <input class="form-control" style="width: 30%;" type="text" v-model="postData.name">
                    </td>
                </tr>
                <tr>
                    <td>key</td>
                    <td>
                        <input class="form-control" style="width: 30%;" type="text" v-model="postData.key">
                    </td>
                </tr>
                <tr>
                    <td>上级</td>
                    <td>
                        <select class="form-control" style="width: 30%;" v-model="postData.pid">
                            <option value="0">├顶级</option>
                            <option v-for="(item, index) in catalogList" :value="item.id" v-if="isShow(index)" >{{item.level | getLevelText}}{{item.name}}</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <a @click="addEditCatalog" class="btn btn-primary">保存</a>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            new Vue({
                el: '#app',
                data: {
                    id: '{:I("get.id")}',
                    project_id: '{:I("get.project_id")}',
                    postData: {
                        pid: 0
                    },
                    catalogList: [],
                    un_show: []
                },
                filters: {
                    getLevelText: function(level){
                        var str = '├';
                        for(var i = 1; i < level; i++){
                            str += '─'
                        }
                        return str;
                    }
                },
                methods: {
                    getCatalogList: function(){
                        var that = this;
                        var where = {
                            project_id: that.project_id
                        };
                        $.ajax({
                            url: "{:U('Translate/ConstantCategory/getConstantCategoryBySort')}",
                            data: where,
                            dataType: 'json',
                            type: 'get',
                            success: function (res) {
                                that.catalogList = res.data;
                            }
                        })
                    },
                    isShow: function(index){
                        var that = this;
                        return true;
                    },
                    getDetail: function () {
                        var that = this;
                        var where = {
                            id: that.id
                        };
                        $.ajax({
                            url: "{:U('Translate/ConstantCategory/getConstantCategory')}",
                            data: where,
                            dataType: 'json',
                            type: 'get',
                            success: function (res) {
                                if(res.status){
                                    that.postData = res.data;
                                    setTimeout(function(){
                                        that.getCatalogList();
                                    }, 0);
                                }
                            }
                        })
                    },
                    addEditCatalog: function(){
                        var that = this;
                        var data = that.postData;
                        data.id = that.id;
                        data.project_id = that.project_id;
                        $.ajax({
                            url: "{:U('Translate/ConstantCategory/addEditConstantCategory')}",
                            data: data,
                            dataType: 'json',
                            type: 'post',
                            success: function (res) {
                                if(res.status){
                                    layer.msg('操作成功', {time: 1000}, function(){
                                        that.closeIframe();
                                    });
                                }else{
                                    layer.msg(res.msg, {time: 1000});
                                }
                            }
                        })
                    },
                    closeIframe: function () {
                        var index = parent.layer.getFrameIndex(window.name);
                        parent.layer.close(index);
                    }
                },
                mounted: function () {
                    if(this.id){
                        this.getDetail();
                    }else{
                        this.getCatalogList();
                    }
                }
            })
        })
    </script>
</block>
