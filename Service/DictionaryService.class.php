<?php
/**
 * User: jayinton
 * Date: 2019-08-10
 * Time: 13:51
 */

namespace Translate\Service;


use System\Service\BaseService;
use Translate\Model\DictionaryModel;

class DictionaryService extends BaseService
{
    /**
     * 兜底语言
     * @var string
     */
    private $fallbackLang = '中文';

    function getDictionary($dictionary_id)
    {
        $dictionary = D('Translate/Dictionary')->where(['dictionary_id' => $dictionary_id])->find();
        return self::createReturn(true, $dictionary);
    }

    /**
     * @return string
     */
    function getFallbackLang()
    {
        return $this->fallbackLang;
    }

    /**
     * @param string $fallbackLang
     */
    function setFallbackLang($fallbackLang)
    {
        $this->fallbackLang = $fallbackLang;
    }


    function getAllValueByKey($key)
    {
        $values = D('Translate/Dictionary')->where(['key' => $key])->select();
        if (empty($values)) {
            $values = [];
        }

        return self::createReturn(true, $values);
    }

    function getAllValueByKeyWithFormat($key)
    {
        $values = $this->getAllValueByKey($key)['data'];

        //格式化返回
        $values = $this->formatValueByCurrentAvailableLang($key, $values)['data'];
        return self::createReturn(true, $values);
    }


    /**
     *
     * @param $key
     * @param $dictionary_list array list of Translate/Dictionary
     * @return array
     */
    function formatValueByCurrentAvailableLang($key, $dictionary_list)
    {
        $langs = LanguageService::getAvailableLang()['data'];
        $result = [];
        foreach ($langs as $index => $lang) {
            $item = [
                'dictionary_id' => 0,
                'key' => $key,
                'value' => '',
                'lang' => $lang['lang'],
                'lang_name' => $lang['lang_name'],
            ];
            //查找是否有默认的语言
            $lang_dictionary = null;
            foreach ($dictionary_list as $i => $dictionary) {
                if ($dictionary['lang'] == $lang['lang']) {
                    $lang_dictionary = $dictionary;
                    break;
                }
            }
            if (!empty($lang_dictionary)) {
                $item['dictionary_id'] = $lang_dictionary['dictionary_id'];
                $item['value'] = $lang_dictionary['value'];
            }

            $result [] = $item;
        }

        return self::createReturn(true, $result);
    }


    /**
     * 获取字典值
     *
     * @param $key
     * @param string $lang
     * @return array
     */
    function getValueByKey($key, $lang)
    {
        $dict = D('Translate/Dictionary')->where(['lang' => $lang, 'key' => $key])->find();
        if ($dict) {
            return self::createReturn(true, $dict['value']);
        } else {
            if ($lang == $this->getFallbackLang()) {
                return self::createReturn(true, '');;
            } else {
                return $this->getValueByKey($key, $this->getFallbackLang());
            }
        }
    }

    function addDictionaryByKey($key, $lang, $value)
    {
        return self::createDictionary([
            'key' => $key,
            'lang' => $lang,
            'value' => $value,
        ]);
    }



    static function delDictionary($key)
    {
        D('Translate/Dictionary')->where(['key' => $key])->delete();
    }

    static function delDictionaryByLang($lang)
    {
        D('Translate/Dictionary')->where(['lang' => $lang])->delete();
    }

    /**
     * 根据ID获取字典
     *
     * @param $id
     * @return array
     */
    static function getDictionaryById($id)
    {
        return self::find('Dictionary', ['id' => $id]);
    }


    /**
     * 获取字典列表
     *
     * @param array $where
     * @param string $order
     * @param int $page
     * @param int $limit
     * @param bool $isRelation
     * @return array
     */
    static function getDictionaryList($where = [], $order = '', $page = 1, $limit = 20, $isRelation = false)
    {
        return self::select('Dictionary', $where, $order, $page, $limit, $isRelation);
    }

    /**
     * 添加字典
     *
     * @param array $data
     * @return array
     */
    static function createDictionary($data = [])
    {
        return self::create('Translate/Dictionary', $data);
    }

    /**
     * 更新字典
     *
     * @param       $id
     * @param array $data
     * @return array
     */
    static function updateDictionary($id, $data = [])
    {
        return self::update('Dictionary', ['id' => $id], $data);
    }

    /**
     * 删除字典
     *
     * @param $id
     * @return array
     */
    function deleteDictionaryById($id)
    {
        return self::delete('Translate/Dictionary', ['id' => $id]);
    }

    /**
     * 删除
     * @param $key
     * @param string $lang
     * @return array
     */
    function deleteDictionaryByKey($key, $lang = '')
    {
        if (empty($lang)) {
            return self::delete('Translate/Dictionary', ['key' => $key]);
        } else {
            return self::delete('Translate/Dictionary', ['key' => $key, 'lang' => $lang]);
        }

    }

}