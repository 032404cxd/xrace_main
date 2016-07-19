{tpl:tpl contentHeader/}
<script type="text/javascript">
$(document).ready(function(){
	$('#add_app').click(function(){
		addAppBox = divBox.showBox('{tpl:$this.sign/}&ac=race.catalog.add', {title:'添加赛事',width:700,height:600});
	});
});

function RaceCatalogDelete(p_id, p_name){
	deleteAppBox = divBox.confirmBox({content:'是否删除 ' + p_name + '?',ok:function(){location.href = '{tpl:$this.sign/}&ac=race.catalog.delete&RaceCatalogId=' + p_id;}});
}

function RaceCatalogModify(mid){
	modifyRaceCatalogBox = divBox.showBox('{tpl:$this.sign/}&ac=race.catalog.modify&RaceCatalogId=' + mid, {title:'修改赛事',width:700,height:600});
}
function RaceCatalogDisclaimerModify(mid){
    modifyRaceCatalogDisclaimerBox = divBox.showBox('{tpl:$this.sign/}&ac=race.catalog.disclaimer.modify&RaceCatalogId=' + mid, {title:'免责声明',width:700,height:600});
}

</script>

<fieldset><legend>操作</legend>
[ <a href="javascript:;" id="add_app">添加赛事</a> ]
</fieldset>

<fieldset><legend>赛事列表 </legend>
<table width="99%" align="center" class="table table-bordered table-striped">
  <tr>
    <th align="center" class="rowtip">赛事ID</th>
    <th align="center" class="rowtip">赛事名称</th>
    <th align="center" class="rowtip">赛事图标</th>
    <th align="center" class="rowtip">操作</th>
  </tr>

{tpl:loop $RaceCatalogList $RaceCatalogInfo}
  <tr class="hover">
    <td align="center">{tpl:$RaceCatalogInfo.RaceCatalogId/}</td>
    <td align="center">{tpl:$RaceCatalogInfo.RaceCatalogName/}</td>
    <td align="center">{tpl:if($RaceCatalogInfo.comment.RaceCatalogIcon_root=="")}未定义{tpl:else}<img src="{tpl:$RootUrl/}{tpl:$RaceCatalogInfo.comment.RaceCatalogIcon_root/}" width='150' height='130'/>{/tpl:if}</td>    </td>
    <td align="center"><a  href="javascript:;" onclick="RaceCatalogDelete('{tpl:$RaceCatalogInfo.RaceCatalogId/}','{tpl:$RaceCatalogInfo.RaceCatalogName/}')">删除</a> |  <a href="javascript:;" onclick="RaceCatalogModify('{tpl:$RaceCatalogInfo.RaceCatalogId/}');">修改</a> |  <a href="javascript:;" onclick="RaceCatalogDisclaimerModify('{tpl:$RaceCatalogInfo.RaceCatalogId/}');">免责声明</a></td>
  </tr>
{/tpl:loop}
</table>
</fieldset>
{tpl:tpl contentFooter/}
