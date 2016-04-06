{tpl:tpl contentHeader/}
<script type="text/javascript">
</script>
<div class="br_bottom"></div>
<form id="team_update_form" name="team_update_form" action="{tpl:$this.sign/}&ac=team.update" metdod="post">
<input type="hidden" name="RaceTeamId" id="RaceTeamId" value="{tpl:$RaceTeamInfo.RaceTeamId/}" />
<table width="99%" align="center" class="table table-bordered table-striped" widtd="99%">
<tr class="hover">
<td>队伍名称</td>
<td align="left"><input name="RaceTeamName" type="text" class="span4" id="RaceTeamName" value="{tpl:$RaceTeamInfo.RaceTeamName/}" size="50" /></td>
</tr>
<tr class="hover"><td>所属赛事</td>
	<td align="left">	<select name="RaceCatalogId"  id="RaceCatalogId" size="1"  onchange='getGroupList()'>
			{tpl:loop $RaceCatalogList $RaceCatalogInfo}
			<option value="{tpl:$RaceCatalogInfo.RaceCatalogId/}" {tpl:if($RaceCatalogInfo.RaceCatalogId==$RaceTeamInfo.RaceCatalogId)}selected="selected"{/tpl:if}>{tpl:$RaceCatalogInfo.RaceCatalogName/}</option>
			{/tpl:loop}
		</select></td>
</tr>
	<tr>
	<td>赛事分组列表</td>
	<td align="left"><div id = "SelectedGroupList">
			{tpl:loop $RaceGroupList $RaceGroupInfo}
			<input type="checkbox"  name="SelectedRaceGroup[{tpl:$RaceGroupInfo.RaceGroupId/}]" value="{tpl:$RaceGroupInfo.RaceGroupId/}" {tpl:if($RaceGroupInfo.selected == 1)}checked{/tpl:if} /> {tpl:$RaceGroupInfo.RaceGroupName/}
			{/tpl:loop}
		</div></td>
	</tr>
	<td>队伍说明</td>
	<td align="left"><textarea class="span3" name="RaceTeamComment"  id="RaceTeamComment" cols ="50" rows = "5"/>{tpl:$RaceTeamInfo.RaceTeamComment/}</textarea></td>
	</tr>
	<tr class="noborder"><td></td>
<td><button type="submit" id="team_update_submit">提交</button></td>
</tr>
</table>
</form>

<script type="text/javascript">
$('#team_update_submit').click(function(){
	var options = {
		dataType:'json',
		beforeSubmit:function(formData, jqForm, options) {
		},
		success:function(jsonResponse) {
			if (jsonResponse.errno) {
				var errors = [];
				errors[1] = '赛事分站名称不能为空，请修正后再次提交';
				errors[2] = '赛事分站ID无效，请修正后再次提交';
				errors[3] = '请选择一个有效的赛事，请修正后再次提交';
				errors[4] = '请选择至少一个赛事分组，请修正后再次提交';
				errors[9] = '入库失败，请修正后再次提交';
				divBox.alertBox(errors[jsonResponse.errno],function(){});
			} else {
				var message = '修改赛事分站成功';
				divBox.confirmBox({content:message,ok:function(){windowParent.getRightHtml('{tpl:$this.sign/}');}});
			}
		}
	};
	$('#team_update_form').ajaxForm(options);
});
function getGroupList()
{
	catalog=$("#RaceCatalogId");
	team=$("#RaceTeamId");
	$.ajax
	({
		type: "GET",
		url: "?ctl=xrace/team&ac=get.selected.group&RaceCatalogId="+catalog.val()+"&RaceTeamId="+team.val(),
		success: function(msg)
		{
			$("#SelectedGroupList").html(msg);
		}
	});
//*/
}
</script>
{tpl:tpl contentFooter/}