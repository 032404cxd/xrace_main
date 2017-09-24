{tpl:tpl contentHeader/}
<div class="br_bottom"></div>
<form id="race_catalog_disclaimer_update_form" name="race_catalog_disclaimer_update_form" action="{tpl:$this.sign/}&ac=race.catalog.disclaimer.update" method="post">
<input type="hidden" name="RaceCatalogId" value="{tpl:$RaceCatalogInfo.RaceCatalogId/}" />
<table width="99%" align="center" class="table table-bordered table-striped" width="99%">
	<tr class="hover" ><textarea id="Disclaimer" name="Disclaimer" style="width:100%" rows="20">{tpl:$RaceCatalogInfo.Disclaimer func="urldecode(@@)"/}</textarea></td>
	</tr>
<tr class="noborder">
<td><button type="submit" id="race_catalog_disclaimer_update_submit">提交</button></td>
</tr>
</table>
</form>

<script type="text/javascript">
$('#race_catalog_disclaimer_update_submit').click(function(){
	var options = {
		dataType:'json',
		beforeSubmit:function(formData, jqForm, options) {
		},
		success:function(jsonResponse) {
			if (jsonResponse.errno) {
				var errors = [];
				errors[9] = '入库失败，请修正后再次提交';
				divBox.alertBox(errors[jsonResponse.errno],function(){});
			} else {
				var message = '修改免责声明成功';
				divBox.confirmBox({content:message,ok:function(){windowParent.getRightHtml('{tpl:$this.sign/}');}});
			}
		}
	};
	$('#race_catalog_disclaimer_update_form').ajaxForm(options);
});
</script>
{tpl:tpl contentFooter/}