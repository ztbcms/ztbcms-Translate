<extend name="../../Admin/View/Common/base_layout"/>

<block name="content">
    <div id="app" style="padding: 8px;" v-cloak>
        <div class="search_type cc mb10">
            <button @click="addLanguage" class="btn btn-primary" style="margin-left: 8px;float: right;">添加</button>
        </div>
        <hr>
        <form action="" method="post">
            <div class="table_list">
                <table class="table table-bordered table-hover">
                    <thead>
                    <tr style="background: ghostwhite;">
                        <td width="20%" align="center">ID</td>
                        <td align="center">名称</td>
                        <td align="center">是否默认</td>
                        <td width="30%" align="center">操作</td>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="item in items">
                        <td align="center">{{item.id}}</td>
                        <td align="center">{{item.lang}}</td>
                        <td align="center">{{item.is_default == 1 ? '是' : '否' }}</td>
                        <td align="center">
                            <a @click="editLanguage(item.id)" class="btn btn-primary">修改</a>
                            <a v-if="item.is_default == 0" @click="setDefault(item.id)" class="btn btn-danger">设为默认</a>
                        </td>
                    </tr>
                    </tbody>
                </table>

                <div v-if="page_count > 1" style="text-align: center">
                    <ul class="pagination pagination-sm no-margin">
                        <li>
                            <a @click="page > 1 ? (page--) : '' ;getList()" href="javascript:;">上一页</a>
                        </li>
                        <li>
                            <a href="javascript:;">{{ page }} / {{ page_count }}</a>
                        </li>
                        <li><a @click="page<page_count ? page++ : '' ;getList()" href="javascript:;">下一页</a></li>
                    </ul>
                </div>
            </div>
        </form>
    </div>

    <script>
        $(document).ready(function () {
            new Vue({
                el: '#app',
                data: {
                    items: [],
                    page: 1,
                    limit: 10,
                    page_count: 0
                },
                methods: {
                    getList: function () {
                        var that = this;
                        var where = {
                            page: this.page,
                            limit: this.limit
                        };
                        $.ajax({
                            url: "{:U('Translate/Language/getList')}",
                            data: where,
                            dataType: 'json',
                            type: 'get',
                            success: function (res) {
                                var data = res.data;
                                that.items = data.items;
                                that.page = data.page;
                                that.limit = data.limit;
                                that.page_count = data.total_pages;
                            }
                        })
                    },
                    addLanguage: function(){
                        var that = this;
                        layer.open({
                            type: 2,
                            title: '添加',
                            content: '{:U("Translate/Language/language")}',
                            area: ['50%', '50%'],
                            end: function(){
                                that.getList();
                            }
                        });
                    },
                    editLanguage: function(id){
                        var that = this;
                        layer.open({
                            type: 2,
                            title: '修改',
                            content: '{:U("Translate/Language/language")}&id='+id,
                            area: ['50%', '50%'],
                            end: function(){
                                that.getList();
                            }
                        });
                    },
                    setDefault: function(id){
                        var that = this;
                        var where = {
                            id: id
                        };
                        $.ajax({
                            url: "{:U('Translate/Language/setDefault')}",
                            data: where,
                            dataType: 'json',
                            type: 'post',
                            success: function (res) {
                                if(res.status){
                                    layer.msg('OK', {time: 1000}, function(){
                                        that.getList();
                                    });
                                }else{
                                    layer.msg(res.msg, {time: 1000});
                                }
                            }
                        })
                    }
                },
                mounted: function () {
                    this.getList();
                }
            })
        })
    </script>
</block>
