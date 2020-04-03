{tpl:tpl contentHeader/}
<script type="text/javascript">
</script>
<fieldset><legend>{tpl:$RaceStageInfo.RaceStageName/} 选手检录列表   ------>{tpl:$CheckInUrl/}</legend>
  <table width="99%" align="center" class="table table-bordered table-striped">
    <tr><th align="center" class="rowtip" colspan="4">
        {tpl:loop $CheckInStatus $Cid $CheckInStatusInfo}
        {tpl:$CheckInStatusInfo.StatusUrl/}
        {/tpl:loop}
      </th></tr>
  <tr>
    <th align="center" class="rowtip">姓名</th>
    <th align="center" class="rowtip">BIB</th>
    <th align="center" class="rowtip">芯片号码</th>
    <th align="center" class="rowtip">检录状态</th>
  </tr>
  {tpl:loop $UserCheckInStatusList $Cid $CheckInInfo}
  <tr>
    <th align="center" class="rowtip">{tpl:$CheckInInfo.RaceUserInfo.Name/}</th>
    <th align="center" class="rowtip">{tpl:$CheckInInfo.BIB/}</th>
    <th align="center" class="rowtip">{tpl:$CheckInInfo.ChipId/}</th>
    <th align="center" class="rowtip">{tpl:$CheckInInfo.CheckInStatusName/}</th>
  </tr>
  {/tpl:loop}
</table>
{tpl:tpl contentFooter/}
