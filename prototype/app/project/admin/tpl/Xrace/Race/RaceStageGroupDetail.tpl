{tpl:tpl contentHeader/}
<script type="text/javascript">
  function SportsTypeAdd(sid,gid,after){
    SportsTypeAddBox = divBox.showBox('{tpl:$this.sign/}&ac=race.stage.group.sports.type.add&RaceGroupId=' + gid + '&RaceStageId=' + sid + '&After=' + after, {title:'添加运动分段',width:350,height:250});
  }
  function TimingPointAdd(sid,gid,tid,after,sname){
    TimingPointAddBox = divBox.showBox('{tpl:$this.sign/}&ac=timing.point.add&RaceGroupId=' + gid + '&RaceStageId=' + sid +  '&SportsTypeId=' + tid + '&After=' + after, {title:'添加'+sname+'计时点',width:400,height:600});
  }
  function TimingPointModify(sid,gid,tid,pid,tname){
    TimingPointModifyBox = divBox.showBox('{tpl:$this.sign/}&ac=timing.point.modify&RaceGroupId=' + gid + '&RaceStageId=' + sid +  '&SportsTypeId=' + tid + '&TimingId=' + pid, {title:'修改计时点-'+tname,width:400,height:400});
  }
  function TimingPointDelete(sid,gid,tid,pid,tname){
    deleteTimingPointBox= divBox.confirmBox({content:'是否删除 ' + tname + '?',ok:function(){location.href = '{tpl:$this.sign/}&ac=timing.point.delete&RaceGroupId=' + gid + '&RaceStageId=' + sid +  '&SportsTypeId=' + tid + '&TimingId=' + pid;}});
  }
  function  SportsTypeDelete(sid,gid,tid, p_name){
    deleteSportsTypeBox= divBox.confirmBox({content:'是否删除 ' + p_name + '?',ok:function(){location.href = '{tpl:$this.sign/}&ac=race.stage.group.sports.type.delete&RaceGroupId=' + gid + '&RaceStageId=' + sid + '&SportsTypeId=' + tid ;}});
  }
</script>

<form action="{tpl:$this.sign/}&ac=race.stage.group.update" name="form" id="form" method="post">
<input type="hidden" name="RaceStageId" id="RaceStageId" value="{tpl:$oRaceStage.RaceStageId/}" />
  <input type="hidden" name="RaceGroupId" id="RaceGroupId" value="{tpl:$oRaceGroup.RaceGroupId/}" />
  <fieldset><legend>{tpl:$oRaceStage.RaceStageName/}-{tpl:$oRaceGroup.RaceGroupName/} 赛段详情配置 </legend>
<table width="99%" align="center" class="table table-bordered table-striped">

  <tr>
    <th align="center" class="rowtip">人数/价格对应</th>
    <th align="center" class="rowtip">是否接受个人报名</th>
    <th align="center" class="rowtip">是否接受团队报名</th>
    <th align="center" class="rowtip">起止时间</th>
  </tr>
  <tr>
    <th align="center" class="rowtip"><input name="PriceList" type="text" class="span2" id="PriceList" value="{tpl:$RaceStageGroupInfo.PriceList/}" size="50" /></th>
    <th align="center" class="rowtip"><input type="radio" name="SingleUser" id="SingleUser" value="1" {tpl:if($RaceStageGroupInfo.SingleUser=="1")}checked{/tpl:if}>接受
      <input type="radio" name="SingleUser" id="SingleUser"  value="0" {tpl:if($RaceStageGroupInfo.SingleUser=="0")}checked{/tpl:if}>不接受</th>
    <th align="center" class="rowtip"><input type="radio" name="TeamUser" id="TeamUser" value="1" {tpl:if($RaceStageGroupInfo.TeamUser=="1")}checked{/tpl:if}>接受
      <input type="radio" name="TeamUser" id="TeamUser" value="0" {tpl:if($RaceStageGroupInfo.TeamUser=="0")}checked{/tpl:if}>不接受</th>
    <th align="center" class="rowtip">
      <input type="text" name="StartTime" value="{tpl:$RaceStageGroupInfo.StartTime/}" class="input-medium"
             onFocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm:ss'})" >
      ---
      <input type="text" name="EndTime" value="{tpl:$RaceStageGroupInfo.EndTime/}" value="" class="input-medium"
             onFocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm:ss'})" >
    </th>
  </tr>
