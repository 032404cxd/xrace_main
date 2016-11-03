{tpl:tpl contentHeader/}
<div class="br_bottom"></div>
<form id="credit_update_form" name="credit_update_form" action="{tpl:$this.sign/}&ac=credit.update" method="post">
<input type="hidden" name="CreditId" value="{tpl:$CreditInfo.CreditId/}" />
<table width="99%" align="center" class="table table-bordered table-striped" widtd="99%">
<tr class="hover">
<td>积分类目名称</td>
<td align="left"><input name="CreditName" type="text" class="span2" id="CreditName" value="{tpl:$CreditInfo.CreditName/}" size="50" /></td>
</tr>
<tr class="hover"><td>所属赛事</td>
	<td align="left">	<select name="RaceCatalogId" size="1">
			{tpl:loop $RaceCatalogList $RaceCatalogInfo}
			<option value="{tpl:$RaceCatalogInfo.RaceCatalogId/}" {tpl:if($RaceCatalogInfo.RaceCatalogId==$CreditInfo.RaceCatalogId)}selected="selected"{/tpl:if}>{tpl:$RaceCatalogInfo.RaceCatalogName/}</option>
			{/tpl:loop}
		</select></td>
</tr>
	<tr class="noborder"><td></td>
<td><button type="submit" id="credit_update_submit">提交</button></td>
</tr>
</table>
</form>

<script type="text/javascript">
$('#credit_update_submit').click(function(){
	var options = {
		dataType:'json',
		beforeSubmit:function(formData, jqForm, options) {
		},
		success:function(jsonResponse) {
			if (jsonResponse.errno) {
				var errors = [];
				errors[1] = '积分类目名称不能为空，请修正后再次提交';
				errors[2] = '请选择一个有效的赛事，请修正后再次提交';
				errors[9] = '入库失败，请修正后再次提交';
				divBox.alertBox(errors[jsonResponse.errno],function(){});
			} else {
				var message = '修改积分类目成功';
				divBox.confirmBox({content:message,ok:function(){windowParent.getRightHtml('{tpl:$this.sign/}');}});
			}
		}
	};
	$('#credit_update_form').ajaxForm(options);
});
</script>
{tpl:tpl contentFooter/}