<?php

/**
 * 报名
 *
 */


header('Access-Control-Allow-Origin: *');

date_default_timezone_set('Asia/Shanghai');

$status_code = 0;
// 获取传入参数
$url_param = isset($_GET['url']) ? trim($_GET['url']) : false;
$level_param = isset($_GET['level']) ? trim($_GET['level']) : false;
//后台自动产生
$ip_param = getClientIP();
$time_param = date('y-m-d H:i:s', time());

if (!$level_param) {
    $level_param = 150;
} else if ($level_param == 1) {
    $level_param = 400;
} else if ($level_param == 2) {
    $level_param = 150;
} else {
    $level_param = 150;
}

// 判断传入参数
if ($url_param == false) {
    $re = "'url' field is empty";
    echo $re;
    return;
}


$url_param = urldecode($url_param);

//如果url没有用 '／' 结尾，自动加上
if (strrchr($url_param, "/") !== "/") {
    $url_param .= '/';
}

$n = 4;//倍数
$sentence = " -n " . $level_param * $n . " -c {$level_param} " . escapeshellarg($url_param);//escapeshellarg() 防止命令注入


$re = shell_exec("ab " . $sentence . " 2>&1");
//$re = shell_exec("ls -12321 2>&1");

file_put_contents("access.log", $time_param . " run '" . "ab" . $sentence . "' ( " . $ip_param . " )" . PHP_EOL, FILE_APPEND);
file_put_contents("access.log", "-----begin-----" . PHP_EOL, FILE_APPEND);
file_put_contents("access.log", $re, FILE_APPEND);
file_put_contents("access.log", "-----end-----" . PHP_EOL . PHP_EOL, FILE_APPEND);

//标红
$re = signRed($re);

echo '<pre>' . $re . '</pre>';
return;


/**
 * 获取客户端IP地址
 *
 * @return string
 */
function getClientIP()
{
    if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown")) {
        $ip = getenv("HTTP_CLIENT_IP");
    } else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown")) {
        $ip = getenv("HTTP_X_FORWARDED_FOR");
    } else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown")) {
        $ip = getenv("REMOTE_ADDR");
    } else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown")) {
        $ip = $_SERVER['REMOTE_ADDR'];
    } else {
        $ip = "unknown";
    }
    return $ip;
}


function signRed($old)
{

    $old = str_replace("Server Hostname", "<font color='red'>Server Hostname</font>", $old);
    $old = str_replace("Document Path", "<font color='red'>Document Path</font>", $old);
    $old = str_replace("Concurrency Level", "<font color='red'>Concurrency Level</font>", $old);
    $old = str_replace("Requests per second", "<font color='red'>Requests per second</font>", $old);
    $old = str_replace("90%", "<font color='red'>90%</font>", $old);


    return $old;
}


?>
