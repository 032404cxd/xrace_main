<?php

$openid = isset($_GET['openid']) ? $_GET['openid'] : '';

include dirname(dirname(__FILE__)) . '/init.php';
$oManager = new Widget_Manager();
$managerInfo = $oManager->getRowByOpenId($openid,"id,name,data_groups");
if($managerInfo["id"])
{
    //返回管理员数据
    $result = array("return" => 1, "ManagerInfo" => json_encode($managerInfo));
}
else
{
    //返回管理员数据
    $result = array("return" => 0, "comment" =>"你好像没绑定过微信到后台哦，联系管理员");
}
echo json_encode($result);


