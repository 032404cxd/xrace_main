{tpl:tpl contentHeader/}
<div class="br_bottom"></div>
<form id="aid_code_gen_form" name="aid_code_gen_form"" action="{tpl:$this.sign/}&ac=aid.code.gen.submit" method="post">
<table width="99%" align="center" class="table table-bordered table-striped">
	<input type="hidden" name="RaceStageId" id="RaceStageId" value="{tpl:$RaceStageId/}" />

	<tr class="hover">
	<tr class="hover"><td>生成数量</td>
		<td align="left">	<select name="AidCodeCount"  id="AidCodeCount" size="1" class="span2">
				{tpl:loop $CountArr $Count}
				<option value="{tpl:$Count/}" >{tpl:$Count/}</option>
				{/tpl:loop}
			</select></td>
	<tr class="hover"><td>分类</td>
		<td align="left">	<select name="AidCodeTypeId"  id="AidCodeTypeId" size="1" class="span2">
                {tpl:loop $AidCodeTypeList $Type $TypeInfo}
				<option value="{tpl:$Type/}">{tpl:$TypeInfo.AidCodeTypeName/}</option>
                {/tpl:loop}
			</select></td>
<tr class="noborder"><td></td>
<td><button type="submit" id="aid_code_gen_submit">提交</button></td>
</tr>
</table>
</form>
<script type="text/javascript">
$('#aid_code_gen_submit').click(function(){
	var options = {
		dataType:'json',
		beforeSubmit:function(formData, jqForm, options) {},
		success:function(jsonResponse) {
			if (jsonResponse.errno) {
				var errors = [];
				errors[9] = '操作失败，请修正后再次提交';
				divBox.alertBox(errors[jsonResponse.errno],function(){});
			} else {
                RaceStageId=$("#RaceStageId");
                var message = '成功生成'+jsonResponse.Gen+'个补给代码';
				divBox.confirmBox({content:message,ok:function(){windowParent.getRightHtml('{tpl:$this.sign/}'+ '&ac=aid.code.list&RaceStageId=' + RaceStageId.val());}});
			}
		}
	};
	$('#aid_code_gen_form').ajaxForm(options);
});

</script>
{tpl:tpl contentFooter/}