{tpl:tpl contentHeader/}
<div class="br_bottom"></div>
<form id="ranking_insert_form" name="ranking_insert_form" action="{tpl:$this.sign/}&ac=ranking.insert" method="post">
	<input type="hidden" name="RaceCatalogId" id="RaceCatalogId" value="{tpl:$RaceCatalogId/}" />
<table width="99%" align="center" class="table table-bordered table-striped" widtd="99%">
<tr class="hover">
<td>排名名称</td>
<td align="left"><input name="RankingName" type="text" class="span4" id="RankingName" value="" size="50" /></td>
</tr>
	<td>排名方式</td>
	<td align="left">
		<select name="RankingType" class="span2" size="1">
            {tpl:loop $RankingTypeList $RankingType $RankingTypeName}
			<option value="{tpl:$RankingType/}" >{tpl:$RankingTypeName/}</option>
            {/tpl:loop}
	</td></tr>


	<tr class="hover"><td colspan = 2>排名介绍</td></tr>
	<tr class="hover"><td colspan = 2><textarea name="RankingComment" id="comment" class="span5" rows="4"></textarea></td>
	</tr>
<tr class="noborder"><td></td>
<td><button type="submit" id="ranking_insert_submit">提交</button></td>
</tr>
</table>
</form>

<script type="text/javascript">
$('#ranking_insert_submit').click(function(){
	var options = {
		dataType:'json',
		beforeSubmit:function(formData, jqForm, options) {
		},
		success:function(jsonResponse) {
			if (jsonResponse.errno) {
				var errors = [];
				errors[1] = '排名名称不能为空，请修正后再次提交';
				errors[9] = '入库失败，请修正后再次提交';
				divBox.alertBox(errors[jsonResponse.errno],function(){});
			} else {
				var message = '添加排名成功';
                RaceCatalogId=$("#RaceCatalogId");
                divBox.confirmBox({content:message,ok:function(){windowParent.getRightHtml('{tpl:$this.sign/}'+ '&ac=ranking.list&RaceCatalogId=' + RaceCatalogId.val());}});
			}
		}
	};
	$('#ranking_insert_form').ajaxForm(options);
});
</script>
{tpl:tpl contentFooter/}