<?php
/**
 * Created by PhpStorm.
 * User: MrN1u
 * Date: 5/5/18
 * Time: 8:53 PM
 */

namespace app\common\controller;


use think\Config;
use think\Controller;
use think\exception\HttpResponseException;
use think\Request;
use think\Response;

class SJController extends Controller {

    // Request实例
    protected $request;
    // 代码
    protected $code;
    // 错误原因
    protected $errorMsg;

    public function __construct(Request $request = null) {
        if (is_null($request)) {
            $request = Request::instance();
        }

        $this->request = $request;

        // 邀请码验证
        $this->_codeValidation();

        parent::__construct($request);

    }

    /**
     * 初始化
     */
    protected function _initialize() {
    }

    /**
     * 代码验证
     */
    protected function _codeValidation() {
    }

    /**
     * 操作成功跳转的快捷方法
     * @access protected
     * @param mixed $msg 提示信息
     * @param mixed $data 返回的数据
     * @param array $header 发送的Header信息
     * @return void
     */
    protected function jSuccess($data = '', $msg = '成功', array $header = []) {
        $code = 1;
        $result = [
            'code' => $code,
            'msg' => $msg,
            'data' => $data,
        ];

        $type = $this->getResponseType();
        $header['Access-Control-Allow-Origin'] = '*';
        $header['Access-Control-Allow-Headers'] = 'X-Requested-With,Content-Type,XX-Device-Type,XX-Token,SJ-Code';
        $header['Access-Control-Allow-Methods'] = 'GET,POST,PATCH,PUT,DELETE,OPTIONS';
        $response = Response::create($result, $type)->header($header);
//        throw new HttpResponseException($response);
    }

    /**
     * 操作错误跳转的快捷方法
     * @access protected
     * @param mixed $msg 提示信息,若要指定错误码,可以传数组,格式为['code'=>您的错误码,'msg'=>'您的错误消息']
     * @param mixed $data 返回的数据
     * @param array $header 发送的Header信息
     * @return void
     */
    protected function jError($data = '', $msg = '失败', array $header = []) {
        $code = 0;
        if (is_array($msg)) {
            $code = $msg['code'];
            $msg = $msg['msg'];
        }
        $result = [
            'code' => $code,
            'msg' => $msg,
            'data' => $data,
        ];

        $type = $this->getResponseType();
        $header['Access-Control-Allow-Origin'] = '*';
        $header['Access-Control-Allow-Headers'] = 'X-Requested-With,Content-Type,XX-Device-Type,XX-Token';
        $header['Access-Control-Allow-Methods'] = 'GET,POST,PATCH,PUT,DELETE,OPTIONS';
        $response = Response::create($result, $type)->header($header);
        throw new HttpResponseException($response);
    }

    /**
     * 获取当前的response 输出类型
     * @access protected
     * @return string
     */
    protected function getResponseType() {
        return Config::get('default_return_type');
    }

}