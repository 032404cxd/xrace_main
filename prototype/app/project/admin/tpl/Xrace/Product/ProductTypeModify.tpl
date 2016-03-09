{tpl:tpl contentHeader/}
<div class="br_bottom"></div>
<form id="product_type_update_form" name="product_type_update_form" action="{tpl:$this.sign/}&ac=product.type.update" metdod="post">
<input type="hidden" name="ProductTypeId" value="{tpl:$oProductType.ProductTypeId/}" />
<table width="99%" align="center" class="table table-bordered table-striped" widtd="99%">
<tr class="hover">
<td>运动类型名称</td>
<td align="left"><input name="ProductTypeName" type="text" class="span2" id="ProductTypeName" value="{tpl:$oProductType.ProductTypeName/}" size="50" /></td>
</tr>
<tr class="noborder"><td></td>
<td><button type="submit" id="product_type_update_submit">提交</button></td>
</tr>
</table>
</form>

<script type="text/javascript">
$('#product_type_update_submit').click(function(){
	var options = {
		dataType:'json',
		beforeSubmit:function(formData, jqForm, options) {
		},
		success:function(jsonResponse) {
			if (jsonResponse.errno) {
				var errors = [];
				errors[1] = '商品类型名称不能为空，请修正后再次提交';
				errors[9] = '入库失败，请修正后再次提交';
				divBox.alertBox(errors[jsonResponse.errno],function(){});
			} else {
				var message = '修改商品类型成功';
				divBox.confirmBox({content:message,ok:function(){windowParent.getRightHtml('{tpl:$this.sign/}');}});
			}
		}
	};
	$('#product_type_update_form').ajaxForm(options);
});
</script>
{tpl:tpl contentFooter/}