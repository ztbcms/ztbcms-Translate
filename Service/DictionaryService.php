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

    /**
     * @return string
     */
    public function getFallbackLang()
    {
        return $this->fallbackLang;
    }

    /**
     * @param string $fallbackLang
     */
    public function setFallbackLang($fallbackLang)
    {
        $this->fallbackLang = $fallbackLang;
    }

    /**
     * 获取字典值
     *
     * @param $key
     * @param string $lang
     * @return string
     */
    function getValueByKey($key, $lang)
    {
        $value = M('Dictionary')->where(['lang' => $lang, 'key' => $key])->getField('value');
        if ($value) {
            return $value;
        } else {
            if ($lang == $this->getFallbackLang()) {
                return '';
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

    function addDictionaryByTableFieldId($table, $field, $id, $lang, $value)
    {
        $key = $table . '_' . $field . '_' . $id;
        return $this->addDictionaryByKey($key, $lang, $value);
    }

    static function delDictionary($key)
    {
        M('Dictionary')->where(['key' => $key])->delete();
    }

    static function delDictionaryByLang($lang)
    {
        M('Dictionary')->where(['lang' => $lang])->delete();
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
        return self::create('Dictionary', $data);
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
    static function deleteDictionaryById($id)
    {
        return self::delete('Dictionary', ['id' => $id]);
    }
}