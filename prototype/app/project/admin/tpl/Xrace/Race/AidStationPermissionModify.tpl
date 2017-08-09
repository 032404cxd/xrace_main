{tpl:tpl contentHeader/}
<form id="aid_station_permission_modify_form" name="aid_station_permission_modify_form" action="{tpl:$this.sign/}&ac=aid.station.permission.update" method="post">
<input type="hidden" name="AidStationId" id="AidStationId" value="{tpl:$AidStationInfo.AidStationId/}" />
<table width="99%" align="center" class="table table-bordered table-striped">

    {tpl:if($RaceStageInfo.comment.RaceStructure!="race")}
    {tpl:loop $RaceList $RaceGroupId $RaceGroupInfo}
	<tr>
		<th align="center" class="rowtip">{tpl:$RaceGroupInfo.RaceGroupInfo.RaceGroupName/}</th>
		<th align="center" class="rowtip">
            {tpl:loop $RaceGroupInfo.RaceList $RaceId $RaceInfo}
            {tpl:$RaceInfo.RaceName/} <input type="checkbox"  name="RaceList[{tpl:$RaceId/}][{tpl:$RaceGroupId/}][Selected]"  {tpl:if($RaceInfo.selected > 0)}checked{/tpl:if} value="1"/> <p>
                {/tpl:loop}
		</th>
	</tr>
    {/tpl:loop}
    {tpl:else}
    {tpl:loop $RaceList $RaceId $RaceInfo}
	<tr>
		<th align="center" class="rowtip">{tpl:$RaceInfo.RaceName/}</th>
		<th align="center" class="rowtip">
            {tpl:loop $RaceInfo.comment.SelectedRaceGroup $RaceGroupId $RaceGroupInfo}
            {tpl:$RaceGroupInfo.RaceGroupName/} <input type="checkbox"  name="RaceList[{tpl:$RaceId/}][{tpl:$RaceGroupId/}][Selected]"  {tpl:if($RaceGroupInfo.selected > 0)}checked{/tpl:if} value="1"/>  领取次数 ： <input type="text" id="RaceList[{tpl:$RaceId/}][{tpl:$RaceGroupId/}][AidCount]" name="RaceList[{tpl:$RaceId/}][{tpl:$RaceGroupId/}][AidCount]"  class="span1" value="{tpl:$RaceGroupInfo.AidCount/}"/>  <p>
			{/tpl:loop}
		</th>
	</tr>
	{/tpl:loop}
    {/tpl:if}

	<tr class="noborder"><td></td>
<td><button type="submit" id="aid_station_permission_submit">提交</button></td>
</tr>
</table>
</form>
{tpl:tpl contentFooter/}