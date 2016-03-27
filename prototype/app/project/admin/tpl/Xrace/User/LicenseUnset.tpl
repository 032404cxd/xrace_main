{tpl:tpl contentHeader/}
<div class="br_bottom"></div>
<form id="license_delete_form" name="license_add_form" action="{tpl:$this.sign/}&ac=license.delete" metdod="post">
<table width="99%" align="center" class="table table-bordered table-striped">
<input type="hidden" name="LicenseId"  id="LicenseId" value="{tpl:$UserLicenseInfo.LicenseId/}" />
	<tr class="hover">
		<td align="center" class="rowtip">所属赛事</td>
		<td align="center" class="rowtip">{tpl:$UserLicenseInfo.RaceCatalogName/}
		</td>
	</tr>
	<tr class="hover">
		<td align="center" class="rowtip">所属组别</td>
		<td align="center" class="rowtip">{tpl:$UserLicenseInfo.RaceGroupName/}
		</td>
	</tr>
	<tr class="hover">
		<td align="center" class="rowtip">生效时间</td>
		<td align="center" class="rowtip">{tpl:$UserLicenseInfo.LicenseStartDate/} - {tpl:$UserLicenseInfo.LicenseEndDate/}
		</td>
	</tr>
	<tr class="hover">
    <td align="center" class="rowtip">执照删除理由</td>
    <td align="center" class="rowtip">
        <input type="text" name="comment" value="" class="input-medium">
    </td>
</tr>
<tr class="hover">
	<td align="center" class="rowtip" colspan = 2>执照更新记录</td>
</tr>
{tpl:loop $UserLicenseInfo.comment.LicenseUpdateLog $UpdateLog}
<tr class="hover">
	<td align="center" class="rowtip">{tpl:$UpdateLog.time/}</td>
	<td align="center" class="rowtip">{tpl:$UpdateLog.LogText/}</td>
</tr>
{/tpl:loop}
<tr class="noborder">
    <td></td>
    <td><button type="submit" id="license_delete_submit">提交</button></td>
</tr>
</table>
</form>
<script type="text/javascript">
$('#license_delete_submit').click(function(){
	var options = {
		dataType:'json',
		beforeSubmit:function(formData, jqForm, options) {},
		success:function(jsonResponse) {
			if (jsonResponse.errno) {
				var errors = [];
                                errors[1] = '执照删除理由不能为空，请修正后再次提交';
				divBox.alertBox(errors[jsonResponse.errno],function(){});
			} else {
				var message = '删除执照成功';
				UserId=$("#UserId");
				divBox.confirmBox({content:message,ok:function(){windowParent.getRightHtml('{tpl:$this.sign/}'+ '&ac=license.list&UserId=' + UserId.val());}});
			}
		}
	};
	$('#license_delete_form').ajaxForm(options);
});
</script>
{tpl:tpl contentFooter/}