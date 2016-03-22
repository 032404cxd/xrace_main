{tpl:tpl contentHeader/}
<script type="text/javascript">
$(document).ready(function(){
	$('#add_app').click(function(){
		addAppBox = divBox.showBox('{tpl:$this.sign/}&ac=race.type.add', {title:'添加比赛分类',width:500,height:300});
	});
});

function RaceTypeDelete(p_id, p_name){
	deleteAppBox = divBox.confirmBox({content:'是否删除 ' + p_name + '?',ok:function(){location.href = '{tpl:$this.sign/}&ac=race.type.delete&RaceTypeId=' + p_id;}});
}

function RaceTypeModify(mid){
	modifyRaceTypeBox = divBox.showBox('{tpl:$this.sign/}&ac=race.type.modify&RaceTypeId=' + mid, {title:'修改比赛分类',width:500,height:300});
}

</script>

<fieldset><legend>操作</legend>
[ <a href="javascript:;" id="add_app">添加比赛分类</a> ]
</fieldset>

<fieldset><legend>比赛分类列表 </legend>
<table width="99%" align="center" class="table table-bordered table-striped">
  <tr>
    <th align="center" class="rowtip">比赛分类ID</th>
    <th align="center" class="rowtip">比赛分类名称</th>
    <th align="center" class="rowtip">操作</th>
  </tr>

{tpl:loop $RaceTypeList $RaceTypeInfo}
  <tr class="hover">
    <td align="center">{tpl:$RaceTypeInfo.RaceTypeId/}</td>
    <td align="center">{tpl:$RaceTypeInfo.RaceTypeName/}</td>
    <td align="center"><a  href="javascript:;" onclick="RaceTypeDelete('{tpl:$RaceTypeInfo.RaceTypeId/}','{tpl:$RaceTypeInfo.RaceTypeName/}')">删除</a> |  <a href="javascript:;" onclick="RaceTypeModify('{tpl:$RaceTypeInfo.RaceTypeId/}');">修改</a></td>
  </tr>
{/tpl:loop}
</table>
</fieldset>
{tpl:tpl contentFooter/}
