<?php
/**
 * User: jayinton
 * Date: 2019-08-10
 * Time: 15:52
 */

namespace Translate\Controller;


use Common\Controller\AdminBase;
use Translate\Service\ConstantService;
use Translate\Service\DictionaryService;

class ConstantController extends AdminBase
{
    /**
     * 字段显示
     */
    public function index()
    {
        $langList = D('Translate/Language')->select();
        $this->assign('langList', $langList);
        $this->display();
    }

    public function getList()
    {
        $category_id = I('get.category_id');
        $items = D('Translate/Constant')->where(['category_id' => $category_id])->order('id DESC')->select();
        $DictionaryService = new DictionaryService();
        foreach ($items as &$item) {
            $item['data'] = null;
            $item['dictionary'] = $DictionaryService->getAllValueByKeyWithFormat($item['key'])['data'];
        }
        $data = [
            'items' => $items ?: []
        ];
        $this->ajaxReturn(self::createReturn(true, $data));
    }

    /**
     * 新增字段
     */
    public function addConstant()
    {
        $category_id = I('post.category_id');
        $key = D('Translate/ConstantCategory')->where(['id' => $category_id])->getField('key');
        $key = $key . '.';
        //检测key是否已占用
        $is_used = D('Translate/Constant')->where(['key' => ['EQ', $key]])->count() > 0;
        if ($is_used) {
            $this->ajaxReturn(self::createReturn(false, null, '参数错误：Key 已被使用'));
            return;
        }
        $res = D('Translate/Constant')->add([
            'category_id' => $category_id,
            'key' => $key . '.',
            'key_name' => '',
        ]);
        $this->ajaxReturn(self::createReturn(true, $res, '添加成功'));
    }

    /**
     * 编辑字段
     */
    public function editConstant()
    {
        $constant = I('post.');
        $id = $constant['id'];
        $dictionary = $constant['dictionary'];
        unset($constant['dictionary']);
        if (!$id) {
            $this->ajaxReturn(self::createReturn(false, null, '参数错误：缺少id'));
            return;
        }
        //检测key是否已占用
        $is_used = D('Translate/Constant')->where(['id' => ['NEQ', $id], 'key' => ['EQ', $constant['key']]])->count() > 0;
        if ($is_used) {
            $this->ajaxReturn(self::createReturn(false, null, '参数错误：Key 已被使用'));
            return;
        }

        //更新constant
        D('Translate/Constant')->where(['id' => ['EQ', $id]])->save($constant);

        //更新dictionary
        if (!empty($dictionary)) {
            foreach ($dictionary as $index => $dict) {
                $dict['key'] = $constant['key'];
                if (empty($dict['dictionary_id'])) {
                    unset($dict['dictionary_id']);
                    D('Translate/Dictionary')->add($dict);
                } else {
                    D('Translate/Dictionary')->where(['dictionary_id' => ['EQ', $dict['dictionary_id']]])->save($dict);
                }
            }
        }

        $this->ajaxReturn(self::createReturn(true, null, '操作成功'));
    }

    /**
     * 删除
     */
    public function delConstant()
    {
        $id = I('post.id');
        if (!$id) {
            $this->ajaxReturn(self::createReturn(false, null, '参数错误：缺少id'));
        }
        $ConstantService = new ConstantService();
        $res = $ConstantService->delConstant($id);
        $this->ajaxReturn($res);
    }
}