<?php
/**
 * Created by PhpStorm.
 * User: yezhilie
 * Date: 2018/8/10
 * Time: 18:49
 */

namespace Translate\Service;

use System\Service\BaseService;

/**
 * 语言管理
 * Class LanguageService
 * @package Translate\Service
 */
class LanguageService extends BaseService
{
    //当前的语言
    private static $lang;
    //当前的项目
    private static $project_id;

    /**
     * 设置当前语言
     * @param $language
     */
    static function setLang($language)
    {
        if ($language) {
            $count = D('Translate/Language')->where(['lang' => $language])->count();
            if ($count) {
                $lang = $language;
            } else {
                $lang = D('Translate/Language')->where(['is_default' => 1])->getField('lang');
            }
        } else {
            $lang = D('Translate/Language')->where(['is_default' => 1])->getField('lang');
        }
        self::$lang = $lang;
    }

    static function setProjectId($project_id)
    {
        self::$project_id = $project_id;
    }

    /**
     * 获取翻译
     * @param $key
     * @return string 翻译
     */
    static function getText($key)
    {
        $language = self::$lang;
        $project_id = self::$project_id;
        $data = D('Translate/Content')->where(['path' => $key, 'project_id' => $project_id])->getField('data');
        $arr = json_decode($data, true);
        if ($arr[$language]) {
            return $arr[$language];
        } else {
            $default_lang = D('Translate/Language')->where(['is_default' => 1])->getField('lang');
            if (!empty($default_lang)) {
                return $arr[$default_lang];
            }

            return '';
        }
    }

    static function getAvailableLang()
    {
        $langs = D('Translate/Language')->order(['is_default' => 'desc'])->select();
        if (empty($langs)) {
            $langs = [];
        }
        return self::createReturn(true, $langs);

    }
}