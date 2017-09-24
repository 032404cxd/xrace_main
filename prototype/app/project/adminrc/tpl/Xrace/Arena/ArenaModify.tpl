{tpl:tpl contentHeader/}
<div class="br_bottom"></div>
<form id="arena_update_form" name="arena_update_form" action="{tpl:$this.sign/}&ac=arena.update" method="post">
<input type="hidden" name="ArenaId" value="{tpl:$ArenaInfo.ArenaId/}" />
<table width="99%" align="center" class="table table-bordered table-striped" widtd="99%">
<tr class="hover">
<td>场地名称</td>
<td align="left"><input name="ArenaName" type="text" class="span2" id="ArenaName" value="{tpl:$ArenaInfo.ArenaName/}" size="50" /></td>
</tr>
<tr class="noborder"><td></td>
<td><button type="submit" id="arena_update_submit">提交</button></td>
</tr>
</table>
</form>

<script type="text/javascript">
$('#arena_update_submit').click(function(){
	var options = {
		dataType:'json',
		beforeSubmit:function(formData, jqForm, options) {
		},
		success:function(jsonResponse) {
			if (jsonResponse.errno) {
				var errors = [];
				errors[1] = '场地名称不能为空，请修正后再次提交';
				errors[9] = '入库失败，请修正后再次提交';
				divBox.alertBox(errors[jsonResponse.errno],function(){});
			} else {
				var message = '修改场地成功';
				divBox.confirmBox({content:message,ok:function(){windowParent.getRightHtml('{tpl:$this.sign/}');}});
			}
		}
	};
	$('#arena_update_form').ajaxForm(options);
});
</script>
{tpl:tpl contentFooter/}