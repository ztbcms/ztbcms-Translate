<extend name="../../Admin/View/Common/element_layout"/>

<block name="content">
    <div id="app" style="padding: 8px;" v-cloak>
        <el-card>
            <h3>翻译对照</h3>

            <div class="filter-container">
                <el-input v-model="listQuery.key" placeholder="Key" style="width: 200px;"
                          class="filter-item"></el-input>

                <el-select v-model="listQuery.lang" placeholder="语言" clearable style="width: 150px"
                           class="filter-item">
                    <el-option v-for="(item,index) in languageList" :key="index" :label="item.lang_name + ' | ' + item.lang"
                               :value="item.lang"></el-option>
                </el-select>


                <el-button class="filter-item" type="primary" @click="searchList">
                    搜索
                </el-button>
                <el-button class="filter-item" style="margin-left: 10px;" type="primary" @click="addDictionary">
                    添加
                </el-button>

            </div>
            <el-table
                    :data="list"
                    border
                    fit
                    highlight-current-row
                    style="width: 100%;"
                    @sort-change="sortChange"
            >
                <el-table-column label="ID" align="center" width="100">
                    <template slot-scope="scope">
                        <span>{{ scope.row.dictionary_id }}</span>
                    </template>
                </el-table-column>

                <el-table-column label="key" min-width="180px">
                    <template slot-scope="scope">
                        {{ scope.row.key }}
                    </template>
                </el-table-column>
                <el-table-column label="value" min-width="180px" align="center">
                    <template slot-scope="scope">
                        <span>{{ scope.row.value }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="语言" width="120px" align="center">
                    <template slot-scope="scope">
                        <span>{{ scope.row.lang }}</span>
                    </template>
                </el-table-column>

                <el-table-column label="操作" align="center" width="230" class-name="small-padding fixed-width">
                    <template slot-scope="{row}">
                        <el-button type="primary" size="mini" @click="editDictionary(row.dictionary_id)">
                            编辑
                        </el-button>

                        <el-button size="mini" type="danger" @click="deleteDictionary(row.dictionary_idf)">
                            删除
                        </el-button>
                    </template>
                </el-table-column>

            </el-table>

            <div class="pagination-container">
                <el-pagination
                        background
                        layout="prev, pager, next, jumper"
                        :total="total"
                        v-show="total>0"
                        :current-page.sync="listQuery.page"
                        :page-size.sync="listQuery.limit"
                        @current-change="getList"
                >
                </el-pagination>
            </div>

        </el-card>
    </div>

    <style>
        .filter-container {
            padding-bottom: 10px;
        }

        .pagination-container {
            padding: 32px 16px;
        }
    </style>

    <script>
        $(document).ready(function () {
            new Vue({
                el: '#app',
                data: {
                    form: {},
                    languageList: [],
                    list: [],
                    total: 0,
                    listQuery: {
                        page: 1,
                        limit: 20,
                        lang: '',
                        key: '',

                    },
                },
                watch: {},
                filters: {},
                methods: {
                    getList: function () {
                        var that = this;
                        var data = this.listQuery;
                        $.ajax({
                            url: "{:U('Translate/DictionaryAdmin/getList')}",
                            data: data,
                            dataType: 'json',
                            type: 'get',
                            success: function (res) {
                                var data = res.data;
                                that.list = data.items;
                                that.listQuery.page = data.page;
                                that.listQuery.limit = data.limit;
                                that.total = data.total_items;
                            }
                        })
                    },
                    getLanguageList: function () {
                        var that = this;
                        var where = {
                            page: 1,
                            limit: 100,
                        };
                        $.ajax({
                            url: "{:U('Translate/Language/getList')}",
                            data: where,
                            dataType: 'json',
                            type: 'get',
                            success: function (res) {
                                var data = res.data;
                                that.languageList = data.items;
                            }
                        })
                    },
                    handleFilter: function () {
                        this.listQuery.page = 1
                        this.getList()
                    },
                    sortChange: function (data) {
                        var order = data.order
                        var prop = data.prop
                        if (prop === 'id') {
                            this.sortByID(order)
                        }
                    },
                    sortByID: function (order) {
                        if (order === 'ascending') {
                            this.listQuery.sort = '+id'
                        } else {
                            this.listQuery.sort = '-id'
                        }
                        this.handleFilter()
                    },
                    getSortClass: function (key) {
                        const sort = this.listQuery.sort
                        return sort === `+${key}`
                            ? 'ascending'
                            : sort === `-${key}`
                                ? 'descending'
                                : ''
                    },
                    handleModifyStatus: function (row, status) {
                        this.$message({
                            message: '操作Success',
                            type: 'success'
                        })
                        row.status = status
                    },
                    //以窗口形式打开链接
                    openArticleLink: function (url) {
                        layer.open({
                            type: 2,
                            title: '预览',
                            content: url,
                            area: ['60%', '70%'],
                        })
                    },
                    deleteDictionary: function (dictionary_id) {
                        var that = this
                        layer.confirm('确认删除？', {title: '提示'}, function (index) {
                            //do something
                            that.doDeleteDictionary(dictionary_id)

                            layer.close(index);
                        });
                    },
                    doDeleteDictionary: function (dictionary_id) {
                        var that = this;
                        var data = {
                            dictionary_id: dictionary_id,
                        };
                        $.ajax({
                            url: "{:U('Translate/DictionaryAdmin/deleteDictionary')}",
                            data: data,
                            dataType: 'json',
                            type: 'post',
                            success: function (res) {
                                if (res.status) {
                                    that.getList()
                                    that.$message.success(res.msg);
                                } else {
                                    that.$message.error(res.msg);
                                }
                            }
                        })
                    },
                    searchList: function () {
                        this.listQuery.page = 1
                        this.getList()
                    },
                    addDictionary: function () {
                        var that = this
                        var url = "/Translate/DictionaryAdmin/editDictionary"
                        layer.open({
                            type: 2,
                            title: '操作',
                            content: url,
                            area: ['60%', '70%'],
                            end: function () {
                                that.getList()
                            }
                        })
                    },
                    editDictionary: function (dictionary_id) {
                        var that = this
                        var url = "/Translate/DictionaryAdmin/editDictionary?dictionary_id=" + dictionary_id
                        layer.open({
                            type: 2,
                            title: '操作',
                            content: url,
                            area: ['60%', '70%'],
                            end: function () {
                                that.getList()
                            }
                        })
                    }
                },
                mounted: function () {
                    this.getLanguageList()
                    this.getList()
                },

            })
        })
    </script>
</block>
