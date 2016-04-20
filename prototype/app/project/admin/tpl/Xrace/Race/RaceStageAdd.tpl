{tpl:tpl contentHeader/}
<div class="br_bottom"></div>
<form id="race_stage_add_form" name="race_stage_add_form"" action="{tpl:$this.sign/}&ac=race.stage.insert" method="post">
<table width="99%" align="center" class="table table-bordered table-striped">
<tr class="hover">
<td>赛事分站名称</td>
	<td align="left"><input type="text" class="span4" name="RaceStageName"  id="RaceStageName" value="" size="50" /></td>
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
<tr class="hover">
	<td>人数/价格对应<p>(人数;单价|人数:单价)</td>
	<td align="left"><input name="PriceList" type="text" class="span4" id="PriceList" size="50" /></td>
</tr>
	<tr>
		<th><label >开始结止时间</label></th>
		<td>
			<input type="text" name="StageStartDate" value="{tpl:$StageStartDate/}" class="input-medium"
				   onFocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:'yyyy-MM-dd'})" >
			---
			<input type="text" name="StageEndDate" value="{tpl:$StageEndDate/}" value="" class="input-medium"
				   onFocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:'yyyy-MM-dd'})" >
		</td>
	</tr>
	<tr class="hover"><td>赛事分站图片1</td>
			<td align="left"><input name="RaceStageIcon[1]" type="file" class="span4" id="RaceStageIcon[1]" /></td>
	</tr>
	<tr class="hover"><td>赛事分站图片2</td>
			<td align="left"><input name="RaceStageIcon[2]" type="file" class="span4" id="RaceStageIcon[2]" /></td>
	</tr>
	<tr class="hover"><td>赛事分站图片3</td>
			<td align="left"><input name="RaceStageIcon[3]" type="file" class="span4" id="RaceStageIcon[3]" /></td>
	</tr>
	<tr class="hover"><td colspan = 2>赛事分站介绍</td></tr>
	<tr class="hover"><td colspan = 2><?php echo $editor->editor("RaceStageComment",""); ?></td>
	</tr>
<tr class="noborder"><td></td>
<td><button type="submit" id="race_stage_add_submit">提交</button></td>
</tr>
</table>
</form>
<script type="text/javascript">
$('#race_stage_add_submit').click(function(){
	var options = {
		dataType:'json',
		beforeSubmit:function(formData, jqForm, options) {},
		success:function(jsonResponse) {
			if (jsonResponse.errno) {
				var errors = [];
				errors[1] = '赛事分站名称不能为空，请修正后再次提交';
				errors[3] = '请选择一个有效的赛事，请修正后再次提交';
				errors[4] = '请选择至少一个赛事分组，请修正后再次提交';
				errors[9] = '入库失败，请修正后再次提交';
				divBox.alertBox(errors[jsonResponse.errno],function(){});
			} else {
				var message = '添加赛事分站成功';
				divBox.confirmBox({content:message,ok:function(){windowParent.getRightHtml('{tpl:$this.sign/}');}});
			}
		}
	};
	$('#race_stage_add_form').ajaxForm(options);
});
function getGroupList()
{
	catalog=$("#RaceCatalogId");
	$.ajax
	({
		type: "GET",
		url: "?ctl=xrace/race.stage&ac=get.selected.group&RaceCatalogId="+catalog.val(),
		success: function(msg)
		{
			$("#SelectedGroupList").html(msg);
		}
	});
//*/
}
</script>
{tpl:tpl contentFooter/}