{tpl:tpl contentHeader/}
<script type="text/javascript">
$(document).ready(function(){
	$('#add_arena').click(function(){
		addArenaBox = divBox.showBox('{tpl:$this.sign/}&ac=arena.add', {title:'添加场地',width:400,height:200});
	});
});

function arenaDelete(p_id, p_name){
	deleteArenaBox = divBox.confirmBox({content:'是否删除 ' + p_name + '?',ok:function(){location.href = '{tpl:$this.sign/}&ac=arena.delete&ArenaId=' + p_id;}});
}

function arenaModify(mid){
	modifyArenaBox = divBox.showBox('{tpl:$this.sign/}&ac=arena.modify&ArenaId=' + mid, {title:'修改场地',width:400,height:200});
}


</script>

<fieldset><legend>操作</legend>
[ <a href="javascript:;" id="add_arena">添加场地</a> ]
</fieldset>

<fieldset><legend>场地列表 </legend>
<table width="99%" align="center" class="table table-bordered table-striped">
  <tr>
    <th align="center" class="rowtip">场地ID</th>
    <th align="center" class="rowtip">场地名称</th>
    <th align="center" class="rowtip">操作</th>
  </tr>

{tpl:loop $ArenaList $ArenaInfo}
  <tr class="hover">
    <td align="center">{tpl:$ArenaInfo.ArenaId/}</td>
    <td align="center">{tpl:$ArenaInfo.ArenaName/}</td>
    <td align="center"><a  href="javascript:;" onclick="arenaDelete('{tpl:$ArenaInfo.ArenaId/}','{tpl:$ArenaInfo.arenaName/}')">删除</a> |  <a href="javascript:;" onclick="arenaModify('{tpl:$ArenaInfo.ArenaId/}');">修改</a> | {tpl:$ArenaInfo.RaceTimeListUrl/}</td>
  </tr>
{/tpl:loop}
</table>
</fieldset>
{tpl:tpl contentFooter/}
