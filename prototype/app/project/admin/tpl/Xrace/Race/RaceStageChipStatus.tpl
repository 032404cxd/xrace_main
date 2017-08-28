{tpl:tpl contentHeader/}
<script type="text/javascript">
    function ChipStatus(rid)
    {
        ChipId=$("#ChipId");
        RaceUserInfoBox = divBox.showBox('{tpl:$this.sign/}&ac=chip.status&ChipId=' + ChipId.val() + '&RaceStageId=' + rid, {title:'芯片使用状态',width:800,height:750});
        return false;
    }
</script>
<fieldset><legend>{tpl:$ChipId/} 芯片使用状态 </legend>
  <form id="chip_status_form" name="chip_status_form" action="{tpl:$this.sign/}&ac=chip.return" method="post">
    <input type="hidden" name="RaceStageId" id="RaceStageId" value="{tpl:$RaceStageId/}" />



  <table width="99%" align="center" class="table table-bordered table-striped">
    <th align="center" class="rowtip">选手</th>
    <th align="center" class="rowtip">比赛</th>
    <th align="center" class="rowtip">分组</th>
    <th align="center" class="rowtip">状态</th>
  </tr>
      {tpl:loop $UserRaceApplyList $key $ApplyInfo}
      <tr>
        <th align="center" class="rowtip" >{tpl:$ApplyInfo.Name/}</th>
        <th align="center" class="rowtip">{tpl:$ApplyInfo.RaceName/}</th>
        <th align="center" class="rowtip">{tpl:$ApplyInfo.RaceGroupName/}</th>
          <th align="center" class="rowtip">
              {tpl:if($ApplyInfo.ChipReturned==1)}已归还{tpl:else}
              <input type="checkbox"  name="ApplyList[{tpl:$ApplyInfo.ApplyId/}][Selected]"value="1"/>
              {/tpl:if}
          </th>
      </tr>
    {/tpl:loop}
      <tr class="hover">
          <th align="center" colspan="4"><button type="submit" id="chip_return_submit">提交</button></th>
      </tr>
</table>
  </form>

    <script type="text/javascript">
        $('#chip_return_submit').click(function(){
            var options = {
                dataType:'json',
                beforeSubmit:function(formData, jqForm, options) {},
                success:function(jsonResponse) {
                    if (jsonResponse.errno) {
                        var errors = [];
                        errors[1] = '归还失败';
                        divBox.alertBox(errors[jsonResponse.errno],function(){});
                    } else {
                        var message = jsonResponse.Success+'人次芯片归还成功';
                        RaceStageId=$("#RaceStageId");
                        divBox.confirmBox({content:message,ok:function(){windowParent.getRightHtml('{tpl:$this.sign/}'+ '&ac=chip.return.status&RaceStageId=' + RaceStageId.val());}});
                    }
                }
            };
            $('#chip_status_form').ajaxForm(options);
        });
    </script>
{tpl:tpl contentFooter/}
