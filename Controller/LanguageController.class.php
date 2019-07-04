<?php
/**
 * Created by PhpStorm.
 * User: yezhilie
 * Date: 2018/8/10
 * Time: 10:16
 */

namespace Translate\Controller;

use Common\Controller\AdminBase;

class LanguageController extends AdminBase{

    /**
     * 语言
     */
    public function index(){
        $this->display();
    }

    /**
     * 获取语言列表
     */
    public function getList(){
        $page = I('get.page', 1);
        $limit = I('get.limit', 20);
        $items = D('Translate/Language')->page($page, $limit)->select();
        $total_items = D('Translate/Language')->count();
        $data = [
            'page' => $page,
            'limit' => $limit,
            'items' => $items ?: [],
            'total_items' => $total_items,
            'total_pages' => ceil($total_items/$limit),
        ];
        $this->ajaxReturn(self::createReturn(true, $data));
    }

    public function language(){
        $this->display();
    }

    public function getLanguage(){
        $id = I('get.id');
        $data = D('Translate/Language')->where(['id' => $id])->find();
        $this->ajaxReturn(self::createReturn(true, $data, '获取成功'));
    }

    /**
     * 编辑
     */
    public function addEditLanguage(){
        $post = I('post.');
        $count = D('Translate/Language')->where(['is_default' => 1])->count();
        if($count == 0){
            $post['is_default'] = 1;
        }
        $id = $post['id'];
        if($id){
            $res = D('Translate/Language')->where(['id' => $id])->save($post);
        }else{
            $res = D('Translate/Language')->add($post);
        }
        $this->ajaxReturn(self::createReturn(true, $res, '操作成功'));
    }

    /**
     * 设置默认
     */
    public function setDefault(){
        $id = I('post.id');
        D('Translate/Language')->where(['is_default' => 1])->save(['is_default' => 0]);
        D('Translate/Language')->where(['id' => $id])->save(['is_default' => 1]);
        $this->ajaxReturn(self::createReturn(true, null, '操作成功'));
    }
}