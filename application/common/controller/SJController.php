<?php
/**
 * Created by PhpStorm.
 * User: MrN1u
 * Date: 5/5/18
 * Time: 8:53 PM
 */

namespace app\common\controller;


use app\code\model\InvitationCodeModel;
use think\Controller;
use think\exception\DbException;
use think\exception\HttpResponseException;
use think\Request;

class SJController extends Controller {

    // Request实例
    protected $request;
    // 代码
    protected $code;
    // 错误原因
    protected $errorMsg;

    public function __construct(Request $request = null) {
        parent::__construct($request);
        if (is_null($request)) {
            $request = Request::instance();
        }

        $this->request = $request;

        // 邀请码验证
        $this->_codeValidation();

        if (empty($this->code)) {
            if ($this->errorMsg) {
                $this->error(['code' => 10001, 'msg' => '登录失败'], $this->errorMsg);
            } else {
                $this->error(['code' => 10001, 'msg' => '登录失败']);
            }
        }
    }

    /**
     * 代码验证
     */
    private function _codeValidation() {
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
            $this->error($e->getMessage());
        }

    }

    /**
     * 操作成功跳转的快捷方法
     * @access protected
     * @param mixed $msg 提示信息
     * @param mixed $data 返回的数据
     * @param array $header 发送的Header信息
     * @return void
     */
    protected function success($msg = '', $data = '', array $header = [])
    {
        $code   = 1;
        $result = [
            'code' => $code,
            'msg'  => $msg,
            'data' => $data,
        ];

        $type                                   = $this->getResponseType();
        $header['Access-Control-Allow-Origin']  = '*';
        $header['Access-Control-Allow-Headers'] = 'X-Requested-With,Content-Type,XX-Device-Type,XX-Token';
        $header['Access-Control-Allow-Methods'] = 'GET,POST,PATCH,PUT,DELETE,OPTIONS';
        $response                               = Response::create($result, $type)->header($header);
        throw new HttpResponseException($response);
    }

    /**
     * 操作错误跳转的快捷方法
     * @access protected
     * @param mixed $msg 提示信息,若要指定错误码,可以传数组,格式为['code'=>您的错误码,'msg'=>'您的错误消息']
     * @param mixed $data 返回的数据
     * @param array $header 发送的Header信息
     * @return void
     */
    protected function error($msg = '', $data = '', array $header = [])
    {
        $code = 0;
        if (is_array($msg)) {
            $code = $msg['code'];
            $msg  = $msg['msg'];
        }
        $result = [
            'code' => $code,
            'msg'  => $msg,
            'data' => $data,
        ];

        $type                                   = $this->getResponseType();
        $header['Access-Control-Allow-Origin']  = '*';
        $header['Access-Control-Allow-Headers'] = 'X-Requested-With,Content-Type,XX-Device-Type,XX-Token';
        $header['Access-Control-Allow-Methods'] = 'GET,POST,PATCH,PUT,DELETE,OPTIONS';
        $response                               = Response::create($result, $type)->header($header);
        throw new HttpResponseException($response);
    }

}