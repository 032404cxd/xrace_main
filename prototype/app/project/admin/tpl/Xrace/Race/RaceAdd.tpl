{tpl:tpl contentHeader/}
<script type="text/javascript">
    function FinalResultChange(Rtype){
        if(Rtype=="credit")
		{
			$("#CreditList").show();
		}
		else
		{
			$("#CreditList").hide();
		}
    }
    $(document).ready(function(){
        $("#CreditList").hide();
    });
</script>
<script src="js/ckeditor5/ckeditor.js"></script>

<form id="race_add_form" name="race_add_form"" action="{tpl:$this.sign/}&ac=race.insert" method="post">
<input type="hidden" name="RaceStageId" id="RaceStageId" value="{tpl:$RaceStageId/}" />
<table width="99%" align="center" class="table table-bordered table-striped">
<tr class="hover"><th align="center" class="rowtip">比赛名称</th><th align="center" class="rowtip"><input name="RaceName" type="text" class="span3" id="RaceName" size="50" /></th></tr>
<tr class="hover"><th align="center" class="rowtip">报名时间</th><th align="center" class="rowtip"><input type="text" name="ApplyStartTime" value="{tpl:$ApplyStartTime/}" class="input-medium"   onFocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm:ss'})" >-<input type="text" name="ApplyEndTime" value="{tpl:$ApplyEndTime/}" value="" class="input-medium"   onFocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm:ss'})" ></th></tr>
<tr class="hover"><th align="center" class="rowtip">赛事分组</th><th align="center" class="rowtip">
{tpl:if($RaceStageInfo.comment.RaceStructure=="race")}
		<table width="99%" align="center" class="table table-bordered table-striped">
			<input type="hidden" name="RaceGroupId" id="RaceGroupId" value="0" />
			{tpl:loop $RaceStageInfo.comment.SelectedRaceGroup $RaceGroupInfo}
			<tr class="hover">
				<th align="center" class="rowtip"><input type="checkbox"  name="SelectedRaceGroup[{tpl:$RaceGroupInfo.RaceGroupId/}][Selected]"  {tpl:if($RaceGroupInfo.Selected > 0)}checked{/tpl:if} value="{tpl:$RaceGroupInfo.RaceGroupId/}" /> {tpl:$RaceGroupInfo.RaceGroupName/}</th>
				<th align="center" class="rowtip"><input type="text" name="SelectedRaceGroup[{tpl:$RaceGroupInfo.RaceGroupId/}][StartTime]" value="{tpl:$RaceGroupInfo.StartTime/}"  class="input-medium"  onFocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm:ss'})" >.<input name="SelectedRaceGroup[{tpl:$RaceGroupInfo.RaceGroupId/}][RaceStartMicro]" type="text" class="span1" id="SelectedRaceGroup[{tpl:$RaceGroupInfo.RaceGroupId/}][RaceStartMicro]" value = "{tpl:$RaceGroupInfo.RaceStartMicro func="sprintf('%03d',@@)"/}" />-<input type="text" name="SelectedRaceGroup[{tpl:$RaceGroupInfo.RaceGroupId/}][EndTime]" value="{tpl:$RaceGroupInfo.EndTime/}" value="" class="input-medium"   onFocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm:ss'})" >
					<p>BIB号码段：<input name="SelectedRaceGroup[{tpl:$RaceGroupInfo.RaceGroupId/}][BibStart]" type="text" class="span1" id="SelectedRaceGroup[{tpl:$RaceGroupInfo.RaceGroupId/}][BibStart]"  size="50" />-<input name="SelectedRaceGroup[{tpl:$RaceGroupInfo.RaceGroupId/}][BibEnd]" type="text" class="span1" id="SelectedRaceGroup[{tpl:$RaceGroupInfo.RaceGroupId/}][BibEnd]" size="50" /></th>
				</th>
				<th align="center" class="rowtip">积分比例：<input type="text" class="span1" name="SelectedRaceGroup[{tpl:$RaceGroupInfo.RaceGroupId/}][CreditRatio]" value="1"></th>
			</tr>
			{/tpl:loop}
		</table>
		{tpl:else}
