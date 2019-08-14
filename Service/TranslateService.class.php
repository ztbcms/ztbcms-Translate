<?php
/**
 * User: jayinton
 * Date: 2019-08-14
 * Time: 12:02
 */

namespace Translate\Service;


use System\Service\BaseService;

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

    function setLang($lang)
    {
        $this->lang = $lang;
    }

    /**
     * 获取翻译
     * @param $key
     * @param string $default
     * @param array $replaces
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