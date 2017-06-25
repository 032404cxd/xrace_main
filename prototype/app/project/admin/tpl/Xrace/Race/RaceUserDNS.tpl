{tpl:tpl contentHeader/}

<div class="br_bottom"></div>
<form id="dns_form" name="dns_form" action="{tpl:$this.sign/}&ac=user.race.dns" method="post">
<input type="hidden" name="ApplyId" value="{tpl:$ApplyId/}" />
	<input type="hidden" name="RaceId" id="RaceId" value="{tpl:$UserRaceApplyInfo.RaceId/}" />

	<table width="99%" align="center" class="table table-bordered table-striped" widtd="99%">
	<tr class="hover">
		<td >操作员</td><td>{tpl:$manager_name/}</td>
	</tr>
	<tr class="hover">
	<td colspan = 2>理由</td>
</tr>
	<tr>
	<td align="left" colspan = 2><textarea name="Reason" id="Reason" class="span6" rows="4"></textarea></td>
<tr class="noborder">
<td colspan = 2 align=""><button type="submit" id="dns_submit" name="dns_submit">提交</button></td>
</tr>
</table>
</form>

<script type="text/javascript">
$('#dns_submit').click(function(){
	var options = {
		dataType:'json',
		beforeSubmit:function(formData, jqForm, options) {
		},
		success:function(jsonResponse) {
			if (jsonResponse.errno) {
				var errors = [];
				errors[0] = '操作失败，请修正后再次提交';
				divBox.alertBox(errors[jsonResponse.errno],function(){});
			} else {
				var message = '操作成功';
                RaceId=$("#RaceId");
				divBox.confirmBox({content:message,ok:function(){windowParent.getRightHtml('{tpl:$this.sign/}&ReturnType=1&ac=race.user.list&RaceId=' + RaceId.val());}});
			}
		}
	};
	$('#dns_form').ajaxForm(options);
});
</script>
{tpl:tpl contentFooter/}