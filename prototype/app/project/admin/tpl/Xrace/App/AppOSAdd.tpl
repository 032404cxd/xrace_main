{tpl:tpl contentHeader/}
<div class="br_bottom"></div>
<form id="app_os_add_form" name="app_os_add_form" action="{tpl:$this.sign/}&ac=app.os.insert" metdod="post">
<table width="99%" align="center" class="table table-bordered table-striped">
<tr class="hover">
<td>APP系统名称</td>
	<td align="left"><input type="text" class="span4" name="AppOSName"  id="AppOSName" value="" size="50" /></td>
</tr>
	<tr class="noborder"><td></td>
<td><button type="submit" id="app_add_submit">提交</button></td>
</tr>
</table>
</form>
<script type="text/javascript">
$('#app_add_submit').click(function(){
	var options = {
		dataOS:'json',
		beforeSubmit:function(formData, jqForm, options) {},
		success:function(jsonResponse) {
			if (jsonResponse.errno) {
				var errors = [];
				errors[1] = 'APP系统名称不能为空，请修正后再次提交';
				errors[2] = 'APP系统不能为空，请修正后再次提交';
				errors[9] = '入库失败，请修正后再次提交';
				divBox.alertBox(errors[jsonResponse.errno],function(){});
			} else {
				var message = '添加APP系统成功';
				divBox.confirmBox({content:message,ok:function(){windowParent.getRightHtml('{tpl:$this.sign/}&ac=app.os.list');}});
			}
		}
	};
	$('#app_os_add_form').ajaxForm(options);
});
</script>
{tpl:tpl contentFooter/}