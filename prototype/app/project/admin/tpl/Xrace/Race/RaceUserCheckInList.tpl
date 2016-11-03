{tpl:tpl contentHeader/}
<script type="text/javascript">
</script>
<fieldset><legend>{tpl:$RaceStageInfo.RaceStageName/} 选手签到列表 <a href="{tpl:$this.sign/}">返回</a></legend>
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
    <th align="center" class="rowtip">{tpl:$CheckInInfo.UserInfo.name/}</th>
    <th align="center" class="rowtip">{tpl:$CheckInInfo.CheckInStatusName/}</th>
    <th align="center" class="rowtip">{tpl:$CheckInInfo.CheckInSmsSentStatusName/}</th>
    <th align="center" class="rowtip">{tpl:$CheckInInfo.Mobile/}</th>
  </tr>
  {/tpl:loop}
</table>
<script type="text/javascript">
  $('#race_user_list_update_submit').click(function(){
    var options = {
      dataType:'json',
      beforeSubmit:function(formData, jqForm, options) {
      },
      success:function(jsonResponse) {
        if (jsonResponse.errno) {
          var errors = [];
          errors[1] = '赛事组别名称不能为空，请修正后再次提交';
          errors[2] = '赛事组别ID无效，请修正后再次提交';
          errors[3] = '请选择一个有效的赛事，请修正后再次提交';
          errors[9] = '入库失败，请修正后再次提交';
          divBox.alertBox(errors[jsonResponse.errno],function(){});
        } else {
          var message = '修改赛事组别成功';
          divBox.confirmBox({content:message,ok:function(){windowParent.getRightHtml('{tpl:$this.sign/}');}});
        }
      }
    };
    $('#race_user_list_update_form').ajaxForm(options);
  });
</script>
{tpl:tpl contentFooter/}
