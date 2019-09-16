<?php

namespace Translate\Controller;

use Common\Controller\AdminBase;
use Translate\Service\LanguageService;

class ImportTranslateController extends AdminBase
{
    public function index()
    {
        $this->display();
    }

    public function getSelectList()
    {
        $page = I('get.page', 1);
        //$limit = I('get.limit', 20);
        $projects = D('Translate/Project')->page($page)->select();
        foreach ($projects as &$v) {
            $project_id = $v['id'];
            $v['constant_category'] = D('Translate/ConstantCategory')->where(['pid' => 0, 'project_id' => $project_id])->select() ?: [];
        }
        $data['projects'] = $projects;
        $data['language'] = LanguageService::getAvailableLang()['data'];
        $this->ajaxReturn(self::createReturn(true, $data));
    }

    public function importTranslate()
    {
        $value = I('value');
        //json数据解码转化
        $value = preg_replace('/[\x00-\x1F\x80-\x9F]/u', '', trim($value));
        $value = json_decode(htmlspecialchars_decode($value), true);
        if (!$value) {
            $this->ajaxReturn(self::createReturn(false, '', 'json格式异常'));
        }
        $project_id = I('project');
        $lang = I('lang');
        $constant_category = I('constant_category');
        if (empty($project_id) || empty($lang) || empty($constant_category)) {
            $this->ajaxReturn(self::createReturn(false, '', '请完善参数'));
        }
        $update_num = 0;
        $add_num = 0;
        $error_num = 0;
        $total_num = 0;
        $error_info = [];
        foreach ($value as $k => $v) {
            //检查该字段是否存在
            $total_num++;
            $is_exist = D('Translate/Constant')->where(['key' => $k, 'category_id' => $constant_category])->find();
            //更新
            if ($is_exist) {
                //更新constant
                $insert_array['value'] = $v;
                $num = 1;
                if ($lang == "cn") {
                    $insert_array['key_name'] = $v;
                    $num = D('Translate/Constant')->where(['key' => ['EQ', $k], 'category_id' => $constant_category])->save($insert_array);
                }
                //成功或失败
                if ($num) {
                    $update_num++;
                } else if ($num === false) {
                    $error_num++;
                    $tmp['key'] = $k;
                    $tmp['value'] = $v;
                    $error_info[] = $tmp;
                }
            } //添加
            else {
                $num = D('Translate/Constant')->add([
                    'key' => $k,
                    'value' => $v,
                    'category_id' => $constant_category,
                    'key_name' => $v
                ]);
                if ($num) {
                    $add_num++;
                    //$this->ajaxReturn(self::createReturn(true, '', "ss"));
                } else {
                    $error_num++;
                    $tmp['key'] = $k;
                    $tmp['value'] = $v;
                    $error_info[] = $tmp;
                }
            }
            //更新dictionary
            if (!empty($lang)) {
                $dictionary = D('Translate/Dictionary')->where(['key' => $k, 'lang' => $lang])->find();
                if ($dictionary) {
                    D('Translate/Dictionary')->where(['dictionary_id' => $dictionary['dictionary_id']])->save([
                        'value' => $v
                    ]);
                } else {
                    D('Translate/Dictionary')->add([
                        'key' => $k,
                        'value' => $v,
                        'lang' => $lang
                    ]);
                }
            }
        }
        $data['update_num'] = $update_num;
        $data['add_num'] = $add_num;
        $data['error_num'] = $error_num;
        $msg = '更新条数：' . $update_num . "添加条数：" . $add_num . "错误条数：" . $error_num;
        $this->ajaxReturn(self::createReturn(true, $error_info, $msg));
    }
}