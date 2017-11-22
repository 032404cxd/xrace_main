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
  function RaceTransfer(aid){
      raceTrnasferBox = divBox.showBox('{tpl:$this.sign/}&ac=race.transfer&ApplyId=' + aid, {title:'比赛转换',width:800,height:600});
  }
  function getThirdByStage(aid)
  {
      stage=$("#RaceStageId");
      race=$("#RaceId");
      $.ajax
      ({
          type: "GET",
          dataType:'json',
          url: "?ctl=xrace/race.stage&ac=get.third.level.by.stage&RaceStageId="+stage.val() + "&RaceId="+race.val(),
          success: function(jsonResponse)
          {
              $("#RaceGroupId").html(jsonResponse.text);
          }});

  }
</script>
<form action="{tpl:$this.sign/}&ac=race.transfer" name="race_transfer_form" id="race_transfer_form" method="post">
<input type="hidden" name="RaceStageId" id="RaceStageId" value="{tpl:$ApplyInfo.RaceStageId/}" />
    <input type="hidden" name="RaceUserId" id="RaceUserId" value="{tpl:$ApplyInfo.RaceUserId/}" />

    <input type="hidden" name="ApplyId" id="ApplyId" value="{tpl:$ApplyInfo.ApplyId/}" />
    <table id="container" width="99%" align="center" class="table table-bordered table-striped">
  <tr>
    <th align="center" class="rowtip">比赛</th>
    <th align="center" class="rowtip">分组</th>
    <th align="center" class="rowtip">选手号码</th>
    <th align="center" class="rowtip">计时芯片ID</th>
  </tr>
  <tr>
    <th align="center" class="rowtip">
        <select name="RaceId" id="RaceId" class="span2" size="1" onchange="getThirdByStage()">
        {tpl:loop $RaceList $R $RInfo}
        <option value="{tpl:$RInfo.RaceId/}" {tpl:if($RInfo.RaceId==$ApplyInfo.RaceId)}selected="selected"{/tpl:if}>{tpl:$RInfo.RaceName/}</option>
        {/tpl:loop}

    </th>
    <th align="center" class="rowtip">
        <select name="RaceGroupId" id="RaceGroupId" class="span2" size="1">
            {tpl:loop $RaceGroupList $G $GInfo}
            <option value="{tpl:$G/}" {tpl:if($G==$ApplyInfo.RaceGroupId)}selected="selected"{/tpl:if}>{tpl:$GInfo.RaceGroupName/}</option>
            {/tpl:loop}
    </th>
    <th align="center" class="rowtip td-edit"><span class="text">{tpl:$ApplyInfo.BIB/}</span><input type="text" class="span1 hidden1" name="BIB" id="BIB" value="{tpl:$ApplyInfo.BIB/}" /> <a href="#" class="btn-edit">编辑</a></th>
    <th align="center" class="rowtip"><input type="text" class="span2 chip-txt" name="ChipId" id="ChipId" value="{tpl:$ApplyInfo.ChipId/}" /></th>
  </tr>
        <tr class="noborder"><td colspan = 7><button type="submit" id="race_transfer_submit">提交</button></td>
  </tr>
</table>
</form>
<script type="text/javascript">
  $('#race_transfer_submit').click(function(){
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
            RaceUserId=$("#RaceUserId");
            divBox.confirmBox({content:message,ok:function(){windowParent.getRightHtml('{tpl:$this.sign/}'+ '&ac=race.stage.user.check.in.bib&RaceStageId=' + RaceStageId.val() + '&RaceUserId=' + RaceUserId.val());}});
        }
      }
    };
    $('#race_transfer_form').ajaxForm(options);
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
