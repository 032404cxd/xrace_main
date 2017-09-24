{tpl:tpl contentHeader/}
<script type="text/javascript">
    function RaceTimeAdd()
    {
        ArenaId=$("#ArenaId");
        RaceTimeAddBox = divBox.showBox('{tpl:$this.sign/}&ac=arena.race.time.add&ArenaId=' + ArenaId.val(), {title:'添加时间段',width:500,height:400});
    }
    function RaceTimeModify(id)
    {
        ArenaId=$("#ArenaId");
        RaceTimeAddBox = divBox.showBox('{tpl:$this.sign/}&ac=arena.race.time.modify&ArenaId=' + ArenaId.val() + '&id=' + id, {title:'修改时间段',width:500,height:400});
    }
    function RaceTimeDelete(id, r_name){
        ArenaId=$("#ArenaId");
        RaceTimeBox = divBox.confirmBox({content:'是否删除 ' + r_name + '?',ok:function(){location.href = '{tpl:$this.sign/}&ac=arena.race.time.delete&ArenaId=' + ArenaId.val() + '&id=' + id;}});
    }
</script>
<div class="br_bottom"></div>
<form id="arena_update_form" name="arena_update_form" action="{tpl:$this.sign/}&ac=arena.update" method="post">
<input type="hidden" name="ArenaId" id="ArenaId" value="{tpl:$ArenaInfo.ArenaId/}" />
<fieldset><legend>{tpl:$ArenaInfo.ArenaName/}-约战时间配置 {tpl:$returnUrl/}</legend>
<table width="99%" align="center" class="table table-bordered table-striped" widtd="99%">
    {tpl:if(count($ArenaInfo.comment.RaceTimeList))}
	<tr>
		<th align="center" class="rowtip">名称</th>
		<th align="center" class="rowtip">每日</th>
		<th align="center" class="rowtip">时间段</th>
		<th align="center" class="rowtip">操作</th>
	</tr>
    {tpl:loop $ArenaInfo.comment.RaceTimeList $id $TimeInfo}
		<tr>
			<td align="center" class="rowtip">{tpl:$TimeInfo.name/}
			</td>

			<td align="center" class="rowtip">
				{tpl:loop $WeekdayList $day $dayName}
					{tpl:loop $TimeInfo.Weekday $weekday}
						{tpl:if($day==$weekday)}{tpl:$dayName/} {/tpl:if}
					{/tpl:loop}
                {/tpl:loop}
			</td>
			<td align="center" class="rowtip">{tpl:$TimeInfo.StartHour/}:{tpl:$TimeInfo.StartMinute/}~{tpl:$TimeInfo.EndHour/}:{tpl:$TimeInfo.EndMinute/}
			</td>
			<td align="center" class="rowtip">
				<a href="javascript:;" onclick="RaceTimeModify('{tpl:$id/}')">修改</a> | <a href="javascript:;" onclick="RaceTimeDelete('{tpl:$id/}','{tpl:$TimeInfo.name/}')">删除</a>
			</td>
		</tr>
    {/tpl:loop}
		<tr>
			<th align="center" class="rowtip" colspan="4"><a href="javascript:;" onclick="RaceTimeAdd()">继续添加</a>
			</th>
		</tr>
    {tpl:else}
	<tr>
		<th align="center" class="rowtip">本站尚未配置任何比赛时间段<a href="javascript:;" onclick="RaceTimeAdd()">点此添加</a>
		</th>
	</tr>
    {/tpl:if}


</table>
</form>
{tpl:tpl contentFooter/}