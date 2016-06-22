{tpl:tpl contentHeader/}
<script type="text/javascript">
</script>
<form action="{tpl:$this.sign/}&ac=race.update" name="form" id="form" method="post">
<input type="hidden" name="RaceStageId" id="RaceStageId" value="{tpl:$RaceStageInfo.RaceStageId/}" />
  <input type="hidden" name="RaceGroupId" id="RaceGroupId" value="{tpl:$RaceGroupId/}" />
  <fieldset><legend>{tpl:$RaceStageInfo.RaceStageName/} 套餐组合列表 <a href="{tpl:$this.sign/}">返回</a></legend>
<table width="99%" align="center" class="table table-bordered table-striped">
  {tpl:if(count($RaceCombinationList))}
  <tr>
    <th align="center" class="rowtip">套餐名称</th>
    <th align="center" class="rowtip">价格列表</th>
    <th align="center" class="rowtip">关联比赛</th>
    <th align="center" class="rowtip">关联产品</th>
    <th align="center" class="rowtip">操作</th>
  </tr>
  {tpl:loop $RaceCombinationList $RaceCombiantionId $RaceCombiantionInfo}
  <tr>
    <th align="center" class="rowtip">{tpl:$RaceCombiantionInfo.RaceCombinationName/}</th>
    <th align="center" class="rowtip">{tpl:$RaceCombiantionInfo.PriceList/}</th>
    <th align="center" class="rowtip">
      {tpl:loop $RaceCombiantionInfo.RaceList $RaceId $RaceInfo}
      {tpl:$RaceInfo.RaceGroupInfo.RaceGroupName/}-{tpl:$RaceInfo.RaceName/}<br>
      {/tpl:loop}
    </th>
    <th align="center" class="rowtip">
      {tpl:loop $RaceCombiantionInfo.ProductList $ProductId $ProductInfo}
      {tpl:$ProductInfo.ProductName/}{tpl:$ProductInfo.SkuListText/}<br>
      {/tpl:loop}
    </th>
    <th align="center" class="rowtip"><a href="{tpl:$this.sign/}&ac=race.combination.modify&RaceCombinationId={tpl:$RaceCombiantionId/}">修改</a></th>
  </tr>
  {/tpl:loop}
  <tr>
    <th align="center" class="rowtip" colspan="15"><a href="{tpl:$this.sign/}&ac=race.combination.add&RaceStageId={tpl:$RaceStageInfo.RaceStageId/}">点此添加</a>
    </th>
  </tr>
  {tpl:else}
  <tr>
    <th align="center" class="rowtip">本站尚未配置任何比赛<a href="javascript:;" onclick="RaceAdd('{tpl:$RaceStageInfo.RaceStageId/}','{tpl:$RaceGroupInfo.RaceGroupId/}')">点此添加比赛</a>
    </th>
    </th>
  </tr>
  {/tpl:if}
</table>
</fieldset>
</form>
{tpl:tpl contentFooter/}
