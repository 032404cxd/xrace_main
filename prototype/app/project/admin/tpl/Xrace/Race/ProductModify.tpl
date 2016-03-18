{tpl:tpl contentHeader/}
<form id="product_update_form" name="product_update_form" action="{tpl:$this.sign/}&ac=product.update" metdod="post">
<input type="hidden" name="RaceCatalogId" id="RaceCatalogId" value="{tpl:$RaceCatalogId /}">   
<input type="hidden" name="RaceStageId" id="RaceStageId" value="{tpl:$RaceStageId/}">   
{tpl:loop $ProductList $ProductTypeId $ProductTypeInfo}
<fieldset>
    <legend>{tpl:$ProductTypeInfo.ProductTypeName/}</legend>
    <table width="99%" align="center" class="table table-bordered table-striped">
        {tpl:loop $ProductTypeInfo.ProductList $ProductTypeId $ProductInfo}
            {tpl:loop $ProductInfo $ProductId $ProductDetailInfo}
                <tr>
                    <th align="center" class="rowtip"><input type="checkbox" name="ProductChecked[{tpl:$ProductTypeId/}][]" id="ProductChecked[{tpl:$ProductTypeId/}][]" value="{tpl:$ProductDetailInfo.ProductId/}" {tpl:$ProductDetailInfo.ProductChecked/}>{tpl:$ProductDetailInfo.ProductName/}</th>
                    <th align="center" class="rowtip"><input type="text" name="ProductPrice[{tpl:$ProductTypeId/}][]" id="ProductPrice[{tpl:$ProductTypeId/}][]" value="{tpl:$ProductDetailInfo.ProductPrice/}"></th>
                </tr>
            {/tpl:loop} 
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
				var message = '更新产品成功';
                                RaceCatalogId=$("#RaceCatalogId");
                                RaceStageId=$("#RaceStageId");
				divBox.confirmBox({content:message,ok:function(){windowParent.getRightHtml('{tpl:$this.sign/}&ac=product.modify&RaceCatalogId=' + RaceCatalogId.val() + '&RaceStageId=' + RaceStageId.val());}});
			}
		}
	};
	$('#product_update_form').ajaxForm(options);
});
</script>
{tpl:tpl contentFooter/}