{tpl:tpl contentHeader/}
<div class="br_bottom"></div>
<form id="product_add_form" name="product_add_form" action="{tpl:$this.sign/}&ac=product.insert" method="post">
<table width="99%" align="center" class="table table-bordered table-striped">
<tr class="hover">
<td>商品名称</td>
    <td align="left"><input type="text" class="span2" name="ProductName"  id="ProductName" value="" size="50" /></td>
</tr>
<input type="hidden" name="ProductTypeId" id="ProductTypeId" value="{tpl:$productTypeId/}" />
<tr class="noborder"><td></td>
<td><button type="submit" id="product_add_submit">提交</button></td>
</tr>
</table>
</form>
<script type="text/javascript">
$('#product_add_submit').click(function(){
	var options = {
		dataType:'json',
		beforeSubmit:function(formData, jqForm, options) {},
		success:function(jsonResponse) {
			if (jsonResponse.errno) {
				var errors = [];
				errors[1] = '商品名称不能为空，请修正后再次提交';
				errors[9] = '入库失败，请修正后再次提交';
				divBox.alertBox(errors[jsonResponse.errno],function(){});
			} else {
				var message = '添加商品成功';
				divBox.confirmBox({content:message,ok:function(){windowParent.getRightHtml('{tpl:$productSign/}');}});
			}
		}
	};
	$('#product_add_form').ajaxForm(options);
});
</script>
{tpl:tpl contentFooter/}