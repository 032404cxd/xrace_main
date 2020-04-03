{tpl:tpl contentHeader/}
<div class="br_bottom"></div>
<form id="product_update_form" name="product_update_form" action="{tpl:$this.sign/}&ac=product.update" method="post">
<table width="99%" align="center" class="table table-bordered table-striped">
<tr class="hover">
<td>商品名称</td>
    <td align="left"><input type="text" class="span2" name="ProductName"  id="ProductName" value="{tpl:$ProductInfo.ProductName/}" size="50" /></td>
</tr>
<input type="hidden" name="ProductId" id="ProductId" value="{tpl:$ProductInfo.ProductId/}" />
<input type="hidden" name="ProductTypeId" id="ProductTypeId" value="{tpl:$ProductInfo.ProductTypeId/}" />
<tr class="noborder"><td></td>
<td><button type="submit" id="product_update_submit">提交</button></td>
</tr>
</table>
</form>
<script type="text/javascript">
$('#product_update_submit').click(function(){
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
				var message = '修改商品成功';
				ProductTypeId=$("#ProductTypeId");
				divBox.confirmBox({content:message,ok:function(){windowParent.getRightHtml('{tpl:$this.sign/}'+ '&ac=product.list&ProductTypeId=' + ProductTypeId.val());}});
			}
		}
	};
	$('#product_update_form').ajaxForm(options);
});
</script>
{tpl:tpl contentFooter/}