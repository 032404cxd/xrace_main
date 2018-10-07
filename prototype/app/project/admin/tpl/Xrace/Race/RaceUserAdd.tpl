{tpl:tpl contentHeader/}
<form id="race_user_add_form" name="race_user_add_form" action="{tpl:$this.sign/}&ac=race.user.insert" method="post">
<input type="hidden" name="RaceId" id="RaceId" value="{tpl:$RaceInfo.RaceId/}" />
<table width="99%" align="center" class="table table-bordered table-striped">
	<tr class="hover"><th align="center" class="rowtip">BIB</th><th align="center" class="rowtip"><input name="BIB" type="text" class="span2" id="BIB"  size="50" /></th></tr>
	{tpl:if($RaceStageInfo.comment.RaceStructure=="race")}
	<tr class="hover"><th align="center" class="rowtip">分组</th>
		<th align="center" class="rowtip">
			<select name="RaceGroupId" size="1" class="span2">
				{tpl:loop $RaceStageInfo.comment.SelectedRaceGroup $RaceGroupInfo}
				<option value="{tpl:$RaceGroupInfo.RaceGroupId/}" >{tpl:$RaceGroupInfo.RaceGroupName/}</option>
				{/tpl:loop}
			</select>
		</th></tr>
	{tpl:else}
	<input type="hidden" name="RaceGroupId" id="RaceGroupId" value="{tpl:$RaceGroupId/}" />
	{/tpl:if}
	</th></tr>
	<tr class="hover"><th align="center" class="rowtip">芯片</th><th align="center" class="rowtip"><input name="ChipId" type="text" class="span2" id="ChipId" value = "" size="50" /></th></tr>

	<tr class="hover"><th align="center" class="rowtip">姓名</th><th align="center" class="rowtip"><input name="Name" type="text" class="span2" id="Name" value = "" size="50" /></th></tr>
	<tr class="hover"><th align="center" class="rowtip">性别</th>
		<th align="center" class="rowtip">
			<select name="Sex" size="1" class="span3">
				{tpl:loop $SexList $Sex $SexName}
				<option value="{tpl:$Sex/}" >{tpl:$SexName/}</option>
				{/tpl:loop}
			</select>

		</th></tr>
	<tr class="hover"><th align="center" class="rowtip">证件类型</th>
		<th align="center" class="rowtip">
			<select name="IdType" size="1" class="span2">
				{tpl:loop $AuthIdTypesList $IdType $IdTypeName}
				<option value="{tpl:$IdType/}" >{tpl:$IdTypeName/}</option>
				{/tpl:loop}
			</select>
		</th></tr>
	<tr class="hover"><th align="center" class="rowtip">BIB</th><th align="center" class="rowtip"><input name="BIB" type="text" class="span2" id="BIB" value = "" size="50" /></th></tr>
	<tr class="hover"><th align="center" class="rowtip">证件号码</th><th align="center" class="rowtip"><input name="IdNo" type="text" class="span4" id="IdNo" value = "" size="50" /></th></tr>
	<tr class="hover"><th align="center" class="rowtip">联系电话</th><th align="center" class="rowtip"><input name="ContactMobile" type="text" class="span2" id="ContactMobile" value = "" size="50" /></th></tr>
	<tr class="hover"><th align="center" class="rowtip">队伍</th><th align="center" class="rowtip"><input name="TeamName" type="text" class="span2" id="TeamName" value = "" size="50" /></th></tr>
	<tr class="noborder"><td></td>
<td><button type="submit" id="race_user_add_submit">提交</button></td>
</tr>
</table>
</form>
<script type="text/javascript">
$('#race_user_add_submit').click(function(){
	var options = {
		dataType:'json',
		beforeSubmit:function(formData, jqForm, options) {},
		success:function(jsonResponse) {
			if (jsonResponse.errno) {
				var errors = [];
				errors[1] = '比赛名称不能为空，请修正后再次提交';
				errors[2] = '请选择一个有效的赛事分站，请修正后再次提交';
				errors[3] = '请选择一个有效的赛事组别，请修正后再次提交';
				errors[4] = '价格参数不能为空，请修正后再次提交';
				errors[5] = '开始时间不能早于当前时间，请修正后再次提交';
				errors[6] = '结束时间不能早于当前时间，请修正后再次提交';
				errors[7] = '个人和团体报名至少要开放一个，请修正后再次提交';
				errors[8] = '比赛ID错误，请修正后再次提交';
				errors[9] = '入库失败，请修正后再次提交';
				errors[10] = '结束时间不能早于开始时间,请修正后再次提交';
				errors[11] = '结束报名时间不能早于开始报名时间,请修正后再次提交';
				errors[12] = '结束报名时间不能晚于比赛开始时间,请修正后再次提交';
				errors[13] = '单人报名人数上限错误,请修正后再次提交';
				errors[14] = '团队报名数量上限错误,请修正后再次提交';
				errors[15] = '团队报名人数限制错误,请修正后再次提交';
				divBox.alertBox(errors[jsonResponse.errno],function(){});
			} else {
				var message = '选手添加成功';
				RaceId=$("#RaceId");
				divBox.confirmBox({content:message,ok:function(){windowParent.getRightHtml('{tpl:$this.sign/}'+ '&ac=race.user.list&RaceId=' + RaceId.val());}});
			}
		}
	};
	$('#race_user_add_form').ajaxForm(options);
});
</script>
{tpl:tpl contentFooter/}