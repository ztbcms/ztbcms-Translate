<extend name="../../Admin/View/Common/element_layout"/>

<block name="content">

    <div id="app" style="padding: 8px;height: 100%;" v-cloak>
        <el-card>
            <h2>车辆信息</h2>

            <div style="margin-top: 10px;">
                <el-form ref="form" label-width="120px">
                    <el-form-item label="VIN">
                        <el-input v-model="form.vin"></el-input>
                    </el-form-item>

                    <el-form-item label="年份">
                        <el-input v-model="form.year"></el-input>
                    </el-form-item>

                    <el-form-item label="变速箱">
                        <el-radio-group v-model="form.transmission">
                            <el-radio label="0">不限制</el-radio>
                            <el-radio label="1">手动</el-radio>
                            <el-radio label="2">自动</el-radio>
                            <el-radio label="3">手自一体</el-radio>
                        </el-radio-group>
                    </el-form-item>

                    <el-tabs v-model="current_lang" @tab-click="">
                        <volist name="data['langList']" id="vo">
                            <el-tab-pane label="{$vo['lang']}" name="{$vo['lang']}">
                                <el-form-item label="车型">
                                    <el-input v-model="form.model.{$vo['lang']}"></el-input>
                                </el-form-item>

                                <div class="el-form-item">
                                    <label class="el-form-item__label" style="width: 120px;">描述</label>
                                    <div class="el-form-item__content" style="margin-left: 120px;line-height: 0;">
                                        <textarea id="editor_content_{$vo.lang}" style="height: 400px;"></textarea>
                                    </div>
                                </div>

                            </el-tab-pane>
                        </volist>
                    </el-tabs>


                    <el-form-item>
                        <el-button type="primary" @click="submit">保存</el-button>
                    </el-form-item>


                </el-form>
            </div>

        </el-card>
    </div>


</block>


<block name="footer">
    <!-- 引入UEditor   -->
    <include file="Common/ueditor"/>

    <script>
        $(document).ready(function () {
            //语言列表
            var _LANG_LIST = JSON.parse('{:json_encode($data["langList"])}') || []
            var _UEDITOR_LIST = {}
            for (var i = 0; i < _LANG_LIST.length; i++) {
                //初始化UEidtor
                var lang = _LANG_LIST[i]['lang']
                _UEDITOR_LIST[lang] = UE.getEditor('editor_content_' + lang, _UEDITOR_CONFIG);
            }

            new Vue({
                el: '#app',
                data: {
                    editor: null,
                    current_lang: '中文',
                    //语言
                    langList: _LANG_LIST,
                    messages: {
                        // '中文': {
                        //     demo_model: '车型',
                        // },
                    },
                    languageForm: {
                        model: '',
                        description: '',
                    },
                    form: {
                        id: '{:I("get.id")}',
                        vin: '',
                        model: {},
                        year: '',
                        transmission: '0',
                        description: {},
                    }
                },
                methods: {
                    //获取详情
                    getDetail: function () {
                        var that = this;
                        var url = '/Translate/Demo/getCarDetail?id=' + this.form.id
                        that.httpGet(url, {}, function (res) {
                            if (res.status) {
                                that.form = res.data;

                                for (var i = 0; i < _LANG_LIST.length; i++) {
                                    var lang = _LANG_LIST[i]['lang']
                                    var editor = _UEDITOR_LIST[lang]

                                    if (!editor) {
                                        continue;
                                    }

                                    editor.ready(function (editor, lang) {
                                        //ueditor 初始化
                                        editor.setContent(that.form['description'][lang]);
                                    }.bind(this, editor, lang))
                                }
                            } else {
                                layer.msg(res.msg, {time: 1000});
                            }
                        });
                    },
                    submit: function () {
                        var that = this;

                        for (var i = 0; i < _LANG_LIST.length; i++) {
                            var lang = _LANG_LIST[i]['lang']
                            that.form['description'][lang] = _UEDITOR_LIST[lang].getContent();
                        }

                        var url = '{:U("Translate/Demo/doAddEditCar")}';
                        var data = that.form;

                        that.httpPost(url, data, function (res) {
                            if (res.status) {
                                layer.msg(res.msg, {time: 1000}, function () {
                                });
                                if(window !== window.parent){
                                    setTimeout(function(){
                                        window.parent.layer.closeAll()
                                    }, 1000)
                                }
                            } else {
                                layer.msg(res.msg, {time: 1000});
                            }
                        });
                    }
                },
                watch: {},
                mounted: function () {
                    var that = this
                    if (that.form.id) {
                        that.getDetail()
                    }
                }
            })
        })
    </script>
</block>