<?php
/**
 * User: jayinton
 * Date: 2019-08-10
 * Time: 15:52
 */

namespace Translate\Controller;


use Common\Controller\AdminBase;

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
        foreach ($items as &$item) {
            $item['data'] = null;
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
        $parent_path = D('Translate/ConstantCategory')->where(['id' => $category_id])->getField('key');
        $res = D('Translate/Constant')->add([
            'category_id' => $category_id,
            'key' => $parent_path . '.',
            'key_name' => '',
        ]);
        $this->ajaxReturn(self::createReturn(true, $res, '添加成功'));
    }

    /**
     * 编辑字段
     */
    public function editContent()
    {
        $post = I('post.');
        $id = $post['id'];
        if (!$id) {
            $this->ajaxReturn(self::createReturn(false, null, '参数错误：缺少id'));
        }
        $path = $post['path'];
        if (!$path) {
            $this->ajaxReturn(self::createReturn(false, null, 'key不能为空'));
        }
        $catalog_id = D('Translate/Content')->where(['id' => $id])->getField('catalog_id');
        $count = D('Translate/Content')->where(['path' => $path, 'catalog_id' => $catalog_id, 'id' => ['NEQ', $id]])->count();
        if ($count) {
            $this->ajaxReturn(self::createReturn(false, null, 'key不能重复'));
        }

        $parent_path = D('Translate/Catalog')->where(['id' => $catalog_id])->getField('path');
        $key = substr($path, strlen($parent_path) + 1); //因为有个.

        $data = json_encode($post['data']);
        $res = D('Translate/Content')->where(['id' => $id])->save([
            'key' => $key,
            'path' => $path,
            'data' => $data
        ]);
        $this->ajaxReturn(self::createReturn(true, $res, '修改成功'));
    }

    /**
     * 删除
     */
    public function delContent()
    {
        $id = I('post.id');
        if (!$id) {
            $this->ajaxReturn(self::createReturn(false, null, '参数错误：缺少id'));
        }
        $res = D('Translate/Content')->where(['id' => $id])->delete();
        $this->ajaxReturn(self::createReturn(true, $res, '删除成功'));
    }
}