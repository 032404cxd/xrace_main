{tpl:tpl contentHeader/}
<div class="br_bottom"></div>
<form id="product_sku_update_form" name="product_sku_update_form" action="{tpl:$this.sign/}&ac=product.sku.update" method="post">
<table width="99%" align="center" class="table table-bordered table-striped">
<tr class="hover">
<td>商品Sku名称</td>
    <td align="left"><input type="text" class="span2" name="ProductSkuName"  id="ProductSkuName" value="{tpl:$ProductSkuInfo.ProductSkuName/}" size="50" /></td>
</tr>
<td>Sku说明</td>
<td align="left"><input type="text" class="span2" name="ProductSkuComment"  id="ProductSkuComment" value="{tpl:$ProductSkuInfo.comment.ProductSkuComment/}" size="50" /></td>
</tr>
<input type="hidden" name="ProductId" id="ProductId" value="{tpl:$ProductSkuInfo.ProductId/}" />
<input type="hidden" name="ProductSkuId" id="ProductSkuId" value="{tpl:$ProductSkuInfo.ProductSkuId/}" />
<tr class="noborder"><td></td>
<td><button type="submit" id="product_sku_update_submit">提交</button></td>
</tr>
</table>
</form>
<script type="text/javascript">
$('#product_sku_update_submit').click(function(){
	var options = {
		dataType:'json',
		beforeSubmit:function(formData, jqForm, options) {},
		success:function(jsonResponse) {
			if (jsonResponse.errno) {
				var errors = [];
				errors[1] = '商品Sku名称不能为空，请修正后再次提交';
				errors[9] = '入库失败，请修正后再次提交';
				divBox.alertBox(errors[jsonResponse.errno],function(){});
			} else {
				var message = '修改商品Sku成功';
				ProductId=$("#ProductId");
				divBox.confirmBox({content:message,ok:function(){windowParent.getRightHtml('{tpl:$this.sign/}'+ '&ac=product.sku.list&ProductId=' + ProductId.val());}});
			}
		}
	};
	$('#product_sku_update_form').ajaxForm(options);
});
</script>
{tpl:tpl contentFooter/}