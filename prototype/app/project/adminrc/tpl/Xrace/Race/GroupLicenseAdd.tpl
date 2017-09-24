{tpl:tpl contentHeader/}
<div class="br_bottom"></div>
<form id="group_license_add_form" name="group_license_add_form" action="{tpl:$this.sign/}&ac=group.license.insert" method="post">
<input type="hidden" name="RaceGroupId" value="{tpl:$RaceGroupInfo.RaceGroupId/}" />
<table width="99%" align="center" class="table table-bordered table-striped" widtd="99%">
<tr class="hover">
<tr class="hover"><td>执照审核方式</td>
		<td align="left">
			<select name="LicenseType" id="LicenseType"  size="1"  onchange="getLicenseConditon()">
				{tpl:loop $RaceLisenceTypeList $LicenseType $LicenseTypeName}
				<option value="{tpl:$LicenseType/}" >{tpl:$LicenseTypeName/}</option>
				{/tpl:loop}
			</select>
		</td>
</tr>
	<tr class="hover"><td>审核条件</td><td align="left"><div id = "LicenseCondition"></div></td></tr>
<tr class="noborder"><td></td>
<td><button type="submit" id="group_license_add_submit">提交</button></td>
</tr>
</table>
</form>

<script type="text/javascript">
$('#group_license_add_submit').click(function(){
	var options = {
		dataType:'json',
		beforeSubmit:function(formData, jqForm, options) {
		},
		success:function(jsonResponse) {
			if (jsonResponse.errno) {
				var errors = [];
				errors[1] = '赛事组别ID无效，请修正后再次提交';
				errors[9] = '入库失败，请修正后再次提交';
				divBox.alertBox(errors[jsonResponse.errno],function(){});
			} else {
				var message = '审核条件添加成功';
				divBox.confirmBox({content:message,ok:function(){windowParent.getRightHtml('{tpl:$this.sign/}');}});
			}
		}
	};
	$('#group_license_add_form').ajaxForm(options);
});
function getLicenseConditon()
{
	type=$("#LicenseType");
	$.ajax
	({
		type: "GET",
		url: "?ctl=xrace/race.group&ac=get.license.condition&LicenseType="+type.val(),
		success: function(msg)
		{
			$("#LicenseCondition").html(msg);
		}
	});
//*/
}
</script>
{tpl:tpl contentFooter/}