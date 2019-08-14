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
     * 获取可用的语言列表
     * @return array
     */
    static function getAvailableLang()
    {
        $langs = D('Translate/Language')->order(['is_default' => 'desc'])->select();
        if (empty($langs)) {
            $langs = [];
        }
        return self::createReturn(true, $langs);

    }
}