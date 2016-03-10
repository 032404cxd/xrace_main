{tpl:tpl contentHeader/}
<div class="br_bottom"></div>
<form id="product_type_add_form" name="product_type_add_form" action="{tpl:$this.sign/}&ac=product.type.insert" metdod="post">
<table width="99%" align="center" class="table table-bordered table-striped">
<tr class="hover">
<td>商品类型名称</td>
	<td align="left"><input type="text" class="span2" name="ProductTypeName"  id="ProductTypeName" value="" size="50" /></td>
</tr>
<tr class="hover"><td>所属赛事</td>
	<td align="left">	<select name="RaceCatalogId" size="1">
			<option value="0">全部</option>
			{tpl:loop $RaceCatalogArr $RaceCatalogInfo}
			<option value="{tpl:$RaceCatalogInfo.RaceCatalogId/}" >{tpl:$RaceCatalogInfo.RaceCatalogName/}</option>
			{/tpl:loop}
		</select></td>
</tr>
	<tr class="noborder"><td></td>
<td><button type="submit" id="app_add_submit">提交</button></td>
</tr>
</table>
</form>
<script type="text/javascript">
$('#app_add_submit').click(function(){
	var options = {
		dataType:'json',
		beforeSubmit:function(formData, jqForm, options) {},
		success:function(jsonResponse) {
			if (jsonResponse.errno) {
				var errors = [];
				errors[1] = '商品类型名称不能为空，请修正后再次提交';
				errors[2] = '商品类型不能为空，请修正后再次提交';
				errors[9] = '入库失败，请修正后再次提交';
				divBox.alertBox(errors[jsonResponse.errno],function(){});
			} else {
				var message = '添加商品类型成功';
				divBox.confirmBox({content:message,ok:function(){windowParent.getRightHtml('{tpl:$this.sign/}');}});
			}
		}
	};
	$('#product_type_add_form').ajaxForm(options);
});
</script>
{tpl:tpl contentFooter/}