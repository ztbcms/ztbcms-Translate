<?php
/**
 * Created by PhpStorm.
 * User: yezhilie
 * Date: 2018/8/10
 * Time: 10:16
 */

namespace Translate\Controller;

use Common\Controller\AdminBase;

class CatalogController extends AdminBase{

    /**
     * 目录页
     */
    public function index(){
        $this->display();
    }

    public function getList(){
        $project_id = I('get.project_id');
        $where = [
            'project_id' => $project_id
        ];
        $items = D('Translate/Catalog')->where($where)->select();
        $data = [
            'items' => $items ?: []
        ];
        $this->ajaxReturn(self::createReturn(true, $data));
    }

    /**
     * 目录编辑页
     */
    public function catalog(){
        $this->display();
    }

    /**
     * 获取目录信息
     */
    public function getCatalog(){
        $id = I('get.id');
        $data = D('Translate/Catalog')->where(['id' => $id])->find();
        $this->ajaxReturn(self::createReturn(true, $data, '获取成功'));
    }

    /**
     * 目录编辑
     */
    public function addEditCatalog(){
        $post = I('post.');

        $id = $post['id'];
        $pid = $post['pid'];
        $name = $post['name'];
        $key = $post['key'];
        $project_id = $post['project_id'];
        if(!$key){
            $this->ajaxReturn(self::createReturn(false, null, 'key不能为空'));
        }
        $count = D('Translate/Catalog')->where(['key' => $key, 'pid' => $pid, 'project_id' => $project_id, 'id' => ['NEQ', $id]])->count();
        if($count){
            $this->ajaxReturn(self::createReturn(false, null, 'key不能重复'));
        }
        if($pid){
            $parent_path = D('Translate/Catalog')->where(['id' => $pid])->getField('path').'.';
        }else{
            $parent_path = '';
        }
        $path = $parent_path.$key;
        if($id){

            //目录上级判断
            //1.不能是自己 2.不能是自己的下级
            if($pid == $id){
                $this->ajaxReturn(self::createReturn(false, null, '上级不能选择自己'));
            }
            $arr = $this->getCatalogByPid($id, $project_id);
            $ids = [];
            foreach($arr as $v){
                $ids[] = $v['id'];
            }
            if(in_array($pid, $ids)){
                $this->ajaxReturn(self::createReturn(false, null, '上级不能选择自己的下级'));
            }

            $res = D('Translate/Catalog')->where(['id' => $id])->save([
                'pid' => $pid,
                'name' => $name,
                'key' => $key,
                'path' => $path,
                'project_id' => $project_id
            ]);
            D('Translate/Content')->where(['catalog_id' => $id])->save(['path' => ['exp', 'concat("'.$path.'.", `key`)']]);
        }else{
            $res = D('Translate/Catalog')->add([
                'pid' => $pid,
                'name' => $name,
                'key' => $key,
                'path' => $path,
                'project_id' => $project_id
            ]);
        }
        $this->ajaxReturn(self::createReturn(true, $res, '操作成功'));
    }

    /**
     * 删除目录
     */
    public function delCatalog(){
        $id = I('post.id');
        $res = D('Translate/Catalog')->where(['id' => $id])->delete();
        if($res){
            D('Translate/Content')->where(['catalog_id' => $id])->delete();
        }
        $this->ajaxReturn(self::createReturn(true, $res, '删除成功'));
    }

    /**
     * 获取目录列表 (树状)
     */
    public $level;
    public function getCatalogBySort(){
        $project_id = I('get.project_id');
        $this->level = 0;
        $items = $this->getCatalogByPid(0, $project_id);
        $this->ajaxReturn(self::createReturn(true, $items, '获取成功'));
    }

    public function getCatalogByPid($pid, $project_id){
        $this->level++;
        $list = D('Translate/Catalog')->where(['pid' => $pid, 'project_id' => $project_id])->select() ?: [];
        $tmp = [];
        foreach($list as $k => &$v){
            $v['level'] = $this->level;
            $tmp[] = $v;
            $tmp2 = $this->getCatalogByPid($v['id'], $project_id);
            $tmp = array_merge($tmp, $tmp2);
        }
        $this->level--;
        return $tmp;
    }

    /**
     * 导出
     */
    public function exportContent(){
        $id = I('get.id');
        $langList = D('Translate/Language')->getField('lang', true);
        $default_lang = D('Translate/Language')->where(['is_default' => 1])->getField('lang');
        $contentList = D('Translate/Content')->where(['catalog_id' => $id])->order('path ASC,id DESC')->select();
        $data = [];
        foreach($langList as $lang){
            $tmp = [];
            foreach($contentList as $content){
                $k = $content['path'];
                $v = json_decode($content['data'], true);
                if($v){
                    if($v[$lang]){
                        $tmp[$k] = $v[$lang];
                    }else{
                        $tmp[$k] = $v[$default_lang];
                    }
                }
            }
            $data[$lang] = $tmp;
        }
        echo json_encode($data, JSON_PRETTY_PRINT);exit();
    }
}