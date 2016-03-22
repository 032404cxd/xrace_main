{tpl:tpl contentHeader/}
<div class="br_bottom"></div>
<form id="race_add_form" name="race_add_form"" action="{tpl:$this.sign/}&ac=race.insert" metdod="post">
<input type="hidden" name="RaceStageId" id="RaceStageId" value="{tpl:$RaceStageId/}" />
<input type="hidden" name="RaceGroupId" id="RaceGroupId" value="{tpl:$RaceGroupId/}" />
<table width="99%" align="center" class="table table-bordered table-striped">
<tr class="hover"><th align="center" class="rowtip">比赛名称</th><th align="center" class="rowtip"><input name="RaceName" type="text" class="span2" id="RaceName" size="50" /></th></tr>
<tr class="hover"><th align="center" class="rowtip">比赛类型</th><th align="center" class="rowtip">
			<select name="RaceTypeId" size="1">
				{tpl:loop $RaceTypeList $RaceTypeInfo}
				<option value="{tpl:$RaceTypeInfo.RaceTypeId/}" >{tpl:$RaceTypeInfo.RaceTypeName/}</option>
				{/tpl:loop}
			</select>
		</th></tr>
<tr class="hover"><th align="center" class="rowtip">百度地图路线ID</th><th align="center" class="rowtip"><input name="BaiDuMapID" type="text" class="span2" id="BaiDuMapID" size="50" /></th></tr>
<tr class="hover"><th align="center" class="rowtip">人数/价格对应</th><th align="center" class="rowtip"><input name="PriceList" type="text" class="span2" id="PriceList" size="50" /></th></tr>
<tr class="hover"><th align="center" class="rowtip">个人报名</th><th align="center" class="rowtip"><input type="radio" name="SingleUser" id="SingleUser" value="1" checked>接受<input type="radio" name="SingleUser" id="SingleUser"  value="0" >不接受</th></tr>
<tr class="hover"><th align="center" class="rowtip">个人报名人数上限</th><th align="center" class="rowtip"><input name="SingleUserLimit" type="text" class="span2" id="SingleUserLimit" value = "0" size="50" /></th></tr>
<tr class="hover"><th align="center" class="rowtip">团队报名</th><th align="center" class="rowtip"><input type="radio" name="TeamUser" id="TeamUser" value="1" checked>接受<input type="radio" name="TeamUser" id="TeamUser" value="0" >不接受</th></tr>
<tr class="hover"><th align="center" class="rowtip">团队数量上限</th><th align="center" class="rowtip"><input name="TeamLimit" type="text" class="span2" id="TeamLimit" value = "0" size="50" /></th></tr>
<tr class="hover"><th align="center" class="rowtip">团队人数限制</th><th align="center" class="rowtip">最少:<input name="TeamUserMin" type="text" class="span1" id="TeamUserMin" value = "6" size="50" />最高:<input name="TeamUserMax" type="text" class="span1" id="TeamUserMax" value = "9" size="50" /></th></tr>
<tr class="hover"><th align="center" class="rowtip">开始报名时间</th><th align="center" class="rowtip"><input type="text" name="ApplyStartTime" value="{tpl:$ApplyStartTime/}" class="input-medium"   onFocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm:ss'})" ></th></tr>
<tr class="hover"><th align="center" class="rowtip">结束报名时间</th><th align="center" class="rowtip"><input type="text" name="ApplyEndTime" value="{tpl:$ApplyEndTime/}" value="" class="input-medium"   onFocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm:ss'})" ></th></tr>
<tr class="hover"><th align="center" class="rowtip">开始时间</th><th align="center" class="rowtip"><input type="text" name="StartTime" value="{tpl:$StartTime/}" class="input-medium"   onFocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm:ss'})" ></th></tr>
<tr class="hover"><th align="center" class="rowtip">结束时间</th><th align="center" class="rowtip"><input type="text" name="EndTime" value="{tpl:$EndTime/}" value="" class="input-medium"   onFocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm:ss'})" ></th></tr>
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