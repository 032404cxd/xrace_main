{tpl:tpl contentHeader/}
<script type="text/javascript">
</script>
<form action="{tpl:$this.sign/}&ac=race.combination.update" name="race_combination_modify_form" id="race_combination_modify_form" method="post">
  <input type="hidden" name="RaceCombinationId" id="RaceCombinationId" value="{tpl:$RaceCombinationInfo.RaceCombinationId/}" />
  <input type="hidden" name="RaceStageId" id="RaceStageId" value="{tpl:$RaceCombinationInfo.RaceStageId/}" />
  <table width="99%" align="center" class="table table-bordered table-striped">
  <tr class="hover"><th align="center" class="rowtip">组合名称</th><th align="center" class="rowtip"  colspan="3"><input name="RaceCombinationName" type="text" class="span2" id="RaceCombinationName" size="50" value="{tpl:$RaceCombinationInfo.RaceCombinationName/}"/></th></tr>
  <tr class="hover"><th align="center" class="rowtip">人数/价格对应<p>(人数;单价|人数:单价)</th><th align="center" class="rowtip"  colspan="3"><input name="PriceList" type="text" class="span2" id="PriceList" size="50" value="{tpl:$RaceCombinationInfo.PriceList/}" /></th></tr>
    {tpl:if(count($RaceList))}
    <tr>
      <th align="center" class="rowtip">比赛类型</th>
      <th align="center" class="rowtip">分组/比赛</th>
      <th align="center" class="rowtip">选择</th>
    </tr>
    {tpl:loop $RaceList $Rid $RaceInfo}
    <tr>
      <th align="center" class="rowtip">{tpl:$RaceInfo.RaceTypeName/}</th>
      <th align="center" class="rowtip">{tpl:$RaceInfo.RaceGroupName/}/{tpl:$RaceInfo.RaceName/}</th>
      <th align="center" class="rowtip"><input type="checkbox" name="RaceList[{tpl:$RaceInfo.RaceId/}]" id="RaceList[{tpl:$RaceInfo.RaceId/}]" value="1" {tpl:if($RaceInfo.selected==1)}checked="checked"{/tpl:if}></th>
    </tr>
    {/tpl:loop}
    {tpl:else}
      <tr>
        <th align="center" class="rowtip">本站尚未配置任何比赛</th>
      </tr>
    {/tpl:if}
  {tpl:if(count($RaceStageInfo.comment.SelectedProductList))}
  <tr>
    <th align="center" class="rowtip">产品</th>
    <th align="center" class="rowtip">规格</th>
    <th align="center" class="rowtip">数量</th>
  </tr>
  {tpl:loop $RaceStageInfo.comment.SelectedProductList $ProductId $ProductInfo}
  <tr>
    {tpl:if(count($ProductInfo.ProductSkuList)>0)}
    <th align="center" class="rowtip">{tpl:$ProductInfo.ProductName/}</th>
    <th align="center" class="rowtip">{tpl:$ProductInfo.ProductSkuList/}</th>
    <th align="center" class="rowtip"><input type="text" class="span1" name="ProductList[{tpl:$ProductId/}][ProductCount]" id="ProductList[{tpl:$ProductId/}][ProductCount]" value="{tpl:$ProductInfo.ProductCount/}"></th>
    </tr>
    {/tpl:if}
  </tr>
  {/tpl:loop}
  {/tpl:if}
  <tr class="noborder"><td></td>
    <td colspan="2"><button type="submit" id="race_combination_modify_submit" >提交</button></td>
</table>
</form>
{tpl:tpl contentFooter/}
<script type="text/javascript">
  $('#race_combination_modify_submit').click(function(){
    var options = {
      dataType:'json',
      beforeSubmit:function(formData, jqForm, options) {},
      success:function(jsonResponse) {
        if (jsonResponse.errno) {
          var errors = [];
          errors[1] = '套餐名称不能为空，请修正后再次提交';
          errors[2] = '至少选择2个比赛或产品，请修正后再次提交';
          errors[3] = '套餐名称已被占用，请修正后再次提交';
          errors[5] = '价格参数不能为空，请修正后再次提交';
          errors[9] = '入库失败，请修正后再次提交';
          divBox.alertBox(errors[jsonResponse.errno],function(){});
        } else {
          var message = '修改套餐成功';
          RaceStageId=$("#RaceStageId");
          divBox.confirmBox({content:message,ok:function(){windowParent.getRightHtml('{tpl:$this.sign/}'+ '&ac=race.combination.list&RaceStageId=' + RaceStageId.val());}});
        }
      }
    };
    $('#race_combination_modify_form').ajaxForm(options);
  });
</script>