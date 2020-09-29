<?php
header("Content-type:text/html;charset=utf-8");
include_once 'mysql.php';
include_once 'functions.php';
include_once 'sqlutils.php';

$postdata=file_get_contents("php://input");
$jsondata=json_decode($postdata);


$id=$jsondata->id;
$expressaddress=$jsondata->expressaddress;

session_start();
$openid=$_SESSION['openid'];
if(!$openid){
	exitJson(1,'非法请求，请重新登录');
}

$db=getDb();


$sql="select * from `".getTablePrefix()."_joins` where luckydrawid='$id' and ownerid='$openid' and getaward=1 LIMIT 1";
$res=mysqli_query($db,$sql) or die(mysqli_error($db));
$row=mysqli_fetch_assoc($res);

$now=time();
if($row['awarddate']!=0 && $now>intval($row['awarddate'])+86400*7){
	exitJson(1,"仅限中奖后7天内填写收货地址");
}

$sql = "update `".getTablePrefix()."_joins` set expressaddress='$expressaddress' where luckydrawid='$id' and ownerid='$openid' and getaward=1 LIMIT 1";
mysqli_query($db,$sql) or die(mysqli_error($db));


exitJson(0,"地址提交成功，请等待发货");


?>