{tpl:tpl contentHeader/}
<div class="br_bottom"></div>
<form id="race_add_form" name="race_add_form"" action="{tpl:$this.sign/}&ac=race.insert" method="post">
<input type="hidden" name="RaceStageId" id="RaceStageId" value="{tpl:$RaceStageId/}" />




<table width="99%" align="center" class="table table-bordered table-striped">
{tpl:if($RaceGroupId==0)}
<tr class="hover"><th align="center" class="rowtip">赛事分组</th><th align="center" class="rowtip">
		<select name="RaceGroupId" size="1" class="span2">
			{tpl:loop $RaceStageInfo.comment.SelectedRaceGroup $RaceGroupInfo}
			<option value="{tpl:$RaceGroupInfo.RaceGroupId/}" >{tpl:$RaceGroupInfo.RaceGroupName/}</option>
			{/tpl:loop}
		</select>
	</th></tr>
{tpl:else}
<input type="hidden" name="RaceGroupId" id="RaceGroupId" value="{tpl:$RaceGroupId/}" />
{/tpl:if}
	<tr class="hover"><th align="center" class="rowtip">比赛名称</th><th align="center" class="rowtip"><input name="RaceName" type="text" class="span2" id="RaceName" size="50" /></th></tr>
<tr class="hover"><th align="center" class="rowtip">比赛类型</th><th align="center" class="rowtip">
			<select name="RaceTypeId" size="1" class="span2">
				{tpl:loop $RaceTypeList $RaceTypeInfo}
				<option value="{tpl:$RaceTypeInfo.RaceTypeId/}" >{tpl:$RaceTypeInfo.RaceTypeName/}</option>
				{/tpl:loop}
			</select>
		</th></tr>
	<tr class="hover"><th align="center" class="rowtip">是否必选</th><th align="center" class="rowtip">
			<input type="radio" name="MustSelect" id="MustSelect" value="1" >是
			<input type="radio" name="MustSelect" id="MustSelect" value="0" checked>否</th>
	</tr>
	<tr class="hover"><th align="center" class="rowtip">是否排他单选</th><th align="center" class="rowtip">
			<input type="radio" name="SingleSelect" id="SingleSelect" value="1" >是
			<input type="radio" name="SingleSelect" id="SingleSelect" value="0" checked>否</th>
	</tr>
	<tr class="hover"><th align="center" class="rowtip">计时数据方式</th><th align="center" class="rowtip">
			<select name="RaceTimingType" size="1" class="span2">
				{tpl:loop $RaceTimingTypeList $RaceTimingType $RaceTimingTypeName}
				<option value="{tpl:$RaceTimingType/}" >{tpl:$RaceTimingTypeName/}</option>
				{/tpl:loop}
			</select>
		</th></tr>
	<tr class="hover"><th align="center" class="rowtip">计时数据方式</th><th align="center" class="rowtip">
			<select name="RaceTimingResultType" size="1" class="span2">
				{tpl:loop $RaceTimingResultTypeList $RaceTimingResultType $RaceTimingResultTypeName}
				<option value="{tpl:$RaceTimingResultType/}" >{tpl:$RaceTimingResultTypeName/}</option>
				{/tpl:loop}
			</select>
		</th></tr>
	<tr class="hover"><th align="center" class="rowtip">Mylaps表前缀</th><th align="center" class="rowtip"><input name="MylapsPrefix" type="text" class="span2" id="MylapsPrefix" size="50" /></th></tr>
	<tr class="hover"><th align="center" class="rowtip">Mylaps计时点忍耐时间</th><th align="center" class="rowtip"><input name="MylapsTolaranceTime" type="text" class="span1" id="MylapsTolaranceTime" value = "60" size="50" />秒</th></tr>
	<tr class="hover"><th align="center" class="rowtip">百度路线ID</th><th align="center" class="rowtip"><input name="BaiDuMapID" type="text" class="span2" id="BaiDuMapID" size="50" /></th></tr>
	<tr class="hover"><th align="center" class="rowtip"  rowspan="2">百度路线起止时间</th><th align="center" class="rowtip"><input name="BaiDuMapStartTime" type="text" class="input-medium" id="BaiDuMapStartTime"  size="50" onFocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm:ss'})"  /></th></tr>
	<tr class="hover"><th align="center" class="rowtip"><input name="BaiDuMapEndTime" type="text" class="input-medium" id="BaiDuMapEndTime"  size="50" onFocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm:ss'})"/></th></tr>
	<tr class="hover"><th align="center" class="rowtip">人数/价格对应<p>(人数;单价|人数:单价)</th><th align="center" class="rowtip"><input name="PriceList" type="text" class="span2" id="PriceList" size="50" /></th></tr>