</table>
<table width="99%" align="center" class="table table-bordered table-striped">
  {tpl:if(count($RaceStageGroupInfo.comment.DetailList))}
  <tr>
    <th align="center" class="rowtip"><a href="javascript:;" onclick="SportsTypeAdd('{tpl:$oRaceStage.RaceStageId/}','{tpl:$oRaceGroup.RaceGroupId/}','-1')">在头部添加</a>
    </th>
  </tr>
  <tr>
  {tpl:loop $RaceStageGroupInfo.comment.DetailList $SportsTypeId $SportsTypeInfo}
  <tr>
  <th align="center" class="rowtip">{tpl:$SportsTypeInfo.SportsTypeName/}
    <a href="javascript:;" onclick="SportsTypeAdd('{tpl:$oRaceStage.RaceStageId/}','{tpl:$oRaceGroup.RaceGroupId/}','{tpl:$SportsTypeId/}')">在此之后添加</a>
     |
    <a href="javascript:;" onclick="SportsTypeDelete('{tpl:$oRaceStage.RaceStageId/}','{tpl:$oRaceGroup.RaceGroupId/}','{tpl:$SportsTypeId/}','{tpl:$SportsTypeInfo.SportsTypeName/}')">删除</a>
  </th>
  </tr>
    {tpl:if(count($SportsTypeInfo.TimingDetailList.comment))}
  <tr><th colspan = 10>
  <table width="99%" align="center"  class="table table-bordered table-striped">
  {tpl:loop $SportsTypeInfo.TimingDetailList.comment $Tid $TimingInfo}
      <tr>
        <td>┠&nbsp;&nbsp;{tpl:$TimingInfo.TName/}</td><td>计时芯片序列号：{tpl:$TimingInfo.ChipId/}</td><td>距离下一计时点：{tpl:$TimingInfo.ToNext/}米</td><td>经过{tpl:$TimingInfo.Round/}次</td><td>海拔上升{tpl:$TimingInfo.AltAsc/}米</td><td>海拔下降{tpl:$TimingInfo.AltDec/}米</td>
        <td><a href="javascript:;" onclick="TimingPointModify('{tpl:$oRaceStage.RaceStageId/}','{tpl:$oRaceGroup.RaceGroupId/}','{tpl:$SportsTypeId/}','{tpl:$Tid/}','{tpl:$TimingInfo.TName/}')">修改</a> |
          <a href="javascript:;" onclick="TimingPointDelete('{tpl:$oRaceStage.RaceStageId/}','{tpl:$oRaceGroup.RaceGroupId/}','{tpl:$SportsTypeId/}','{tpl:$Tid/}','{tpl:$TimingInfo.TName/}')">删除</a> |
          <a href="javascript:;" onclick="TimingPointAdd('{tpl:$oRaceStage.RaceStageId/}','{tpl:$oRaceGroup.RaceGroupId/}','{tpl:$SportsTypeId/}','{tpl:$Tid/}','{tpl:$SportsTypeInfo.SportsTypeName/}')">在此之后添加</a>
        </td>
      </tr>
    {/tpl:loop}
  </th>
  </table>
    </tr>
    {tpl:else}
      <tr>
      <th align="center" class="rowtip">┠&nbsp;&nbsp;尚未配置任何计时点信息<a href="javascript:;" onclick="TimingPointAdd('{tpl:$oRaceStage.RaceStageId/}','{tpl:$oRaceGroup.RaceGroupId/}','{tpl:$SportsTypeId/}','-1','{tpl:$SportsTypeInfo.SportsTypeName/}')">在头部添加</a></th>
      </tr>
    {/tpl:if}
  {/tpl:loop}

  {tpl:else}
  <tr>
    <th align="center" class="rowtip" colspan="4">尚未配置任何分段数据
      <a href="javascript:;" onclick="SportsTypeAdd('{tpl:$oRaceStage.RaceStageId/}','{tpl:$oRaceGroup.RaceGroupId/}','-1')">在头部添加</a>
    </th>
  </tr>
  {/tpl:if}
</table>
</fieldset>
  <td><button type="submit" id="race_stage_group_submit">提交</button></td>
</form>
{tpl:tpl contentFooter/}
