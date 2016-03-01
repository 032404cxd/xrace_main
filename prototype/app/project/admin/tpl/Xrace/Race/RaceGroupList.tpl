{tpl:tpl contentHeader/}
<script type="text/javascript">
$(document).ready(function(){
	$('#add_app').click(function(){
		addAppBox = divBox.showBox('{tpl:$this.sign/}&ac=race.group.add', {title:'添加赛事组别',width:500,height:300});
	});
});

function RaceGroupDelete(p_id, p_name){
	deleteAppBox = divBox.confirmBox({content:'是否删除 ' + p_name + '?',ok:function(){location.href = '{tpl:$this.sign/}&ac=race.group.delete&RaceGroupId=' + p_id;}});
}

function RaceGroupModify(mid){
	modifyRaceGroupBox = divBox.showBox('{tpl:$this.sign/}&ac=race.group.modify&RaceGroupId=' + mid, {title:'修改赛事组别',width:500,height:300});
}
function LicenseAdd(mid){
  modifyRaceGroupBox = divBox.showBox('{tpl:$this.sign/}&ac=group.license.add&RaceGroupId=' + mid, {title:'添加审核条件',width:500,height:300});
}

</script>

<fieldset><legend>操作</legend>
[ <a href="javascript:;" id="add_app">添加赛事组别</a> ]
</fieldset>
<form action="{tpl:$this.sign/}" name="form" id="form" method="post">
  <select name="RaceCatalogId" size="1">
    <option value="0">全部</option>
    {tpl:loop $RaceCatalogArr $RaceCatalogInfo}
    <option value="{tpl:$RaceCatalogInfo.RaceCatalogId/}" {tpl:if($RaceCatalogInfo.RaceCatalogId==$RaceCatalogId)}selected="selected"{/tpl:if}>{tpl:$RaceCatalogInfo.RaceCatalogName/}</option>
    {/tpl:loop}
  </select>
  <input type="submit" name="Submit" value="查询" />
</form>
<fieldset><legend>赛事组别列表 </legend>
  <table width="99%" align="center" class="table table-bordered table-striped">
  <tr>
    <th align="center" class="rowtip">对应赛事</th>
    <th align="center" class="rowtip">赛事组别ID</th>
    <th align="center" class="rowtip">赛事组别名称</th>
    <th align="center" class="rowtip">审核条件</th>
    <th align="center" class="rowtip">操作</th>
  </tr>

{tpl:loop $RaceGroupList $RaceCatalogId $RaceCatalogInfo}
  <tr>
    <th align="center" class="rowtip"  rowspan = {tpl:$RaceCatalogInfo.RowCount /}>{tpl:$RaceCatalogInfo.RaceCatalogName/}</th>
  </tr>
  {tpl:loop $RaceCatalogInfo.RaceGroupList $RaceGroupId $RaceGroupInfo}
  <tr>
    <th align="center" class="rowtip" >{tpl:$RaceGroupInfo.RaceGroupId/}</th>
    <th align="center" class="rowtip" >{tpl:$RaceGroupInfo.RaceGroupName/}</th>
    <th align="center" class="rowtip" >{tpl:$RaceGroupInfo.LicenseListText/}</th>
    <th align="center" class="rowtip" ><a  href="javascript:;" onclick="RaceGroupDelete('{tpl:$RaceGroupInfo.RaceGroupId/}','{tpl:$RaceGroupInfo.RaceGroupName/}')">删除</a> |  <a href="javascript:;" onclick="RaceGroupModify('{tpl:$RaceGroupInfo.RaceGroupId/}');">修改</a> |  <a href="javascript:;" onclick="LicenseAdd('{tpl:$RaceGroupInfo.RaceGroupId/}');">添加审核条件</a></th>
  </tr>
  {/tpl:loop}
{/tpl:loop}
</table>
</fieldset>
{tpl:tpl contentFooter/}
