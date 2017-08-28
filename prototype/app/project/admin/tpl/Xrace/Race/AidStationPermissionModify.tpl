{tpl:tpl contentHeader/}
<form id="aid_station_permission_modify_form" name="aid_station_permission_modify_form" action="{tpl:$this.sign/}&ac=aid.station.permission.update" method="post">
<input type="hidden" name="AidStationId" id="AidStationId" value="{tpl:$AidStationInfo.AidStationId/}" />
<table width="99%" align="center" class="table table-bordered table-striped">

    {tpl:loop $AidCodeTypeList $AidCodeTypeId $AidCodeTypeInfo}
	<tr>
		<th align="center" class="rowtip">{tpl:$AidCodeTypeInfo.AidCodeTypeName/}</th>
		<th align="center" class="rowtip">
 		<input type="checkbox"  name="AidCodeTypeList[{tpl:$AidCodeTypeId/}][Selected]"  {tpl:if($AidCodeTypeInfo.selected > 0)}checked{/tpl:if} value="1"/>领取次数 ： <input type="text" id="AidCodeTypeList[{tpl:$AidCodeTypeId/}][AidCount]" name="AidCodeTypeList[{tpl:$AidCodeTypeId/}][AidCount]"  class="span1" value="{tpl:$AidCodeTypeInfo.AidCount/}"/>
		</th>
	</tr>
	{/tpl:loop}
	<tr class="noborder"><td></td>
<td><button type="submit" id="aid_station_permission_submit">提交</button></td>
</tr>
</table>
</form>
{tpl:tpl contentFooter/}