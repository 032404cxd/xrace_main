{tpl:tpl contentHeader/}
<script type="text/javascript">

function RaceStageIconDelete(sid,name,logo_id){
	deleteStageLogoBox = divBox.confirmBox({content:'是否删除 ' + name + '?',ok:function(){location.href = '{tpl:$this.sign/}&ac=race.stage.icon.delete&RaceStageId=' + sid + '&LogoId=' +logo_id;}});
}
</script>
<script src="js/ckeditor5/ckeditor.js"></script>
<div class="br_bottom"></div>
<form id="race_stage_update_form" name="race_stage_update_form" action="{tpl:$this.sign/}&ac=race.stage.update" method="post">
<input type="hidden" name="RaceStageId" id="RaceStageId" value="{tpl:$RaceStageInfo.RaceStageId/}" />
<table width="99%" align="center" class="table table-bordered table-striped" widtd="99%">
<tr class="hover">
	<td>分站名称</td>
	<td align="left"><input name="RaceStageName" type="text" class="span3" id="RaceStageName" value="{tpl:$RaceStageInfo.RaceStageName/}"/></td>
</tr>
<td>搜索关键字</td>
	<td align="left"><textarea name="SearchKeyWord" id="SearchKeyWord" class="span5" rows="4">{tpl:$RaceStageInfo.comment.SearchKeyWord/}</textarea></td>
</tr>
	<td>赛事举办地</td>
<td align="left"><input name="Location" type="text" class="span3" id="Location" value="{tpl:$RaceStageInfo.Location/}"/></td>
</tr>
<td>分站通票价格</td>
<td align="left"><input name="PriceList" type="text" class="span3" id="PriceList" value="{tpl:$RaceStageInfo.comment.PriceList/}"/></td>
</tr>
<td>分站折扣</td>
<td align="left"><input name="PriceDiscount" type="text" class="span3" id="PriceDiscount" value="{tpl:$RaceStageInfo.comment.PriceDiscount/}" /></td>
</tr>
<tr class="hover"><td>其他计价规则</td>
    <td align="left">	<select name="SpecialDiscount"  id="SpecialDiscount" size="1">
            {tpl:loop $ApplySepcialDiscount $Discount $SepcialDiscountInfo}
            <option value="{tpl:$Discount/}" {tpl:if($Discount==$RaceStageInfo.comment.SpecialDiscount)}selected="selected"{/tpl:if}>{tpl:$SepcialDiscountInfo/}</option>
            {/tpl:loop}
        </select></td>
</tr>
<td>积分抵扣比例(0%-100%)</td>
<td align="left"><input name="CreditRate[min]" type="text" class="span1" id="CreditRate[min]" value="{tpl:$RaceStageInfo.comment.CreditRate.min/}" />% - <input name="CreditRate[max]" type="text" class="span1" id="CreditRate[max]" value="{tpl:$RaceStageInfo.comment.CreditRate.max/}" />%</td>
</tr>
    <td>单次报名人数上限</td>
    <td align="left"><input name="ApplyLimit" type="text" class="span3" id="ApplyLimit" value="{tpl:$RaceStageInfo.comment.ApplyLimit/}" /></td>
    </tr>
	<td>积分抵扣最小单位</td>
	<td align="left"><input name="CreditStack" type="text" class="span3" id="CreditStack" value="{tpl:$RaceStageInfo.comment.CreditStack/}" /></td>
	</tr>
<tr class="hover"><td>分站Id</td>
<td align="left">{tpl:$RaceStageInfo.RaceStageId/}</td>
</tr>
<tr class="hover"><td>所属赛事</td>
	<td align="left">	<select name="RaceCatalogId"  id="RaceCatalogId" size="1"  onchange='getGroupList()'>
			{tpl:loop $RaceCatalogList $RaceCatalogInfo}
			<option value="{tpl:$RaceCatalogInfo.RaceCatalogId/}" {tpl:if($RaceCatalogInfo.RaceCatalogId==$RaceStageInfo.RaceCatalogId)}selected="selected"{/tpl:if}>{tpl:$RaceCatalogInfo.RaceCatalogName/}</option>
			{/tpl:loop}
		</select></td>
</tr>
<tr class="hover"><th align="center" class="rowtip">是否显示</th><th align="center" class="rowtip">
		<input type="radio" name="Display" id="Display" value="1" {tpl:if($RaceStageInfo.Display=="1")}checked{/tpl:if}>是
		<input type="radio" name="Display" id="Display" value="0" {tpl:if($RaceStageInfo.Display=="0")}checked{/tpl:if}>否</th>
</tr>
<tr class="hover"><th align="center" class="rowtip">比赛类型</th><th align="center" class="rowtip">
		<select name="RaceTypeId" size="1" class="span2">
			<option value="0" {tpl:if(0==$RaceStageInfo.comment.RaceTypeId)}selected="selected"{/tpl:if}>全部</option>
            {tpl:loop $RaceTypeList $RaceTypeInfo}
			<option value="{tpl:$RaceTypeInfo.RaceTypeId/}" {tpl:if($RaceTypeInfo.RaceTypeId==$RaceStageInfo.comment.RaceTypeId)}selected="selected"{/tpl:if}>{tpl:$RaceTypeInfo.RaceTypeName/}</option>
			{/tpl:loop}
		</select>
	</th></tr>
<tr class="hover"><td>赛事结构</td>
	<td align="left">	<select name="RaceStructure"  id="RaceStructure" size="1">
			{tpl:loop $RaceStructureList $RaceStructure $RaceStructureName}
			<option value="{tpl:$RaceStructure/}" {tpl:if($RaceStructure==$RaceStageInfo.comment.RaceStructure)}selected="selected"{/tpl:if}>{tpl:$RaceStructureName/}</option>
			{/tpl:loop}
		</select></td>
