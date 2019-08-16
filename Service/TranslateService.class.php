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
        $this->dictionaryService->setFallbackLang('');

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

    /**
     * 构建key，格式：表+字段+ID
     * @param $table
     * @param $field
     * @param $id
     * @return string
     */
    function _getTableFieldKey($table, $field, $id)
    {
        return $table . '_' . $field . '_' . $id;
    }

    /**
     * 根据表格式(表+字段+ID)获取翻译值
     * @param $table
     * @param $field
     * @param $id
     * @param $lang
     * @return array
     */
    function getTranslateByTableFieldId($table, $field, $id, $lang)
    {
        $key = $this->_getTableFieldKey($table, $field, $id);
        return $this->dictionaryService->getValueByKey($key, $lang);
    }

    /**
     * 设置翻译(根据表格式(表+字段+ID))
     * @param $table
     * @param $field
     * @param $id
     * @param $lang
     * @param $value
     * @return array
     */
    function setTranslateByTableFieldId($table, $field, $id, $lang, $value)
    {
        $key = $this->_getTableFieldKey($table, $field, $id);
        $this->deleteTranslateByTableFieldId($table, $field, $id, $lang);
        return $this->dictionaryService->addDictionaryByKey($key, $lang, $value);
    }

    /**
     * 删除翻译（根据表格式(表+字段+ID)）
     * @param $table
     * @param $field
     * @param $id
     * @param $lang
     * @return array
     */
    function deleteTranslateByTableFieldId($table, $field, $id, $lang)
    {
        $key = $this->_getTableFieldKey($table, $field, $id);
        return $this->dictionaryService->deleteDictionaryByKey($key, $lang);
    }
}