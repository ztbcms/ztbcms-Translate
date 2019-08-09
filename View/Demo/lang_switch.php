<extend name="../../Admin/View/Common/element_layout"/>

<block name="content">
    <!--  引入vue  -->
    <script src="https://unpkg.com/vue-i18n/dist/vue-i18n.js"></script>
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
                            hello: '你好世界'
                        },
                        EN: {
                            hello: 'hello world'
                        },
                        JP: {
                            hello: 'こんにちは、世界'
                        },
                        FR: {
                            //法语没有翻译，用回 fallbackLocale
                        }
                    }
                },
                methods: {},
                watch: {
                    current_lang: function(newValue){
                        console.log('语言切换为==>'+newValue)
                        this.$i18n.locale = newValue
                    }
                },
                mounted: function () {
                    for (var lang in this.messages) {
                        this.$i18n.setLocaleMessage(lang, this.messages[lang])
                    }

                }
            })
        })
    </script>
</block>
