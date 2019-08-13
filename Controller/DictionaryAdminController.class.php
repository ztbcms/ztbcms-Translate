<?php
/**
 * User: jayinton
 * Date: 2019-08-13
 * Time: 12:18
 */

namespace Translate\Controller;


use Common\Controller\AdminBase;
use Translate\Service\DictionaryService;

class DictionaryAdminController extends AdminBase
{
    function dictionaryList()
    {
        $this->display();
    }

    /**
     * 列表页
     */
    function getList()
    {
        $page = I('page', 1);
        $limit = I('limit', 20);
        $key = I('key');
        $lang = I('lang');
        $where = [];
        if (!empty($key)) {
            $where['key'] = ['LIKE', '%' . $key . '%'];
        }
        if (!empty($lang)) {
            $where['lang'] = ['LIKE', '%' . $lang . '%'];
        }

        $order = 'dictionary_id DESC';
        $lists = D('Translate/Dictionary')->where($where)->order($order)->page($page, $limit)->select();
        $total = D('Translate/Dictionary')->where($where)->count();
        $lists = $lists ? $lists : [];
        $total_page_amount = ceil($total / $limit);

        $this->ajaxReturn(self::createReturnList(true, $lists, $page, $limit, $total, $total_page_amount));
    }

    /**
     * 新增/编辑字典
     */
    function editDictionary()
    {
        $dictionary_id = I('get.dictionary_id');
        if (!empty($dictionary_id)) {
            $DictionaryService = new DictionaryService();
            $res = $DictionaryService->getDictionary($dictionary_id)['data'];
            $this->assign('data', $res);
        }
        $this->display();
    }

    /**
     * 新增/编辑字典
     * @return void
     */
    function doEditDictionary()
    {
        $dictionary_id = I('post.dictionary_id');
        $key = I('post.key');
        $value = I('post.value');
        $lang = I('post.lang');

        if (empty($lang)) {
            $this->ajaxReturn(self::createReturn(false, null, '请指定语言'));
            return;
        }

        $DictionaryService = new DictionaryService();
        if (empty($dictionary_id)) {
            //检测key是否已占用
            $is_used = D('Translate/Dictionary')->where(['key' => ['EQ', $key], 'lang' => ['EQ', $lang]])->count() > 0;
            if ($is_used) {
                $this->ajaxReturn(self::createReturn(false, null, '参数错误：Key 已被使用'));
                return;
            }

            $res = $DictionaryService->addDictionaryByKey($key, $lang, $value);
            $this->ajaxReturn($res);
        } else {
            //检测key是否已占用
            $is_used = D('Translate/Dictionary')->where(['key' => ['EQ', $key], 'lang' => ['EQ', $lang], 'dictionary_id' => ['NEQ', $dictionary_id],])->count() > 0;
            if ($is_used) {
                $this->ajaxReturn(self::createReturn(false, null, '参数错误：Key 已被使用'));
                return;
            }

            $res = D('Translate/Dictionary')->where(['dictionary_id' => $dictionary_id])->save([
                'key' => $key,
                'value' => $value,
                'lang' => $lang,
            ]);
            if ($res) {
                $this->ajaxReturn(self::createReturn(true, null, '操作成功'));
            } else {
                $this->ajaxReturn(self::createReturn(false, null, '操作失败'));
            }
        }
    }

    /**
     * 删除操作
     */
    function deleteDictionary()
    {
        $dictionary_id = I('post.dictionary_id');
        $res = D('Translate/Dictionary')->where(['dictionary_id' => $dictionary_id])->delete();
        if ($res) {
            $this->ajaxReturn(self::createReturn(true, null, '操作成功'));
        }
        $this->ajaxReturn(self::createReturn(false, null, '操作失败'));
    }
}