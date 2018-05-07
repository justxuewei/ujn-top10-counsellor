<?php
/**
 * Created by PhpStorm.
 * User: MrN1u
 * Date: 5/6/18
 * Time: 3:29 PM
 */

namespace app\common\controller;


use think\Config;
use think\exception\DbException;
use app\code\model\InvitationCode;

class SJPrivateController extends SJController {

    protected $code;
    protected $statusCode = 10001;

    protected function _initialize() {
        $startAt = strtotime(Config::get('start_at'));
        if (time() < $startAt) $this->jError('系统未开放');
        $endAt = strtotime(Config::get('end_at'));
        if (time() > $endAt) $this->jError('系统已关闭');
        if (empty($this->code)) {
            if ($this->errorMsg) {
                $this->jError($this->errorMsg, ['code' => $this->statusCode, 'msg' => '登录失败']);
            } else {
                $this->jError('', ['code' => $this->statusCode, 'msg' => '登录失败']);
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
            $mCode = InvitationCode::get($code);
            if (empty($mCode)) {
                $this->errorMsg = "邀请码不存在";
                return;
            }
            if ($mCode['is_confirm'] == 1) {
                $this->statusCode = 10002;
                $this->errorMsg = "邀请码已过期（每个邀请码仅能投一次）";
                return;
            }
            $this->code = $code;
        } catch (DbException $e) {
            $this->jError($e->getMessage());
        }

    }

    public function getCode() {
        return $this->code;
    }

}