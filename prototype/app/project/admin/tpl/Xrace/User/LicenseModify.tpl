{tpl:tpl contentHeader/}
<div class="br_bottom"></div>
<form id="license_update_form" name="license_update_form" action="{tpl:$this.sign/}&ac=license.update" metdod="post">
<table width="99%" align="center" class="table table-bordered table-striped">
<input type="hidden" name="UserId"  id="UserId" value="{tpl:$UserId/}" />
<input type="hidden" name="LicenseId"  id="LicenseId" value="{tpl:$LicenseId/}" />
<tr class="hover">
    <td>执照所属赛事</td>
    <td align="left">	
        <select name="CatalogId"  id="CatalogId" size="1"  onchange='getGroupList()'>
            <option value="0">全部</option>
            {tpl:loop $RaceCatalogArr $RaceCatalogInfo}
            <option value="{tpl:$RaceCatalogInfo.RaceCatalogId/}" {tpl:if($RaceCatalogInfo.RaceCatalogId==$UserLicenseInfo.CatalogId)}selected="selected"{/tpl:if}>{tpl:$RaceCatalogInfo.RaceCatalogName/}</option>
            {/tpl:loop}
        </select>
    </td>
</tr>
<tr>
<tr class="hover">
    <td>执照所属赛事分组</td>
    <td align="left">
        <select name="GroupId"  id="GroupId" size="1">
            {tpl:$SelectedGroupText/}
        </select>        
    </td>
</tr>
<tr class="hover">
    <td align="center" class="rowtip">执照生效时间</td>
    <td align="center" class="rowtip">
        <input type="text" name="LicenseStartDate" value="{tpl:$UserLicenseInfo.LicenseStartDate/}" class="input-medium"   onFocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm:ss'})" >
    </td>
</tr>
<tr class="hover">
    <td align="center" class="rowtip">执照到期时间</td>
    <td align="center" class="rowtip">
        <input type="text" name="LicenseEndDate" value="{tpl:$UserLicenseInfo.LicenseEndDate/}" class="input-medium"   onFocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm:ss'})" >
    </td>
</tr>
<tr class="hover">
    <td align="center" class="rowtip">执照修改理由</td>
    <td align="center" class="rowtip">
        <input type="text" name="comment" value="" class="input-medium">
    </td>
</tr>
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
				divBox.confirmBox({content:message,ok:function(){windowParent.getRightHtml('{tpl:$this.sign/}'+ '&ac=license.index&UserId=' + UserId.val());}});
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