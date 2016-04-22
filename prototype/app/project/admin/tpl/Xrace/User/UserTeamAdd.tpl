{tpl:tpl contentHeader/}
<div class="br_bottom"></div>
<form id="user_team_add_form" name="user_team_add_form"" action="{tpl:$this.sign/}&ac=user.team.insert" method="post">
<table width="99%" align="center" class="table table-bordered table-striped">
	<input type="hidden"  name="UserId"  id="UserId" value="{tpl:$UserInfo.user_id/}" />
<tr class="hover">
<td>用户</td>
	<td align="left">{tpl:$UserInfo.name/}</td>
</tr>
<tr class="hover"><td>所属赛事</td>
	<td align="left">	<select name="RaceCatalogId" id="RaceCatalogId" size="1" class="span2" onchange='getRaceTeamList()'>
			{tpl:loop $RaceCatalogList $RaceCatalogInfo}
			<option value="{tpl:$RaceCatalogInfo.RaceCatalogId/}" >{tpl:$RaceCatalogInfo.RaceCatalogName/}</option>
			{/tpl:loop}
		</select></td>
</tr>
	<tr class="hover"><td>队伍</td>
		<td align="left"><div id = "TeamList"><select name="RaceTeamId" id="RaceTeamId" size="1" class="span2"  onchange='getRaceGroupList()'>
				{tpl:loop $RaceTeamList.RaceTeamList $RaceTeamInfo}
				<option value="{tpl:$RaceTeamInfo.RaceTeamId/}" >{tpl:$RaceTeamInfo.RaceTeamName/}</option>
				{/tpl:loop}
			</select></div></td>
	</tr>
	<tr class="hover"><td>组别</td>
		<td align="left"><select name="RaceGroupId" id="RaceGroupId" size="1" class="span2">
				</select></td>
	</tr>
<tr class="noborder"><td></td>
<td><button type="submit" id="user_team_add_submit">提交</button></td>
</tr>
</table>
</form>
<script type="text/javascript">
	function getRaceTeamList()
	{
		catalog=$("#RaceCatalogId");
		$.ajax
		({
			type: "GET",
			url: "?ctl=xrace/team&ac=get.team.by.catalog&RaceCatalogId="+catalog.val(),
			success: function(msg)
			{
				$("#RaceTeamId").html(msg);
			}
		});
	}
	function getRaceGroupList()
	{
		team=$("#RaceTeamId");
		$.ajax
		({
			type: "GET",
			url: "?ctl=xrace/team&ac=get.group.by.team&RaceTeamId="+team.val(),
			success: function(msg)
			{
				$("#RaceGroupId").html(msg);
			}
		});
	}

$('#user_team_add_submit').click(function(){
	var options = {
		dataType:'json',
		beforeSubmit:function(formData, jqForm, options) {},
		success:function(jsonResponse) {
			if (jsonResponse.errno) {
				var errors = [];
				errors[1] = '请选择一个有效的队伍，请修正后再次提交';
				errors[2] = '请选择一个有效的分组，请修正后再次提交';
				errors[9] = '入库失败，请修正后再次提交';
				divBox.alertBox(errors[jsonResponse.errno],function(){});
			} else {
				var message = '加入队伍成功';
				UserId=$("#UserId");
				divBox.confirmBox({content:message,ok:function(){windowParent.getRightHtml('{tpl:$this.sign/}&ac=user.team.list&UserId=' + UserId.val());}});
			}
		}
	};
	$('#user_team_add_form').ajaxForm(options);
});
</script>
{tpl:tpl contentFooter/}