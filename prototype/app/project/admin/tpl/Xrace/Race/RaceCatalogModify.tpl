{tpl:tpl contentHeader/}
<div class="br_bottom"></div>

<!--<script src="https://cdn.ckeditor.com/ckeditor5/10.0.1/classic/ckeditor.js"></script>-->
<script src="js/ckeditor5/ckeditor.js"></script>


<form id="race_catalog_update_form" name="race_catalog_update_form" action="{tpl:$this.sign/}&ac=race.catalog.update" method="post">
<input type="hidden" name="RaceCatalogId" value="{tpl:$RaceCatalogInfo.RaceCatalogId/}" />
<table width="99%" align="center" class="table table-bordered table-striped" widtd="99%">
<tr class="hover">
<td>赛事名称</td>
<td align="left"><input name="RaceCatalogName" type="text" class="span4" id="RaceCatalogName" value="{tpl:$RaceCatalogInfo.RaceCatalogName/}" size="50" /></td>
</tr>
<tr class="hover"><td>赛事ID</td>
<td align="left">{tpl:$RaceCatalogInfo.RaceCatalogId/}</td>
</tr>
	<tr class="hover"><td>赛事图标</td>
		<td align="left"><input name="RaceCatalogIcon[1]" type="file" class="span4" id="RaceCatalogIcon[1]" /></td>
	</tr>
	<tr class="hover"><th align="center" class="rowtip">是否显示</th><th align="center" class="rowtip">
			<input type="radio" name="Display" id="Display" value="1" {tpl:if($RaceCatalogInfo.Display=="1")}checked{/tpl:if}>是
			<input type="radio" name="Display" id="Display" value="0" {tpl:if($RaceCatalogInfo.Display=="0")}checked{/tpl:if}>否</th>
	</tr>
	<tr class="hover"><td colspan = 2>赛事介绍</td></tr>
	<tr class="hover"><td colspan = 2>
	<textarea name="RaceCatalogComment" id="RaceCatalogComment" rows="100" cols="20">{tpl:$RaceCatalogInfo.RaceCatalogComment/}</textarea>
		</td>
	</tr>
<tr class="noborder"><td></td>
<td><button type="submit" id="race_catalog_update_submit">提交</button></td>
</tr>
</table>
</form>

<script type="text/javascript">
$('#race_catalog_update_submit').click(function(){
	var options = {
		dataType:'json',
		beforeSubmit:function(formData, jqForm, options) {
		},
		success:function(jsonResponse) {
			if (jsonResponse.errno) {
				var errors = [];
				errors[1] = '赛事名称不能为空，请修正后再次提交';
				errors[9] = '入库失败，请修正后再次提交';
				divBox.alertBox(errors[jsonResponse.errno],function(){});
			} else {
				var message = '修改赛事成功';
				divBox.confirmBox({content:message,ok:function(){windowParent.getRightHtml('{tpl:$this.sign/}');}});
			}
		}
	};
	$('#race_catalog_update_form').ajaxForm(options);
});
</script>
<script>
    ClassicEditor
        .create( document.querySelector( '#RaceCatalogComment' ) )
        .catch( error => {
        console.error( error );
    } );
</script>
{tpl:tpl contentFooter/}