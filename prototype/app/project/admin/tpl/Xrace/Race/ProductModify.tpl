{tpl:tpl contentHeader/}
<form id="product_update_form" name="product_update_form" action="{tpl:$this.sign/}&ac=product.update" method="post">
<input type="hidden" name="RaceCatalogId" id="RaceCatalogId" value="{tpl:$RaceCatalogId /}">   
<input type="hidden" name="RaceStageId" id="RaceStageId" value="{tpl:$RaceStageId/}">   
{tpl:loop $ProductTypeList $ProductTypeId $ProductTypeInfo}
<fieldset>
    <legend>{tpl:$ProductTypeInfo.ProductTypeName/}</legend>
    <table width="99%" align="center" class="table table-bordered table-striped">
        {tpl:loop $ProductTypeInfo.ProductList $ProductId $ProductInfo}
                <tr>
                    <th align="center" class="rowtip"><input type="checkbox" name="ProductChecked[{tpl:$ProductId/}]" id="ProductChecked[{tpl:$ProductId/}]" value="{tpl:$ProductId/}" {tpl:if($ProductInfo.selected==1)}checked="checked"{/tpl:if}>{tpl:$ProductInfo.ProductName/}</th>
                    <th align="center" class="rowtip">单价:<input type="text" name="ProductPrice[{tpl:$ProductId/}][ProductPrice]" id="ProductPrice[{tpl:$ProductId/}][ProductPrice]" value="{tpl:$ProductInfo.ProductPrice/}">0表示免费</th>
					<th align="center" class="rowtip">限购数量:<input type="text" name="ProductPrice[{tpl:$ProductId/}][ProductLimit]" id="ProductPrice[{tpl:$ProductId/}][ProductLimit]" value="{tpl:$ProductInfo.ProductLimit/}"></th>
                </tr>
        {/tpl:loop}
    </table>
</fieldset>
{/tpl:loop}
<table>
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
				errors[9] = '入库失败，请修正后再次提交';
				divBox.alertBox(errors[jsonResponse.errno],function(){});
			} else {
				var message = '产品列表更新成功';
				RaceCatalogId=$("#RaceCatalogId");
				RaceStageId=$("#RaceStageId");
				divBox.confirmBox({content:message,ok:function(){windowParent.getRightHtml('{tpl:$this.sign/}&ac=index&RaceCatalogId=' + RaceCatalogId.val());}});
			}
		}
	};
	$('#product_update_form').ajaxForm(options);
});
</script>
{tpl:tpl contentFooter/}