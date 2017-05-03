{tpl:tpl contentHeader/}
<script type="text/javascript">
</script>
<fieldset><legend>{tpl:$RaceStageInfo.RaceStageName/} 选手签到 {tpl:$CheckInStatusUrl/}</legend>
  <form id="check_in_form" name="check_in_form" action="{tpl:$this.sign/}&ac=user.check.in" method="post">
    <input type="hidden" name="CheckInType" id="CheckInType" value="{tpl:$CheckInType/}" />
    <input type="hidden" name="RaceStageId" id="RaceStageId" value="{tpl:$RaceStageId/}" />
    <table width="99%" align="center" class="table table-bordered table-striped">
      <tr class="hover">
          {tpl:if($CheckInType=="Code")}
        <td><input type="text" class="span4" name="CheckInCode"  id="CheckInCode" value="" size="50" /></td>
          {tpl:else}
            <td>证件号：<input type="text" class="span4" name="IdNo"  id="IdNo" value="" size="50" /></td>
          {/tpl:if}
        <td><button type="submit" id="check_in_submit">提交</button></td>
      </tr>
    </table>
  </form>
  <script type="text/javascript">
    $('#check_in_submit').click(function(){
      var options = {
        dataType:'json',
        beforeSubmit:function(formData, jqForm, options) {},
        success:function(jsonResponse) {
          if (jsonResponse.errno) {
            var errors = [];
            errors[1] = '签到失败';
            divBox.alertBox(errors[jsonResponse.errno],function(){});
          } else {
            var message = '签到成功';
              RaceStageId=$("#RaceStageId");
              CheckInType=$("#CheckInType");
              divBox.confirmBox({content:message,ok:function(){windowParent.getRightHtml('{tpl:$this.sign/}'+ '&ac=race.stage.user.check.in.BIB&RaceStageId=' + RaceStageId.val() + '&RaceUserId=' + jsonResponse.RaceUserId + '&CheckInType=' + CheckInType.val());}});
          }
        }
      };
      $('#check_in_form').ajaxForm(options);
    });
  </script>
{tpl:tpl contentFooter/}
