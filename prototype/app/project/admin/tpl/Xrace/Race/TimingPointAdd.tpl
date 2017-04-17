{tpl:tpl contentHeader/}

<form action="{tpl:$this.sign/}&ac=timing.point.insert" name="timing_point_add_form" id="timing_point_add_form" method="post">
  <input type="hidden" name="RaceId" id="RaceId" value="{tpl:$RaceId/}" />
  <input type="hidden" name="SportsTypeId" id="SportsTypeId" value="{tpl:$SportsTypeId/}" />

<table width="99%" align="center" class="table table-bordered table-striped">
  <tr>
    <th align="center" class="rowtip">在</th>
    <th align="center" class="rowtip">
      <select name="After" id="After" size="1" class="span2" >
        <option value="-1" >头部</option>
        {tpl:loop $SportsTypeInfo.TimingDetailList.comment $TimingId $TimingInfo}
        <option value="{tpl:$TimingId/}" {tpl:if($TimingId==$After)}selected="selected"{/tpl:if}>{tpl:$TimingInfo.TName/}</option>
        {/tpl:loop}
      </select>之后添加</th>
  </tr>
  <tr>
    <th align="center" class="rowtip">计时点名称</th>
    <th align="center" class="rowtip"><input type="text" class="span2" name="TName" id="TName" value="" /></th>
  </tr>
  <tr>
    <th align="center" class="rowtip">计时点序列号</th>
    <th align="center" class="rowtip"><input type="text" class="span2" name="ChipId" id="ChipId" value="" /></th>
  </tr>
  <tr>
    <th align="center" class="rowtip">距上一点<br>(米，负数不计时)</th>
    <th align="center" class="rowtip"><input type="text" class="span2" name="ToPrevious" id="ToPrevious" value="" /></th>
  </tr>
  <tr>
    <th align="center" class="rowtip">圈数(次)</th>
    <th align="center" class="rowtip"><input type="text" class="span2" name="Round" id="Round" value="" /></th>
  </tr>
  <tr>
    <th align="center" class="rowtip">海拔上升(米)</th>
    <th align="center" class="rowtip"><input type="text" class="span2" name="AltAsc" id="AltAsc" value="" /></th>
  </tr>
  <tr>
    <th align="center" class="rowtip">海拔下降(米)</th>
    <th align="center" class="rowtip"><input type="text" class="span2" name="AltDec" id="AltDec" value="" /></th>
  </tr>
  <tr>
    <th align="center" class="rowtip">等待时间(秒)</th>
    <th align="center" class="rowtip"><input type="text" class="span2" name="TolaranceTime" id="TolaranceTime" value="60" /></th>
  </tr>
  <tr><th align="center" class="rowtip" colspan="2">
    <button type="submit" id="timing_point_add">提交</button>
    </th></tr>
  <tr>
</table>
</form>
<script type="text/javascript">
  $('#timing_point_add').click(function(){
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
          var message = '计时点添加成功';
          RaceId=$("#RaceId");
          divBox.confirmBox({content:message,ok:function(){windowParent.getRightHtml('{tpl:$this.sign/}'+ '&ac=race.detail&RaceId=' + RaceId.val());}});
        }
      }
    };
    $('#timing_point_add_form').ajaxForm(options);
  });
</script>
{tpl:tpl contentFooter/}