<tr class="hover"><th align="center" class="rowtip">比赛时间</th><th align="center" class="rowtip"><input type="text" name="StartTime" value="{tpl:$StartTime/}" class="input-medium"   onFocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm:ss'})" >.<input name="RaceStartMicro" type="text" class="span1" id="RaceStartMicro" value = "000" />-<input type="text" name="EndTime" value="{tpl:$EndTime/}" value="" class="input-medium"   onFocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm:ss'})" ></th></tr>
	{tpl:if($RaceGroupId==0)}
		<select name="RaceGroupId" size="1" class="span2">
			{tpl:loop $RaceStageInfo.comment.SelectedRaceGroup $RaceGroupInfo}
			<option value="{tpl:$RaceGroupInfo.RaceGroupId/}" >{tpl:$RaceGroupInfo.RaceGroupName/}</option>
			{/tpl:loop}
		</select>
		<input type="hidden" name="SelectedRaceGroup" id="SelectedRaceGroup" value="" />
{/tpl:if}
</th></tr>
<input type="hidden" name="RaceGroupId" id="RaceGroupId" value="{tpl:$RaceGroupId/}" />
{/tpl:if}
<tr class="hover"><th align="center" class="rowtip">比赛类型</th><th align="center" class="rowtip">
			<select name="RaceTypeId" size="1" class="span2">
				{tpl:loop $RaceTypeList $RaceTypeInfo}
				<option value="{tpl:$RaceTypeInfo.RaceTypeId/}" >{tpl:$RaceTypeInfo.RaceTypeName/}</option>
				{/tpl:loop}
			</select>
		</th></tr>
	<tr class="hover"><th align="center" class="rowtip">确认后发布成绩</th><th align="center" class="rowtip">
			<select name="ResultNeedConfirm" size="1" class="span2">
				<option value="0" >不需要确认</option>
				<option value="1" >需要确认</option>
			</select>
		</th></tr>
	<tr class="hover"><th align="center" class="rowtip">排他性</th><th align="center" class="rowtip">
            是否必选：<input type="radio" name="MustSelect" id="MustSelect" value="1" >是
			<input type="radio" name="MustSelect" id="MustSelect" value="0" checked>否<p>
            是否排他单选：<input type="radio" name="SingleSelect" id="SingleSelect" value="1" >是
            <input type="radio" name="SingleSelect" id="SingleSelect" value="0" checked>否</th>
	</tr>
	<tr class="hover"><th align="center" class="rowtip">计时数据方式</th><th align="center" class="rowtip">
			<select name="RaceTimingType" size="1" class="span2">
				{tpl:loop $RaceTimingTypeList $RaceTimingType $RaceTimingTypeName}
				<option value="{tpl:$RaceTimingType/}" >{tpl:$RaceTimingTypeName/}</option>
				{/tpl:loop}
			</select>
		</th></tr>
	<tr class="hover"><th align="center" class="rowtip">成绩计算</th><th align="center" class="rowtip">
            成绩计算方式：<select name="RaceTimingResultType" size="1" class="span2">
                {tpl:loop $RaceTimingResultTypeList $RaceTimingResultType $RaceTimingResultTypeName}
                <option value="{tpl:$RaceTimingResultType/}" >{tpl:$RaceTimingResultTypeName/}</option>
                {/tpl:loop}
            </select>
            最终成绩计算方式：<select name="FinalResultType" size="1" class="span2" onchange="FinalResultChange($(this).val())">
				{tpl:loop $FinalResultTypeList $FinalResultType $FinalResultTypeName}
				<option value="{tpl:$FinalResultType/}">{tpl:$FinalResultTypeName/}</option>
				{/tpl:loop}
			</select>
			<div id="CreditList">{tpl:loop $CreditArr $CreditId $CreditInfo} <input type = 'checkbox' name = 'CreditList[{tpl:$CreditId/}]' {tpl:if($CreditInfo.checked==1)}checked{/tpl:if} value="1">{tpl:$CreditInfo.CreditName/} {/tpl:loop}</div></th></tr>
	<tr class="hover"><th align="center" class="rowtip">团队/个人排名</th><th align="center" class="rowtip">
			<select name="ResultType" size="1" class="span2">
				{tpl:loop $RaceResultTypeList $RaceResulutType $RaceResulutName}
				<option value="{tpl:$RaceResulutType/}" >{tpl:$RaceResulutName/}</option>
				{/tpl:loop}
			</select>
		</th></tr>
	<tr class="hover"><th align="center" class="rowtip">数据源</th><th align="center" class="rowtip">数据库：<input name="TimeDB" type="text" class="span2" id="TimeDB" value = "mylaps" size="50" />表前缀：<input name="TimePrefix" type="text" class="span2" id="TimePrefix" size="50" /></th></tr>
	<tr class="hover"><th align="center" class="rowtip">每分钟处理次数</th><th align="center" class="rowtip">
			<select name="ProcessRate" size="1" class="span2">
                {tpl:loop $PrecessRateList $PrecessRate}
				<option value="{tpl:$PrecessRate/}">{tpl:$PrecessRate/}次</option>
                {/tpl:loop}
			</select>
		</th></tr>
	<tr class="hover"><th align="center" class="rowtip">人数/价格对应<p>(人数;单价|人数:单价)</th><th align="center" class="rowtip"><input name="PriceList" type="text" class="span2" id="PriceList" size="50" /></th></tr>
