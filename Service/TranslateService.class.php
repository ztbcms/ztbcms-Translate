<?php
/**
 * User: jayinton
 * Date: 2019-08-14
 * Time: 12:02
 */

namespace Translate\Service;


use System\Service\BaseService;

/**
 * 翻译服务
 * Class TranslateService
 * @package Translate\Service
 */
class TranslateService extends BaseService
{

    private $lang = '';

    /**
     * @var DictionaryService
     */
    private $dictionaryService;

    /**
     * TranslateService constructor.
     * @param $lang
     */
    public function __construct($lang)
    {
        $this->setLang($lang);
        $this->dictionaryService = new DictionaryService();

    }

    /**
     * 设置语言
     * @param $lang
     */
    function setLang($lang)
    {
        $this->lang = $lang;
    }

    /**
     * 获取翻译
     * @param string $key key
     * @param array $replaces 变量替换
     * @param string $default 默认值
     * @return array
     */
    function getTranslate($key, $replaces = [], $default = '')
    {
        $value = $this->dictionaryService->getValueByKey($key, $this->lang)['data'];
        if (empty($value)) {
            $value = $default;
        }
        if (!empty($replaces)) {
            foreach ($replaces as $k => $v) {
                $value = str_replace('{{' . $k . '}}', $v, $value);
            }
        }

        return self::createReturn(true, $value);
    }
}