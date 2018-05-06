<?php
/**
 * Created by PhpStorm.
 * User: MrN1u
 * Date: 5/6/18
 * Time: 3:29 PM
 */

namespace app\common\controller;


use think\exception\DbException;
use app\code\model\InvitationCodeModel;

class SJPrivateController extends SJController {

    protected function _initialize() {
        if (empty($this->code)) {
            if ($this->errorMsg) {
                $this->jError($this->errorMsg, ['code' => 10001, 'msg' => '登录失败']);
            } else {
                $this->jError('', ['code' => 10001, 'msg' => '登录失败']);
            }
        }
    }

    /**
     * 代码验证
     */
    protected function _codeValidation() {
        $code = $this->request->header('SJ-Code');

        if (empty($code)) {
            $this->errorMsg = "传入参数不正确";
            return;
        }

        // 从数据库中获取邀请码
        try {
            $mCode = InvitationCodeModel::get($code);
            if (empty($mCode)) {
                $this->errorMsg = "邀请码不存在";
                return;
            }
            if ($mCode['is_confirm'] == 1) {
                $this->errorMsg = "邀请码已过期";
                return;
            }
            $this->code = $code;
        } catch (DbException $e) {
            $this->jError($e->getMessage());
        }

    }

}