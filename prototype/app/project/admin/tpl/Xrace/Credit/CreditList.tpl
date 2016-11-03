{tpl:tpl contentHeader/}
<script type="text/javascript">
$(document).ready(function(){
	$('#add_credit').click(function(){
		addCreditBox = divBox.showBox('{tpl:$this.sign/}&ac=credit.add', {title:'添加积分类目',width:400,height:200});
	});
});

function creditDelete(p_id, p_name){
	deleteCreditBox = divBox.confirmBox({content:'是否删除 ' + p_name + '?',ok:function(){location.href = '{tpl:$this.sign/}&ac=credit.delete&CreditId=' + p_id;}});
}

function creditModify(mid){
	modifyCreditBox = divBox.showBox('{tpl:$this.sign/}&ac=credit.modify&CreditId=' + mid, {title:'修改积分类目',width:400,height:200});
}

</script>

<fieldset><legend>操作</legend>
[ <a href="javascript:;" id="add_credit">添加积分类目</a> ]
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
<fieldset><legend>积分类目列表 </legend>
<table width="99%" align="center" class="table table-bordered table-striped">
  <tr>
    <th align="center" class="rowtip">对应赛事</th>
    <th align="center" class="rowtip">积分类目ID</th>
    <th align="center" class="rowtip">积分类目名称</th>
    <th align="center" class="rowtip">操作</th>
  </tr>

{tpl:loop $CreditList $RaceCatalogId $RaceCatalogInfo}
  <tr>
    <th align="center" class="rowtip"  rowspan = {tpl:$RaceCatalogInfo.CreditCount  func="@@+1" /}>{tpl:$RaceCatalogInfo.RaceCatalogName/}</th>
  </tr>
  {tpl:loop $RaceCatalogInfo.CreditList $CreditInfo}
  <tr class="hover">
    <td align="center">{tpl:$CreditInfo.CreditId/}</td>
    <td align="center">{tpl:$CreditInfo.CreditName/}</td>
    <td align="center"><a  href="javascript:;" onclick="creditDelete('{tpl:$CreditInfo.CreditId/}','{tpl:$CreditInfo.CreditName/}')">删除</a> |  <a href="javascript:;" onclick="creditModify('{tpl:$CreditInfo.CreditId/}');">修改</a></td>
  </tr>
{/tpl:loop}
{/tpl:loop}
</table>
</fieldset>
{tpl:tpl contentFooter/}
