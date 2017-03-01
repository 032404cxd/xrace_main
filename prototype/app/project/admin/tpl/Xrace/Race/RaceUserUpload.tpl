{tpl:tpl contentHeader/}
<div class="br_bottom"></div>
<form id="race_add_form" name="race_add_form" action="{tpl:$this.sign/}&ac=race.user.upload" method="post">
<input type="hidden" name="RaceStageId" id="RaceStageId" value="{tpl:$RaceInfo.RaceStageId/}" />
<input type="hidden" name="CurrentRaceGroupId" id="CurrentRaceGroupId" value="{tpl:$RaceGroupId/}" />
<input type="hidden" name="RaceId" id="RaceId" value="{tpl:$RaceId/}" />
<table width="99%" align="center" class="table table-bordered table-striped">
	<tr class="hover"><td>txt文件导入</td>
		<td align="left"><input name="RaceUserList[1]" type="file" class="span2" id="RaceUserList[1]" /></td>

	</tr>
	<tr class="hover"><td>格式</td><td align="left">BIB,组别,姓名,性别,证件类型,证件号码,芯片,手机,队伍</td></tr>
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

				divBox.alertBox(errors[jsonResponse.errno],function(){});
			} else {
				var message = '导入完毕，更新'+jsonResponse.ApplyCount+ '条数据。<br>以下选手姓名有误：<br>'+jsonResponse.NameErrorUser;
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