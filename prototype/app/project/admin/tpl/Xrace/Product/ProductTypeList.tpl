{tpl:tpl contentHeader/}
<script type="text/javascript">
$(document).ready(function(){
	$('#add_app').click(function(){
		addAppBox = divBox.showBox('{tpl:$this.sign/}&ac=product.type.add', {title:'添加商品类型',width:400,height:200});
	});
});

function productTypeDelete(p_id, p_name){
	deleteAppBox = divBox.confirmBox({content:'是否删除 ' + p_name + '?',ok:function(){location.href = '{tpl:$this.sign/}&ac=product.type.delete&ProductTypeId=' + p_id;}});
}

function productTypeModify(mid){
	modifyProductTypeBox = divBox.showBox('{tpl:$this.sign/}&ac=product.type.modify&ProductTypeId=' + mid, {title:'修改商品类型',width:400,height:200});
}

</script>

<fieldset><legend>操作</legend>
[ <a href="javascript:;" id="add_app">添加商品类型</a> ]
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
<fieldset><legend>商品类型列表 </legend>
<table width="99%" align="center" class="table table-bordered table-striped">
  <tr>
    <th align="center" class="rowtip">对应赛事</th>
    <th align="center" class="rowtip">商品类型ID</th>
    <th align="center" class="rowtip">商品类型名称</th>
    <th align="center" class="rowtip">操作</th>
  </tr>

{tpl:loop $ProductTypeList $RaceCatalogId $RaceCatalogInfo}
  <tr>
    <th align="center" class="rowtip"  rowspan = {tpl:$RaceCatalogInfo.ProductTypeCount  func="@@+1" /}>{tpl:$RaceCatalogInfo.RaceCatalogName/}</th>
  </tr>
  {tpl:loop $RaceCatalogInfo.ProductTypeList $ProductTypeInfo}
  <tr class="hover">
    <td align="center">{tpl:$ProductTypeInfo.ProductTypeId/}</td>
    <td align="center">{tpl:$ProductTypeInfo.ProductTypeName/}</td>
    <td align="center"><a  href="javascript:;" onclick="productTypeDelete('{tpl:$ProductTypeInfo.ProductTypeId/}','{tpl:$ProductTypeInfo.ProductTypeName/}')">删除</a> |  <a href="javascript:;" onclick="productTypeModify('{tpl:$ProductTypeInfo.ProductTypeId/}');">修改</a> |  <a href="?ProductTypeId={tpl:$ProductTypeInfo.ProductTypeId/}&ctl=xrace/product&ac=product.list">产品配置</a></td>
  </tr>
{/tpl:loop}
{/tpl:loop}
</table>
</fieldset>
{tpl:tpl contentFooter/}
