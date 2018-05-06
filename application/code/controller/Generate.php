<?php
/**
 * Created by PhpStorm.
 * User: MrN1u
 * Date: 5/5/18
 * Time: 8:41 PM
 */

namespace app\code\controller;


use app\common\controller\SJController;
use PHPQRCode\QRcode;
use think\Config;
use think\Validate;

class Generate extends SJController {

    /**
     * [GET] 创建邀请码并输出二维码
     *
     * psw 密码
     * type 权限
     */
    public function index() {

        $param = $this->request->param();

        $password = Config::get('psw');
        $validate = new Validate([
            'psw|口令' => "require|/^$password$/",
            'type|权限' => "require|in:0,1,2"
        ]);

        if (!$validate->check($param)) {
            $this->jError($validate->getError());
        }

        $code = $this->_generateInvitationCode();

        // TODO: 存入数据库

        // TODO: 记得换URL
        $url = "http://" . $this->request->host()."/grading?code=$code";
        $savePath = Config::get('upload_path')."$code.png";
        QrCode::png($url, $savePath, 'L', 6, 2);

        $this->jSuccess([
            'code' => $code,
            'code_path' => "http://" . $this->request->host() . "/" . Config::get('qr_code_file_path') . "/$code.png"]);
    }

    /**
     * 创建指定长度的随机字符串
     *
     * @param int $length
     * @return int|mixed
     */
    private function _generateInvitationCode($length = 6) {
        $chars = '123456789abcdefghijklmnpqrstuvwxyz';
        $string = "";
        for ( $i = 0; $i < $length; $i++ ) {
            $string .= $chars[ mt_rand(0, strlen($chars) - 1) ];
        }
        return $string;
    }

}