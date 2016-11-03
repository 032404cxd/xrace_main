{tpl:tpl contentHeader/}
<form id="race_add_form" name="race_add_form" action="{tpl:$this.sign/}&ac=race.update" method="post">
<input type="hidden" name="RaceStageId" id="RaceStageId" value="{tpl:$RaceInfo.RaceStageId/}" />
<input type="hidden" name="RaceId" id="RaceId" value="{tpl:$RaceId/}" />
<table width="99%" align="center" class="table table-bordered table-striped">
<tr class="hover"><th align="center" class="rowtip">比赛名称</th><th align="center" class="rowtip"><input name="RaceName" type="text" class="span2" id="RaceName" value = "{tpl:$RaceInfo.RaceName/}" size="50" /></th></tr>
<tr class="hover"><th align="center" class="rowtip">报名时间</th><th align="center" class="rowtip"><input type="text" name="ApplyStartTime" value="{tpl:$RaceInfo.ApplyStartTime/}" class="input-medium"   onFocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm:ss'})" >-<input type="text" name="ApplyEndTime" value="{tpl:$RaceInfo.ApplyEndTime/}" class="input-medium"   onFocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm:ss'})" ></th></tr>
			{tpl:if($RaceStageInfo.comment.RaceStructure=="race")}
			<tr class="hover"><th align="center" class="rowtip">赛事分组</th><th align="center" class="rowtip">
			<table width="99%" align="center" class="table table-bordered table-striped">
				{tpl:loop $RaceStageInfo.comment.SelectedRaceGroup $RaceGroupInfo}
				<tr class="hover">
					<th align="center" class="rowtip"><input type="checkbox"  name="SelectedRaceGroup[{tpl:$RaceGroupInfo.RaceGroupId/}][Selected]"  {tpl:if($RaceGroupInfo.Selected > 0)}checked{/tpl:if} value="{tpl:$RaceGroupInfo.RaceGroupId/}" /> {tpl:$RaceGroupInfo.RaceGroupName/}</th>
					<th align="center" class="rowtip"><input type="text" name="SelectedRaceGroup[{tpl:$RaceGroupInfo.RaceGroupId/}][StartTime]" value="{tpl:$RaceGroupInfo.StartTime/}"  class="input-medium"  onFocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm:ss'})" >.<input name="SelectedRaceGroup[{tpl:$RaceGroupInfo.RaceGroupId/}][RaceStartMicro]" type="text" class="span1" id="SelectedRaceGroup[{tpl:$RaceGroupInfo.RaceGroupId/}][RaceStartMicro]" value = "{tpl:$RaceGroupInfo.RaceStartMicro func="sprintf('%03d',@@)"/}" />-<input type="text" name="SelectedRaceGroup[{tpl:$RaceGroupInfo.RaceGroupId/}][EndTime]" value="{tpl:$RaceGroupInfo.EndTime/}" value="" class="input-medium"   onFocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm:ss'})" ></th>
				</tr>
				{/tpl:loop}
			</table>
			<input type="hidden" name="RaceGroupId" id="RaceGroupId" value="0" />
			<input type="hidden" name="CurrentRaceGroupId" id="CurrentRaceGroupId" value="0" />
			{tpl:else}
	<tr class="hover"><th align="center" class="rowtip">比赛时间</th>
		<th align="center" class="rowtip"><input type="text" name="StartTime" value="{tpl:$RaceInfo.StartTime/}" class="input-medium"   onFocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm:ss'})" >.<input name="RaceStartMicro" type="text" class="span1" id="RaceStartMicro" value = "{tpl:$RaceInfo.comment.RaceStartMicro func="sprintf('%03d',@@)"/}" />-<input type="text" name="EndTime" value="{tpl:$RaceInfo.EndTime/}" value="" class="input-medium"   onFocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm:ss'})" ></th></tr>
	<input type="hidden" name="RaceGroupId" id="RaceGroupId" value="{tpl:$RaceInfo.RaceGroupId/}" />
			<input type="hidden" name="CurrentRaceGroupId" id="CurrentRaceGroupId" value="{tpl:$RaceGroupId/}" />
			{/tpl:if}
		</th></tr>
	<tr class="hover"><th align="center" class="rowtip">比赛类型</th><th align="center" class="rowtip">
		<select name="RaceTypeId" size="1" class="span2">
			{tpl:loop $RaceTypeList $RaceTypeInfo}
			<option value="{tpl:$RaceTypeInfo.RaceTypeId/}" {tpl:if($RaceTypeInfo.RaceTypeId==$RaceInfo.RaceTypeId)}selected="selected"{/tpl:if}>{tpl:$RaceTypeInfo.RaceTypeName/}</option>
			{/tpl:loop}
		</select>
	</th></tr>
