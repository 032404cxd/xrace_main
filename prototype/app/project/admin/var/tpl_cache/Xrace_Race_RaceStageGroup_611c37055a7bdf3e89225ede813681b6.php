<?php include Base_Common::tpl('contentHeader'); ?>
<script type="text/javascript">

</script>

<form action="<?php echo $this->sign; ?>&ac=race.stage.group.update" name="form" id="form" method="post">
<input type="hidden" name="RaceStageId" id="RaceStageId" value="<?php echo $oRaceStage['RaceStageId']; ?>" />
  <fieldset><legend><?php echo $oRaceStage['RaceStageName']; ?> 赛段详情列表 </legend>
<table width="99%" align="center" class="table table-bordered table-striped">

  <tr>
    <th align="center" class="rowtip">对应分组</th>
    <th align="center" class="rowtip">人数/价格对应</th>
    <th align="center" class="rowtip">是否接受个人报名</th>
    <th align="center" class="rowtip">是否接受团队报名</th>
    <th align="center" class="rowtip">起止时间</th>
  </tr>

  <?php if (is_array($oRaceStage['comment']['SelectedRaceGroup'])) { foreach ($oRaceStage['comment']['SelectedRaceGroup'] as $RaceGroupId => $RaceGroup) { ?>
  <tr>
    <th align="center" class="rowtip"><?php echo $RaceGroup['RaceGroupInfo']['RaceGroupName']; ?></th>
    <th align="center" class="rowtip"><input name="SelectedGroup[<?php echo $RaceGroupId; ?>][PriceList]" type="text" class="span2" id="SelectedGroup[<?php echo $RaceGroupId; ?>][PriceList]" value="<?php echo $RaceGroup['RaceStageGroupInfo']['PriceList']; ?>" size="50" /></th>
    <th align="center" class="rowtip"><input type="radio" name="SelectedGroup[<?php echo $RaceGroupId; ?>][SingleUser]" id="SelectedGroup[<?php echo $RaceGroupId; ?>][SingleUser]" value="1" <?php if($RaceGroup['RaceStageGroupInfo']['SingleUser']=="1") { ?>checked<?php } ?>>接受
      <input type="radio" name="SelectedGroup[<?php echo $RaceGroupId; ?>][SingleUser]" id="SelectedGroup[<?php echo $RaceGroupId; ?>[SingleUser]"  value="0" <?php if($RaceGroup['RaceStageGroupInfo']['SingleUser']=="0") { ?>checked<?php } ?>>不接受</th>
    <th align="center" class="rowtip"><input type="radio" name="SelectedGroup[<?php echo $RaceGroupId; ?>][TeamUser]" id="SelectedGroup[<?php echo $RaceGroupId; ?>][TeamUser]" value="1" <?php if($RaceGroup['RaceStageGroupInfo']['TeamUser']=="1") { ?>checked<?php } ?>>接受
      <input type="radio" name="SelectedGroup[<?php echo $RaceGroupId; ?>][TeamUser]" id="SelectedGroup[<?php echo $RaceGroupId; ?>][TeamUser]" value="0" <?php if($RaceGroup['RaceStageGroupInfo']['TeamUser']=="0") { ?>checked<?php } ?>>不接受</th>
    <th align="center" class="rowtip">
      <input type="text" name="SelectedGroup[<?php echo $RaceGroupId; ?>][StartTime]" value="<?php echo $RaceGroup['RaceStageGroupInfo']['StartTime']; ?>" class="input-medium"
             onFocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm:ss'})" >
      ---
      <input type="text" name="SelectedGroup[<?php echo $RaceGroupId; ?>][EndTime]" value="<?php echo $RaceGroup['RaceStageGroupInfo']['EndTime']; ?>" value="" class="input-medium"
             onFocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm:ss'})" >
    </th>
  </tr>
  <?php } } ?>


</table>
</fieldset>
  <td><button type="submit" id="race_stage_group_submit">提交</button></td>
</form>
<?php include Base_Common::tpl('contentFooter'); ?>
