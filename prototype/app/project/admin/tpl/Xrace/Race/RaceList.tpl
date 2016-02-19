{tpl:tpl contentHeader/}
<script type="text/javascript">
  function RaceAdd(sid,gid){
    RaceAddBox = divBox.showBox('{tpl:$this.sign/}&ac=race.add&RaceGroupId=' + gid + '&RaceStageId=' + sid, {title:'添加比赛',width:400,height:400});
  }
  function RaceModify(sid,gid,rid,rname){
    RaceModifyBox = divBox.showBox('{tpl:$this.sign/}&ac=race.modify&RaceGroupId=' + gid + '&RaceStageId=' + sid + '&RaceId=' + rid, {title:'修改比赛-'+rname,width:400,height:400});
  }
</script>
<form action="{tpl:$this.sign/}&ac=race.stage.group.update" name="form" id="form" method="post">
<input type="hidden" name="RaceStageId" id="RaceStageId" value="{tpl:$oRaceStage.RaceStageId/}" />
  <input type="hidden" name="RaceGroupId" id="RaceGroupId" value="{tpl:$oRaceGroup.RaceGroupId/}" />
  <fieldset><legend>{tpl:$oRaceStage.RaceStageName/}-{tpl:$oRaceGroup.RaceGroupName/} 赛段详情配置 </legend>
<table width="99%" align="center" class="table table-bordered table-striped">
  {tpl:if(count($RaceList))}
  <tr>
    <th align="center" class="rowtip">比赛名称</th>
    <th align="center" class="rowtip">人数/价格对应</th>
    <th align="center" class="rowtip">是否接受个人报名</th>
    <th align="center" class="rowtip">是否接受团队报名</th>
    <th align="center" class="rowtip">开始时间</th>
    <th align="center" class="rowtip">结束时间</th>
    <th align="center" class="rowtip">操作</th>
  </tr>
  {tpl:loop $RaceList $Rid $RaceInfo}
  <tr>
    <th align="center" class="rowtip">{tpl:$RaceInfo.RaceName/}</th>
    <th align="center" class="rowtip">{tpl:$RaceInfo.PriceList/}</th>
    <th align="center" class="rowtip">{tpl:if($RaceInfo.SingleUser==1)}接受{tpl:else}不接受{/tpl:if}</th>
    <th align="center" class="rowtip">{tpl:if($RaceInfo.TeamUser==1)}接受{tpl:else}不接受{/tpl:if}</th>
    <th align="center" class="rowtip">{tpl:$RaceInfo.StartTime/}</th>
    <th align="center" class="rowtip">{tpl:$RaceInfo.EndTime/}</th>
    <th align="center" class="rowtip"><a href="javascript:;" onclick="RaceModify('{tpl:$oRaceStage.RaceStageId/}','{tpl:$oRaceGroup.RaceGroupId/}','{tpl:$RaceInfo.RaceId/}','{tpl:$RaceInfo.RaceName/}')">修改</a>
     | <a href="{tpl:$this.sign/}&ac=race.detail&RaceStageId={tpl:$oRaceStage.RaceStageId/}&RaceGroupId={tpl:$oRaceGroup.RaceGroupId/}&RaceId={tpl:$RaceInfo.RaceId/}">计时点详情</a></th>
    </th>
  </tr>
  {/tpl:loop}
  <tr>
    <th align="center" class="rowtip" colspan="10">  <a href="javascript:;" onclick="RaceAdd('{tpl:$oRaceStage.RaceStageId/}','{tpl:$oRaceGroup.RaceGroupId/}')">点此添加</a>
    </th>
  </tr>
  {tpl:else}
  <tr>
    <th align="center" class="rowtip">本站尚未配置任何比赛  <a href="javascript:;" onclick="RaceAdd('{tpl:$oRaceStage.RaceStageId/}','{tpl:$oRaceGroup.RaceGroupId/}')">点此添加</a>
    </th>
    </th>
  </tr>
  {/tpl:if}
</table>
</fieldset>
</form>
{tpl:tpl contentFooter/}
