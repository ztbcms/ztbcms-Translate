<extend name="../../Admin/View/Common/element_layout"/>

<block name="content">
    <!--  引入vue，仅作示例，生产环境请勿引入第三方  -->
    <script src="https://cdn.bootcss.com/vue-i18n/8.12.0/vue-i18n.min.js"></script>
    <div id="app" style="padding: 8px;height: 100%;" v-cloak>
        <el-card>
            <h2>切换示例（Vue-i18n）</h2>

            <el-select v-model="current_lang" placeholder="请选择">
                <el-option
                    v-for="(item, i) in langList"
                    :key="i"
                    :label="item.lang_name"
                    :value="item.lang">
                </el-option>
            </el-select>

            <div style="margin-top: 10px;">
                <el-form ref="form" label-width="120px" >
                    <el-form-item :label="$t('demo.model')">
                        <el-input v-model="form.model"></el-input>
                    </el-form-item>

                    <el-form-item :label="$t('demo.year')">
                        <el-input v-model="form.year"></el-input>
                    </el-form-item>

                    <el-form-item :label="$t('demo.transmission')">
                        <el-radio-group v-model="form.transmission">
                            <el-radio label="0">{{ $t('demo.transmission.not_limited') }}</el-radio>
                            <el-radio label="1">{{ $t('demo.transmission.manual') }}</el-radio>
                            <el-radio label="2">{{ $t('demo.transmission.automatic') }}</el-radio>
                        </el-radio-group>
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
                    current_lang: '',
                    //语言
                    langList: [],
                    messages: {
                        // '中文': {
                        //     demo_model: '车型',
                        // },
                    },
                    form: {
                        model: '',
                        year: '',
                        transmission: '0'
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
                    clickRequestAdmin: function(){
                        var that = this
                        $.ajax({
                            type: "POST",
                            url: '/Translate/Demo/doRequestAdmin',
                            headers: {
                                'Lang': that.current_lang //请求头，指定语言
                            },
                            dataType: "json",
                            success: function (res) {
                                layer.msg(res.msg)
                            }
                        });
                    },
                },
                watch: {
                    current_lang: function(newValue){
                        var that = this
                        this.$message.success('语言切换为==>'+newValue)
                        this.getDictionaryList(function(){
                            that.$i18n.locale = newValue
                        }.bind(this))
                    }
                },
                //headers
                mounted: function () {
                    this.getLangList()
                }
            })
        })
    </script>
</block>