<tr class="hover"><th align="center" class="rowtip">数量限制</th><th align="center" class="rowtip">个人：<input type="radio" name="SingleUser" id="SingleUser" value="1" checked>接受<input type="radio" name="SingleUser" id="SingleUser"  value="0" >不接受 人数上限:<input name="SingleUserLimit" type="text" class="span1" id="SingleUserLimit" value = "0" size="50" /><p>团队：<input type="radio" name="TeamUser" id="TeamUser" value="1" checked>接受<input type="radio" name="TeamUser" id="TeamUser" value="0" >不接受 数量上限:<input name="TeamLimit" type="text" class="span1" id="TeamLimit" value = "0" size="50" /></th></tr>
<tr class="hover"><th align="center" class="rowtip">团队人数限制</th><th align="center" class="rowtip">最少:<input name="TeamUserMin" type="text" class="span1" id="TeamUserMin" value = "6" size="50" />最高:<input name="TeamUserMax" type="text" class="span1" id="TeamUserMax" value = "9" size="50" /></th></tr>
<tr class="hover"><th align="center" class="rowtip">成员数量</th><th align="center" class="rowtip">男性 最低:<input name="SexUser[Min][1]" type="text" class="span1" id="SexUser[Min][1]" value = "1" size="50" />最高:<input name="SexUser[Max][1]" type="text" class="span1" id="SexUser[Max][1]" value = "0" size="50" /> 女性  最低:<input name="SexUser[Min][2]" type="text" class="span1" id="SexUser[Min][2]" value = "1" size="50" />最高:<input name="SexUser[Max][2]" type="text" class="span1" id="SexUser[Max][2]" value = "0" size="50" /></th></tr>
<tr class="hover"><th align="center" class="rowtip">起点</th><th align="center" class="rowtip"><input type="radio" name="NoStart" id="NoStart" value="1">无起点<input type="radio" name="NoStart" id="NoStart"  value="0"  checked>有起点</th></tr>
<tr class="hover"><th align="center" class="rowtip">团队成绩名次</th><th align="center" class="rowtip">
        <select name="TeamResultRankType" size="1" class="span2">
            {tpl:loop $TeamResultTypeList $teamResultType $teamResultTypeName }
            <option value="{tpl:$teamResultType/}" >{tpl:$teamResultTypeName/}</option>
            {/tpl:loop}</select>


        <select name="TeamResultRank" size="1" class="span2">
			{tpl:loop $t $i }
			<option value="{tpl:$i/}" >{tpl:$i/}人</option>
			{/tpl:loop}</select></tr>

	<tr class="hover"><td colspan = 2>比赛介绍</td></tr>
    <tr class="hover"><td colspan = 2><textarea name="RaceComment" id="RaceComment" ></textarea></td>
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
<script>
    ClassicEditor
        .create( document.querySelector( '#RaceComment' ),
            {
                config: { height: '300px', width: '552px' },
                ckfinder: {
                    uploadUrl: '/callback/upload.php?type=img',
                }
            }
        )
        .catch( error => {
        console.error( error );
    } );

</script>
{tpl:tpl contentFooter/}