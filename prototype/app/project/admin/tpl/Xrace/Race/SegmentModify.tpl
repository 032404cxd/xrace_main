{tpl:tpl contentHeader/}

<form action="{tpl:$this.sign/}&ac=race.segment.update" name="race_segment_modify_form" id="race_segment_modify_form" method="post">
  <input type="hidden" name="RaceId" id="RaceId" value="{tpl:$SegmentInfo.RaceId/}" />
  <input type="hidden" name="SegmentId" id="SegmentId" value="{tpl:$SegmentInfo.SegmentId/}" />
<table width="99%" align="center" class="table table-bordered table-striped">
  <tr>
    <th align="center" class="rowtip">起始编号</th>
    <th align="center" class="rowtip"><input type="text" class="span2" name="StartId" id="StartId" value="{tpl:$SegmentInfo.StartId/}" /></th>
    </tr>
  <tr>
    <th align="center" class="rowtip">结束编号</th>
    <th align="center" class="rowtip"><input type="text" class="span2" name="EndId" id="EndId" value="{tpl:$SegmentInfo.EndId/}" /></th>
  </tr>
  <tr>
    <th align="center" class="rowtip">赛段名称</th>
    <th align="center" class="rowtip"><input type="text" class="span2" name="SegmentName" id="SegmentName" value="{tpl:$SegmentInfo.SegmentName/}" /></th>
  </tr>
  <tr class="hover"><th align="center" class="rowtip">成绩计算方式</th><th align="center" class="rowtip">
      <select name="ResultType" size="1" class="span2">
          {tpl:loop $RaceTimingResultTypeList $RaceTimingResultType $RaceTimingResultTypeName}
        <option value="{tpl:$RaceTimingResultType/}"  {tpl:if($RaceTimingResultType==$SegmentInfo.ResultType)}selected="selected"{/tpl:if}>{tpl:$RaceTimingResultTypeName/}</option>
          {/tpl:loop}
      </select>
    </th></tr>
  <tr class="hover"><th align="center" class="rowtip">是否要求完赛</th><th align="center" class="rowtip">
      <select name="NeedFinish" size="1" class="span2">
        <option value="1" {tpl:if(1==$SegmentInfo.comment.NeedFinish)}selected="selected"{/tpl:if}>是</option>
        <option value="0" {tpl:if(0==$SegmentInfo.comment.NeedFinish)}selected="selected"{/tpl:if}>否</option>
      </select>
    </th></tr>

  <tr><th align="center" class="rowtip" colspan="2">
    <button type="submit" id="race_segment_modify">提交</button>
    </th></tr>
  <tr>
</table>
</form>
<script type="text/javascript">
    $('#race_segment_modify').click(function(){
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
          var message = '赛段修改成功';
          RaceId=$("#RaceId");
          divBox.confirmBox({content:message,ok:function(){windowParent.getRightHtml('{tpl:$this.sign/}'+ '&ac=race.detail&RaceId=' + RaceId.val());}});
        }
      }
    };
    $('#race_segment_modify_form').ajaxForm(options);
  });
</script>
{tpl:tpl contentFooter/}
