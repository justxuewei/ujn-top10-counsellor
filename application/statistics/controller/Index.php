<?php
/**
 * Created by PhpStorm.
 * User: xgzx
 * Date: 2018/5/7
 * Time: 16:06
 */

namespace app\statistics\controller;


use app\common\controller\SJController;
use app\grading\model\Candidate;
use app\grading\model\Vote;
use think\Config;
use think\Db;
use think\exception\DbException;

class Index extends SJController {

    /**
     * [GET] 统计全部候选人得分情况
     */
    public function index() {
        try {
            $candidates = Candidate::all();
            if (!Config::has('role_weight')) {
                $this->jError("配置选项'role_weight'不存在，错误代码0001000");
            }
            $weight = Config::get('role_weight');
            $ret = [];
            foreach ($candidates as $candidate) {
                $cid = $candidate['id'];
                // 获取全部得分
                $count = Db::query("select count(0) co from sj_vote where candidate_id = $cid");
                if (empty($count)) {
                    array_push($ret, [
                        'name' => $candidate['c_name'],
                        'school' => $candidate['c_school'],
                        'profile' => $candidate['c_profile'],
                        'avg_score' => 0
                    ]);
                    continue;
                }
                $count = $count[0]['co'];
                if (!Config::has('removal_ratio')) {
                    $this->jError("配置选项'removal_ratio'不存在，错误代码0001001");
                }
                $removalRatio = Config::get('removal_ratio');
                $removeCount = floor($count * $removalRatio);
                $remainCount = $count - 2 * $removeCount;
                $sql = "select
                        v.score,
                        ic.role
                        from (
                                select *
                                from sj_vote
                                where candidate_id = $cid and delete_time is null
                                order by score
                                limit $removeCount, $remainCount
                              ) v
                        left join sj_invitation_code ic on ic.i_code = v.i_code and ic.delete_time is null";
                $scores = Db::query($sql);
                $fCount = count($scores);
                $total = 0;
                foreach ($scores as $score) {
                    $role = $score['role'];
                    $total += $score['score'] * $weight[$role];
                }
                array_push($ret, [
                    'name' => $candidate['c_name'],
                    'school' => $candidate['c_school'],
                    'profile' => $candidate['c_profile'],
                    'avg_score' => $total / $fCount
                ]);
            }
            $this->jSuccess($ret);
        } catch (DbException $e) {
            $this->jError($e->getTraceAsString(), $e->getMessage());
        }
    }

    /**
     * [GET] 返回完成情况
     */
    public function completion() {
        $this->jSuccess([
            'count' => Db::name('invitation_code')->where('is_confirm', 1)->count()
        ]);
    }

}