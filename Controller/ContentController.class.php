<?php
/**
 * Created by PhpStorm.
 * User: yezhilie
 * Date: 2018/8/10
 * Time: 10:16
 */

namespace Translate\Controller;

use Common\Controller\AdminBase;

class ContentController extends AdminBase{

    /**
     * 字段显示
     */
    public function index(){
        $langList = D('Translate/Language')->select();
        $this->assign('langList', $langList);
        $this->display();
    }

    public function getList(){
        $catalog_id = I('get.catalog_id');
        $items = D('Translate/Content')->where(['catalog_id' => $catalog_id])->order('path ASC,id DESC')->select();
        foreach($items as &$item){
            $item['data'] = json_decode($item['data']) ?: (object)null;
        }
        $data = [
            'items' => $items ?: []
        ];
        $this->ajaxReturn(self::createReturn(true, $data));
    }

    /**
     * 新增字段
     */
    public function addContent(){
        $catalog_id = I('post.catalog_id');
        $parent_path = D('Translate/Catalog')->where(['id' => $catalog_id])->getField('path');
        $path = $parent_path.'.';
        $res = D('Translate/Content')->add([
            'catalog_id' => $catalog_id,
            'path' => $path
        ]);
        $this->ajaxReturn(self::createReturn(true, $res, '添加成功'));
    }

    /**
     * 编辑字段
     */
    public function editContent(){
        $post = I('post.');
        $id = $post['id'];
        if(!$id){
            $this->ajaxReturn(self::createReturn(false, null, '参数错误：缺少id'));
        }
        $path = $post['path'];
        if(!$path){
            $this->ajaxReturn(self::createReturn(false, null, 'key不能为空'));
        }
        $catalog_id = D('Translate/Content')->where(['id' => $id])->getField('catalog_id');
        $count = D('Translate/Content')->where(['path' => $path, 'catalog_id' => $catalog_id, 'id' => ['NEQ', $id]])->count();
        if($count){
            $this->ajaxReturn(self::createReturn(false, null, 'key不能重复'));
        }

        $parent_path = D('Translate/Catalog')->where(['id' => $catalog_id])->getField('path');
        $key = substr($path, strlen($parent_path)+1 ); //因为有个.

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
    public function delContent(){
        $id = I('post.id');
        if(!$id){
            $this->ajaxReturn(self::createReturn(false, null, '参数错误：缺少id'));
        }
        $res = D('Translate/Content')->where(['id' => $id])->delete();
        $this->ajaxReturn(self::createReturn(true, $res, '删除成功'));
    }
}