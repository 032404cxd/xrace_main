{tpl:tpl contentHeader/}
<script type="text/javascript">
</script>
<fieldset><legend>{tpl:$RaceStageInfo.RaceStageName/} 选手签到 {tpl:$CheckInStatusUrl/}</legend>
  <form id="check_in_form" name="check_in_form" action="{tpl:$this.sign/}&ac=user.check.in" method="post">
    <input type="hidden" name="RaceStageId" id="RaceStageId" value="{tpl:$RaceStageId/}" />
    <table width="99%" align="center" class="table table-bordered table-striped">
      <tr class="hover">
        <td><input type="text" class="span4" name="CheckInCode"  id="CheckInCode" value="" size="50" /></td>
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
              divBox.confirmBox({content:message,ok:function(){windowParent.getRightHtml('{tpl:$this.sign/}'+ '&ac=race.user.check.in.BIB&RaceStageId=' + RaceStageId.val() + '&UserId=' + jsonResponse.UserId);}});
          }
        }
      };
      $('#check_in_form').ajaxForm(options);
    });
  </script>
{tpl:tpl contentFooter/}
