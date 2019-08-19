<?php
/**
 * User: jayinton
 * Date: 2019-08-19
 * Time: 10:38
 */

namespace Translate\Controller;


use Common\Controller\AdminBase;
use Translate\Service\DictionaryService;

/**
 * 导出
 * Class ExportController
 * @package Translate\Controller
 */
class ExportController extends AdminBase
{

    /**
     * 根据常量分类导出
     */
    public function exportConstantByCategory()
    {
        $category_id = I('get.category_id');
        $given_lang = I('get.lang');
        if (!empty($given_lang)) {
            $langList [] = $given_lang;
        } else {
            $langList = D('Translate/Language')->getField('lang', true);
        }
        $constantList = D('Translate/Constant')->where(['category_id' => $category_id])->order('id DESC')->select();
        $data = [];
        $DictionaryService = new DictionaryService();
        foreach ($langList as $lang) {
            $tmp = [];
            foreach ($constantList as $constant) {
                $key = $constant['key'];
                $value = $DictionaryService->getValueByKey($key, $lang)['data'];
                $tmp[$key] = $value;
            }
            $data[$lang] = $tmp;
        }

        $this->ajaxReturn($data);
    }

    /**
     * 根据项目导出
     */
    public function exportConstantByProject()
    {
        $project_id = I('get.project_id');
        $given_lang = I('get.lang');
        $where = [];
        if (!empty($given_lang)) {
            $langList = explode(',', $given_lang);
        } else {
            $langList = D('Translate/Language')->getField('lang', true);
        }

        if(!empty($project_id)){
            $category_ids = D('Translate/ConstantCategory')->where(['project_id' => $project_id])->getField('id', true);
            $where[]= ['category_id' => ['IN', $category_ids]];
        }

        $constantList = D('Translate/Constant')->where($where)->order('id DESC')->select();
        $data = [];
        $DictionaryService = new DictionaryService();
        foreach ($langList as $lang) {
            $tmp = [];
            foreach ($constantList as $constant) {
                $key = $constant['key'];
                $value = $DictionaryService->getValueByKey($key, $lang)['data'];
                $tmp[$key] = $value;
            }
            $data[$lang] = $tmp;
        }

        $this->ajaxReturn($data);
    }
}