<?php include Base_Common::tpl('contentHeader'); ?>

<form action="<?php echo $this->sign; ?>&ac=race.stage.group.sports.type.add" name="stage_group_sports_type_add_form" id="stage_group_sports_type_add_form" method="post">
<input type="hidden" name="RaceStageId" id="RaceStageId" value="<?php echo $oRaceStage['RaceStageId']; ?>" />
  <input type="hidden" name="RaceGroupId" id="RaceGroupId" value="<?php echo $oRaceGroup['RaceGroupId']; ?>" />

<table width="99%" align="center" class="table table-bordered table-striped">
  <tr>
    <th align="center" class="rowtip">继续在</th>
    </tr>
  <tr>
    <th align="center" class="rowtip">
      <select name="After" id="After" size="1">
        <option value="-1" >头部</option>
        <?php if (is_array($RaceStageGroupInfo['comment']['DetailList'])) { foreach ($RaceStageGroupInfo['comment']['DetailList'] as $SportsTypeId => $SportsTypeInfo) { ?>
        <option value="<?php echo $SportsTypeId; ?>" <?php if($SportsTypeId==$After) { ?>selected="selected"<?php } ?>><?php echo $SportsTypeInfo['SportsTypeName']; ?></option>
        <?php } } ?>
      </select>
  </tr>
  <tr>
    <th align="center" class="rowtip">
      之后添加
      <select name="SportsTypeId" id="SportsTypeId" size="1">
        <?php if (is_array($SportsTypeList)) { foreach ($SportsTypeList as $SportsType) { ?>
        <option value="<?php echo $SportsType['SportsTypeId']; ?>" ><?php echo $SportsType['SportsTypeName']; ?></option>
        <?php } } ?>
      </select>
    </th>
  </tr>
  <tr><th align="center" class="rowtip">
    <button type="submit" id="stage_group_sports_type_add">提交</button>
    </th></tr>
  <tr>
</table>
</form>
<script type="text/javascript">
  $('#stage_group_sports_type_add').click(function(){
    var options = {
      dataType:'json',
      beforeSubmit:function(formData, jqForm, options) {
      },
      success:function(jsonResponse) {
        if (jsonResponse.errno) {
          var errors = [];
          errors[1] = '当前组别尚未配置';
          errors[2] = '分组数据错误';
          errors[3] = '请选择一个有效的运动类型，请修正后再次提交';
          errors[9] = '入库失败，请修正后再次提交';
          divBox.alertBox(errors[jsonResponse.errno],function(){});
        } else {
          var message = '添加运动分段成功';
          RaceStageId=$("#RaceStageId");
          RaceGroupId=$("#RaceGroupId");
          divBox.confirmBox({content:message,ok:function(){windowParent.getRightHtml('<?php echo $this->sign; ?>'+ '&ac=race.stage.group.detail&RaceStageId=' + RaceStageId.val() + '&RaceGroupId=' + RaceGroupId.val());}});
        }
      }
    };
    $('#stage_group_sports_type_add_form').ajaxForm(options);
  });
</script>
<?php include Base_Common::tpl('contentFooter'); ?>
