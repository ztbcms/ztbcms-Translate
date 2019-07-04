<extend name="../../Admin/View/Common/base_layout"/>

<block name="content">
    <div id="app" style="padding: 8px;height: 100%;" v-cloak>
        <div class="table_list">
            <table class="table table-bordered table-hover">
                <tr>
                    <td>lang</td>
                    <td>
                        <input class="form-control" style="width: 30%;" type="text" v-model="postData.lang">
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
                    postData: {}
                },
                methods: {
                    getDetail: function () {
                        var that = this;
                        var where = {
                            id: that.id
                        };
                        $.ajax({
                            url: "{:U('Translate/Language/getLanguage')}",
                            data: where,
                            dataType: 'json',
                            type: 'get',
                            success: function (res) {
                                if(res.status){
                                    that.postData = res.data;
                                }
                            }
                        })
                    },
                    addEditCatalog: function(){
                        var that = this;
                        var data = that.postData;
                        data.id = that.id;
                        $.ajax({
                            url: "{:U('Translate/Language/addEditLanguage')}",
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
                    }
                }
            })
        })
    </script>
</block>
