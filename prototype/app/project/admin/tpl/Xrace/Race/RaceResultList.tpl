{tpl:tpl contentHeader/}
<script type="text/javascript">
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
  <fieldset><legend>{tpl:$RaceInfo.RaceName/} 成绩列表 </legend>
<table width="99%" align="center" class="table table-bordered table-striped">
  <tr>
    <th align="center" class="rowtip">计时点</th>
    <th align="center" class="rowtip" colspan="10">选手</th>
  </tr>
  {tpl:loop $RaceResultList.UserRaceTimingInfo.Point $Pid $PointInfo}
  <tr>
    <th align="center" class="rowtip">{tpl:$PointInfo.TName/}</th>
    {tpl:loop $PointInfo.UserList $id $UserInfo}
    <th align="center" class="rowtip">{tpl:$UserInfo.Name/}/{tpl:$UserInfo.Rank/}{tpl:if($UserInfo.TimeLag>0)}<p>{tpl:$UserInfo.TimeLag func="Base_Common::parthTimeLag(sprintf('%10.3f',@@))"/}{/tpl:if}</th>
    {/tpl:loop}
  </tr>
  {/tpl:loop}

</table>
</fieldset>
</form>
{tpl:tpl contentFooter/}
