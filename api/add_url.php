<?php

/**
 * 增加网址记录
 */
header('Content-type: application/json; charset=utf-8');
sleep(1);
$url = defalutGetData($_POST, 'a', '');
$password = defalutGetData($_POST, 'b', '');
$desc = defalutGetData($_POST, 'c', '');
$guoqi = (int)defalutGetData($_POST, 'd', 0);
$key_time = defalutGetData($_POST, 'e', '');
$key_val = defalutGetData($_POST, 'f', ''); // 双层base64

if (encodeStr([$url, $password, $guoqi, $key_time]) != $key_val) {
    error(901, '验证出错');
    die();
} elseif (time() * 1000 - $key_time > 2000) {
    error(907, '请求过期');
    die();
}

$create_time = time();
require '../config.php';
$mysql = $config['mysql'];
$table = $config['table']['url'];
$conn = mysqli_connect($mysql['host'], $mysql['user'], $mysql['pass'], $mysql['db']);
mysqli_set_charset($conn, "utf8");
$end = defalutGetData($_POST, 'g', endIdGood());
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
sqlError($result);

// 校验参数
if (!parse_url($url) && strlen($url) < 2084) {
    error(905, 'URL格式错误');
} elseif (!preg_match('/^\w{0,20}$/', $password)) {
    error(905, '密码格式错误，要求1-20位数字、字母、下划线');
} elseif (mb_strlen($desc) > 200) {
    error(905, '描述文本长度不能超过200');
} elseif ($guoqi < 0 || $guoqi > 10000) {
    error(905, '有效天数必须是1-10000的整数');
} elseif ($end && !preg_match('/^\w{6,20}$/', $end)) {
    error(905, '自定义后缀格式错误，要求6-20位数字、字母、下划线');
}

// 没有密码和时间限制时，查询记录是否已经存在
if (!$password && !$guoqi && !$end) {
    $sql = "SELECT * FROM `$table` WHERE `url` = '$url' AND `desc` = '$desc';";
    $result = mysqli_query($conn, $sql);
    sqlError($result);
    if (mysqli_num_rows($result) > 0) {
        // 存在记录，直接返回
        $row = mysqli_fetch_assoc($result);
        unset($row['id']);
        success('生成成功', $row);
    }
}

// 删除数据表中所有过期的记录
// $sql = "DELETE FROM `$table` WHERE create_time + guoqi * 24 * 3600 < $create_time";
// mysqli_query($conn, $sql);

$sql = "SELECT `end` FROM `$table` WHERE `end` = '$end';";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    error(902, '后缀已存在，请换一个试试');
}

// 递归查询后缀是否存在，存在则重新生成
function endIdGood()
{
    global $conn, $table;
    $end = randId(6);
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
sqlError($result);
success('生成成功', array(
    'url' => $url,
    'password' => $password,
    'desc' => $desc,
    'guoqi' => $guoqi,
    'end' => $end,
    'create_time' => $create_time,
));

/**
 * 获取关联数组中指定键名的值
 * @param array $array 关联数组
 * @param string $key 键名
 * @param mixed $default 当键名未定义时的默认返回值
 * @return mixed 获取结果
 */
function defalutGetData($array, $key, $default)
{
    $v = isset($array[$key]) && $array[$key] != '' ? $array[$key] : $default;
    return addslashes($v);
}

/**
 * 生成一个指定长度的随机字符串
 */
function randId($length)
{
    $v = substr(str_shuffle(md5(str_shuffle(time()))), 0, $length);
    for ($x = 0; $x < $length; $x++) {
        if (rand(0, 1)) {
            $v[$x] = strtoupper($v[$x]);
        }
    }
    return $v;
}

/**
 * 返回错误信息
 * @param int $code 错误码
 * @param string $msg 错误信息
 * | 状态码  | 描述          |
 * | ------ | ------------- |
 * | 900    | 参数缺失       |
 * | 901    | 验证出错       |
 * | 902    | 记录已经存在   |
 * | 903    | 数据库错误     |
 * | 904    | 记录不存在     |
 * | 905    | 类型或格式错误 |
 * | 906    | 资源获取失败   |
 * | 907    | 资源或数据失效 |
 */
function error($code, $msg)
{
    echo json_encode(array(
        'code' => $code,
        'msg' => $msg
    ));
    die();
}

/**
 * 数据库查询出错时报错
 * @param mysqli_result $result 查询结果
 * @return bool
 */
function sqlError($result)
{
    if (!$result) {
        error(903, '数据库错误');
    }
    return true;
}


/**
 * 输出成功信息和数据
 * @param string $msg 成功信息
 * @param mixed $data 输出数据
 */
function success($msg, $data)
{
    echo json_encode(array(
        'code' => 200,
        'msg' => $msg,
        'data' => $data
    ));
    die();
}


/**
 * 数组加密
 * @param array $array 待加密数组
 * @return string 加密结果
 */
function encodeStr($array)
{
    $key_val = '';
    for ($x = 0; $x < count($array); $x++) {
        $key_val .= base64_encode(urlencode($array[$x]));
    }
    $ks = 'YM=AN';
    for ($x = 0; $x < strlen($ks); $x++) {
        $key_val = str_replace($ks[$x], '/' . base64_encode($ks[$x] . '_apee'), $key_val);
    }
    return base64_encode(urlencode($key_val));
}
