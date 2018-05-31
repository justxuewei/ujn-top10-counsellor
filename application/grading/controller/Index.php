<?php
/**
 * Created by PhpStorm.
 * User: xgzx
 * Date: 2018/5/7
 * Time: 9:31
 */

namespace app\grading\controller;


use app\code\model\InvitationCode;
use app\common\controller\SJPrivateController;
use app\grading\model\Candidate;
use app\grading\model\Vote;
use think\exception\DbException;
use think\Validate;

class Index extends SJPrivateController {

    /**
     * [POST] 打分
     *
     * c 候选人ID
     * s 分数
     */
    public function index() {

        $param = $this->request->post();

        $validate = new Validate([
            'c|候选人' => 'require|number',
            's|分数' => 'require|between:0,100'
        ]);
        if (!$validate->check($param)) {
            $this->jError($validate->getError());
        }

        $code = $this->getCode();

        try {
            // 检测是否存在该候选人
            $can = Candidate::get($param['c']);
            if (empty($can)) $this->jError('对应候选人不存在');
            // 查看投票信息
            // 如果原先有则更新 如果没有则新建
            $voteModel = new Vote();
            $vote = $voteModel->where('candidate_id', $param['c'])->where('i_code', $code)->find();
            if (empty($vote)) {
                // 如果为空新建一个
                Vote::create([
                    'candidate_id' => $param['c'],
                    'i_code' => $code,
                    'score' => $param['s']
                ]);
            } else {
                // 如果不为空则更新
                $vote->isUpdate(true)->save([
                    'score' => $param['s']
                ]);
            }
            $this->jSuccess();
        } catch (DbException $e) {
            $this->jError($e->getMessage()."(code: 000001)");
        }
    }

    /**
     * [GET] 确认提交
     */
    public function confirm() {
        try {
            // 获取全部提交的分数
            $voteModel = new Vote();
            $votes = $voteModel->where('i_code', $this->getCode())->select();
            // 获取全部候选人
            $candidates = Candidate::all();
            foreach ($candidates as $candidate) {
                $cid = $candidate['id'];
                $flag = false;
                foreach ($votes as $vote) {
                    if ($vote['candidate_id'] == $cid) {
                        $flag = true;
                        break;
                    }
                }
                if (!$flag) $this->jError('请您为候选人'.$candidate['c_name'].'打分后再次提交，谢谢！'.'[code: 000004]');
            }

            $invitationCode = InvitationCode::get($this->getCode());
            $invitationCode->save([
                'is_confirm' => 1,
                'confirm_time' => time()
            ]);
            $this->jSuccess();
        } catch (DbException $e) {
            $this->jError($e->getMessage()."(code: 000002)");
        }
    }

    /**
     * [GET] 确认提交v2
     * 这是15进10的确认，每个人最多打分10个人
     */
    public function comfirmv2() {
        $voteModel = new Vote();
        $voteCount = $voteModel->where('i_code', $this->getCode())->where('score', 1)->count();

        if ($voteCount < 1) {
            $this->jError("请您至少选择一位候选人");
        }

        if ($voteCount > 10) {
            $this->jError("您最多可以投10票，您目前选择了$voteCount 位辅导员，请核查后再次提交，谢谢！");
        }

        $invitationCode = null;
        try  {
            $invitationCode = InvitationCode::get($this->getCode());
        } catch (DbException $e) {
            $this->jError($e->getMessage()."(code: 000003)");
        }
        $invitationCode->save([
            'is_confirm' => 1,
            'confirm_time' => time()
        ]);
        $this->jSuccess();
    }

}