<?php
header('Content-type:application/json');
require_once('./class/ta.class.php');

$dy = $_GET['dy'];
$server = $_GET['server'];
$gz = $_GET['gz'];
$fs = $_GET['fs'];
$hz = $_GET['hz'];
$zp = $_GET['zp'];
$xh = $_GET['xh'];
$key = $_GET['key'];

$ta = new Ta();
$res = $ta->getTaInfo($dy);
if ($server) {
    $server = substr($server, -1) == '/' ? $server : $server.'/';
    $uid = $res['data']['uid'];
    $nickname = $res['data']['nickname'];
    $signature = $res['data']['signature'];
    $change = $res['data']['change'];
    if ($change) {
        $following = $change['following'];
        $follower = $change['follower'];
        $favorited = $change['favorited'];
        $aweme = $change['aweme'];
        $favoriting = $change['favoriting'];
        if ($following && $gz != '0') {
            get_url($server.'你关注的抖音用户有了新动态'.'/'.$following.'?url=snssdk1128://user/profile/'.$uid);
            sleep(0.5);
        }
        if ($follower && $fs != '0') {
            get_url($server.'你关注的抖音用户有了新动态'.'/'.$follower.'?url=snssdk1128://user/profile/'.$uid);
            sleep(0.5);
        }
        if ($favorited && $hz != '0') {
            get_url($server.'你关注的抖音用户有了新动态'.'/'.$favorited.'?url=snssdk1128://user/profile/'.$uid);
            sleep(0.5);
        }
        if ($aweme && $zp != '0') {
            get_url($server.'你关注的抖音用户有了新动态'.'/'.$aweme.'?url=snssdk1128://user/profile/'.$uid);
            sleep(0.5);
        }
        if ($favoriting && $xh != '0') {
            get_url($server.'你关注的抖音用户有了新动态'.'/'.$favoriting.'?url=snssdk1128://user/profile/'.$uid);
            sleep(0.5);
        }
        if ($aweme && $key == 'onAug11') {
            require_once('./class/dl.class.php');
            $dl = new Dl();
            $dl->dlVideo($dy, 1);
        }
    } else {
        if ($signature) {
            get_url($server.'关注抖音用户 '.$nickname.' 成功/'.str_replace(PHP_EOL, ' ', $signature).'?url=snssdk1128://user/profile/'.$uid);
        } else {
            get_url($server.'关注抖音用户 '.$nickname.' 成功/'.$nickname.'未设置签名?url=snssdk1128://user/profile/'.$uid);
        }
    }
}

echo json_encode($res);
?>