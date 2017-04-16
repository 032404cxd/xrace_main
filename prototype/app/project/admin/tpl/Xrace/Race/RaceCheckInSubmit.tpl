{tpl:tpl contentHeader/}
<script type="text/javascript">
  function RaceCheckIn(a_id,u_name){
    RaceCheckInBox= divBox.confirmBox({content:'选手:'+ u_name + '是否确认检录 ?',ok:function(){location.href = '{tpl:$this.sign/}&ac=race.user.check.in&ApplyId=' + a_id;}});
  }
</script>
<fieldset><legend>{tpl:$RaceInfo.RaceName/} 选手检录 {tpl:$CheckInStatusUrl/}</legend>
  <form id="check_in_form" name="check_in_form" action="{tpl:$this.sign/}&ac=race.check.in" method="post">
    <input type="hidden" name="RaceId" id="RaceId" value="{tpl:$RaceId/}" />
    <table width="99%" align="center" class="table table-bordered table-striped">
      {tpl:if($RaceUserCheckInfo.ApplyInfo.CheckInStatus!=1)}
      <tr class="hover">
        <th align="center" class="rowtip">操作</th><td align="left"><a  href="javascript:;" onclick="RaceCheckIn('{tpl:$RaceUserCheckInfo.ApplyInfo.ApplyId/}','{tpl:$RaceUserCheckInfo.RaceUserInfo.Name/}');">确认无误，检录</a></td>
      </tr>
      {tpl:else}
      <tr class="hover">
        <th align="center" class="rowtip">检录状态</th>
        <td align="left">已检录 <a href="{tpl:$this.sign/}&ac=race.check.in.submit&RaceId={tpl:$RaceInfo.RaceId/}">返回</a></td>
      </tr>
    <tr class="hover">
        <th align="center" class="rowtip">检录时间</th>
        <td align="left">{tpl:$RaceUserCheckInfo.ApplyInfo.CheckInTime/}</td>
      </tr>
      {/tpl:if}

      <tr class="hover">
        <th align="center" class="rowtip" colspan="2">比赛信息</th>
      </tr>
      <tr class="hover">
        <th align="center" class="rowtip">组别</th>
        <td align="left">{tpl:$RaceUserCheckInfo.ApplyInfo.RaceGroupName/}</td>
      </tr>
      <tr class="hover">
        <th align="center" class="rowtip">BIB</th>
        <td align="left">{tpl:$RaceUserCheckInfo.ApplyInfo.BIB/}</td>
      </tr>
      <tr class="hover">
        <th align="center" class="rowtip">芯片</th>
        <td align="left">{tpl:$RaceUserCheckInfo.ApplyInfo.ChipId/}</td>
      </tr>
      <tr class="hover">
        <th align="center" class="rowtip">队伍名称</th>
        <td align="left">{tpl:$RaceUserCheckInfo.ApplyInfo.TeamName/}</td>
      </tr>
      <tr class="hover">
        <th align="center" class="rowtip" colspan="2">用户信息</th>
      </tr>
        <tr class="hover">
          <th align="center" class="rowtip">姓名</th>
          <td align="left">{tpl:$RaceUserCheckInfo.RaceUserInfo.Name/}</td>
        </tr>
        <tr class="hover">
          <th align="center" class="rowtip">用户性别</th>
          <td align="left">{tpl:$RaceUserCheckInfo.RaceUserInfo.Sex/}</td>
        </tr>
      <tr class="hover">
        <th align="center" class="rowtip">生日</th>
        <td align="left">{tpl:$RaceUserCheckInfo.RaceUserInfo.Birthday/}</td>
      </tr>
        <tr class="hover">
          <th align="center" class="rowtip">证件类型</th>
          <td align="left">{tpl:$RaceUserCheckInfo.RaceUserInfo.AuthIdType/}</td>

        </tr>
        <tr class="hover">
          <th align="center" class="rowtip">证件号码</th>
          <td align="left">{tpl:$RaceUserCheckInfo.RaceUserInfo.IdNo/}</td>
        </tr>
    </table>
  </form>
  <script type="text/javascript">
    $('#check_in_submi').click(function(){
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
