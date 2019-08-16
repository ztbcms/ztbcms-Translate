<extend name="../../Admin/View/Common/element_layout"/>

<block name="content">
    <div id="app" style="padding: 8px;" v-cloak>
        <el-card>
            <h3>车辆列表</h3>

            <div class="filter-container">

                <el-button class="filter-item" type="primary" @click="clickAddCar">
                    添加
                </el-button>
            </div>
            <el-table
                :key="tableKey"
                :data="items"
                border
                fit
                highlight-current-row
                style="width: 100%;"
            >
                <el-table-column label="ID" prop="id"  align="center" width="80">
                    <template slot-scope="scope">
                        <span>{{ scope.row.id }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="车型" align="center">
                    <template slot-scope="scope">
                        <span>{{ scope.row.model }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="VIN" align="center">
                    <template slot-scope="scope">
                        <span>{{ scope.row.vin }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="年份" align="center">
                    <template slot-scope="scope">
                        <span>{{ scope.row.year }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="操作" align="center" width="230" class-name="small-padding fixed-width">
                    <template slot-scope="{row}">
                        <el-button size="mini" type="primary"
                                   @click="editItem(row.id)">
                            编辑
                        </el-button>
                    </template>
                </el-table-column>

            </el-table>

            <div class="pagination-container">
                <el-pagination
                    background
                    layout="prev, pager, next, jumper"
                    :total="total"
                    v-show="total > 0"
                    :current-page.sync="where.page"
                    :page-size.sync="where.limit"
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
                    tableKey: 0,
                    items: [],
                    total: 0,
                    where: {
                        page: 1,
                        limit: 20,
                    },
                },
                watch: {

                },
                filters: {
                    parseTime: function (time, format) {
                        return Ztbcms.formatTime(time, format)
                    },
                },
                methods: {
                    getList: function () {
                        var that = this;
                        $.ajax({
                            url: '{:U("Translate/Demo/getCarList")}',
                            data: that.where,
                            type: 'get',
                            dataType: 'json',
                            success: function (res) {
                                if (res.status) {
                                    that.items = res.data.items;
                                    that.total = res.data.total_items;
                                    that.where.page = res.data.page;
                                }
                            }
                        });
                    },
                    search: function () {
                        this.where.page = 1;
                        this.getList();
                    },
                    handleFilter: function () {
                        this.where.page = 1
                        this.getList()
                    },

                    clickAddCar: function (id = 0){
                        var url = "/Translate/Demo/demo_edit_car"
                        if(id !== 0){
                            url += '?id='+id
                        }
                        var that = this
                        layer.open({
                            type: 2,
                            title: '编辑',
                            content: url,
                            area: ['60%', '70%'],
                            end: function(){
                                that.getList()
                            }
                        })
                    },
                    editItem: function(id){
                        this.clickAddCar(id)
                    }
                },
                mounted: function () {
                    this.getList();
                },

            })
        })
    </script>
</block>
