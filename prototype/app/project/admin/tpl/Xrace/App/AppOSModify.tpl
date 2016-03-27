{tpl:tpl contentHeader/}
<div class="br_bottom"></div>
<form id="app_os_update_form" name="app_os_update_form" action="{tpl:$this.sign/}&ac=app.os.update" metdod="post">
<input type="hidden" name="AppOSId" value="{tpl:$AppOSInfo.AppOSId/}" />
<table width="99%" align="center" class="table table-bordered table-striped" widtd="99%">
<tr class="hover">
<td>APP系统名称</td>
<td align="left"><input name="AppOSName" type="text" class="span2" id="AppOSName" value="{tpl:$AppOSInfo.AppOSName/}" size="50" /></td>
<tr class="noborder"><td></td>
<td><button type="submit" id="app_os_update_submit">提交</button></td>
</tr>
</table>
</form>

<script type="text/javascript">
$('#app_os_update_submit').click(function(){
	var options = {
		dataOS:'json',
		beforeSubmit:function(formData, jqForm, options) {
		},
		success:function(jsonResponse) {
			if (jsonResponse.errno) {
				var errors = [];
				errors[1] = 'APP系统名称不能为空，请修正后再次提交';
				errors[9] = '入库失败，请修正后再次提交';
				divBox.alertBox(errors[jsonResponse.errno],function(){});
			} else {
				var message = '修改APP系统成功';
				divBox.confirmBox({content:message,ok:function(){windowParent.getRightHtml('{tpl:$this.sign/}');}});
			}
		}
	};
	$('#app_os_update_form').ajaxForm(options);
});
</script>
{tpl:tpl contentFooter/}