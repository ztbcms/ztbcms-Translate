<?php
/**
 * User: jayinton
 * Date: 2019-08-13
 * Time: 16:55
 */

namespace Translate\Controller;


use Common\Controller\Base;
use Think\Controller;
use Translate\Service\LanguageService;

class PublicController extends Base
{

    /**
     * 获取当前的语言列表
     */
    function getLanguageList(){
        $res = LanguageService::getAvailableLang();
        $this->ajaxReturn($res);
    }

    /**
     * 根据给定的语言获取全部的翻译
     */
    function getAllDictionaryByLang()
    {
        //是否指定语言
        $lang = I('get.lang');
        if (empty($lang)) {
            $this->ajaxReturn(self::createReturn(false, null, '请指定语言'));
        }
        $result = [];
        $lists = D('Translate/Dictionary')->where(['lang' => ['EQ', $lang]])->field('key,value')->select();//
        if (!empty($lists)) {
            foreach ($lists as $i => $item) {
                $result[$item['key']] = $item['value'];
            }
        }
        $this->ajaxReturn(self::createReturn(true, $result));
    }
}