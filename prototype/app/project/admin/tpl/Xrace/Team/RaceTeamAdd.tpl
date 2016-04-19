{tpl:tpl contentHeader/}
<div class="br_bottom"></div>
<form id="team_add_form" name="team_add_form" action="{tpl:$this.sign/}&ac=team.insert" metdod="post">
<table width="99%" align="center" class="table table-bordered table-striped">
<tr class="hover">
<td>队伍名称</td>
	<td align="left"><input type="text" class="span2" name="RaceTeamName"  id="RaceTeamName" value="" size="50" /></td>
</tr>
	<tr class="hover"><td>所属赛事</td>
		<td align="left">	<select name="RaceCatalogId" id="RaceCatalogId" size="1" onchange="getGroupList()">
				{tpl:loop $RaceCatalogList $RaceCatalogInfo}
				<option value="{tpl:$RaceCatalogInfo.RaceCatalogId/}" >{tpl:$RaceCatalogInfo.RaceCatalogName/}</option>
				{/tpl:loop}
			</select></td>
	</tr>
	<tr class="hover">
		<td>赛事分组列表</td>
		<td align="left"><div id = "SelectedGroupList"></div></td>
	</tr>
	<td>队伍说明</td>
	<td align="left"><textarea class="span3" name="RaceTeamComment"  id="RaceTeamComment" cols ="50" rows = "5"/></textarea></td>
	</tr>
<tr class="noborder"><td></td>
<td><button type="submit" id="team_add_submit">提交</button></td>
</tr>
</table>
</form>
<script type="text/javascript">
$('#team_add_submit').click(function(){
	var options = {
		dataType:'json',
		beforeSubmit:function(formData, jqForm, options) {},
		success:function(jsonResponse) {
			if (jsonResponse.errno) {
				var errors = [];
				errors[1] = '队伍名称不能为空，请修正后再次提交';
				errors[3] = '请选择一个有效的赛事，请修正后再次提交';
				errors[4] = '请选择至少一个赛事分组，请修正后再次提交';
				errors[9] = '入库失败，请修正后再次提交';
				divBox.alertBox(errors[jsonResponse.errno],function(){});
			} else {
				var message = '添加队伍成功';
				divBox.confirmBox({content:message,ok:function(){windowParent.getRightHtml('{tpl:$this.sign/}');}});
			}
		}
	};
	$('#team_add_form').ajaxForm(options);
});
function getGroupList()
{
	catalog=$("#RaceCatalogId");
	$.ajax
	({
		type: "GET",
		url: "?ctl=xrace/team&ac=get.selected.group&RaceCatalogId="+catalog.val(),
		success: function(msg)
		{
			$("#SelectedGroupList").html(msg);
		}
	});
//*/
}
</script>
{tpl:tpl contentFooter/}