# ztbcms-Translate
多语言翻译

## API

1. 根据给定的语言获取全部的翻译 `/Translate/Public/getAllDictionaryByLang?lang=CN`
2. 获取当前的语言列表 `/Translate/Public/getLanguageList`

## 使用

对于后台使用者来说，你只需要使用`Translate\Service\TranslateService`即可。请参考DemoController 中的使用方法