<?php
function encodeStr($array)
{
    $key_val = '';
    for ($x = 0; $x < count($array); $x++) {
        $key_val .= base64_encode(urlencode($array[$x]));
    }
    $ks = 'YM=AN';
    for ($x = 0; $x < strlen($ks); $x++) {
        $key_val = str_replace($ks[$x], '/' . base64_encode('apee_' . $ks[$x]), $key_val);
    }
    return $key_val;
}
echo encodeStr([
    "http://localhost:3000/index.html",
    "",
    "",
    ""
]);
