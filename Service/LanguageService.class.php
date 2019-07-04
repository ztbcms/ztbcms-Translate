<?php
/**
 * Created by PhpStorm.
 * User: yezhilie
 * Date: 2018/8/10
 * Time: 18:49
 */

namespace Translate\Service;

use System\Service\BaseService;

class LanguageService extends BaseService{

    const HTTP_LANG = 'Lang';

    //当前的语言
    private static $lang;
    //当前的项目
    private static $project_id;

    static function setLang($language){
        if($language){
            $count = D('Translate/Language')->where(['lang' => $language])->count();
            if($count){
                $lang = $language;
            }else{
                $lang = D('Translate/Language')->where(['is_default' => 1])->getField('lang');
            }
        }else{
            $lang = D('Translate/Language')->where(['is_default' => 1])->getField('lang');
        }
        self::$lang = $lang;
    }

    static function setProjectId($project_id){
        self::$project_id = $project_id;
    }

    /**
     * @param $key
     */
    static function getText($key){
        $language = self::$lang;
        $project_id = self::$project_id;
        $data = D('Translate/Content')->where(['path' => $key, 'project_id' => $project_id])->getField('data');
        $arr = json_decode($data, true);
        if($arr[$language]){
            return $arr[$language];
        }else{
            $default_lang = D('Translate/Language')->where(['is_default' => 1])->getField('lang');
            return $arr[$default_lang];
        }
    }
}