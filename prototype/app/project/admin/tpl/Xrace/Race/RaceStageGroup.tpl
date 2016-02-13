{tpl:tpl contentHeader/}
<script type="text/javascript">

</script>

<form action="{tpl:$this.sign/}&ac=race.stage.group.update" name="form" id="form" method="post">
<input type="hidden" name="RaceStageId" id="RaceStageId" value="{tpl:$oRaceStage.RaceStageId/}" />
  <fieldset><legend>{tpl:$oRaceStage.RaceStageName/} 赛段详情列表 </legend>
<table width="99%" align="center" class="table table-bordered table-striped">

  <tr>
    <th align="center" class="rowtip">对应分组</th>
    <th align="center" class="rowtip">人数/价格对应</th>
    <th align="center" class="rowtip">是否接受个人报名</th>
    <th align="center" class="rowtip">是否接受团队报名</th>
    <th align="center" class="rowtip">起止时间</th>
  </tr>

  {tpl:loop $oRaceStage.comment.SelectedRaceGroup $RaceGroupId $RaceGroup}
  <tr>
    <th align="center" class="rowtip">{tpl:$RaceGroup.RaceGroupInfo.RaceGroupName /}</th>
    <th align="center" class="rowtip"><input name="SelectedGroup[{tpl:$RaceGroupId/}][PriceList]" type="text" class="span2" id="SelectedGroup[{tpl:$RaceGroupId/}][PriceList]" value="{tpl:$RaceGroup.RaceStageGroupInfo.PriceList/}" size="50" /></th>
    <th align="center" class="rowtip"><input type="radio" name="SelectedGroup[{tpl:$RaceGroupId/}][SingleUser]" id="SelectedGroup[{tpl:$RaceGroupId/}][SingleUser]" value="1" {tpl:if($RaceGroup.RaceStageGroupInfo.SingleUser=="1")}checked{/tpl:if}>接受
      <input type="radio" name="SelectedGroup[{tpl:$RaceGroupId/}][SingleUser]" id="SelectedGroup[{tpl:$RaceGroupId/}][SingleUser]"  value="0" {tpl:if($RaceGroup.RaceStageGroupInfo.SingleUser=="0")}checked{/tpl:if}>不接受</th>
    <th align="center" class="rowtip"><input type="radio" name="SelectedGroup[{tpl:$RaceGroupId/}][TeamUser]" id="SelectedGroup[{tpl:$RaceGroupId/}][TeamUser]" value="1" {tpl:if($RaceGroup.RaceStageGroupInfo.TeamUser=="1")}checked{/tpl:if}>接受
      <input type="radio" name="SelectedGroup[{tpl:$RaceGroupId/}][TeamUser]" id="SelectedGroup[{tpl:$RaceGroupId/}][TeamUser]" value="0" {tpl:if($RaceGroup.RaceStageGroupInfo.TeamUser=="0")}checked{/tpl:if}>不接受</th>
    <th align="center" class="rowtip">
      <input type="text" name="SelectedGroup[{tpl:$RaceGroupId/}][StartTime]" value="{tpl:$RaceGroup.RaceStageGroupInfo.StartTime/}" class="input-medium"
             onFocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm:ss'})" >
      ---
      <input type="text" name="SelectedGroup[{tpl:$RaceGroupId/}][EndTime]" value="{tpl:$RaceGroup.RaceStageGroupInfo.EndTime/}" value="" class="input-medium"
             onFocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm:ss'})" >
    </th>
  </tr>
  {/tpl:loop}


</table>
</fieldset>
  <td><button type="submit" id="race_stage_group_submit">提交</button></td>
</form>
{tpl:tpl contentFooter/}
