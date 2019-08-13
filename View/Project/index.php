<extend name="../../Admin/View/Common/base_layout"/>

<block name="content">
    <div id="app" style="padding: 8px;" v-cloak>
        <div class="search_type cc mb10">
            <button @click="addProject" class="btn btn-primary" style="margin-left: 8px;float: right;">添加</button>
        </div>
        <hr>
        <form action="" method="post">
            <div class="table_list">
                <table class="table table-bordered table-hover">
                    <thead>
                    <tr style="background: ghostwhite;">
                        <td width="20%" align="center">ID</td>
                        <td align="center">名称</td>
                        <td width="30%" align="center">操作</td>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="item in items">
                        <td align="center">{{item.id}}</td>
                        <td align="center">{{item.name}}</td>
                        <td align="center">
                            <a @click="editProject(item.id)" class="btn btn-primary">修改</a>
                            <a @click="showCatalog(item.id)" class="btn btn-primary">编辑文档</a>
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
                            url: "{:U('Translate/Project/getList')}",
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
                    addProject: function(){
                        var that = this;
                        layer.open({
                            type: 2,
                            title: '修改',
                            content: '{:U("Translate/Project/project")}',
                            area: ['50%', '50%'],
                            end: function(){
                                that.getList();
                            }
                        });
                    },
                    editProject: function(id){
                        var that = this;
                        layer.open({
                            type: 2,
                            title: '修改',
                            content: '{:U("Translate/Project/project")}&id='+id,
                            area: ['50%', '50%'],
                            end: function(){
                                that.getList();
                            }
                        });
                    },
                    showCatalog: function(id){
                        layer.open({
                            type: 2,
                            title: '编辑文档',
                            content: '/Translate/ConstantCategory/index?id='+id,
                            area: ['90%', '90%']
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
