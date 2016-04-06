{tpl:tpl contentHeader/}
<script type="text/javascript">

</script>
<form action="{tpl:$this.sign/}&ac=race.user.list.update" name="race_user_list_update_form" id="race_user_list_update_form" method="post">
<input type="hidden" name="RaceId" id="RaceId" value="{tpl:$RaceInfo.RaceId/}" />
<table width="99%" align="center" class="table table-bordered table-striped">
  {tpl:if(count($RaceUserList))}
  <tr>
    <th align="center" class="rowtip">姓名</th>
    <th align="center" class="rowtip">报名时间</th>
    <th align="center" class="rowtip">选手号码</th>
    <th align="center" class="rowtip">计时芯片ID</th>
  </tr>
  {tpl:loop $RaceUserList $Aid $UserInfo}
  <tr>
    <th align="center" class="rowtip">{tpl:$UserInfo.Name/}</th>
    <th align="center" class="rowtip">{tpl:$UserInfo.ApplyTime/}</th>
    <th align="center" class="rowtip"><input type="text" class="span1" name="UserList[{tpl:$UserInfo.UserId/}][BIB]" id="UserList[{tpl:$UserInfo.UserId/}][BIB]" value="{tpl:$UserInfo.BIB/}" /></th>
    <th align="center" class="rowtip"><input type="text" class="span1" name="UserList[{tpl:$UserInfo.UserId/}][ChipId]" id="UserList[{tpl:$UserInfo.UserId/}][ChipId]" value="{tpl:$UserInfo.ChipId/}" /></th>
  </tr>
  {/tpl:loop}
  <tr class="noborder"><td colspan = 4><button type="submit" id="race_user_list_update_submit">提交</button></td>
  </tr>
  {tpl:else}
  <tr>
    <th align="center" class="rowtip">尚未有选手报名<a href="javascript:;" onclick="RaceAdd('{tpl:$RaceStageInfo.RaceStageId/}','{tpl:$RaceGroupInfo.RaceGroupId/}')">点此添加</a>
    </th>
    </th>
  </tr>
  {/tpl:if}
</table>
</form>
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