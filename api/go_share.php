<?php

/**
 * 跳转到分享页面
 */


require 'Tool.php';

use Tool\Tool;

$Tool = new Tool;
$end = $Tool->defalutGetData($_GET, 'end', '');
header('Location: /#/share/' . $end);
