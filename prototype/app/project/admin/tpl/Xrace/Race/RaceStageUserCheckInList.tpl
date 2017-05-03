{tpl:tpl contentHeader/}
<script type="text/javascript">
</script>
<fieldset><legend>{tpl:$RaceStageInfo.RaceStageName/} 选手签到列表 <a href="{tpl:$this.sign/}">返回</a>   ------>{tpl:$CheckInByCodeUrl/}--{tpl:$CheckInByIdUrl/}</legend>
  <table width="99%" align="center" class="table table-bordered table-striped">
    <tr><th align="center" class="rowtip" colspan="4">
        {tpl:loop $CheckInStatus $Cid $CheckInStatusInfo}
        {tpl:$CheckInStatusInfo.StatusUrl/}
        {/tpl:loop}
      </th></tr>
  <tr>
    <th align="center" class="rowtip">姓名</th>
    <th align="center" class="rowtip">签到状态</th>
    <th align="center" class="rowtip">短信发送状态</th>
    <th align="center" class="rowtip">电话号码</th>
  </tr>
  {tpl:loop $UserCheckInStatusList $Cid $CheckInInfo}
  <tr>
    <th align="center" class="rowtip">{tpl:$CheckInInfo.RaceUserInfo.Name/}</th>
    <th align="center" class="rowtip">{tpl:$CheckInInfo.CheckInStatusName/}</th>
    <th align="center" class="rowtip">{tpl:$CheckInInfo.CheckInSmsSentStatusName/}</th>
    <th align="center" class="rowtip">{tpl:$CheckInInfo.Mobile/}</th>
  </tr>
  {/tpl:loop}
</table>
{tpl:tpl contentFooter/}