</tr>
	<tr>
		<th><label >开始结止时间</label></th>
		<td>
			<input type="text" name="StageStartDate" value="{tpl:$RaceStageInfo.StageStartDate/}" class="input-medium"
				   onFocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:'yyyy-MM-dd'})" >
			---
			<input type="text" name="StageEndDate" value="{tpl:$RaceStageInfo.StageEndDate/}" value="" class="input-medium"
				   onFocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:'yyyy-MM-dd'})" >
		</td>
</tr>
	<tr>
	<th><label >报名时间</label></th>
		<td>
			<input type="text" name="ApplyStartTime" value="{tpl:$RaceStageInfo.ApplyStartTime/}" class="input-medium"
				   onFocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm:ss'})" >
		---
			<input type="text" name="ApplyEndTime" value="{tpl:$RaceStageInfo.ApplyEndTime/}" class="input-medium"
				   onFocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:'yyyy-MM-dd HH:mm:ss'})" >
		</td>
	</tr>
	<tr>
	<td>赛事分组列表</td>
	<td align="left"><div id = "SelectedGroupList">
			{tpl:loop $RaceGroupList $RaceGroupInfo}
			<input type="checkbox"  name="SelectedRaceGroup[{tpl:$RaceGroupInfo.RaceGroupId/}]" value="{tpl:$RaceGroupInfo.RaceGroupId/}" {tpl:if($RaceGroupInfo.selected == 1)}checked{/tpl:if} /> {tpl:$RaceGroupInfo.RaceGroupName/}
			{/tpl:loop}
		</div></td>
	</tr>
        <tr class="hover"><td>分站图片1</td>
            <td align="left">
                {tpl:if($RaceStageIconList.1.RaceStageIcon_root!="")}
                已选图片:<img src="{tpl:$RootUrl/}{tpl:$RaceStageIconList.1.RaceStageIcon_root/}" width="30px;" height="30px;"/>
                <a href="javascript:void(0);" onclick="RaceStageIconDelete('{tpl:$RaceStageInfo.RaceStageId/}','图片1','1')">删除</a><br>       
                {/tpl:if}
                更改图片:<input name="RaceStageIcon[1]" type="file" class="span4" id="RaceStageIcon[1]"/>
            </td>
        </tr>
        <tr class="hover"><td>分站图片2</td>
            <td align="left">
                {tpl:if($RaceStageIconList.2.RaceStageIcon_root!="")}
                已选图片:<img src="{tpl:$RootUrl/}{tpl:$RaceStageIconList.2.RaceStageIcon_root/}" width="30px;" height="30px;"/>
                <a href="javascript:void(0);" onclick="RaceStageIconDelete('{tpl:$RaceStageInfo.RaceStageId/}','图片2','2')">删除</a><br>           
                {/tpl:if}
                更改图片:<input name="RaceStageIcon[2]" type="file" class="span4" id="RaceStageIcon[2]"/>
            </td>
        </tr>
        <tr class="hover"><td>分站图片3</td>
            <td align="left">
                {tpl:if($RaceStageIconList.3.RaceStageIcon_root!="")}
                已选图片:<img src="{tpl:$RootUrl/}{tpl:$RaceStageIconList.3.RaceStageIcon_root/}" width="30px;" height="30px;"/>
                <a href="javascript:void(0);" onclick="RaceStageIconDelete('{tpl:$RaceStageInfo.RaceStageId/}','图片3','3')">删除</a><br>            
                {/tpl:if}
                更改图片:<input name="RaceStageIcon[3]" type="file" class="span4" id="RaceStageIcon[3]"/>
            </td>
        </tr>
	<tr class="hover"><td colspan = 2>分站介绍</td></tr>
	<tr class="hover"><td colspan = 2><textarea name="RaceStageComment" id="RaceStageComment" >{tpl:$RaceStageInfo.RaceStageComment/}</textarea></td>
	</tr>
	<tr class="noborder"><td></td>
<td><button type="submit" id="race_stage_update_submit">提交</button></td>
</tr>
</table>
</form>

<script type="text/javascript">
$('#race_stage_update_submit').click(function(){
	var options = {
		dataType:'json',
		beforeSubmit:function(formData, jqForm, options) {
		},
		success:function(jsonResponse) {
			if (jsonResponse.errno) {
				var errors = [];
				errors[1] = '分站名称不能为空，请修正后再次提交';
				errors[2] = '分站ID无效，请修正后再次提交';
				errors[3] = '请选择一个有效的赛事，请修正后再次提交';
				errors[4] = '请选择至少一个赛事分组，请修正后再次提交';
				errors[9] = '入库失败，请修正后再次提交';
				divBox.alertBox(errors[jsonResponse.errno],function(){});
			} else {
				var message = '修改分站成功';
				divBox.confirmBox({content:message,ok:function(){windowParent.getRightHtml('{tpl:$this.sign/}');}});
			}
		}
	};
	$('#race_stage_update_form').ajaxForm(options);
});
function getGroupList()
{
	catalog=$("#RaceCatalogId");
	stage=$("#RaceStageId");
	$.ajax
	({
		type: "GET",
		url: "?ctl=xrace/race.stage&ac=get.selected.group&RaceCatalogId="+catalog.val()+"&RaceStageId="+stage.val(),
		success: function(msg)
		{
			$("#SelectedGroupList").html(msg);
		}
	});
//*/
}
</script>
<script>
    ClassicEditor
        .create( document.querySelector( '#RaceStageComment' ),
            {
                config: { height: '300px', width: '552px' },
                ckfinder: {
                    uploadUrl: '/callback/upload.php?type=img',
                }
            }
        )
        .catch( error => {
        console.error( error );
    } );

</script>
{tpl:tpl contentFooter/}