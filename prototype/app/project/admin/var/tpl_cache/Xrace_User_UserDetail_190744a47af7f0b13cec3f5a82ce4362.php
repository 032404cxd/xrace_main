<?php include Base_Common::tpl('contentHeader'); ?>
<div class="br_bottom"></div>


    <table width="99%" align="center" class="table table-bordered table-striped" widtd="99%">
        <tr class="hover">
            <th align="center" class="rowtip" rowspan="7" colspan="2"><img src="<?php echo $UserInfo['thumb']; ?>" width='160' height='160'/></th>
        </tr>
        <tr class="hover">
            <th align="center" class="rowtip">用户昵称</th>
            <td align="left"><?php echo $UserInfo['nick_name']; ?></td>
        </tr>
        <tr class="hover">
            <th align="center" class="rowtip">用户姓名</th>
            <td align="left"><?php echo $UserInfo['name']; ?></td>
        </tr>
        <tr class="hover">
            <th align="center" class="rowtip">用户性别</th>
            <td align="left"><?php echo $UserInfo['sex']; ?></td>
        </tr>
        <tr class="hover">
            <th align="center" class="rowtip">联系电话</th>
            <td align="left"><?php echo $UserInfo['phone']; ?></td>
        </tr>
        <tr class="hover">
            <th align="center" class="rowtip">实名认证状态</th>
            <td align="left"><?php echo $UserInfo['AuthStatus']; ?></td>
        </tr>
        <tr class="hover">
            <th align="center" class="rowtip">微信openId</th>
            <td align="left"><?php echo $UserInfo['wx_open_id']; ?></td>
        </tr>
        <?php if($UserInfo['id_number']!="") { ?>
        <tr class="hover">
            <th align="center" class="rowtip">证件类型</th>
            <td align="left" colspan = "3"><?php echo $UserInfo['AuthIdType']; ?></td>

        </tr>
        <tr class="hover">
            <th align="center" class="rowtip">证件号码</th>
            <td align="left"><?php echo $UserInfo['id_number']; ?></td>
            <th align="center" class="rowtip">证件有效期</th>
            <td align="left"><?php echo $UserInfo['AuthExpireDate']; ?></td>
        </tr>
        <?php } ?>
        <?php if(count($UserInfo['UserAuthLog'])) { ?>
        <tr class="hover">
            <th align="center" class="rowtip" colspan="4">实名认证记录</th>
        </tr>
        <tr class="hover">
            <th align="center" class="rowtip">操作时间</th>
            <th align="center" class="rowtip">后台管理员账号</th>
            <th align="center" class="rowtip">操作结果</th>
            <th align="center" class="rowtip">说明</th>
        </tr>
            <?php if (is_array($UserInfo['UserAuthLog'])) { foreach ($UserInfo['UserAuthLog'] as $LogId => $LogInfo) { ?>
                <tr class="hover">
                        <td align="left"><?php echo $LogInfo['op_time']; ?></th>
                        <td align="left"><?php echo $LogInfo['ManagerName']; ?></th>
                        <td align="left"><?php echo $LogInfo['AuthResult']; ?></th>
                        <td align="left"><?php echo $LogInfo['auth_resp']; ?></th>
                </tr>
            <?php } } ?>

        <?php } ?>


    </table>

<?php include Base_Common::tpl('contentFooter'); ?>