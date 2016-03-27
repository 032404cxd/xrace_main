{tpl:tpl contentHeader/}
<div class="br_bottom"></div>
<form id="license_add_form" name="license_add_form" action="{tpl:$this.sign/}&ac=license.insert" metdod="post">
<table width="99%" align="center" class="table table-bordered table-striped">
<input type="hidden" name="UserId"  id="UserId" value="{tpl:$UserId/}" />
<tr class="hover">
    <td>执照所属赛事</td>
    <td align="left">	
        <select name="RaceCatalogId"  id="RaceCatalogId" size="1"  onchange='getRaceGroupList()'>
            <option value="0">全部</option>
            {tpl:loop $RaceCatalogList $RaceCatalogInfo}
            <option value="{tpl:$RaceCatalogInfo.RaceCatalogId/}">{tpl:$RaceCatalogInfo.RaceCatalogName/}</option>
            {/tpl:loop}
        </select>
    </td>
</tr>
<tr>
<tr class="hover">
    <td>执照所属赛事分组</td>
    <td align="left">
        <select name="RaceGroupId"  id="RaceGroupId" size="1">
        </select>        
    </td>
</tr>
<tr class="hover">
    <td align="center" class="rowtip">执照生效日期</td>
    <td align="center" class="rowtip">
        <input type="text" name="LicenseStartDate" value="" class="input-medium"   onFocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:'yyyy-MM-dd'})" >
    </td>
</tr>
<tr class="hover">
    <td align="center" class="rowtip">执照结束日期</td>
    <td align="center" class="rowtip">
        <input type="text" name="LicenseEndDate" value="" class="input-medium"   onFocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:'yyyy-MM-dd'})" >
    </td>
</tr>
<tr class="hover">
    <td align="center" class="rowtip">执照添加理由</td>
    <td align="center" class="rowtip">
        <input type="text" name="comment" value="" class="input-medium">
    </td>
</tr>
<tr class="noborder">
    <td></td>
    <td><button type="submit" id="license_add_submit">提交</button></td>
</tr>
</table>
</form>
<script type="text/javascript">
$('#license_add_submit').click(function(){
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
				var message = '添加执照成功';
				UserId=$("#UserId");
				divBox.confirmBox({content:message,ok:function(){windowParent.getRightHtml('{tpl:$this.sign/}'+ '&ac=license.index&UserId=' + UserId.val());}});
			}
		}
	};
	$('#license_add_form').ajaxForm(options);
});
function getRaceGroupList()
{
	catalog=$("#RaceCatalogId");
	$.ajax
	({
		type: "GET",
		url: "?ctl=xrace/race.group&ac=get.group.by.catalog&RaceCatalogId="+catalog.val(),
		success: function(msg)
		{
			$("#RaceGroupId").html(msg);
		}
	});
//*/
}
</script>
{tpl:tpl contentFooter/}