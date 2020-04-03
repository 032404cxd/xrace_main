{tpl:tpl contentHeader/}
<form id="aid_station_modify_form" name="aid_station_modify_form" action="{tpl:$this.sign/}&ac=aid.station.update" method="post">
<input type="hidden" name="AidStationId" id="AidStationId" value="{tpl:$AidStationInfo.AidStationId/}" />
<table width="99%" align="center" class="table table-bordered table-striped">
<tr class="hover"><th align="center" class="rowtip">补给点名称</th><th align="center" class="rowtip"><input name="AidStationName" type="text" class="span3" id="AidStationName" value = "{tpl:$AidStationInfo.AidStationName/}" size="50" /></th></tr>
	<tr class="hover"><th align="center" class="rowtip">补给点说明</th><th align="center" class="rowtip"><textarea class="span3" name="AidStationComment"  id="AidStationComment" cols ="50" rows = "5"/>{tpl:$AidStationInfo.AidStationComment/}</textarea></th>
		</td>
	</tr>
<tr class="noborder"><td></td>
<td><button type="submit" id="aid_station_modify_submit">提交</button></td>
</tr>
</table>
</form>
<script type="text/javascript">
$('#aid_station_modify_submit').click(function(){
	var options = {
		dataType:'json',
		beforeSubmit:function(formData, jqForm, options) {},
		success:function(jsonResponse) {
			if (jsonResponse.errno) {
				var errors = [];
				errors[1] = '补给点名称不能为空，请修正后再次提交';
                errors[9] = '更新失败，请修正后再次提交';
				divBox.alertBox(errors[jsonResponse.errno],function(){});
			} else {
				var message = '更新补给点成功';
				divBox.confirmBox({content:message,ok:function(){windowParent.getRightHtml('{tpl:$this.sign/}'+ '&ac=aid.station.list&RaceStageId=' + jsonResponse.RaceStageId);}});
			}
		}
	};
	$('#aid_station_modify_form').ajaxForm(options);
});
</script>
{tpl:tpl contentFooter/}