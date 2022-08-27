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
$sql = "SELECT * FROM `$table` WHERE `end` = '$end' AND `password` = '$password' AND ((`create_time` + `guoqi` * 24 * 3600 > $now_time) OR (`guoqi` = 0))";
$result = mysqli_query($conn, $sql);
$Tool->sqlError($result);
if (mysqli_num_rows($result) == 0) {
    header('Location: /');
    die();
}
$row = mysqli_fetch_assoc($result);
header('Location: ' . $row['url']);
