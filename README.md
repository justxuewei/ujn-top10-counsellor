# 济南大学十佳辅导员在线打分系统

---

## 使用注意事项

- 在部署服务器中将`./application/config.php.tmp`和`./application/database.php.tmp`的后缀`.tmp`去除，并根据实际需要自行修改参数。其中需要注意的是：
    - 在数据库配置文件中检查数据库配置和数据库前缀
    - 在总配置文件中需要将`default_return_type`设置为`json`
    - 在总配置文件中配置系统开启时间和截止时间
- 需要自行创建:

    - 打分配置文件，目录`application/grading/config.php`
    - 邀请码配置文件，目录`application/code/config.php`
    - 统计配置文件，目录`application/statistics/config.php`
    
```
/** 打分配置文件 **/
<?php
return [
    // 默认候选人图像
    'default_candidate_pic' => 'http://xxx.png'
];

/** 邀请码配置文件 **/
<?php
return [
    'psw' => 'SJExcVMNbauTzDRx',    // 生成二维码密码
    'upload_path' => "upload",  // 二维码保存目录，为ROOT_PATH/public/upload
    'qr_code_entrance' => 'http://sj.ujnxgzx.com/fe',     // 二维码扫描入口，后端自动加入邀请码http://sj.ujnxgzx.com/fe?code=xxxxxx
    'code_length' => 6      // 二维码长度
];

/** 统计配置文件 **/
<?php
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
``` 

## 数据库

![数据库ER图](http://res.niuxuewei.com/2018-05-09-104717.png)

数据库结构sql文件请[点击这里下载](https://gitee.com/ujnxgzx/sj/attach_files/download?i=135498&u=http%3A%2F%2Ffiles.git.oschina.net%2Fgroup1%2FM00%2F03%2F9E%2FPaAvDFry4CSAazJUAAAKxgb94ZU400.sql%3Ftoken%3Dc4a564b7402e2cb389d2b905f0301a88%26ts%3D1525866532%26attname%3Dujnxgzxsj.sql)，推荐使用PHPMyAdmin导入，并检查是否开启了InnoDB引擎。

---

作者: MrN1u<br>
邮箱: a@niuxuewei.com<br>
主页: http://www.niuxuewei.com