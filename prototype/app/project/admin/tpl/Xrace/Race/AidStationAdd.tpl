{tpl:tpl contentHeader/}
<form id="aid_station_add_form" name="aid_station_add_form"" action="{tpl:$this.sign/}&ac=aid.station.insert" method="post">
<input type="hidden" name="RaceStageId" id="RaceStageId" value="{tpl:$RaceStageId/}" />
<table width="99%" align="center" class="table table-bordered table-striped">
	<tr class="hover"><th align="center" class="rowtip">补给点名称</th><th align="center" class="rowtip"><input name="AidStationName" type="text" class="span3" id="AidStationName" value = "" size="50" /></th></tr>
	<tr class="hover"><th align="center" class="rowtip">补给点说明</th><th align="center" class="rowtip"><textarea class="span3" name="AidStationComment"  id="AidStationComment" cols ="50" rows = "5"/></textarea></th>
		</td>
	</tr>
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
                errors[1] = '补给点名称不能为空，请修正后再次提交';
                errors[9] = '添加失败，请修正后再次提交';
				divBox.alertBox(errors[jsonResponse.errno],function(){});
			} else {
                var message = '添加补给点成功';
                divBox.confirmBox({content:message,ok:function(){windowParent.getRightHtml('{tpl:$this.sign/}'+ '&ac=aid.station.list&RaceStageId=' + jsonResponse.RaceStageId);}});
			}
		}
	};
	$('#race_add_form').ajaxForm(options);
});
</script>
{tpl:tpl contentFooter/}