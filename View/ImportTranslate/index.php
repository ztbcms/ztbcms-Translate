<extend name="../../Admin/View/Common/element_layout"/>

<block name="content">
    <div id="app" style="padding: 8px;" v-cloak>
        <el-card>
            <h3>导入翻译</h3>
            <!--错误信息-->
            <template>
                <el-table
                        :data="error_info"
                        style="width: 100%;margin-bottom: 20px">
                    <el-table-column
                            label="失败表"
                            align="center">
                        <el-table-column
                                label="key"
                                prop="key"
                                align="center">
                        </el-table-column>
                        <el-table-column
                                label="值"
                                prop="value"
                                align="center">
                        </el-table-column>
                    </el-table-column>
                </el-table>
            </template>

            <el-row>
                <el-col :span="8">
                    <div class="grid-content ">
                        <el-form ref="form" :model="form" label-width="80px">
                            <el-form-item label="项目名称">
                                <template>
                                    <el-select v-model="form.project" placeholder="请选择" @change="changeProject">
                                        <el-option
                                                v-for="item in selectList.projects"
                                                :key="item.id"
                                                :label="item.name"
                                                :value="item.id">
                                        </el-option>
                                    </el-select>
                                </template>
                            </el-form-item>
                            <el-form-item label="文档名称">
                                <template>
                                    <el-select v-model="form.constant_category" placeholder="请选择">
                                        <el-option
                                                v-for="item in selectList.constant_category"
                                                :key="item.id"
                                                :label="item.name+' | '+item.key"
                                                :value="item.id">
                                        </el-option>
                                    </el-select>
                                </template>
                            </el-form-item>

                            <el-form-item label="语言">
                                <el-select v-model="form.lang" placeholder="请选择" clearable style="width: 150px"
                                           class="filter-item">
                                    <el-option v-for="(item,index) in languageList" :key="index"
                                               :label="item.lang_name + ' | ' + item.lang"
                                               :value="item.lang"></el-option>
                                </el-select>
                            </el-form-item>

                            <el-form-item label="翻译文件">
                                <el-input
                                        type="textarea"
                                        rows="20"
                                        placeholder="请输入内容"
                                        v-model="form.value">
                                </el-input>
                            </el-form-item>
                            <el-form-item>
                                <el-button type="primary" @click="onSubmit">确认</el-button>
                                <el-button @click="onCancel">取消</el-button>
                            </el-form-item>
                        </el-form>
                    </div>
                </el-col>
                <el-col :span="16">
                    <div class="grid-content "></div>
                </el-col>
            </el-row>
        </el-card>
    </div>

    <style>

    </style>

    <script>
        $(document).ready(function () {
            new Vue({
                el: '#app',
                data: {
                    form: {
                        constant_category: '',
                        key: "",
                        value: "",
                        lang: '',
                        project: '',
                    },
                    error_info: [],
                    selectList: {
                        'projects': [],
                        'constant_category': [],
                    },
                    languageList: [],
                },
                watch: {},
                filters: {},
                methods: {
                    onSubmit: function () {
                        var that = this;
                        $.ajax({
                            url: '{:U("Translate/ImportTranslate/importTranslate")}',
                            data: that.form,
                            type: 'post',
                            dataType: 'json',
                            success: function (res) {
                                console.log(res);
                                layer.msg(res.msg)
                                if (res.data) {
                                    that.error_info = res.data;
                                }
                            }
                        });
                    },
                    onCancel: function () {
                        parent.window.layer.closeAll();
                    },
                    getSelectList: function () {
                        var that = this;
                        var where = {
                            page: 1,
                            limit: 100,
                        };
                        $.ajax({
                            url: "{:U('Translate/ImportTranslate/getSelectList')}",
                            dataType: 'json',
                            type: 'get',
                            success: function (res) {
                                var data = res.data;
                                that.languageList = data.language;
                                that.selectList.projects = data.projects;
                            }
                        })
                    },
                    changeProject: function () {
                        for (var i = 0; i < this.selectList.projects.length; i++) {
                            if (this.selectList.projects[i]['id'] == this.form.project) {
                                this.selectList.constant_category = this.selectList.projects[i]['constant_category'];
                                return;
                            }
                        }

                    }
                },
                mounted: function () {
                    this.getSelectList();
                },

            })
        })
    </script>
</block>
