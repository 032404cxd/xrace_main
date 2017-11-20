{tpl:tpl contentHeader/}
<style>
    .td-edit .hidden1{
        opacity: 0 !important;
    }
    .td-edit .visible1{
        opacity: 1 !important;
    }
    .td-edit{
        position: relative;
    }
    .td-edit .text{
        display: inline-block;
        min-width: 100px;
    }
    .td-edit input{
        position: absolute;
        left: 0;
        top: 0;
        min-width: 100px;
    }
</style>
<script type="text/javascript">
  function UserRaceDelete(uname,aid){
    deleteUserRaceBox = divBox.confirmBox({content:'确定将退出比赛?',ok:function(){location.href = '{tpl:$this.sign/}&ac=user.race.delete&ApplyId=' + aid;}});
  }
</script>
<form action="{tpl:$this.sign/}&ac=user.race.list.update" name="user_race_list_update_form" id="user_race_list_update_form" method="post">
<input type="hidden" name="RaceStageId" id="RaceStageId" value="{tpl:$RaceStageInfo.RaceStageId/}" />
<input type="hidden" name="RaceUserId" id="RaceUserId" value="{tpl:$RaceUserId/}" />
<input type="hidden" name="CheckInType" id="CheckInType" value="{tpl:$CheckInType/}" />


    <table id="container" width="99%" align="center" class="table table-bordered table-striped">
    <tr><th align="center" class="rowtip" colspan="5">{tpl:$RaceUserInfo.Name/} 共计报名记录{tpl:$UserRaceList func="count(@@)"/} {tpl:$CheckInUrl/}</th></tr>
    {tpl:if(count($UserRaceList))}
  <tr>
    <th align="center" class="rowtip">报名来源</th>
    {tpl:if(count($RaceStageInfo.comment.RaceStructure=='race'))}
    <th align="center" class="rowtip">比赛</th>
    <th align="center" class="rowtip">分组</th>
    {tpl:else}
    <th align="center" class="rowtip">分组</th>
    <th align="center" class="rowtip">比赛</th>
    {/tpl:if}
    <th align="center" class="rowtip">所属队伍</th>
    <th align="center" class="rowtip">报名时间</th>
    <th align="center" class="rowtip">选手号码</th>
    <th align="center" class="rowtip">计时芯片ID</th>
  </tr>
  {tpl:loop $UserRaceList $Aid $UserInfo}
  <tr>
    <input type="hidden" name="UserRaceList[{tpl:$Aid/}][ApplyId]" id="UserRaceList[{tpl:$UserInfo.UserId/}][ApplyId]" value="{tpl:$UserInfo.ApplyId/}" />
    <th align="center" class="rowtip">{tpl:$UserInfo.ApplySourceName/}</th>
    {tpl:if(count($RaceStageInfo.comment.RaceStructure=='race'))}
    <th align="center" class="rowtip">{tpl:$UserInfo.RaceName/}</th>
    <th align="center" class="rowtip">
        <select name="UserRaceList[{tpl:$Aid/}][RaceGroupId]" id="UserRaceList[{tpl:$Aid/}][RaceGroupId]" class="span2" size="1">
            {tpl:loop $UserInfo.RaceGroupList $G $GInfo}
            <option value="{tpl:$G/}" {tpl:if($G==$UserInfo.RaceGroupId)}selected="selected"{/tpl:if}>{tpl:$GInfo.RaceGroupName/}</option>
            {/tpl:loop}
    </th>
    {tpl:else}
    <th align="center" class="rowtip">{tpl:$UserInfo.RaceGroupName/}</th>
    <th align="center" class="rowtip">{tpl:$UserInfo.RaceName/}</th>
    {/tpl:if}
    <th align="center" class="rowtip">{tpl:$UserInfo.TeamName/}</th>
    <th align="center" class="rowtip">{tpl:$UserInfo.ApplyTime/}</th>
    <th align="center" class="rowtip td-edit"><span class="text">{tpl:$UserInfo.BIB/}</span><input type="text" class="span1 hidden1" name="UserRaceList[{tpl:$Aid/}][BIB]" id="UserRaceList[{tpl:$UserInfo.UserId/}][BIB]" value="{tpl:$UserInfo.BIB/}" /> <a href="#" class="btn-edit">编辑</a></th>
    <th align="center" class="rowtip"><input type="text" class="span2 chip-txt" name="UserRaceList[{tpl:$Aid/}][ChipId]" id="UserRaceList[{tpl:$UserInfo.UserId/}][ChipId]" value="{tpl:$UserInfo.ChipId/}" /></th>
  </tr>
  {/tpl:loop}

        <tr class="noborder"><td align="center" class="rowtip">补给代码列表<p>以","分割</td><td colspan = 6><textarea name="AidCodeList" id="AidCodeList" class="span5" rows="4">{tpl:$UserCheckInInfo.comment.AidCodeList func="implode(',',@@)"/}</textarea></td>
        </tr>
        <tr class="noborder"><td colspan = 7><button type="submit" id="user_list_update_submit">提交</button></td>
  </tr>
  {tpl:else}
  {/tpl:if}
</table>
</form>
<script type="text/javascript">
  $('#user_list_update_submit').click(function(){
    var options = {
      dataType:'json',
      beforeSubmit:function(formData, jqForm, options) {
      },
      success:function(jsonResponse) {
        if (jsonResponse.errno) {
          var errors = [];
          errors[9] = '入库失败，请修正后再次提交';
          divBox.alertBox(errors[jsonResponse.errno],function(){});
        } else {
          var message = '选手信息修改成功';
          RaceStageId=$("#RaceStageId");
            CheckInType=$("#CheckInType");

            divBox.confirmBox({content:message,ok:function(){windowParent.getRightHtml('{tpl:$this.sign/}'+ '&ac=race.stage.user.check.in&RaceStageId=' + RaceStageId.val() + '&CheckInType=' + CheckInType.val());}});
        }
      }
    };
    $('#user_race_list_update_form').ajaxForm(options);
  });
</script>
<script type="text/javascript">

    $(function () {

        initInputEdit();

        function initInputEdit()
        {
            var $container = $('#container');
            var $text, $input;

            $container.on('click', '.btn-edit', function(e){

                e.preventDefault();

                $text = $(this).siblings('.text');
                $input = $(this).siblings('input');

                $text.removeClass('visible1').addClass('hidden1');
                $input.removeClass('hidden1').addClass('visible1').width($text.width()).focus();

            });

            $container.on('blur', '.td-edit input', function(e){

                $text = $(this).siblings('.text');

                $text.removeClass('hidden1').addClass('visible1').text($(this).val());
                $(this).removeClass('visible1').addClass('hidden1');

            });

            $container.on('keyup', '.td-edit input', function(e){

                if($(this).hasClass('hidden1'))
                {
                    console.log(e);
                    $(this).closest('tr').find('.chip-txt').focus();
                }

            });

            $container.on('focus', '.chip-txt', function(e){

                $(this).select();

            });
        }

    });

</script>
{tpl:tpl contentFooter/}
