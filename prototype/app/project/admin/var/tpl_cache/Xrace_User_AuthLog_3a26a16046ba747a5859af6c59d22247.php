<?php include Base_Common::tpl('contentHeader'); ?>
<script type="text/javascript">
    function userDetail(uid){
        userDetailBox = divBox.showBox('<?php echo $this->sign; ?>&ac=user.detail&UserId=' + uid, {title:'用户详情',width:600,height:400});
    }
    function userAuth(uid){
        userAuthBox = divBox.showBox('<?php echo $this->sign; ?>&ac=user.auth.info&UserId=' + uid, {title:'实名认证',width:600,height:400});
    }
</script>

</fieldset>
<fieldset><legend>实名认证记录</legend>
    <form action="<?php echo $this->sign; ?>&ac=auth.log" name="form" id="form" method="post">
        开始日期:<input type="text" name="StartDate" value="<?php echo $params['StartDate']; ?>" class="input-medium"
                    onFocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:'yyyy-MM-dd'})" >
        结束日期:<input type="text" name="EndDate" value="<?php echo $params['EndDate']; ?>" class="input-medium"
                    onFocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:'yyyy-MM-dd'})" >
        实名认证状态:<select name="AuthResult" class="span2" size="1">
            <option value=""  <?php if($params['AuthResult']=="") { ?>selected="selected"<?php } ?>>全部</option>
            <?php if (is_array($AuthLogIdStatusList)) { foreach ($AuthLogIdStatusList as $AuthResult => $AuthResultName) { ?>
            <option value="<?php echo $AuthResult; ?>" <?php if($params['AuthResult']==$AuthResult) { ?>selected="selected"<?php } ?>><?php echo $AuthResultName; ?></option>
            <?php } } ?>
        </select>
        操作人:<select name="ManagerId" class="span2" size="1">
            <option value=""  <?php if($params['ManagerId']==0) { ?>selected="selected"<?php } ?>>全部</option>
            <?php if (is_array($ManagerList)) { foreach ($ManagerList as $ManagerId => $ManagerInfo) { ?>
            <option value="<?php echo $ManagerId; ?>" <?php if($params['ManagerId']==$ManagerId) { ?>selected="selected"<?php } ?>><?php echo $ManagerInfo['name']; ?></option>
            <?php } } ?>
        </select>

        <input type="submit" name="Submit" value="查询" /><?php echo $export_var; ?>
    </form>
    <table width="99%" align="center" class="table table-bordered table-striped">
      <tr>
        <th align="center" class="rowtip">认证记录Id</th>
        <th align="center" class="rowtip">提交认证时间</th>
        <th align="center" class="rowtip">用户账号</th>
        <th align="center" class="rowtip">认证结果</th>
        <th align="center" class="rowtip">操作时间</th>
        <th align="center" class="rowtip" colspan="2">证件照片</th>
        <th align="center" class="rowtip">说明</th>
      </tr>
    <?php if (is_array($AuthLog['AuthLog'])) { foreach ($AuthLog['AuthLog'] as $LogInfo) { ?>
      <tr class="hover">
        <td align="center"><?php echo $LogInfo['auth_id']; ?></td>
          <td align="center"><?php echo $LogInfo['submit_time']; ?></td>
          <td align="center"><?php echo $LogInfo['UserName']; ?></td>
          <td align="center"><?php echo $LogInfo['AuthResultName']; ?></td>
          <td align="center"><?php echo $LogInfo['op_time']; ?></td>
          <td align="center"><?php if($LogInfo['submit_img1']!="") { ?>
          <a href="<?php echo $LogInfo['submit_img1']; ?>" target="_blank">点此查看图片1</a>
          <?php } else { ?>
              图片1：无
          <?php } ?>
          </td>
          <td align="center"><?php if($LogInfo['submit_img2']!="") { ?>
              <a href="<?php echo $LogInfo['submit_img2']; ?>" target="_blank">点此查看图片2</a>
              <?php } else { ?>
                  图片1：无
              <?php } ?>
          </td>
          <td align="center"><?php echo $LogInfo['auth_resp']; ?></td>
      </tr>
    <?php } } ?>
    <tr><th colspan="10" align="center" class="rowtip"><?php echo $page_content; ?></th></tr>

</table>
</fieldset>
<?php include Base_Common::tpl('contentFooter'); ?>
