<?php
/**
 * Created by PhpStorm.
 * User: MrN1u
 * Date: 5/5/18
 * Time: 9:20 PM
 */

namespace app\code\model;


use think\Model;
use traits\model\SoftDelete;

class InvitationCode extends Model {

    use SoftDelete;

    protected $pk = "i_code";

}