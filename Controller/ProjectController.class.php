<?php
/**
 * Created by PhpStorm.
 * User: yezhilie
 * Date: 2018/8/10
 * Time: 10:16
 */

namespace Translate\Controller;

use Common\Controller\AdminBase;

class ProjectController extends AdminBase
{

    /**
     * 项目
     */
    public function index()
    {
        $this->display();
    }

    /**
     * 项目列表
     */
    public function getList()
    {
        $page = I('get.page', 1);
        $limit = I('get.limit', 20);
        $items = D('Translate/Project')->page($page, $limit)->select();
        $total_items = D('Translate/Project')->count();
        $data = [
            'page' => $page,
            'limit' => $limit,
            'items' => $items ?: [],
            'total_items' => $total_items,
            'total_pages' => ceil($total_items / $limit),
        ];
        $this->ajaxReturn(self::createReturn(true, $data));
    }

    /**
     * 项目列表
     */
    public function project()
    {
        $this->display();
    }

    public function getProject()
    {
        $id = I('get.id');
        $data = D('Translate/Project')->where(['id' => $id])->find();
        $this->ajaxReturn(self::createReturn(true, $data, '获取成功'));
    }

    /**
     * 编辑
     */
    public function addOrEditProject()
    {
        $post = I('post.');
        $id = $post['id'];
        if (empty($post['name'])) {
            $this->ajaxReturn(self::createReturn(false, null, '名称不能为空'));
            return;
        }
        if ($id) {
            $res = D('Translate/Project')->where(['id' => $id])->save($post);
        } else {
            $res = D('Translate/Project')->add($post);
        }
        $this->ajaxReturn(self::createReturn(true, $res, '操作成功'));
    }
}