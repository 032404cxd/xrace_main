{tpl:tpl contentHeader/}
<div class="br_bottom"></div>
<form id="race_add_form" name="race_add_form"" action="{tpl:$this.sign/}&ac=race.update" metdod="post">
<input type="hidden" name="RaceStageId" id="RaceStageId" value="{tpl:$RaceStageId/}" />
<input type="hidden" name="RaceGroupId" id="RaceGroupId" value="{tpl:$RaceGroupId/}" />
<input type="hidden" name="RaceId" id="RaceId" value="{tpl:$RaceId/}" />
<table width="99%" align="center" class="table table-bordered table-striped">
<tr class="hover"><th align="center" class="rowtip">比赛名称</th><th align="center" class="rowtip"><input name="RaceName" type="text" class="span2" id="RaceName" value = "{tpl:$RaceInfo.RaceName/}" size="50" /></th></tr>
<tr class="hover"><th align="center" class="rowtip">人数/价格对应</th><th align="center" class="rowtip"><input name="PriceList" type="text" class="span2" id="PriceList" value = "{tpl:$RaceInfo.PriceList/}" size="50" /></th></tr>
<tr class="hover"><th align="center" class="rowtip">个人报名</th><th align="center" class="rowtip">
		<input type="radio" name="SingleUser" id="SingleUser" value="1" {tpl:if($RaceInfo.SingleUser=="1")}checked{/tpl:if}>接受
		<input type="radio" name="SingleUser" id="SingleUser" value="0" {tpl:if($RaceInfo.SingleUser=="0")}checked{/tpl:if}>不接受</th>
</tr>
<tr class="hover"><th align="center" class="rowtip">团队报名</th><th align="center" class="rowtip">
		<input type="radio" name="TeamUser" id="TeamUser" value="1" {tpl:if($RaceInfo.TeamUser=="1")}checked{/tpl:if}>接受
		<input type="radio" name="TeamUser" id="TeamUser" value="0" {tpl:if($RaceInfo.TeamUser=="0")}checked{/tpl:if}>不接受</th>
</tr>
<tr class="hover"><th align="center" class="rowtip">开始时间</th>
	<th align="center" class="rowtip"><input type="text" name="StartTime" value="{tpl:$RaceInfo.StartTime/}" class="input-medium"   onFocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm:ss'})" ></th></tr>
<tr class="hover"><th align="center" class="rowtip">结束时间</th>
	<th align="center" class="rowtip"><input type="text" name="EndTime" value="{tpl:$RaceInfo.EndTime/}" value="" class="input-medium"   onFocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm:ss'})" ></th></tr>

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
				errors[5] = '开始时间错误，请修正后再次提交';
				errors[6] = '结束时间错误，请修正后再次提交';
				errors[7] = '个人和团体报名至少要开放一个，请修正后再次提交';
				errors[8] = '比赛ID错误，请修正后再次提交';
				errors[9] = '入库失败，请修正后再次提交';
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