{tpl:tpl contentHeader/}
<script type="text/javascript">
  function AidStationAdd(sid){
      AidStationBox = divBox.showBox('{tpl:$this.sign/}&ac=aid.station.add&RaceStageId=' + sid, {title:'添加补给点',width:1000,height:750});
  }
  function AidStationModify(a_id, a_name){
    AidStationModifyBox = divBox.showBox('{tpl:$this.sign/}&ac=aid.station.modify&AidStationId=' + a_id, {title:'修改补给点-'+a_name,width:1000,height:750});
  }
  function AidStationDelete(a_id, a_name){
    deleteAppBox = divBox.confirmBox({content:'是否删除 ' + a_name + '?',ok:function(){location.href = '{tpl:$this.sign/}&ac=aid.station.delete&AidStationId=' + a_id;}});
  }

</script>
<form action="{tpl:$this.sign/}&ac=race.update" name="form" id="form" method="post">
<input type="hidden" name="RaceStageId" id="RaceStageId" value="{tpl:$RaceStageInfo.RaceStageId/}" />
  <input type="hidden" name="RaceGroupId" id="RaceGroupId" value="{tpl:$RaceGroupId/}" />
  <fieldset><legend>{tpl:$RaceStageInfo.RaceStageName/} 补给点列表 <a href="{tpl:$this.sign/}">返回</a></legend>
<table width="99%" align="center" class="table table-bordered table-striped">
  {tpl:if(count($AidStationList))}
  <tr>
    <th align="center" class="rowtip">补给点</th>
    <th align="center" class="rowtip">操作</th>
  </tr>
  {tpl:loop $AidStationList $AidStationId $AidStationInfo}
  <tr>
    <th align="center" class="rowtip">{tpl:$AidStationInfo.AidStationName/}</th>
    <th align="center" class="rowtip"><a href="javascript:;" onclick="AidStationModify('{tpl:$AidStationInfo.AidStationId/}','{tpl:$AidStationInfo.AidStationName/}')">修改</a> | <a  href="javascript:;" onclick="AidStationDelete('{tpl:$AidStationInfo.AidStationId/}','{tpl:$AidStationInfo.AidStationName/}')">删除</a></th>
    </th>
  </tr>
  {/tpl:loop}
  <tr>
    <th align="center" class="rowtip" colspan="15">  <a href="javascript:;" onclick="AidStationAdd('{tpl:$RaceStageInfo.RaceStageId/}')">点此添加补给点</a>
    </th>
  </tr>
  {tpl:else}
  <tr>
    <th align="center" class="rowtip">本站尚未配置任何补给点<a href="javascript:;" onclick="RaceAdd('{tpl:$RaceStageInfo.RaceStageId/}','{tpl:$RaceGroupInfo.RaceGroupId/}')">点此添加补给点</a>
    </th>
    </th>
  </tr>
  {/tpl:if}
</table>
</fieldset>
</form>
{tpl:tpl contentFooter/}
