<?php
/**
 * Created by PhpStorm.
 * User: xgzx
 * Date: 2018/5/7
 * Time: 16:13
 */
return [
    // 角色权重(详见数据库sj_invitation_code的role)
    // 最终得分 = 分数 * 角色权重
    'role_weight' => [
        '0' => 0.2,
        '1' => 0.2,
        '2' => 0.3,
        '3' => 0.3
    ],
    // 移除最高分和最低分比率
    'removal_ratio' => 0.1
];