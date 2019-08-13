<extend name="../../Admin/View/Common/element_layout"/>

<block name="content">
    <div id="app" style="padding: 8px;" v-cloak>
        <el-card>
            <h3>编辑信息</h3>
            <el-row>
                <el-col :span="8">
                    <div class="grid-content ">
                        <el-form ref="form" :model="form" label-width="80px">
                            <el-form-item label="key">
                                <el-input v-model="form.key"></el-input>
                            </el-form-item>


                            <el-form-item label="语言">
                                <el-select v-model="form.lang" placeholder="请选择" clearable style="width: 90px"
                                           class="filter-item">
                                    <el-option v-for="(item,index) in languageList" :key="index" :label="item.lang" :value="item.lang"></el-option>
                                </el-select>
                            </el-form-item>

                            <el-form-item label="value">
                                <el-input v-model="form.value"></el-input>
                            </el-form-item>

                            <el-form-item>
                                <el-button type="primary" @click="onSubmit">确认</el-button>
                                <el-button @click="onCancel">取消</el-button>
                            </el-form-item>
                        </el-form>
                    </div>
                </el-col>
                <el-col :span="16"><div class="grid-content "></div></el-col>
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
                        dictionary_id: "{$data.dictionary_id}",
                        key: "{$data.key}",
                        value: "{$data.value}",
                        lang: "{$data.lang}",
                    },
                    languageList: [],
                },
                watch: {},
                filters: {},
                methods: {
                    onSubmit: function(){
                        var that = this;
                        $.ajax({
                            url: '{:U("Translate/DictionaryAdmin/doEditDictionary")}',
                            data: that.form,
                            type: 'post',
                            dataType: 'json',
                            success: function (res) {
                                if (res.status) {
                                    that.$message.success(res.msg);
                                    setTimeout(function(){
                                        parent.window.layer.closeAll();
                                    }, 1000)
                                } else {
                                    that.$message.error(res.msg);
                                }
                            }
                        });
                    },
                    onCancel: function(){
                        parent.window.layer.closeAll();
                    },
                    getLanguageList: function() {
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
                },
                mounted: function () {
                    this.getLanguageList()
                },

            })
        })
    </script>
</block>
