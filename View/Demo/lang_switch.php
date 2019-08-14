<extend name="../../Admin/View/Common/element_layout"/>

<block name="content">
    <!--  引入vue，仅作示例，生产环境请勿引入第三方  -->
    <script src="https://cdn.bootcss.com/vue-i18n/8.12.0/vue-i18n.min.js"></script>
    <div id="app" style="padding: 8px;height: 100%;" v-cloak>
        <el-card>
            <h2>语言切换示例（Vue-i18n）</h2>
            <p><span style="color: red;">*</span> 法语没有翻译，用回 fallbackLocale: CN(中文)</p>
            <el-select v-model="current_lang" placeholder="请选择">
                <el-option
                        v-for="(item, i) in lang_info"
                        :key="i"
                        :label="item.lang_name"
                        :value="item.lang">
                </el-option>
            </el-select>
            <h4>{{ $t('hello') }}</h4>
            <el-button type="primary" @click="countLangAmount">点击统计语言数量（vue-i18n模板替换示例）</el-button>
            <el-button type="primary" @click="clickRequestAdmin">点击请求后台(使用 TranslateService 示例)</el-button>

        </el-card>
    </div>

    <script>
        $(document).ready(function () {
            Vue.use(VueI18n)
            // 通过选项创建 VueI18n 实例
            var i18n = new VueI18n({
                locale: 'EN', // 设置地区
                fallbackLocale: 'CN',
            })

            new Vue({
                i18n: i18n,//初始化
                el: '#app',
                data: {
                    current_lang: 'EN',
                    //语言
                    lang_info: [
                        {lang: 'CN', lang_name: '中文',},
                        {lang: 'EN', lang_name: 'English',},
                        {lang: 'JP', lang_name: '日本語',},
                        {lang: 'FR', lang_name: 'Français',}
                    ],
                    messages: {
                        CN: {
                            hello: '你好世界',
                            countLangAmount: '合计有{amount}种语言',
                        },
                        EN: {
                            hello: 'hello world',
                            countLangAmount: 'There are {amount} languages in total',
                        },
                        JP: {
                            hello: 'こんにちは、世界',
                            countLangAmount: '合計して、{amount}言語があります',
                        },
                        FR: {
                            //法语没有翻译，用回 fallbackLocale
                        }
                    }
                },
                methods: {
                    countLangAmount: function(){
                        this.$message.success(this.$t('countLangAmount', {amount: this.lang_info.length}))
                    },
                    getDictionaryList: function(){
                        $.ajax({
                            type: "POST",
                            url: '/Translate/Public/getAllDictionaryByLang',
                            headers: {
                                'Lang': 'CN'
                            },
                            dataType: "json",
                            success: function (res) {
                                console.log(res)
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
                        this.$message.success('语言切换为==>'+newValue)
                        this.$i18n.locale = newValue
                    }
                },
                //headers
                mounted: function () {
                    for (var lang in this.messages) {
                        this.$i18n.setLocaleMessage(lang, this.messages[lang])
                    }

                }
            })
        })
    </script>
</block>
