<?php
/**
 * User: jayinton
 * Date: 2019-08-09
 * Time: 18:31
 */

namespace Translate\Controller;


use Common\Controller\AdminBase;
use Translate\Service\TranslateService;

/**
 *
 * @package Translate\Controller
 */
class DemoController extends AdminBase
{
    protected $lang = '中文';

    /**
     * @var TranslateService
     */
    protected $translateService;

    protected function _initialize()
    {
        parent::_initialize();

        if (!empty($_SERVER['HTTP_LANG'])) {
            $this->lang = $_SERVER['HTTP_LANG'];
        }

        $this->translateService = new TranslateService($this->lang);
    }

    /**
     * 语言切换示例
     */
    function lang_switch()
    {
        $this->display();
    }

    /**
     * 实时切换实例
     */
    function fetch_lang_switch(){
        $this->display();
    }

    /**
     * 使用 TranslateService 示例
     */
    function doRequestAdmin()
    {
        $key = 'demo_msg';
        $replaces = ['balance' => 1, 'integral' => 2];
        $default = '余额: {{balance}} 积分:{{integral}}';

        $msg = $this->translateService->getTranslate($key, $replaces, $default)['data'];
        $this->ajaxReturn(self::createReturn(true, $msg, $msg));
    }
}