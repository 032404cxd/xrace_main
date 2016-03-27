{tpl:tpl contentHeader/}
<div class="br_bottom"></div>
<form id="license_update_form" name="license_update_form" action="{tpl:$this.sign/}&ac=license.update" metdod="post">
<table width="99%" align="center" class="table table-bordered table-striped">
<input type="hidden" name="UserId"  id="UserId" value="{tpl:$UserId/}" />
<input type="hidden" name="LicenseId"  id="LicenseId" value="{tpl:$LicenseId/}" />
<tr class="hover">
    <td>执照所属赛事</td>
    <td align="left">	
        <select name="RaceCatalogId"  id="RaceCatalogId" size="1"  onchange='getRaceGroupList()'>
            <option value="0">全部</option>
            {tpl:loop $RaceCatalogList $RaceCatalogInfo}
            <option value="{tpl:$RaceCatalogInfo.RaceCatalogId/}" {tpl:if($RaceCatalogInfo.RaceCatalogId==$UserLicenseInfo.RaceCatalogId)}selected="selected"{/tpl:if}>{tpl:$RaceCatalogInfo.RaceCatalogName/}</option>
            {/tpl:loop}
        </select>
    </td>
</tr>
<tr>
<tr class="hover">
    <td>执照所属分组</td>
    <td align="left">
        <select name="GroupId"  id="GroupId" size="1">
            {tpl:loop $RaceGroupList $RaceGroupInfo}
            <option value="{tpl:$RaceGroupInfo.RaceGroupId/}" {tpl:if($RaceGroupInfo.RaceGroupId==$UserLicenseInfo.RaceGroupId)}selected="selected"{/tpl:if}>{tpl:$RaceGroupInfo.RaceGroupName/}</option>
            {/tpl:loop}
        </select>        
    </td>
</tr>
<tr class="hover">
    <td align="center" class="rowtip">执照生效时间</td>
    <td align="center" class="rowtip">
        <input type="text" name="LicenseStartDate" value="{tpl:$UserLicenseInfo.LicenseStartDate/}" class="input-medium"   onFocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:'yyyy-MM-dd'})" >
    </td>
</tr>
<tr class="hover">
    <td align="center" class="rowtip">执照到期时间</td>
    <td align="center" class="rowtip">
        <input type="text" name="LicenseEndDate" value="{tpl:$UserLicenseInfo.LicenseEndDate/}" class="input-medium"   onFocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:'yyyy-MM-dd'})" >
    </td>
</tr>
<tr class="hover">
    <td align="center" class="rowtip">执照修改理由</td>
    <td align="center" class="rowtip">
        <input type="text" name="comment" value="" class="input-medium">
    </td>
</tr>
<tr class="hover">
    <td align="center" class="rowtip" colspan = 2>执照更新记录</td>
</tr>
    {tpl:loop $UserLicenseInfo.comment.LicenseUpdateLog $UpdateLog}
        <tr class="hover">
            <td align="center" class="rowtip">{tpl:$UpdateLog.action/}</td>
            <td align="center" class="rowtip">{tpl:$UpdateLog.time/}</td>
        </tr>
    {/tpl:loop}
<tr class="noborder">
    <td></td>
    <td><button type="submit" id="license_update_submit">提交</button></td>
</tr>
</table>
</form>
<script type="text/javascript">
$('#license_update_submit').click(function(){
	var options = {
		dataType:'json',
		beforeSubmit:function(formData, jqForm, options) {},
		success:function(jsonResponse) {
			if (jsonResponse.errno) {
				var errors = [];
				errors[1] = '执照所属赛组不能为空，请修正后再次提交';
				errors[2] = '执照结束时间不能小于当前时间，请修正后再次提交';
				errors[3] = '执照开始时间不能小于执照结束时间，请修正后再次提交';
                errors[4] = '执照添加理由不能为空，请修正后再次提交';
                errors[5] = '同一用户同赛组中添加的有效执照不能有时间冲突，请修正后再次提交';
				divBox.alertBox(errors[jsonResponse.errno],function(){});
			} else {
				var message = '修改执照成功';
				UserId=$("#UserId");
				divBox.confirmBox({content:message,ok:function(){windowParent.getRightHtml('{tpl:$this.sign/}'+ '&ac=license.list&UserId=' + UserId.val());}});
			}
		}
	};
	$('#license_update_form').ajaxForm(options);
});
function getGroupList()
{
	catalog=$("#LicenseCatalogId");
	$.ajax
	({
		type: "GET",
		url: "?ctl=xrace/user&ac=get.selected.group&RaceCatalogId="+catalog.val(),
		success: function(msg)
		{
			$("#GroupId").html(msg);
		}
	});
//*/
}
</script>
{tpl:tpl contentFooter/}