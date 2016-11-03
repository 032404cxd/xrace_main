{tpl:tpl contentHeader/}

<form action="{tpl:$this.sign/}&ac=timing.point.credit.insert" name="timing_point_credit_add_form" id="timing_point_credit_add_form" method="post">
  <input type="hidden" name="RaceId" id="RaceId" value="{tpl:$RaceId/}" />
  <input type="hidden" name="SportsTypeId" id="SportsTypeId" value="{tpl:$SportsTypeId/}" />
  <input type="hidden" name="TimingId" id="TimingId" value="{tpl:$TimingId/}" />

<table width="99%" align="center" class="table table-bordered table-striped">
  <tr>
    <th align="center" class="rowtip">积分类目</th>
    <th align="center" class="rowtip">
      <select name="CreditId" id="CreditId" size="1" class="span2" >
        {tpl:loop $CreditArr $CreditId $CreditInfo}
        <option value="{tpl:$CreditId/}">{tpl:$CreditInfo.CreditName/}</option>
        {/tpl:loop}
      </select></th>
  </tr>
  <tr>
    <th align="center" class="rowtip">积分规则列表</th>
    <th align="center" class="rowtip"><textarea class="span3" name="CreditRule"  id="CreditRule" cols ="50" rows = "5"/></textarea></th>
  </tr>
  </tr>
  <tr><th align="center" class="rowtip" colspan="2">
    <button type="submit" id="timing_point_credit_add">提交</button>
    </th></tr>
  <tr>
</table>
</form>
<script type="text/javascript">
  $('#timing_point_credit_add').click(function(){
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
          var message = '积分更新成功';
          RaceId=$("#RaceId");
          divBox.confirmBox({content:message,ok:function(){windowParent.getRightHtml('{tpl:$this.sign/}'+ '&ac=race.detail&RaceId=' + RaceId.val());}});
        }
      }
    };
    $('#timing_point_credit_add_form').ajaxForm(options);
  });
</script>
{tpl:tpl contentFooter/}
