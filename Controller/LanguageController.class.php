<?php
/**
 * Created by PhpStorm.
 * User: yezhilie
 * Date: 2018/8/10
 * Time: 10:16
 */

namespace Translate\Controller;

use Common\Controller\AdminBase;

class LanguageController extends AdminBase
{

    /**
     * 语言列表
     */
    function index()
    {
        $this->display();
    }

    /**
     * 获取语言列表
     */
    function getList()
    {
        $page = I('get.page', 1);
        $limit = I('get.limit', 20);
        $items = D('Translate/Language')->page($page, $limit)->select();
        $total_items = D('Translate/Language')->count();
        $total_pages = ceil($total_items / $limit);
        $this->ajaxReturn(self::createReturnList(true, $items, $page, $limit, $total_items, $total_pages));
    }

    /**
     * 语言详情
     */
    function language()
    {
        $this->display();
    }

    /**
     * 获取语言详情
     */
    function getLanguage()
    {
        $id = I('get.id');
        $data = D('Translate/Language')->where(['id' => $id])->find();
        $this->ajaxReturn(self::createReturn(true, $data, '获取成功'));
    }

    /**
     * 编辑
     */
    function addOrEditLanguage()
    {
        $post = I('post.');
        $count = D('Translate/Language')->where(['is_default' => 1])->count();
        if ($count == 0) {
            $post['is_default'] = 1;
        }
        $id = $post['id'];
        if (empty($post['lang'])) {
            $this->ajaxReturn(self::createReturn(false, null, '语言名称不能为空'));
            return;
        }
        if ($id) {
            $res = D('Translate/Language')->where(['id' => $id])->save($post);
        } else {
            $res = D('Translate/Language')->add($post);
        }
        $this->ajaxReturn(self::createReturn(true, $res, '操作成功'));
    }

    /**
     * 设置默认
     */
    function setDefault()
    {
        $id = I('post.id');
        D('Translate/Language')->where(['is_default' => 1])->save(['is_default' => 0]);
        D('Translate/Language')->where(['id' => $id])->save(['is_default' => 1]);
        $this->ajaxReturn(self::createReturn(true, null, '操作成功'));
    }

    function deleteLanguage()
    {
        $id = I('id');
        $num = D('Translate/Language')->where(['id' => $id])->delete();
        if ($num === false) {
            $this->ajaxReturn(self::createReturn(false, '', '删除失败'));
        } else {
            $this->ajaxReturn(self::createReturn(true, '', '删除成功'));
        }
    }

}