<tr class="hover"><th align="center" class="rowtip">个人报名</th><th align="center" class="rowtip"><input type="radio" name="SingleUser" id="SingleUser" value="1" checked>接受<input type="radio" name="SingleUser" id="SingleUser"  value="0" >不接受</th></tr>
<tr class="hover"><th align="center" class="rowtip">个人报名人数上限</th><th align="center" class="rowtip"><input name="SingleUserLimit" type="text" class="span2" id="SingleUserLimit" value = "0" size="50" /></th></tr>
<tr class="hover"><th align="center" class="rowtip">团队报名</th><th align="center" class="rowtip"><input type="radio" name="TeamUser" id="TeamUser" value="1" checked>接受<input type="radio" name="TeamUser" id="TeamUser" value="0" >不接受</th></tr>
<tr class="hover"><th align="center" class="rowtip">团队数量上限</th><th align="center" class="rowtip"><input name="TeamLimit" type="text" class="span2" id="TeamLimit" value = "0" size="50" /></th></tr>
<tr class="hover"><th align="center" class="rowtip">团队人数限制</th><th align="center" class="rowtip">最少:<input name="TeamUserMin" type="text" class="span1" id="TeamUserMin" value = "6" size="50" />最高:<input name="TeamUserMax" type="text" class="span1" id="TeamUserMax" value = "9" size="50" /></th></tr>
<tr class="hover"><th align="center" class="rowtip">开始报名时间</th><th align="center" class="rowtip"><input type="text" name="ApplyStartTime" value="{tpl:$ApplyStartTime/}" class="input-medium"   onFocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm:ss'})" ></th></tr>
<tr class="hover"><th align="center" class="rowtip">结束报名时间</th><th align="center" class="rowtip"><input type="text" name="ApplyEndTime" value="{tpl:$ApplyEndTime/}" value="" class="input-medium"   onFocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm:ss'})" ></th></tr>
<tr class="hover"><th align="center" class="rowtip">开始时间</th><th align="center" class="rowtip"><input type="text" name="StartTime" value="{tpl:$StartTime/}" class="input-medium"   onFocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm:ss'})" ></th></tr>
<tr class="hover"><th align="center" class="rowtip">结束时间</th><th align="center" class="rowtip"><input type="text" name="EndTime" value="{tpl:$EndTime/}" value="" class="input-medium"   onFocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm:ss'})" ></th></tr>
	<tr class="hover"><td colspan = 2>比赛介绍</td></tr>
	<tr class="hover"><td colspan = 2><?php echo $editor->editor("RaceComment",""); ?></td>
	</tr>	<tr class="noborder"><td></td>
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
				errors[9] = '入库失败，请修正后再次提交';
				errors[10] = '结束时间不能早于开始时间,请修正后再次提交';
				errors[11] = '结束报名时间不能早于开始报名时间,请修正后再次提交';
				errors[12] = '结束报名时间不能晚于比赛开始时间,请修正后再次提交';
				errors[13] = '单人报名人数上限错误,请修正后再次提交';
				errors[14] = '团队报名数量上限错误,请修正后再次提交';
				errors[15] = '团队报名人数限制错误,请修正后再次提交';
				divBox.alertBox(errors[jsonResponse.errno],function(){});
			} else {
				var message = '添加比赛成功';
				RaceStageId=$("#RaceStageId");
				RaceGroupId=$("#RaceGroupId");
				divBox.confirmBox({content:message,ok:function(){windowParent.getRightHtml('{tpl:$this.sign/}'+ '&ac=race.list&RaceStageId=' + RaceStageId.val() + '&RaceGroupId=' + RaceGroupId.val());}});
			}
		}
	};
	$('#race_add_form').ajaxForm(options);
});
</script>
{tpl:tpl contentFooter/}