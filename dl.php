<?php
header('Content-type:application/json');
require_once('./class/dl.class.php');

$dy = $_GET['dy'];

$dl = new Dl();
$res = $dl->dlVideo($dy, 0);
echo json_encode($res);
?>