<?php
/**
 * Created by PhpStorm.
 * User: MrN1u
 * Date: 5/18/18
 * Time: 4:43 AM
 */

namespace app\info\controller;


use app\common\controller\SJPrivateController;

class Code extends SJPrivateController {

    /**
     * [GET]判断是否过期
     */
    public function isExpired() {
        $this->jSuccess();
    }

}