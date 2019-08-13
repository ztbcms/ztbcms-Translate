<?php
/**
 * User: jayinton
 * Date: 2019-08-10
 * Time: 15:15
 */

namespace Translate\Controller;


use Common\Controller\AdminBase;
use Translate\Service\DictionaryService;

class ConstantCategoryController extends AdminBase
{
    /**
     * 目录页
     */
    public function index()
    {
        $this->display();
    }

    public function getList()
    {
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
    public function editConstantCategory()
    {
        $this->display();
    }

    /**
     * 获取目录信息
     */
    public function getConstantCategory()
    {
        $id = I('get.id');
        $data = D('Translate/ConstantCategory')->where(['id' => $id])->find();
        $this->ajaxReturn(self::createReturn(true, $data, '获取成功'));
    }

    /**
     * 目录编辑
     */
    public function addEditConstantCategory()
    {
        $post = I('post.');

        $id = $post['id'];
        $pid = $post['pid'];
        $name = $post['name'];
        $key = $post['key'];
        $project_id = $post['project_id'];
        if (!$key) {
            $this->ajaxReturn(self::createReturn(false, null, 'key不能为空'));
        }
        $count = D('Translate/ConstantCategory')->where(['key' => $key, 'pid' => $pid, 'project_id' => $project_id, 'id' => ['NEQ', $id]])->count();
        if ($count) {
            $this->ajaxReturn(self::createReturn(false, null, 'key不能重复'));
        }
        if ($id) {
            //目录上级判断
            //1.不能是自己 2.不能是自己的下级
            if ($pid == $id) {
                $this->ajaxReturn(self::createReturn(false, null, '上级不能选择自己'));
            }
            $arr = $this->getConstantCategoryByPid($id, $project_id);
            $ids = [];
            foreach ($arr as $v) {
                $ids[] = $v['id'];
            }
            if (in_array($pid, $ids)) {
                $this->ajaxReturn(self::createReturn(false, null, '上级不能选择自己的下级'));
            }

            $res = D('Translate/ConstantCategory')->where(['id' => $id])->save([
                'pid' => $pid,
                'name' => $name,
                'key' => $key,
                'project_id' => $project_id
            ]);
        } else {
            $res = D('Translate/ConstantCategory')->add([
                'pid' => $pid,
                'name' => $name,
                'key' => $key,
                'project_id' => $project_id
            ]);
        }
        $this->ajaxReturn(self::createReturn(true, $res, '操作成功'));
    }

    /**
     * 删除目录
     */
    public function delCatalog()
    {
        $id = I('post.id');
        $res = D('Translate/Catalog')->where(['id' => $id])->delete();
        if ($res) {
            D('Translate/Content')->where(['catalog_id' => $id])->delete();
        }
        $this->ajaxReturn(self::createReturn(true, $res, '删除成功'));
    }

    /**
     * 获取目录列表 (树状)
     */
    public $level;

    public function getConstantCategoryBySort()
    {
        $project_id = I('get.project_id');
        $this->level = 0;
        $items = $this->getConstantCategoryByPid(0, $project_id);
        $this->ajaxReturn(self::createReturn(true, $items, '获取成功'));
    }

    public function getConstantCategoryByPid($pid, $project_id)
    {
        $this->level++;
        $list = D('Translate/ConstantCategory')->where(['pid' => $pid, 'project_id' => $project_id])->select() ?: [];
        $tmp = [];
        foreach ($list as $k => &$v) {
            $v['level'] = $this->level;
            $tmp[] = $v;
            $tmp2 = $this->getConstantCategoryByPid($v['id'], $project_id);
            $tmp = array_merge($tmp, $tmp2);
        }
        $this->level--;
        return $tmp;
    }

    /**
     * 导出
     */
    public function exportConstant()
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
        echo json_encode($data, JSON_PRETTY_PRINT);
        exit();
    }
}