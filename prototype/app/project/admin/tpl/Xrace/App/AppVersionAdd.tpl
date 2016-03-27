{tpl:tpl contentHeader/}
<div class="br_bottom"></div>
<form id="app_version_add_form" name="app_version_add_form" action="{tpl:$this.sign/}&ac=app.version.insert" metdod="post">
<table width="99%" align="center" class="table table-bordered table-striped">
<tr class="hover">
<td>APP版本</td>
	<td align="left"><input type="text" class="span2" name="AppVersion"  id="AppVersion" value="" size="50" /></td>
</tr>
<tr class="hover">
<td>APP类型</td>
	<td align="left">
		<select name="AppTypeId" class="span2" size="1">
			{tpl:loop $AppTypeList $AppTypeInfo}
			<option value="{tpl:$AppTypeInfo.AppTypeId/}" >{tpl:$AppTypeInfo.AppTypeName/}</option>
			{/tpl:loop}
		</select>
	</td>
</tr>
<tr class="hover">
	<td>APP系统</td>
	<td align="left">
		<select name="AppOSId" class="span2" size="1">
			{tpl:loop $AppOSList $AppOSInfo}
			<option value="{tpl:$AppOSInfo.AppOSId/}" >{tpl:$AppOSInfo.AppOSName/}</option>
			{/tpl:loop}
		</select>
	</td>
</tr>
<td>APP下载路径</td>
<td align="left"><input type="text" class="span4" name="AppDownloadUrl"  id="AppDownloadUrl" value="" size="50" /></td>
</tr>
<td>APP版本说明</td>
<td align="left"><textarea class="span4" name="VersionComment"  id="VersionComment" cols ="50" rows = "5"/></textarea></td>
</tr>
	<tr class="noborder"><td></td>
<td><button type="submit" id="app_add_submit">提交</button></td>
</tr>
</table>
</form>
<script type="text/javascript">
$('#app_add_submit').click(function(){
	var options = {
		dataType:'json',
		beforeSubmit:function(formData, jqForm, options) {},
		success:function(jsonResponse) {
			if (jsonResponse.errno) {
				var errors = [];
				errors[1] = 'APP类型选择错误，请修正后再次提交';
				errors[2] = 'APP系统选择错误，请修正后再次提交';
				errors[3] = 'APP下载路径填写错误，请修正后再次提交';
				errors[4] = 'APP版本号填写错误，请修正后再次提交';
				errors[9] = '入库失败，请修正后再次提交';
				divBox.alertBox(errors[jsonResponse.errno],function(){});
			} else {
				var message = '添加APP版本成功';
				divBox.confirmBox({content:message,ok:function(){windowParent.getRightHtml('{tpl:$this.sign/}');}});
			}
		}
	};
	$('#app_version_add_form').ajaxForm(options);
});
</script>
{tpl:tpl contentFooter/}