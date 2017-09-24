{tpl:tpl contentHeader/}

<form action="{tpl:$this.sign/}&ac=timing.point.update" name="timing_point_modify_form" id="timing_point_modify_form" method="post">
  <input type="hidden" name="SportsTypeId" id="SportsTypeId" value="{tpl:$SportsTypeId/}" />
  <input type="hidden" name="RaceId" id="RaceId" value="{tpl:$RaceId/}" />
  <input type="hidden" name="TimingId" id="TimingId" value="{tpl:$TimingId/}" />

<table width="99%" align="center" class="table table-bordered table-striped">
  <tr>
    <th align="center" class="rowtip">计时点名称</th>
    <th align="center" class="rowtip"><input type="text" class="span2" name="TName" id="TName" value="{tpl:$TimingInfo.TName/}" /></th>
  </tr>
  <tr>
    <th align="center" class="rowtip">计时点序列号</th>
    <th align="center" class="rowtip"><input type="text" class="span2" name="ChipId" id="ChipId" value="{tpl:$TimingInfo.ChipId/}" /></th>
  </tr>
  <tr>
    <th align="center" class="rowtip">距上一点<br>(米，负数不计时)</th>
    <th align="center" class="rowtip"><input type="text" class="span2" name="ToPrevious" id="ToPrevious" value="{tpl:$TimingInfo.ToPrevious/}" /></th>
  </tr>
  <tr>
    <th align="center" class="rowtip">圈数(次)</th>
    <th align="center" class="rowtip"><input type="text" class="span2" name="Round" id="Round" value="{tpl:$TimingInfo.Round/}" /></th>
  </tr>
  <tr>
    <th align="center" class="rowtip">海拔上升(米)</th>
    <th align="center" class="rowtip"><input type="text" class="span2" name="AltAsc" id="AltAsc" value="{tpl:$TimingInfo.AltAsc/}" /></th>
  </tr>
  <tr>
    <th align="center" class="rowtip">海拔下降(米)</th>
    <th align="center" class="rowtip"><input type="text" class="span2" name="AltDec" id="AltDec" value="{tpl:$TimingInfo.AltDec/}" /></th>
  </tr>
  <tr>
    <th align="center" class="rowtip">等待时间(秒)</th>
    <th align="center" class="rowtip"><input type="text" class="span2" name="TolaranceTime" id="TolaranceTime" value="{tpl:$TimingInfo.TolaranceTime/}" /></th>
  </tr>
  <tr><th align="center" class="rowtip" colspan="2">
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
          RaceId=$("#RaceId");
          divBox.confirmBox({content:message,ok:function(){windowParent.getRightHtml('{tpl:$this.sign/}'+ '&ac=race.detail&RaceId=' + RaceId.val());}});
        }
      }
    };
    $('#timing_point_modify_form').ajaxForm(options);
  });
</script>
{tpl:tpl contentFooter/}
