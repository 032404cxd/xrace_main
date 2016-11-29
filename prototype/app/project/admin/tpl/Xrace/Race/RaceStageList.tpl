{tpl:tpl contentHeader/}
<script type="text/javascript">
  $(document).ready(function(){
    $('#add_race_stage').click(function(){
      addRaceStageBox= divBox.showBox('{tpl:$this.sign/}&ac=race.stage.add', {title:'添加赛事分站',width:800,height:600});
    });
  });
  function RaceStageModify(mid){
    modifyRaceStageBox = divBox.showBox('{tpl:$this.sign/}&ac=race.stage.modify&RaceStageId=' + mid, {title:'修改赛事分站',width:800,height:600});
  }
  function RaceStageDelete(p_id, p_name){
    deleteRaceStageBox= divBox.confirmBox({content:'是否删除 ' + p_name + '?',ok:function(){location.href = '{tpl:$this.sign/}&ac=race.stage.delete&RaceStageId=' + p_id;}});
  }
  function ProductModify(sid){
    modifyProductBox= divBox.showBox('{tpl:$this.sign/}&ac=product.modify&RaceStageId=' + sid, {title:'编辑产品',width:800,height:600});
  }
  function Combination(sid){
    modifyProductBox= divBox.showBox('{tpl:$this.sign/}&ac=race.combination.list&RaceStageId=' + sid, {title:'报名组合',width:600,height:400});
  }
</script>

<fieldset><legend>操作</legend>
[ <a href="javascript:;" id="add_race_stage">添加赛事分站</a> ]
</fieldset>
<form action="{tpl:$this.sign/}" name="form" id="form" method="post">
  <select name="RaceCatalogId" size="1">
    <option value="0">全部</option>
    {tpl:loop $RaceCatalogList $RaceCatalogInfo}
    <option value="{tpl:$RaceCatalogInfo.RaceCatalogId/}" {tpl:if($RaceCatalogInfo.RaceCatalogId==$RaceCatalogId)}selected="selected"{/tpl:if}>{tpl:$RaceCatalogInfo.RaceCatalogName/}</option>
    {/tpl:loop}
  </select>
  <input type="submit" name="Submit" value="查询" />
</form>
<fieldset><legend>赛事分站列表 </legend>
<table width="99%" align="center" class="table table-bordered table-striped">
  <tr>
    <th align="center" class="rowtip">对应赛事</th>
    <th align="center" class="rowtip">赛事分站名称</th>
    <th align="center" class="rowtip">赛事分站图标</th>
    <th align="center" class="rowtip">日期</th>
    <th align="center" class="rowtip">比赛列表</th>
    <th align="center" class="rowtip" width = "30%">已开设组别</th>
    <th align="center" class="rowtip">已选择产品</th>
    <th align="center" class="rowtip">当前状态</th>
    <th align="center" class="rowtip">操作</th>
  </tr>

  {tpl:loop $RaceStageList $RaceCatalogId $RaceCatalogInfoInfo}
  <tr>
    <th align="center" class="rowtip"  rowspan = {tpl:$RaceCatalogInfoInfo.RaceStageList func="count(@@)+1" /}>{tpl:$RaceCatalogInfoInfo.RaceCatalogName/}</th>
  </tr>
  {tpl:loop $RaceCatalogInfoInfo.RaceStageList $RaceStageId $RaceStageInfo}
  <tr>
    <th align="center" class="rowtip" >{tpl:$RaceStageInfo.RaceStageName/}<br>{tpl:$RaceStageInfo.RaceStructureName/}</th>
    <th align="center">{tpl:$RaceStageInfo.RaceStageIconList/}</th>
    <th align="center" class="rowtip" >{tpl:$RaceStageInfo.StageStartDate/}<br>~<br>{tpl:$RaceStageInfo.StageEndDate/}</th>
    <td align="center" class="rowtip" >{tpl:$RaceStageInfo.RaceList/}</td>
    <td align="center" class="rowtip" >{tpl:$RaceStageInfo.SelectedGroupList/}</td>
    <td align="center" class="rowtip" ><a href="javascript:;" onclick="ProductModify('{tpl:$RaceStageInfo.RaceStageId/}');">{tpl:$RaceStageInfo.SelectedProductList/}</a></td>
    <td align="center" class="rowtip" >{tpl:$RaceStageInfo.RaceStageStatus.StageStatusName/}</td>
    <th align="center" class="rowtip" ><a href="{tpl:$this.sign/}&ac=race.user.check.in.status&RaceStageId={tpl:$RaceStageInfo.RaceStageId/}">签到</a> | <a  href="javascript:;" onclick="RaceStageDelete('{tpl:$RaceStageInfo.RaceStageId/}','{tpl:$RaceStageInfo.RaceStageName/}');">删除</a> |  <a href="javascript:;" onclick="RaceStageModify('{tpl:$RaceStageInfo.RaceStageId/}');">修改</a> | <a href="javascript:;" onclick="ProductModify('{tpl:$RaceStageInfo.RaceStageId/}');">产品</a> |  <a href="{tpl:$this.sign/}&ac=race.combination.list&RaceStageId={tpl:$RaceStageInfo.RaceStageId/}">套餐组合</a></th></tr>

  </tr>
  {/tpl:loop}
  {/tpl:loop}


</table>
</fieldset>
{tpl:tpl contentFooter/}
