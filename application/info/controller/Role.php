<?php
/**
 * Created by PhpStorm.
 * User: MrN1u
 * Date: 5/18/18
 * Time: 2:05 AM
 */

namespace app\info\controller;


use app\common\controller\SJPrivateController;

class Role extends SJPrivateController {

    /**
     * [GET] 获取用户角色
     */
    public function index() {
        $this->jSuccess([
            'role' => $this->role
        ]);
    }

}