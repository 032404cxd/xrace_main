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
            <td align="left"><?php echo $UserInfo['AuthIdType']; ?></td>
            <th align="center" class="rowtip">证件号码</th>
            <td align="left"><?php echo $UserInfo['id_number']; ?></td>
        </tr>
        <?php } ?>

    </table>

<?php include Base_Common::tpl('contentFooter'); ?>