<tr class="hover"><th align="center" class="rowtip">是否必选</th><th align="center" class="rowtip">
			<input type="radio" name="MustSelect" id="MustSelect" value="1" {tpl:if($RaceInfo.MustSelect=="1")}checked{/tpl:if}>是
			<input type="radio" name="MustSelect" id="MustSelect" value="0" {tpl:if($RaceInfo.MustSelect=="0")}checked{/tpl:if}>否</th>
	</tr>
	<tr class="hover"><th align="center" class="rowtip">是否排他单选</th><th align="center" class="rowtip">
			<input type="radio" name="SingleSelect" id="SingleSelect" value="1" {tpl:if($RaceInfo.SingleSelect=="1")}checked{/tpl:if}>是
			<input type="radio" name="SingleSelect" id="SingleSelect" value="0" {tpl:if($RaceInfo.SingleSelect=="0")}checked{/tpl:if}>否</th>
	</tr>
	<tr class="hover"><th align="center" class="rowtip">计时数据方式</th><th align="center" class="rowtip">
			<select name="RaceTimingType" size="1" class="span2">
				{tpl:loop $RaceTimingTypeList $RaceTimingType $RaceTimingTypeName}
				<option value="{tpl:$RaceTimingType/}" {tpl:if($RaceTimingType==$RaceInfo.RouteInfo.RaceTimingType)}selected="selected"{/tpl:if} >{tpl:$RaceTimingTypeName/}</option>
				{/tpl:loop}
			</select>
		</th></tr>
	<tr class="hover"><th align="center" class="rowtip">成绩计算方式</th><th align="center" class="rowtip">
			<select name="RaceTimingResultType" size="1" class="span2">
				{tpl:loop $RaceTimingResultTypeList $RaceTimingResultType $RaceTimingResultTypeName}
				<option value="{tpl:$RaceTimingResultType/}" {tpl:if($RaceTimingResultType==$RaceInfo.RouteInfo.RaceTimingResultType)}selected="selected"{/tpl:if}>{tpl:$RaceTimingResultTypeName/}</option>
				{/tpl:loop}
			</select>
		</th></tr>
	<tr class="hover"><th align="center" class="rowtip">Mylaps表前缀</th><th align="center" class="rowtip"><input name="MylapsPrefix" type="text" class="span2" id="MylapsPrefix" value = "{tpl:$RaceInfo.RouteInfo.MylapsPrefix/}" size="50" /></th></tr>
<tr class="hover"><th align="center" class="rowtip">人数/价格对应<p>(人数;单价|人数:单价)</th><th align="center" class="rowtip"><input name="PriceList" type="text" class="span2" id="PriceList" value = "{tpl:$RaceInfo.PriceList/}" size="50" /></th></tr>
<tr class="hover"><th align="center" class="rowtip">个人报名</th><th align="center" class="rowtip">
		<input type="radio" name="SingleUser" id="SingleUser" value="1" {tpl:if($RaceInfo.SingleUser=="1")}checked{/tpl:if}>接受
		<input type="radio" name="SingleUser" id="SingleUser" value="0" {tpl:if($RaceInfo.SingleUser=="0")}checked{/tpl:if}>不接受 人数上限:<input name="SingleUserLimit" type="text" class="span1" id="SingleUserLimit" value = "{tpl:$RaceInfo.comment.SingleUserLimit/}" size="50" /></th></tr>
