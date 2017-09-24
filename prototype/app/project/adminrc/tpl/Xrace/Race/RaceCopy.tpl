{tpl:tpl contentHeader/}
<form id="race_copy_form" name="race_copy_form" action="{tpl:$this.sign/}&ac=race.copy" method="post">
<input type="hidden" name="RaceId" id="RaceId" value="{tpl:$RaceId/}" />
<input type="hidden" name="RaceStageId" id="RaceStageId" value="{tpl:$RaceInfo.RaceStageId/}" />
<table width="99%" align="center" class="table table-bordered table-striped">
<tr class="hover"><th align="center" class="rowtip">比赛名称</th>
	<th align="center" class="rowtip"><input name="RaceName" type="text" class="span3" id="RaceName" value = "{tpl:$RaceInfo.RaceName/}" size="50" /></th></tr>
<tr class="noborder"><td></td>
<td><button type="submit" id="race_copy_submit">提交</button></td>
</tr>
</table>
</form>
<script type="text/javascript">
$('#race_copy_submit').click(function(){
	var options = {
		dataType:'json',
		beforeSubmit:function(formData, jqForm, options) {},
		success:function(jsonResponse) {
			if (jsonResponse.errno) {
				var errors = [];
				errors[1] = '比赛名称不能为空,请修正后再次提交';
				errors[9] = '提交失败,请修正后再次提交';
				divBox.alertBox(errors[jsonResponse.errno],function(){});
			} else {
				var message = '复制比赛成功';
				RaceStageId=$("#RaceStageId");
				divBox.confirmBox({content:message,ok:function(){windowParent.getRightHtml('{tpl:$this.sign/}'+ '&ac=race.list&RaceStageId=' + RaceStageId.val());}});
			}
		}
	};
	$('#race_copy_form').ajaxForm(options);
});
</script>
{tpl:tpl contentFooter/}