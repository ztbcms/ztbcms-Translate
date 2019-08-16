<?php
/**
 * User: jayinton
 * Date: 2019-08-09
 * Time: 18:31
 */

namespace Translate\Controller;


use Common\Controller\AdminBase;
use Translate\Model\DemoCarModel;
use Translate\Service\LanguageService;
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
    function fetch_lang_switch()
    {
        $this->display();
    }

    function demo_edit_car()
    {
        $langList = LanguageService::getAvailableLang()['data'];
        $this->assign('data', [
            'langList' => $langList
        ]);
        $this->display();
    }

    function demo_car_list()
    {
        $this->display();
    }

    /**
     * 添加或编辑车辆
     */
    function doAddEditCar()
    {
        $form = I('post.');

        $id = $form['id'];
        unset($form['id']);
        //需要多语言处理的字段
        $translate_fields = ['model', 'description'];
        $translate_field_values = [];
        //构建默认语言的 form
        foreach ($translate_fields as $field) {
            $translate_field_values[$field] = $form[$field];
            $form[$field] = isset($form[$field][LanguageService::DEFAULT_LANG]) ? $form[$field][LanguageService::DEFAULT_LANG] : '';//默认中文
        }

        if ($id) {
            $form['update_time'] = time();
            $res = D('Translate/DemoCar')->where(['id' => $id])->save($form);
        } else {
            $form['input_time'] = time();
            $form['update_time'] = time();
            $res = D('Translate/DemoCar')->add($form);
            $id = $res;
        }

        $tableName = (new DemoCarModel())->getTableName();
        if (!$res) {
            $this->ajaxReturn(self::createReturn(false, null, '操作失败'));
            return;
        }
        foreach ($translate_field_values as $field => $field_value) {
            foreach ($field_value as $lang => $value) {
                $this->translateService->setTranslateByTableFieldId($tableName, $field, $id, $lang, $value);
            }
        }

        $this->ajaxReturn(self::createReturn(true, null, '操作成功'));
    }

    /**
     * 获取车辆详情
     * @return void
     */
    function getCarDetail(){
        $id = I('get.id');
        $res = D('Translate/DemoCar')->where(['id' => $id])->find();
        if(!$res){
            $this->ajaxReturn(self::createReturn(false, null ,'找不到信息'));
        }

        $LangList = LanguageService::getAvailableLang()['data'];
        //需要多语言处理的字段
        $translate_fiselds = ['model','description'];
        $tableName = (new DemoCarModel())->getTableName();
        foreach($translate_fiselds as $field){
            $dict = [];
            foreach($LangList as $langInfo){
                $lang = $langInfo['lang'];
                $value = $this->translateService->getTranslateByTableFieldId($tableName, $field, $id, $lang)['data'];
                //文本编辑器的字段需要转码
                if($field == 'description'){
                    $value = htmlspecialchars_decode($value);
                }
                $dict[$lang] = $value;
            }
            $res[$field] = $dict;
        }
        $this->ajaxReturn(self::createReturn(true, $res));
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