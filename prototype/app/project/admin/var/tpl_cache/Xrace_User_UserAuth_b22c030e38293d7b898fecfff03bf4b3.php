<?php include Base_Common::tpl('contentHeader'); ?>
<script type="text/javascript">
    function userAuth(uid){
        userAuthBox = divBox.showBox('<?php echo $this->sign; ?>&ac=user.auth.submit&UserId=' + uid, {title:'实名认证-提交',width:500,height:600});
    }
</script>
<div class="br_bottom"></div>
    <table width="99%" align="center" class="table table-bordered table-striped" widtd="99%">
        <tr class="hover">
            <th align="center" class="rowtip" rowspan="7" colspan="2"><img src="<?php echo $UserInfo['thumb']; ?>" width='200' height='160'/></th>
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
            <th align="center" class="rowtip">实名认证提交时间</th>
            <td align="left"><?php echo $UserAuthInfo['submit_time']; ?></td>
        </tr>
        <tr class="hover">
            <th align="center" class="rowtip">联系电话</th>
            <td align="left"><?php echo $UserInfo['phone']; ?></td>
        </tr>
        <tr class="hover">
            <th align="center" class="rowtip">
                <?php if($UserAuthInfo['submit_img1']!="") { ?>
                <a href="<?php echo $UserAuthInfo['submit_img1']; ?>" target="_blank">点此查看图片1</a>
                    <?php } else { ?>
                        图片1：无
                    <?php } ?>
            </th>
            <th align="center" class="rowtip">
                <?php if($UserAuthInfo['submit_img2']!="") { ?>
                <a href="<?php echo $UserAuthInfo['submit_img2']; ?>" target="_blank">点此查看图片2</a>
                    <?php } else { ?>
                        图片2：无
                    <?php } ?>
            </td>
            <th align="center" class="rowtip" colspan = 2><?php if($UserInfo['auth_state']=="AUTHING") { ?><a  href="javascript:;" onclick="userAuth('<?php echo $UserInfo['user_id']; ?>')">审核</a><?php } ?></th>
        </tr>
    </table>

<?php include Base_Common::tpl('contentFooter'); ?>