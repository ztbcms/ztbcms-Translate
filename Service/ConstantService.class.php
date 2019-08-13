<?php
/**
 * User: jayinton
 * Date: 2019-08-13
 * Time: 11:15
 */

namespace Translate\Service;


use System\Service\BaseService;

class ConstantService extends BaseService
{
    function getConstantById($constant_id)
    {
        $constant = D('Translate/Constant')->where(['id' => $constant_id])->select();
        if ($constant) {
            return self::createReturn(true, $constant);
        } else {
            return self::createReturn(false, null, '找不到信息');
        }
    }

    /**
     * 删除常量
     * @param $constant_id
     * @return array|mixed
     */
    public function delConstant($constant_id)
    {
        if (!$constant_id) {
            return self::createReturn(false, null, '参数错误：缺少id');
        }

        $res = $this->getConstantById($constant_id);
        if (!$res['status']) {
            return $res;
        }
        $constant = $res['data'];
        M()->startTrans();
        $res = D('Translate/Constant')->where(['id' => $constant_id])->delete();
        if (!$res) {
            return self::createReturn(false, null, '操作失败');
        }

        $DictionaryService = new DictionaryService();
        $res = $DictionaryService->deleteDictionaryByKey($constant['key']);
        if (!$res['status']) {
            return self::createReturn(false, null, '操作失败');
        }

        M()->commit();
        return self::createReturn(true, null, '操作成功');

    }
}