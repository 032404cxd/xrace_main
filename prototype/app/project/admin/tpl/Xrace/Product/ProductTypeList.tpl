{tpl:tpl contentHeader/}
<script type="text/javascript">
$(document).ready(function(){
	$('#add_app').click(function(){
		addAppBox = divBox.showBox('{tpl:$this.sign/}&ac=product.type.add', {title:'添加商品类型',width:400,height:200});
	});
});

function productTypeDelete(p_id, p_name){
	deleteAppBox = divBox.confirmBox({content:'是否删除 ' + p_name + '?',ok:function(){location.href = '{tpl:$this.sign/}&ac=product.type.delete&productTypeId=' + p_id;}});
}

function productTypeModify(mid){
	modifyProductTypeBox = divBox.showBox('{tpl:$this.sign/}&ac=product.type.modify&productTypeId=' + mid, {title:'修改商品类型',width:400,height:200});
}

</script>

<fieldset><legend>操作</legend>
[ <a href="javascript:;" id="add_app">添加商品类型</a> ]
</fieldset>

<fieldset><legend>商品类型列表 </legend>
<table width="99%" align="center" class="table table-bordered table-striped">
  <tr>
    <th align="center" class="rowtip">商品类型ID</th>
    <th align="center" class="rowtip">商品类型名称</th>
    <th align="center" class="rowtip">操作</th>
  </tr>

{tpl:loop $SportTypeArr $oProductType}
  <tr class="hover">
    <td align="center">{tpl:$oProductType.ProductTypeId/}</td>
    <td align="center">{tpl:$oProductType.ProductTypeName/}</td>
    <td align="center"><a  href="javascript:;" onclick="productTypeDelete('{tpl:$oProductType.ProductTypeId/}','{tpl:$oProductType.ProductTypeName/}')">删除</a> |  <a href="javascript:;" onclick="productTypeModify('{tpl:$oProductType.ProductTypeId/}');">修改</a></td>
  </tr>
{/tpl:loop}
</table>
</fieldset>
{tpl:tpl contentFooter/}
