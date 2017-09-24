{tpl:tpl contentHeader/}
<script type="text/javascript">
    function RaceUserInfo(uid,rname,rid){
        RaceUserInfoBox = divBox.showBox('{tpl:$this.sign/}&ac=race.stage.check.in.user.info&RaceUserId=' + uid + '&RaceStageId=' + rid, {title:rname+'个人信息',width:800,height:750});
    }
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
    <th align="center" class="rowtip"><a href="javascript:;" onclick="RaceUserInfo('{tpl:$CheckInInfo.RaceUserInfo.RaceUserId/}','{tpl:$CheckInInfo.RaceUserInfo.Name/}','{tpl:$RaceStageInfo.RaceStageId/}')">{tpl:$CheckInInfo.RaceUserInfo.Name/}</a></th>
    <th align="center" class="rowtip">{tpl:$CheckInInfo.CheckInStatusName/}</th>
    <th align="center" class="rowtip">{tpl:$CheckInInfo.CheckInSmsSentStatusName/}</th>
    <th align="center" class="rowtip">{tpl:$CheckInInfo.Mobile/}</th>
  </tr>
  {/tpl:loop}
</table>
{tpl:tpl contentFooter/}
