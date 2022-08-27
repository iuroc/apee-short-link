<?php

/**
 * 增加网址记录
 */
require '../config.php';
require 'Tool.php';

use Tool\Tool;

$Tool = new Tool();
header('Content-type: application/json; charset=utf-8');
sleep(1);
$url = $Tool->defalutGetData($_POST, 'a', '');
$password = $Tool->defalutGetData($_POST, 'b', '');
$desc = $Tool->defalutGetData($_POST, 'c', '');
$guoqi = (int)$Tool->defalutGetData($_POST, 'd', 0);
$key_time = $Tool->defalutGetData($_POST, 'e', '');
$key_val = $Tool->defalutGetData($_POST, 'f', ''); // 双层base64

if ($Tool->encodeStr([$url, $password, $guoqi, $key_time]) != $key_val) {
    $Tool->error(901, '验证出错');
    die();
} elseif (time() * 1000 - $key_time > 10000) {
    $Tool->error(904, '请求过期');
    die();
}

$create_time = time();
$mysql = $config['mysql'];
$table = $config['table']['url'];
$conn = mysqli_connect($mysql['host'], $mysql['user'], $mysql['pass'], $mysql['db']);
mysqli_set_charset($conn, "utf8");

// 初始化数据表
$sql = "CREATE TABLE IF NOT EXISTS `$table` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `url` TEXT NOT NULL,
    `password` VARCHAR(255),
    `desc` VARCHAR(255),
    `guoqi` INT(11) NOT NULL,
    `end` VARCHAR(255) NOT NULL,
    `create_time` INT(11) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
$result = mysqli_query($conn, $sql);
$Tool->sqlError($result);

$endd = $Tool->defalutGetData($_POST, 'g', '');
$end = $endd ? $endd : endIdGood();

// 校验参数
if (!parse_url($url) && strlen($url) < 2084) {
    $Tool->error(905, 'URL格式错误');
} elseif (!preg_match('/^\w{0,20}$/', $password)) {
    $Tool->error(905, '密码格式错误，要求1-20位数字、字母、下划线');
} elseif (mb_strlen($desc) > 200) {
    $Tool->error(905, '描述文本长度不能超过200');
} elseif ($guoqi < 0 || $guoqi > 10000) {
    $Tool->error(905, '有效天数必须是1-10000的整数');
} elseif ($end && !preg_match('/^\w{6,20}$/', $end)) {
    $Tool->error(905, '自定义后缀格式错误，要求6-20位数字、字母、下划线');
}

// 没有密码和时间限制时，查询记录是否已经存在
if (!$password && !$guoqi && !$endd) {
    $sql = "SELECT * FROM `$table` WHERE `url` = '$url' AND `desc` = '$desc' AND `password` = '';";
    $result = mysqli_query($conn, $sql);
    $Tool->sqlError($result);
    if (mysqli_num_rows($result) > 0) {
        // 存在记录，直接返回
        $row = mysqli_fetch_assoc($result);
        unset($row['id']);
        $Tool->success('生成成功', $row);
    }
}

// 删除数据表中所有过期的记录
// $sql = "DELETE FROM `$table` WHERE create_time + guoqi * 24 * 3600 < $create_time";
// mysqli_query($conn, $sql);

$sql = "SELECT `end` FROM `$table` WHERE `end` = '$end';";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    $Tool->error(902, '后缀已存在，请换一个试试');
}

// 递归查询后缀是否存在，存在则重新生成
function endIdGood()
{
    global $conn, $table, $Tool;
    $end = $Tool->randId(6);
    $sql = "SELECT `end` FROM `$table` WHERE `end` = '$end';";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 0) {
        return $end;
    } else {
        endIdGood();
    }
}
// 插入记录
$sql = "INSERT INTO `$table` (`url`, `password`, `desc`, `guoqi`, `end`, `create_time`) VALUES ('$url', '$password', '$desc', $guoqi, '$end', $create_time);";
$result = mysqli_query($conn, $sql);
$Tool->sqlError($result);
$Tool->success('生成成功', array(
    'url' => $url,
    'password' => $password,
    'desc' => $desc,
    'guoqi' => $guoqi,
    'end' => $end,
    'create_time' => $create_time,
));
