<extend name="../../Admin/View/Common/element_layout"/>

<!--纯前端实现-->

<block name="content">
    <!--  引入vue，仅作示例，生产环境请勿引入第三方  -->
    <script src="https://cdn.bootcss.com/vue-i18n/8.12.0/vue-i18n.min.js"></script>
    <script type="text/javascript" charset="utf-8" src="{$config_siteurl}statics/admin/ueditor/ueditor.config.js"></script>
    <script type="text/javascript" charset="utf-8" src="{$config_siteurl}statics/admin/ueditor/ueditor.all.min.js"></script>
    <script type="text/javascript" charset="utf-8" src="{$config_siteurl}statics/admin/ueditor/lang/zh-cn/zh-cn.js"></script>
    <script>
        var ueditor_config = {
            rowspacingtop: ['0', '5', '10', '15', '20', '25'],
            rowspacingbottom: ['0', '5', '10', '15', '20', '25'],
            lineheight: ['0', '1', '1.5','1.75','2', '3', '4', '5']
        }
    </script>
    <div id="app" style="padding: 8px;height: 100%;" v-cloak>
        <el-card>
            <h2>切换示例（Vue-i18n）</h2>

            <div style="margin-top: 10px;">
                <el-form ref="form" label-width="120px" >
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

                    <el-tabs v-model="current_lang" @tab-click="clickLangTab">
                        <template v-for="(item, i) in langList">
                            <el-tab-pane :label="item.lang" :name="item.lang">


                            </el-tab-pane>
                        </template>

                    </el-tabs>

                    <el-form-item label="车型">
                        <el-input v-model="languageForm.model"></el-input>
                    </el-form-item>

                    <div class="el-form-item">
                        <label class="el-form-item__label" style="width: 120px;">描述</label>
                        <div class="el-form-item__content" style="margin-left: 120px;line-height: 0;">
                            <textarea id="editor_content" style="height: 400px;"></textarea>
                        </div>
                    </div>

                    <el-form-item >
                        <el-button type="primary">保存</el-button>
                    </el-form-item>


                </el-form>
            </div>

        </el-card>
    </div>

    <script>
        $(document).ready(function () {
            Vue.use(VueI18n)
            // 通过选项创建 VueI18n 实例
            var i18n = new VueI18n({
                locale: '中文', // 设置地区
                fallbackLocale: '中文',
            })

            new Vue({
                i18n: i18n,//初始化
                el: '#app',
                data: {
                    editor: null,
                    current_lang: '',
                    //语言
                    langList: [],
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
                        vin:'',
                        model: '',
                        year: '',
                        transmission: '0',
                        description: '<p>我就是我</p>',
                        dictionary: {
                            '中文':{
                                description: '<p>我就是我</p>'
                            }
                        },
                    }
                },
                methods: {
                    /**
                     * 获取字典
                     * @param callback 更新后的回掉
                     */
                    getDictionaryList: function(callback){
                        var that = this
                        var lang = this.current_lang
                        if(that.messages[lang] && that.messages[lang].length > 0){
                            if(callback && callback instanceof Function){
                                callback()
                            }
                            return;
                        }
                        $.ajax({
                            type: "GET",
                            url: '/Translate/Public/getAllDictionaryByLang?lang=' + lang,
                            dataType: "json",
                            success: function (res) {
                                // this.message
                                that.messages[lang] = res.data
                                that.$i18n.setLocaleMessage(lang, that.messages[lang])
                                if(callback && callback instanceof Function){
                                    callback()
                                }
                            }
                        });
                    },
                    getLangList: function(){
                        var that = this
                        $.ajax({
                            type: "POST",
                            url: '/Translate/Public/getLanguageList',
                            headers: {
                                'Lang': that.current_lang
                            },
                            dataType: "json",
                            success: function (res) {
                                that.langList = res.data
                                if(that.langList.length > 0){
                                    

                                    that.current_lang = that.langList[0].lang
                                }
                            }
                        });
                    },
                    clickLangTab: function(tab, event) {
                        console.log(tab, event);
                    },
                    _syncToLanguageFormField: function (field){
                        var value = '';
                        if(this.form.dictionary[this.current_lang] && this.form.dictionary[this.current_lang][field]){
                            value = this.form.dictionary[this.current_lang][field]
                        }
                        this.languageForm[field] = value
                    },
                    //同步元信息到表单
                    syncToLanguageForm: function (){
                        this._syncToLanguageFormField('model')
                        this._syncToLanguageFormField('description')
                        console.log('syncToLanguageForm..')
                        console.log(this.editor)
                        console.log(this.languageForm.description)
                        this.editor.setContent(this.languageForm.description);
                    },
                    _syncFromLanguageFormField: function(field, value = null){
                        if(value === null){
                            value = this.languageForm[field]
                        }
                        this.form.dictionary[this.current_lang][field] = value
                    },
                    //同步表单到元信息
                    syncFromLanguageForm: function(){
                        this._syncFromLanguageFormField('model')
                        this._syncFromLanguageFormField('description', this.editor.getContent())
                    },
                    onEdiotorContentChanged: function () {
                        console.log('onEdiotorContentChanged')
                        console.log(this.editor.getContent())
                        this.languageForm.description = this.editor.getContent()
                    }
                },
                watch: {
                    current_lang: function(newValue, oldValue){
                        var that = this
                        //当前
                        if(newValue == 0 || newValue == ''){
                            return
                        }
                        console.log('语言切换为==>'+oldValue +' -> '+newValue)
                        this.$message.success('语言切换为==>'+newValue)
                        // this.getDictionaryList(function(){
                        //
                        // }.bind(this))
                        that.$i18n.locale = newValue

                        this.syncToLanguageForm()
                    },
                    'languageForm.model': function(newValue){
                        this.syncFromLanguageForm()
                    },
                    'languageForm.description': function(newValue){
                        this.syncFromLanguageForm()
                    }
                },
                //headers
                mounted: function () {
                    var that = this
                    this.editor = UE.getEditor('editor_content', ueditor_config);
                    this.editor.ready(function() {
                        that.getLangList()

                        that.editor.addListener("contentchange", that.onEdiotorContentChanged)
                    });

                }
            })
        })
    </script>
</block>
