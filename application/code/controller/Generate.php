<?php
/**
 * Created by PhpStorm.
 * User: MrN1u
 * Date: 5/5/18
 * Time: 8:41 PM
 */

namespace app\code\controller;


use app\code\model\InvitationCode;
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
     * num 数量
     */
    public function index() {

        $param = $this->request->param();

        $password = Config::get('psw');
        $validate = new Validate([
            'psw|口令' => "require|/^$password$/",
            'type|权限' => "require|in:0,1,2,3",
            'num|数量' => 'number'
        ]);

        if (!$validate->check($param)) {
            $this->jError($validate->getError());
        }

        $num = null;
        if (empty($param['num']) || $param['num'] < 0) {
            $num = 1;
        } else {
            $num = $param['num'];
        }

        $ret = [];

        for ($i = 0; $i < $num; $i++) {
            $code = $this->_generateInvitationCode(Config::get('code_length'));

            // 将授权码存入数据库
            InvitationCode::create([
                'i_code' => $code,
                'role' => $param['type'],
                'create_time' => time()
            ]);

            $url = Config::get('qr_code_entrance')."?code=$code";
            $savePath = ROOT_PATH . "public". DS .Config::get('upload_path'). DS ."$code.png";
            // 如果文件夹不存在则创建
            if (!is_dir(Config::get('upload_path'))) {
                mkdir(Config::get('upload_path'), 0777, true);
            }
            QrCode::png($url, $savePath, 'L', 6, 2);

            $data = [
                'code' => $code,
                'code_path' => "http://" . $this->request->host() . "/" . Config::get('qr_code_image_url_path') . "/$code.png"];
            array_push($ret, $data);
        }

        $this->jSuccess($ret);
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