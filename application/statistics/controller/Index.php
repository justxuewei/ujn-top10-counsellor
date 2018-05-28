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
            if (!Config::has('removal_ratio')) {
                $this->jError("配置选项'removal_ratio'不存在，错误代码0001001");
            }
            $weight = Config::get('role_weight');
            $removalRatio = Config::get('removal_ratio');
            $ret = [];
            foreach ($candidates as $candidate) {
                $cid = $candidate['id'];
                $avgScore = 0;
                foreach ($weight as $role => $w) {
                    // 获取全部得分
                    $count = Db::query("select count(*) co
                                                from (
                                                  select *
                                                  from sj_vote
                                                  where candidate_id = $cid and delete_time is null
                                                  order by score
                                                ) v
                                                left join sj_invitation_code ic on ic.i_code = v.i_code and ic.delete_time is null
                                                where ic.role = $role"
                    );
                    if (empty($count)) {
                        continue;
                    }
                    $count = $count[0]['co'];
                    $removeCount = round($count * $removalRatio);
                    $remainCount = $count - 2 * $removeCount;
                    $scores = Db::query("select v.score, ic.role
                                                from (
                                                  select *
                                                  from sj_vote
                                                  where candidate_id = $cid and delete_time is null
                                                  order by score
                                                ) v
                                                left join sj_invitation_code ic on ic.i_code = v.i_code and ic.delete_time is null
                                                where ic.role = $role
                                                limit $removeCount, $remainCount
                                             ");
                    $total = 0;
                    foreach ($scores as $score) {
                        $total += $score['score'] * $weight[$role];
                    }
                    $avgScore += ($total / $remainCount);
                }
                array_push($ret, [
                    'name' => $candidate['c_name'],
                    'school' => $candidate['c_school'],
                    'profile' => $candidate['c_profile'],
                    'avg_score' => $avgScore
                ]);
            }
            $this->jSuccess($ret);
        } catch (DbException $e) {
            $this->jError($e->getTraceAsString(), $e->getMessage());
        }
    }

    /**
     * [GET] 统计评分完成情况
     */
    public function completion() {
        $this->jSuccess([
            'count' => Db::table('sj_invitation_code')->where('is_confirm', 1)->count()
        ]);
    }

}