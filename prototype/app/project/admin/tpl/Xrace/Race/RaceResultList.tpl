{tpl:tpl contentHeader/}
<script type="text/javascript">
  function RaceResultList(rid,uid,rname){
    RaceResultListBox = divBox.showBox('{tpl:$this.sign/}&ac=race.result.list&RaceId=' + rid + '&UserId=' + uid, {title:rname+'成绩单',width:800,height:750});
  }
  function RaceAdd(sid,gid){
    RaceAddBox = divBox.showBox('{tpl:$this.sign/}&ac=race.add&RaceGroupId=' + gid + '&RaceStageId=' + sid, {title:'添加比赛',width:800,height:750});
  }
  function RaceModify(rid,rname,gid){
    RaceModifyBox = divBox.showBox('{tpl:$this.sign/}&ac=race.modify&RaceId=' + rid + '&RaceGroupId=' + gid, {title:'修改比赛-'+rname,width:800,height:750});
  }
  function RaceUserUpload(rid,rname,gid){
    RaceModifyBox = divBox.showBox('{tpl:$this.sign/}&ac=race.user.upload.submit&RaceId=' + rid + '&RaceGroupId=' + gid, {title:'批量导入报名记录-'+rname,width:300,height:150});
  }
  function RaceUserList(rid,rname,gname){
    RaceUserListBox = divBox.showBox('{tpl:$this.sign/}&ac=race.user.list&RaceId=' + rid, {title:gname+'-'+rname+'选手名单',width:800,height:750});
  }
</script>
<form action="{tpl:$this.sign/}&ac=race.update" name="form" id="form" method="post">
<input type="hidden" name="RaceStageId" id="RaceStageId" value="{tpl:$RaceStageInfo.RaceStageId/}" />
  <input type="hidden" name="RaceGroupId" id="RaceGroupId" value="{tpl:$RaceGroupId/}" />
  <fieldset><legend>{tpl:$RaceInfo.RaceName/} 成绩单{tpl:if(isset($UserInfo.user_id))} - {tpl:$UserInfo.name/}{/tpl:if}</legend>
<table width="99%" align="center" class="table table-bordered table-striped">
  <tr>
    <th align="center" class="rowtip" colspan="15">最后更新时间{tpl:$RaceResultList.UserRaceTimingInfo.LastUpdateTime func="date('Y-m-d H:i:s.u',@@)"/}</th>
  </tr>
  <tr>
    <th align="center" class="rowtip" colspan="2">计时点</th>
    <th align="center" class="rowtip" colspan="10">选手名次</th>
  </tr>

  {tpl:loop $RaceResultList.UserRaceTimingInfo.Sports $Sid $SInfo}
  <tr><th align="center" class="rowtip" rowspan="{tpl:$SInfo.TimingPointList func="count(@@)"/}">{tpl:$SInfo.SportsTypeInfo.SportsTypeName/}</th>
    {tpl:loop $SInfo.TimingPointList $id $Tid}
    {tpl:loop $RaceResultList.UserRaceTimingInfo.Point $Pid $PointInfo}
    {tpl:if($Tid==$Pid)}
    <th align="center" class="rowtip">{tpl:$PointInfo.TName/}<p>{tpl:$PointInfo.CurrentDistense/}米<p>{tpl:$PointInfo.UserList func="count(@@)"/}人通过</th>
    {tpl:if(count($PointInfo.UserList)>=1)}
    {tpl:loop $PointInfo.UserList $id $RaceUserInfo}
    {tpl:if((isset($UserInfo.user_id) && ($RaceUserInfo.UserId==$UserInfo.user_id)) || !isset($UserInfo.user_id))}
    <th align="center" class="rowtip"><a href="javascript:;" onclick="RaceResultList('{tpl:$RaceInfo.RaceId/}','{tpl:$RaceUserInfo.UserId/}','{tpl:$RaceInfo.RaceName/}')">{tpl:$RaceUserInfo.Name/}</a><p>{tpl:$RaceUserInfo.RaceTeamName/}<p>{tpl:$RaceUserInfo.TotalTime func="Base_Common::parthTimeLag(@@)"/}<p>{tpl:$RaceUserInfo.TotalNetTime func="Base_Common::parthTimeLag(@@)"/}<p>{tpl:$RaceUserInfo.Rank/}{tpl:if($RaceUserInfo.TimeLag>0)}/+{tpl:$RaceUserInfo.TimeLag func="Base_Common::parthTimeLag(@@)"/}{/tpl:if}{tpl:if($RaceUserInfo.NetTimeLag>0)}/+{tpl:$RaceUserInfo.NetTimeLag func="Base_Common::parthTimeLag(@@)"/}{/tpl:if}</th>
  {/tpl:if}
  {/tpl:loop}
    {tpl:else}
    <th align="center" class="rowtip">尚未通过</th>
  {/tpl:if}
    {/tpl:if}

    {/tpl:loop}
  </tr>
{/tpl:loop}
  {/tpl:loop}
  <tr><th  align="center" class="rowtip">名次</th>
    <th  align="center" class="rowtip" colspan="2">姓名</th>
    <th  align="center" class="rowtip" colspan="2">总时间</th>
    <th  align="center" class="rowtip" colspan="2">总净时间</th>
    <th  align="center" class="rowtip" colspan="2">BIB</th>
    <th  align="center" class="rowtip" colspan="2">当前位置</th>
  </tr>
  {tpl:loop $RaceResultList.UserRaceTimingInfo.Total $Tid $TInfo}
  <tr>
    <th  align="center" class="rowtip">{tpl:$TInfo.Rank/}</th>
    <th  align="center" class="rowtip" colspan="2">{tpl:$TInfo.Name/}</th>
    <th  align="center" class="rowtip" colspan="2">{tpl:$TInfo.TotalTime func="Base_Common::parthTimeLag(@@)"/}</th>
    <th  align="center" class="rowtip" colspan="2">{tpl:$TInfo.TotalNetTime func="Base_Common::parthTimeLag(@@)"/}</th>
    <th  align="center" class="rowtip" colspan="2">{tpl:$TInfo.BIB/}</th>
    <th  align="center" class="rowtip" colspan="2">{tpl:$TInfo.CurrentPositionName/}</th></tr>
  {/tpl:loop}
</table>
</fieldset>
</form>
{tpl:tpl contentFooter/}
