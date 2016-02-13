<?php include Base_Common::tpl('contentHeader'); ?>
<script type="text/javascript">
  function SportsTypeAdd(){
    RaceStageId=$("#RaceStageId");
    RaceGroupId=$("#RaceGroupId");
    SportsType=$("#SportsTypeSelect");
    After=$("#After");
      location.href = '<?php echo $this->sign; ?>&ac=race.stage.group.sports.type.add&RaceGroupId=' + RaceGroupId.val() + '&RaceStageId=' + RaceStageId.val() + '&SportsTypeId=' + SportsType.val() + '&After=' + After.val();

  }
</script>

<form action="<?php echo $this->sign; ?>&ac=race.stage.group.update" name="form" id="form" method="post">
<input type="hidden" name="RaceStageId" id="RaceStageId" value="<?php echo $oRaceStage['RaceStageId']; ?>" />
  <input type="hidden" name="RaceGroupId" id="RaceGroupId" value="<?php echo $oRaceGroup['RaceGroupId']; ?>" />
  <fieldset><legend><?php echo $oRaceStage['RaceStageName']; ?>-<?php echo $oRaceGroup['RaceGroupName']; ?> 赛段详情配置 </legend>
<table width="99%" align="center" class="table table-bordered table-striped">

  <tr>
    <th align="center" class="rowtip">人数/价格对应</th>
    <th align="center" class="rowtip">是否接受个人报名</th>
    <th align="center" class="rowtip">是否接受团队报名</th>
    <th align="center" class="rowtip">起止时间</th>
  </tr>
  <tr>
    <th align="center" class="rowtip"><input name="RaceStageGroupInfo[PriceList]" type="text" class="span2" id="RaceStageGroupInfo[PriceList]" value="<?php echo $RaceStageGroupInfo['PriceList']; ?>" size="50" /></th>
    <th align="center" class="rowtip"><input type="radio" name="RaceStageGroupInfo[SingleUser]" id="RaceStageGroupInfo[SingleUser]" value="1" <?php if($RaceStageGroupInfo['SingleUser']=="1") { ?>checked<?php } ?>>接受
      <input type="radio" name="RaceStageGroupInfo[SingleUser]" id="RaceStageGroupInfo[SingleUser]"  value="0" <?php if($RaceStageGroupInfo['SingleUser']=="0") { ?>checked<?php } ?>>不接受</th>
    <th align="center" class="rowtip"><input type="radio" name="RaceStageGroupInfo[TeamUser]" id="RaceStageGroupInfo[TeamUser]" value="1" <?php if($RaceStageGroupInfo['TeamUser']=="1") { ?>checked<?php } ?>>接受
      <input type="radio" name="RaceStageGroupInfo[TeamUser]" id="RaceStageGroupInfo[TeamUser]" value="0" <?php if($RaceStageGroupInfo['TeamUser']=="0") { ?>checked<?php } ?>>不接受</th>
    <th align="center" class="rowtip">
      <input type="text" name="RaceStageGroupInfo[StartTime]" value="<?php echo $RaceStageGroupInfo['StartTime']; ?>" class="input-medium"
             onFocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm:ss'})" >
      ---
      <input type="text" name="RaceStageGroupInfo[EndTime]" value="<?php echo $RaceStageGroupInfo['EndTime']; ?>" value="" class="input-medium"
             onFocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm:ss'})" >
    </th>
  </tr>
</table>
<table width="99%" align="center" class="table table-bordered table-striped">
  <?php if(count($RaceStageGroupInfo['comment']['DetailList'])) { ?>
  <tr>
  <?php if (is_array($RaceStageGroupInfo['comment']['DetailList'])) { foreach ($RaceStageGroupInfo['comment']['DetailList'] as $SportsTypeId => $SportsTypeInfo) { ?>
  <tr>
  <th align="center" class="rowtip"><?php echo $SportsTypeInfo['SportsTypeName']; ?></th>
  </tr>
  <?php } } ?>
  <tr>
    <th align="center" class="rowtip">继续在
      <select name="After" id="After" size="1">
        <option value="-1" >尾部</option>
        <?php if (is_array($RaceStageGroupInfo['comment']['DetailList'])) { foreach ($RaceStageGroupInfo['comment']['DetailList'] as $STypeId => $STypeInfo) { ?>
        <option value="<?php echo $STypeId; ?>" ><?php echo $STypeInfo['SportsTypeName']; ?> 之后</option>
        <?php } } ?>
      </select>
      <button type="button" onclick="SportsTypeAdd()">添加</button>
      <select name="SportsTypeSelect" id="SportsTypeSelect" size="1">
        <?php if (is_array($SportTypeArr)) { foreach ($SportTypeArr as $SportsType) { ?>
        <option value="<?php echo $SportsType['SportsTypeId']; ?>" ><?php echo $SportsType['SportsTypeName']; ?></option>
        <?php } } ?>
      </select>
    </th>
  </tr>
  <?php } else { ?>
  <tr>
    <th align="center" class="rowtip" colspan="4">尚未配置任何赛段计时点数据
      <input type="hidden" name="After" id="After" value="-1" />
      <select name="SportsTypeSelect" id="SportsTypeSelect" size="1">
      <?php if (is_array($SportTypeArr)) { foreach ($SportTypeArr as $SportsType) { ?>
      <option value="<?php echo $SportsType['SportsTypeId']; ?>" ><?php echo $SportsType['SportsTypeName']; ?></option>
      <?php } } ?>
      </select>
      <button type="button" onclick="SportsTypeAdd()">添加</button>
    </th>
  </tr>
  <?php } ?>

</table>
</fieldset>
  <td><button type="submit" id="race_stage_group_submit">提交</button></td>
</form>
<?php include Base_Common::tpl('contentFooter'); ?>
