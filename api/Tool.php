<?php

/**
 * 工具库
 */

namespace Tool;

class Tool
{

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
     * | 904    | 记录或资源不存在或失效     |
     * | 905    | 类型或格式错误 |
     * | 906    | 资源获取失败   |
     */
    function error($code, $msg)
    {
        header('Content-type: application/json; charset=utf-8');
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
        header('Content-type: application/json; charset=utf-8');
        if (!$result) {
            $this->error(903, '数据库错误');
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
        header('Content-type: application/json; charset=utf-8');
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
}
