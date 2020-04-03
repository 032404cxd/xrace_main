{tpl:tpl contentHeader/}
<script type="text/javascript">
    function RaceUserInfoById(rid)
    {
        IdNo=$("#IdNo");
        RaceUserInfoBox = divBox.showBox('{tpl:$this.sign/}&ac=race.stage.check.in.user.info&CheckInType=IdNo&IdNo=' + IdNo.val() + '&RaceStageId=' + rid, {title:'个人信息',width:800,height:750});
        return false;
    }
    function RaceUserInfoByID(rid)
    {
        CheckInCode=$("#CheckInCode");
        RaceUserInfoBox = divBox.showBox('{tpl:$this.sign/}&ac=race.stage.check.in.user.info&CheckInType=Code&CheckInCode=' + CheckInCode.val() + '&RaceStageId=' + rid, {title:'个人信息',width:800,height:750});
        return false;
    }
    function RaceUserInfoByBIB(rid)
    {
        BIB=$("#BIB");
        RaceUserInfoBox = divBox.showBox('{tpl:$this.sign/}&ac=race.stage.check.in.user.info&CheckInType=BIB&BIB=' + BIB.val() + '&RaceStageId=' + rid, {title:'个人信息',width:800,height:750});
        return false;
    }
</script>
<fieldset><legend>{tpl:$RaceStageInfo.RaceStageName/} 选手签到 {tpl:$CheckInStatusUrl/}</legend>
  <form id="check_in_form" name="check_in_form" action="{tpl:$this.sign/}&ac=user.check.in" method="post">
    <input type="hidden" name="CheckInType" id="CheckInType" value="{tpl:$CheckInType/}" />
    <input type="hidden" name="RaceStageId" id="RaceStageId" value="{tpl:$RaceStageId/}" />
    <table width="99%" align="center" class="table table-bordered table-striped">
      <tr class="hover">
          {tpl:if($CheckInType=="Code")}
        <td><input type="text" class="span4" name="CheckInCode"  id="CheckInCode" value="" size="50" /></td>
        <td><a href="javascript:;" onclick="RaceUserInfoByCode('{tpl:$RaceStageInfo.RaceStageId/}')">点击提交</a></td>
          {tpl:else}
              {tpl:if($CheckInType=="Id")}
              <td><input type="text" class="span4" name="CheckInCode"  id="CheckInCode" value="" size="50" /></td>
              <td><a href="javascript:;" onclick="RaceUserInfoByID('{tpl:$RaceStageInfo.RaceStageId/}')">点击提交</a></td>
              {tpl:else}
                  <td>BIB：<input type="text" class="span4" name="BIB"  id="BIB" value="" size="50" /></td>
                  <td><a href="javascript:;" onclick="RaceUserInfoByBIB('{tpl:$RaceStageInfo.RaceStageId/}')">点击提交</a></td>
              {/tpl:if}
          {/tpl:if}
      </tr>
    </table>
  </form>
{tpl:tpl contentFooter/}
