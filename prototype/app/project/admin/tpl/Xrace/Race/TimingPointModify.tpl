{tpl:tpl contentHeader/}

<form action="{tpl:$this.sign/}&ac=timing.point.update" name="timing_point_modify_form" id="timing_point_modify_form" method="post">
<input type="hidden" name="RaceStageId" id="RaceStageId" value="{tpl:$oRaceStage.RaceStageId/}" />
  <input type="hidden" name="RaceGroupId" id="RaceGroupId" value="{tpl:$oRaceGroup.RaceGroupId/}" />
  <input type="hidden" name="SportsTypeId" id="SportsTypeId" value="{tpl:$SportsTypeId/}" />
  <input type="hidden" name="RaceId" id="RaceId" value="{tpl:$RaceId/}" />
  <input type="hidden" name="TimingId" id="TimingId" value="{tpl:$TimingId/}" />

<table width="99%" align="center" class="table table-bordered table-striped">
  <tr>
    <th align="center" class="rowtip">
      计时点名称：<input type="text" name="TName" id="TName" value="{tpl:$TimingInfo.TName/}" />
    </th>
  </tr>
  <tr>
    <th align="center" class="rowtip">
      计时芯片序列号：<input type="text" name="ChipId" id="ChipId" value="{tpl:$TimingInfo.ChipId/}" />
    </th>
  </tr>
  <tr>
    <th align="center" class="rowtip">
      距下一计时点：<input type="text" name="ToNext" id="ToNext" value="{tpl:$TimingInfo.ToNext/}" />米
    </th>
  </tr>
  <tr>
    <th align="center" class="rowtip">
      圈数：<input type="text" name="Round" id="Round" value="{tpl:$TimingInfo.Round/}" />次
    </th>
  </tr>
  <tr>
    <th align="center" class="rowtip">
      海拔上升：<input type="text" name="AltAsc" id="AltAsc" value="{tpl:$TimingInfo.AltAsc/}" />米
    </th>
  </tr>
  <tr>
    <th align="center" class="rowtip">
      海拔下降：<input type="text" name="AltDec" id="AltDec" value="{tpl:$TimingInfo.AltDec/}" />米
    </th>
  </tr>
  <tr><th align="center" class="rowtip">
    <button type="submit" id="timing_point_modify">提交</button>
    </th></tr>
  <tr>
</table>
</form>
<script type="text/javascript">
  $('#timing_point_modify').click(function(){
    var options = {
      dataType:'json',
      beforeSubmit:function(formData, jqForm, options) {
      },
      success:function(jsonResponse) {
        if (jsonResponse.errno) {
          var errors = [];
          errors[1] = '当前组别尚未配置';
          errors[2] = '分组数据错误';
          errors[3] = '请选择一个有效的运动类型，请修正后再次提交';
          errors[9] = '入库失败，请修正后再次提交';
          divBox.alertBox(errors[jsonResponse.errno],function(){});
        } else {
          var message = '计时点修改成功';
          RaceStageId=$("#RaceStageId");
          RaceGroupId=$("#RaceGroupId");
          RaceId=$("#RaceId");
          divBox.confirmBox({content:message,ok:function(){windowParent.getRightHtml('{tpl:$this.sign/}'+ '&ac=race.detail&RaceStageId=' + RaceStageId.val() + '&RaceGroupId=' + RaceGroupId.val() + '&RaceId=' + RaceId.val());}});
        }
      }
    };
    $('#timing_point_modify_form').ajaxForm(options);
  });
</script>
{tpl:tpl contentFooter/}
