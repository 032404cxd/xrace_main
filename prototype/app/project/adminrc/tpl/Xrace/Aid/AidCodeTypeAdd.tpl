{tpl:tpl contentHeader/}
<div class="br_bottom"></div>
<form id="aid_code_type_add_form" name="aid_code_type_add_form"" action="{tpl:$this.sign/}&ac=aid.code.type.insert" method="post">
<table width="99%" align="center" class="table table-bordered table-striped">
	<input type="hidden" name="RaceStageId" id="RaceStageId" value="{tpl:$RaceStageId/}" />

	<tr class="hover">
	<tr class="hover"><td>分类名称</td>
		<td align="left">	<input type="text" name="AidCodeTypeName" id="AidCodeTypeName" class="span2"/>
		</td>
<tr class="noborder"><td></td>
<td><button type="submit" id="aid_code_type_add_submit">提交</button></td>
</tr>
</table>
</form>
<script type="text/javascript">
$('#aid_code_type_add_submit').click(function(){
	var options = {
		dataType:'json',
		beforeSubmit:function(formData, jqForm, options) {},
		success:function(jsonResponse) {
			if (jsonResponse.errno) {
				var errors = [];
                errors[1] = '分类名称不能为空，请修正后再次提交';
				errors[9] = '操作失败，请修正后再次提交';
				divBox.alertBox(errors[jsonResponse.errno],function(){});
			} else {
                RaceStageId=$("#RaceStageId");
                var message = '分类名称创建成功';
				divBox.confirmBox({content:message,ok:function(){windowParent.getRightHtml('{tpl:$this.sign/}'+ '&ac=aid.code.list&RaceStageId=' + RaceStageId.val());}});
			}
		}
	};
	$('#aid_code_type_add_form').ajaxForm(options);
});

</script>
{tpl:tpl contentFooter/}