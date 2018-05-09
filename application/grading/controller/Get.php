<?php
/**
 * Created by PhpStorm.
 * User: xgzx
 * Date: 2018/5/7
 * Time: 15:12
 */

namespace app\grading\controller;


use app\common\controller\SJController;
use think\Config;
use think\Db;
use think\Validate;

class Get extends SJController {

    /**
     * [GET] 获取历史打分记录
     *
     * c 邀请码
     */
    public function index() {

        $param = $this->request->get();

        $validate = new Validate(['c|邀请码' => 'require']);
        if (!$validate->check($param)) {
            $this->jError($validate->getError());
        }

        $code = $param['c'];
        $votes = Db::query("select v.score, c.* from (
                                        select * from sj_vote where i_code = '$code' and delete_time is null
                                    ) v
                                    left join sj_candidate c on c.id = v.candidate_id");
        $ret = [];
        foreach ($votes as $vote) {
            $v = [];
            $v['score'] = $vote['score'];
            $v['name'] = $vote['c_name'];
            $v['school'] = $vote['c_school'];
            $v['profile'] = $vote['c_profile'];
            if (empty($vote['c_pic'])) {
                $v['pic'] = "http://res.niuxuewei.com/2018-05-09-123553.jpg";
            } else {
                $v['pic'] = $vote['c_pic'];
            }
            array_push($ret, $v);
        }

        $this->jSuccess($ret);
    }

}