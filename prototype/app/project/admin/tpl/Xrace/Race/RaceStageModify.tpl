{tpl:tpl contentHeader/}
<div class="br_bottom"></div>
<form id="race_stage_update_form" name="race_stage_update_form" action="{tpl:$this.sign/}&ac=race.stage.update" metdod="post">
<input type="hidden" name="RaceStageId" id="RaceStageId" value="{tpl:$RaceStageInfo.RaceStageId/}" />
<table width="99%" align="center" class="table table-bordered table-striped" widtd="99%">
<tr class="hover">
<td>赛事分站名称</td>
<td align="left"><input name="RaceStageName" type="text" class="span4" id="RaceStageName" value="{tpl:$RaceStageInfo.RaceStageName/}" size="50" /></td>
</tr>
<tr class="hover"><td>赛事分站Id</td>
<td align="left">{tpl:$RaceStageInfo.RaceStageId/}</td>
</tr>
<tr class="hover"><td>所属赛事</td>
	<td align="left">	<select name="RaceCatalogId"  id="RaceCatalogId" size="1"  onchange='getGroupList()'>
			<option value="0">全部</option>
			{tpl:loop $RaceCatalogArr $RaceCatalogInfo}
			<option value="{tpl:$RaceCatalogInfo.RaceCatalogId/}" {tpl:if($RaceCatalogInfo.RaceCatalogId==$RaceStageInfo.RaceCatalogId)}selected="selected"{/tpl:if}>{tpl:$RaceCatalogInfo.RaceCatalogName/}</option>
			{/tpl:loop}
		</select></td>
</tr>
	<tr>
		<th><label >开始结止时间</label></th>
		<td>
			<input type="text" name="StageStartDate" value="{tpl:$RaceStageInfo.StageStartDate/}" class="input-medium"
				   onFocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:'yyyy-MM-dd'})" >
			---
			<input type="text" name="StageEndDate" value="{tpl:$RaceStageInfo.StageEndDate/}" value="" class="input-medium"
				   onFocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:'yyyy-MM-dd'})" >
		</td>
	</tr>
	<tr>
	<td>赛事分组列表</td>
	<td align="left"><div id = "SelectedGroupList">
			{tpl:loop $RaceGroupArr $RaceGroupInfo}
			<input type="checkbox"  name="SelectedRaceGroup[{tpl:$RaceGroupInfo.RaceGroupId/}]" value="{tpl:$RaceGroupInfo.RaceGroupId/}" {tpl:if($RaceGroupInfo.selected == 1)}checked{/tpl:if} /> {tpl:$RaceGroupInfo.RaceGroupName/}
			{/tpl:loop}
		</div></td>
	</tr>
        <tr class="hover"><td>赛事分组图标1</td>
                <td align="left"><input name="RaceStageIcon[1]" type="file" class="span4" id="RaceStageIcon[1]" /></td>
        </tr>
        <tr class="hover"><td>赛事分组图标2</td>
                <td align="left"><input name="RaceStageIcon[2]" type="file" class="span4" id="RaceStageIcon[2]" /></td>
        </tr>
        <tr class="hover"><td>赛事分组图标3</td>
                <td align="left"><input name="RaceStageIcon[3]" type="file" class="span4" id="RaceStageIcon[3]" /></td>
        </tr>        
	<tr class="noborder"><td></td>
<td><button type="submit" id="race_stage_update_submit">提交</button></td>
</tr>
</table>
</form>

<script type="text/javascript">
$('#race_stage_update_submit').click(function(){
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
	$('#race_stage_update_form').ajaxForm(options);
});
function getGroupList()
{
	catalog=$("#RaceCatalogId");
	stage=$("#RaceStageId");
	$.ajax
	({
		type: "GET",
		url: "?ctl=xrace/race.stage&ac=get.selected.group&RaceCatalogId="+catalog.val()+"&RaceStageId="+stage.val(),
		success: function(msg)
		{
			$("#SelectedGroupList").html(msg);
		}
	});
//*/
}
</script>
{tpl:tpl contentFooter/}