<tr class="hover"><th align="center" class="rowtip">团队报名</th><th align="center" class="rowtip">
		<input type="radio" name="TeamUser" id="TeamUser" value="1" {tpl:if($RaceInfo.TeamUser=="1")}checked{/tpl:if}>接受
		<input type="radio" name="TeamUser" id="TeamUser" value="0" {tpl:if($RaceInfo.TeamUser=="0")}checked{/tpl:if}>不接受 数量上限:<input name="TeamLimit" type="text" class="span1" id="TeamLimit" value = "{tpl:$RaceInfo.comment.TeamLimit/}" size="50" /></th></tr>
<tr class="hover"><th align="center" class="rowtip">团队人数限制</th><th align="center" class="rowtip">最低:<input name="TeamUserMin" type="text"  class="span1" id="TeamUserMin" value = "{tpl:$RaceInfo.comment.TeamUserMin/}"/>最高:<input name="TeamUserMax" type="text"  class="span1" id="TeamUserMax" value = "{tpl:$RaceInfo.comment.TeamUserMax/}"/></th></tr>
<tr class="hover"><th align="center" class="rowtip">成员数量</th><th align="center" class="rowtip">男性 最低:<input name="SexUser[Min][1]" type="text" class="span1" id="SexUser[Min][1]" value = "{tpl:$RaceInfo.comment.SexUser.Min.1/}" size="50" />最高:<input name="SexUser[Max][1]" type="text" class="span1" id="SexUser[Max][1]" value = "{tpl:$RaceInfo.comment.SexUser.Max.1/}" size="50" /> 女性  最低:<input name="SexUser[Min][2]" type="text" class="span1" id="SexUser[Min][2]" value = "{tpl:$RaceInfo.comment.SexUser.Min.2/}" size="50" />最高:<input name="SexUser[Max][2]" type="text" class="span1" id="SexUser[Max][2]" value = "{tpl:$RaceInfo.comment.SexUser.Max.2/}" size="50" /></th></tr>
<tr class="hover"><th align="center" class="rowtip">起点</th><th align="center" class="rowtip"><input type="radio" name="NoStart" id="NoStart" value="1" {tpl:if($RaceInfo.comment.NoStart=="1")}checked{/tpl:if}>无起点<input type="radio" name="NoStart" id="NoStart"  value="0"  {tpl:if($RaceInfo.comment.NoStart=="0")}checked{/tpl:if}>有起点</th></tr>
	<tr class="hover"><th align="center" class="rowtip">团队成绩名次</th><th align="center" class="rowtip"><select name="TeamResultRank" size="1" class="span2">
				{tpl:loop $t $i }
				<option value="{tpl:$i/}" {tpl:if($i==$RaceInfo.comment.TeamResultRank)}selected="selected"{/tpl:if}>第{tpl:$i/}人</option>
				{/tpl:loop}</select></tr>

	<tr class="hover"><td colspan = 2>比赛介绍</td></tr>
	<tr class="hover"><td colspan = 2><?php echo $editor->editor("RaceComment",$RaceInfo['RaceComment']); ?></td>
	</tr>
<tr class="noborder"><td></td>
<td><button type="submit" id="race_add_submit">提交</button></td>
</tr>
</table>
</form>
<script type="text/javascript">
$('#race_add_submit').click(function(){
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
				var message = '更新比赛成功';
				RaceStageId=$("#RaceStageId");
				RaceGroupId=$("#CurrentRaceGroupId");
				divBox.confirmBox({content:message,ok:function(){windowParent.getRightHtml('{tpl:$this.sign/}'+ '&ac=race.list&RaceStageId=' + RaceStageId.val() + '&RaceGroupId=' + RaceGroupId.val());}});
			}
		}
	};
	$('#race_add_form').ajaxForm(options);
});
</script>
{tpl:tpl contentFooter/}