<?php

/**
 * 重定向到URL
 */
require 'Tool.php';
require '../config.php';

use Tool\Tool;

$Tool = new Tool();
$end = $Tool->defalutGetData($_GET, 'end', '');
$password = $Tool->defalutGetData($_GET, 'password', '');
$type = $Tool->defalutGetData($_GET, 'type', 'cdx');
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

// 查找URL
$now_time = time();
$sql = "SELECT * FROM `$table` WHERE `end` = '$end' AND ((`create_time` + `guoqi` * 24 * 3600 > $now_time) OR (`guoqi` = 0))";
$result = mysqli_query($conn, $sql);
$Tool->sqlError($result);
if (mysqli_num_rows($result) == 0) {
    $Tool->error(904, '链接不存在或失效');
}
$row = mysqli_fetch_assoc($result);
if ($row['password'] != $password) {
    $Tool->error(901, '密码错误');
}
if ($type == 'cdx') {
    header('Location: ' . $row['url']);
} elseif ($type == 'json') {
    unset($row['id']);
    $Tool->success('获取成功', $row);
}
