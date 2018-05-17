<?php
/**
 * Created by PhpStorm.
 * User: MrN1u
 * Date: 5/18/18
 * Time: 2:06 AM
 */

namespace app\info;


use app\common\controller\SJController;
use app\grading\model\GradingTitle;
use think\exception\DbException;
use app\grading\model\Candidate as CandidateModel;

class Candidate extends SJController {

    /**
     * [GET] 获取全部候选人以及题目信息
     */
    public function getAll() {
        try {
            // 获取全部候选人
            $candidateModel = new CandidateModel();
            $candidates = $candidateModel->order('id')->select();
            $ret_candidates = [];
            foreach ($candidates as $candidate) {
                $can = [];
                $can['id'] = $candidate['id'];
                $can['name'] = $candidate['c_name'];
                $can['school'] = $candidate['c_school'];
                $can['profile'] = $candidate['c_profile'];
                if (empty($candidate['c_pic'])) {
                    $can['pic'] = "http://res.niuxuewei.com/2018-05-09-123553.jpg";
                } else {
                    $can['pic'] = $candidate['c_pic'];
                }
                array_push($ret_candidates, $can);
            }
            // 获取全部题目
            $titles = GradingTitle::all();
            $ret_titles = [];
            foreach ($titles as $title) {
                $tit = [];
                $tit['title'] = $title['title'];
                $tit['val'] = $title['max_value'];
                array_push($ret_titles, $tit);
            }

            $this->jSuccess([
                'candidates' => $ret_candidates,
                'titles' => $ret_titles
            ]);
        } catch (DbException $e) {
            $this->jError($e->getMessage());
        }

    }

}