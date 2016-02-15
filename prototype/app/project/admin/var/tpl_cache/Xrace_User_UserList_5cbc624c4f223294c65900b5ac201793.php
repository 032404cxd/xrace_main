<?php include Base_Common::tpl('contentHeader'); ?>
<script type="text/javascript">
    function userDetail(uid){
        userDetailBox = divBox.showBox('<?php echo $this->sign; ?>&ac=user.detail&UserId=' + uid, {title:'用户详情',width:600,height:400});
    }
    function userAuth(uid){
        userAuthBox = divBox.showBox('<?php echo $this->sign; ?>&ac=user.auth.info&UserId=' + uid, {title:'实名认证',width:600,height:400});
    }
</script>

<fieldset><legend>操作</legend>
</fieldset>
<form action="<?php echo $this->sign; ?>" name="form" id="form" method="post">
    姓名:<input type="text" class="span2" name="Name" value="<?php echo $params['Name']; ?>" />
    昵称:<input type="text" class="span2" name="NickName" value="<?php echo $params['NickName']; ?>" />
    性别:<select name="Sex" class="span2" size="1">
        <option value="">全部</option>
        <?php if (is_array($SexList)) { foreach ($SexList as $SexSymble => $SexName) { ?>
        <option value="<?php echo $SexSymble; ?>" <?php if($params['Sex']==$SexSymble) { ?>selected="selected"<?php } ?>><?php echo $SexName; ?></option>
        <?php } } ?>
    </select>
    实名认证状态:<select name="AuthStatus" class="span2" size="1">
        <option value="">全部</option>
        <?php if (is_array($AuthStatusList)) { foreach ($AuthStatusList as $AuthStatus => $AuthStatusName) { ?>
        <option value="<?php echo $AuthStatus; ?>" <?php if($params['AuthStatus']==$AuthStatus) { ?>selected="selected"<?php } ?>><?php echo $AuthStatusName; ?></option>
        <?php } } ?>
    </select>

    <input type="submit" name="Submit" value="查询" /><?php echo $export_var; ?>
</form>
<fieldset><legend>用户列表</legend>
<table width="99%" align="center" class="table table-bordered table-striped">
      <tr>
        <th align="center" class="rowtip">用户ID</th>
        <th align="center" class="rowtip">用户昵称</th>
        <th align="center" class="rowtip">微信openId</th>
        <th align="center" class="rowtip">联系电话</th>
        <th align="center" class="rowtip">性别</th>
        <th align="center" class="rowtip">实名认证状态</th>
        <th align="center" class="rowtip">生日</th>
        <th align="center" class="rowtip">操作</th>
      </tr>
    <?php if (is_array($UserList['UserList'])) { foreach ($UserList['UserList'] as $oUserInfo) { ?>
      <tr class="hover">
        <td align="center"><?php echo $oUserInfo['user_id']; ?></td>
          <td align="center"><?php echo $oUserInfo['nick_name']; ?></td>
          <td align="center"><?php echo $oUserInfo['wx_open_id']; ?></td>
        <td align="center"><?php echo $oUserInfo['phone']; ?></td>
        <td align="center"><?php echo $oUserInfo['sex']; ?></td>
          <td align="center"><?php echo $oUserInfo['AuthStatus']; ?></td>
          <td align="center"><?php echo $oUserInfo['Birthday']; ?></td>
          <td align="center"><a  href="javascript:;" onclick="userDetail('<?php echo $oUserInfo['user_id']; ?>')">详细</a><?php if($oUserInfo['auth_state']=="AUTHING") { ?> | <a  href="javascript:;" onclick="userAuth('<?php echo $oUserInfo['user_id']; ?>')">审核</a><?php } ?></td>
      </tr>
    <?php } } ?>
    <tr><th colspan="10" align="center" class="rowtip"><?php echo $page_content; ?></th></tr>

</table>
</fieldset>
<?php include Base_Common::tpl('contentFooter'); ?>
