{tpl:tpl contentHeader/}
<script type="text/javascript">
    function SegmentAdd(rid){
        SegmentAddBox = divBox.showBox('{tpl:$this.sign/}&ac=race.segment.add&RaceId=' + rid, {title:'添加赛段',width:350,height:350});
    }
    function SegmentModify(sid,sname){
        SegmentModifyBox = divBox.showBox('{tpl:$this.sign/}&ac=race.segment.modify&SegmentId=' + sid, {title:'修改赛段-'+sname,width:350,height:350});
    }
  function SportsTypeAdd(sid,gid,rid,after){
    SportsTypeAddBox = divBox.showBox('{tpl:$this.sign/}&ac=race.sports.type.add&RaceGroupId=' + gid + '&RaceStageId=' + sid + '&RaceId=' + rid + '&After=' + after, {title:'添加运动分段',width:350,height:250});
  }
  function TimingPointAdd(rid,tid,after,sname){
    TimingPointAddBox = divBox.showBox('{tpl:$this.sign/}&ac=timing.point.add&RaceId=' + rid +  '&SportsTypeId=' + tid + '&After=' + after, {title:'添加'+sname+'计时点',width:400,height:650});
  }
  function TimingPointModify(rid,tid,pid,tname){
    TimingPointModifyBox = divBox.showBox('{tpl:$this.sign/}&ac=timing.point.modify&RaceId=' + rid +  '&SportsTypeId=' + tid + '&TimingId=' + pid, {title:'修改计时点-'+tname,width:400,height:600});
  }
  function TimingPointDelete(rid,tid,pid,tname){
    deleteTimingPointBox= divBox.confirmBox({content:'是否删除 ' + tname + '?',ok:function(){location.href = '{tpl:$this.sign/}&ac=timing.point.delete&RaceId=' + rid +  '&SportsTypeId=' + tid + '&TimingId=' + pid;}});
  }
  function SportsTypeDelete(sid,gid,rid,tid, p_name){
    deleteSportsTypeBox= divBox.confirmBox({content:'是否删除分段 ' + p_name + '?',ok:function(){location.href = '{tpl:$this.sign/}&ac=race.sports.type.delete&RaceGroupId=' + gid + '&RaceStageId=' + sid + '&SportsTypeId=' + tid + '&RaceId=' + rid;}});
  }
  function CreditAdd(rid,tid,pid){
    TimingPointCreditAddBox = divBox.showBox('{tpl:$this.sign/}&ac=timing.point.credit.add&RaceId=' + rid + '&SportsTypeId=' + tid + '&TimingId=' + pid, {title:'添加积分',width:400,height:450});
  }
  function CreditModify(rid,tid,pid,cid,c_name){
    TimingPointCreditModifyBox = divBox.showBox('{tpl:$this.sign/}&ac=timing.point.credit.modify&RaceId=' + rid + '&SportsTypeId=' + tid + '&TimingId=' + pid + '&CreditId=' + cid, {title:'修改'+c_name,width:400,height:450});
  }
  function CreditDelete(rid,tid,pid,cid,c_name){
    deleteSportsTypeBox= divBox.confirmBox({content:'是否删除 ' + c_name + '?',ok:function(){location.href = '{tpl:$this.sign/}&ac=timing.point.credit.delete&RaceId=' + rid + '&SportsTypeId=' + tid + '&TimingId=' + pid + '&CreditId=' + cid;}});

  }
  function WechatTimingQr(rid,location,title){
      WechatTimingQRBox = divBox.showBox('{tpl:$this.sign/}&ac=wechat.timing.qr&RaceId=' + rid + '&Location=' + location, {title:title,width:430,height:430});
  }
</script>

<input type="hidden" name="RaceStageId" id="RaceStageId" value="{tpl:$RaceStageInfo.RaceStageId/}" />
  <input type="hidden" name="RaceGroupId" id="RaceGroupId" value="{tpl:$RaceGroupInfo.RaceGroupId/}" />
  <fieldset><legend>{tpl:$RaceStageInfo.RaceStageName/}-{tpl:$RaceGroupInfo.RaceGroupName/}-{tpl:$RaceInfo.RaceName/}详情配置 <a href="{tpl:$this.sign/}&ac=race.list&RaceStageId={tpl:$RaceStageInfo.RaceStageId/}&RaceGroupId={tpl:$RaceGroupInfo.RaceGroupId/}">返回</a></legend>
<table width="99%" align="center" class="table table-bordered table-striped">
  {tpl:if(count($RaceInfo.comment.DetailList))}
  <tr>
    <th align="center" class="rowtip">      <a href="javascript:;" onclick="SegmentAdd('{tpl:$RaceId/}')">添加赛段</a>
    </th>
  </tr>
  <tr>
    <th colspan = 10>

    {tpl:if(count($RaceSegmentList)>0)}
    <table width="99%" align="center" class="table table-bordered table-striped">
      <tr>
        <td>名称</td>
        <td>范围</td>
        <td>计时方式</td>
        <td>是否需要完赛</td>
        <td>操作</td>
      </tr>

        {tpl:loop $RaceSegmentList $SegmentId $SegmentInfo}
      <tr>
        <td>{tpl:$SegmentInfo.SegmentName/}</td>
        <td>计时点从：{tpl:$SegmentInfo.StartId/} 到 {tpl:$SegmentInfo.EndId/}</td>
        <td>
            {tpl:loop $RaceTimingResultTypeList $RaceTimingResultType $RaceTimingResultTypeName}
            {tpl:if($RaceTimingResultType == $SegmentInfo.ResultType)}           {tpl:$RaceTimingResultTypeName/}            {/tpl:if}
            {/tpl:loop}
        </td>
        <td>
            {tpl:if(1 == $SegmentInfo.comment.NeedFinish)} 是 {tpl:else} 否 {/tpl:if}
        </td>
        <td>
          <a href="javascript:;" onclick="SegmentModify('{tpl:$SegmentId/}','{tpl:$SegmentInfo.SegmentName/}')">修改</a> | <a href="javascript:;" onclick="SegmentDelete('{tpl:$SegmentInfo.SegmentName/}','{tpl:$SegmentId/}')">删除</a>
        </td>
      </tr>
        {/tpl:loop}
    </table>
      {/tpl:if}
    </th>
  </tr>

  <tr>
    <th align="center" class="rowtip"><a href="javascript:;" onclick="SportsTypeAdd('{tpl:$RaceStageId/}','{tpl:$RaceGroupId/}','{tpl:$RaceId/}','-1')">在头部添加</a>
    </th>
  </tr>
  <tr>
  {tpl:loop $RaceInfo.comment.DetailList $SportsTypeId $SportsTypeInfo}
  <tr>
  <th align="center" class="rowtip">{tpl:$SportsTypeInfo.SportsTypeName/}
    <a href="javascript:;" onclick="SportsTypeAdd('{tpl:$RaceStageId/}','{tpl:$RaceGroupId/}','{tpl:$RaceId/}','{tpl:$SportsTypeId/}')">添加</a>
     |
    <a href="javascript:;" onclick="SportsTypeDelete('{tpl:$RaceStageInfo.RaceStageId/}','{tpl:$RaceGroupInfo.RaceGroupId/}','{tpl:$RaceId/}','{tpl:$SportsTypeId/}','{tpl:$SportsTypeInfo.SportsTypeName/}')">删除</a>
  </th>
    {tpl:if(count($SportsTypeInfo.TimingDetailList.comment))}
  <tr>
    <th align="center" class="rowtip">
      <table width="99%" align="center"  class="table table-bordered table-striped">
        <tr>
          <td>总距离：{tpl:$SportsTypeInfo.Total.Distance/}米</td>
          <td>计时点：{tpl:$SportsTypeInfo.Total.ChipCount/}个</td>
        </tr>
      </table>
    </th>
  </tr>

  {/tpl:if}
  </tr>
  {tpl:if(count($SportsTypeInfo.TimingDetailList.comment))}
  <tr><th colspan = 10>
      <table width="99%" align="center"  class="table table-bordered table-striped">
        {tpl:loop $SportsTypeInfo.TimingDetailList.comment $Tid $TimingInfo}
        <tr>
          <td>┠&nbsp;&nbsp;{tpl:$TimingInfo.key/}</td><td>{tpl:$TimingInfo.TName/}</td><td>计时点序列号：{tpl:$TimingInfo.ChipId/}</td>{tpl:if($TimingInfo.ToPrevious>=0)}<td>距离上一点：{tpl:$TimingInfo.ToPrevious/}米</td>{tpl:else}<td>不计时</td>{/tpl:if}<td>圈数: {tpl:$TimingInfo.Round/} 次</td><!--<td>海拔上升:{tpl:$TimingInfo.BaiduMapX/}米</td><td>海拔下降:{tpl:$TimingInfo.BaiduMapY/}米</td>--><td>等待时间:{tpl:$TimingInfo.TolaranceTime/}秒</td>
          <td><a href="javascript:;" onclick="TimingPointModify('{tpl:$RaceId/}','{tpl:$SportsTypeId/}','{tpl:$Tid/}','{tpl:$TimingInfo.TName/}')">修改</a> |
            <a href="javascript:;" onclick="TimingPointDelete('{tpl:$RaceId/}','{tpl:$SportsTypeId/}','{tpl:$Tid/}','{tpl:$TimingInfo.TName/}')">删除</a> |
            <a href="javascript:;" onclick="TimingPointAdd('{tpl:$RaceId/}','{tpl:$SportsTypeId/}','{tpl:$Tid/}','{tpl:$SportsTypeInfo.SportsTypeName/}')">添加</a> |
            <a href="javascript:;" onclick="CreditAdd('{tpl:$RaceId/}','{tpl:$SportsTypeId/}','{tpl:$Tid/}')">积分</a> | <a href="javascript:;" onclick="WechatTimingQr('{tpl:$RaceId/}','{tpl:$TimingInfo.ChipId/}','{tpl:$TimingInfo.TName/}')">二维码</a>

          </td>
        </tr>
        {tpl:if(count($TimingInfo.CreditList))}
        <tr><th colspan = 10>
        <table width="99%" align="center"  class="table table-bordered table-striped">
        {tpl:loop $TimingInfo.CreditList $CId $CInfo}
        <tr>
          <td colspan="10">{tpl:$CInfo.CreditName/}/{tpl:if($CInfo.CreditRoundList!="0")}第{tpl:$CInfo.CreditRoundList/}圈{tpl:else}所有圈{/tpl:if}/{tpl:$CInfo.CreditRule/}             <a href="javascript:;" onclick="CreditModify('{tpl:$RaceId/}','{tpl:$SportsTypeId/}','{tpl:$Tid/}','{tpl:$CId/}','{tpl:$CInfo.CreditName/}')">修改</a> | <a href="javascript:;" onclick="CreditDelete('{tpl:$RaceId/}','{tpl:$SportsTypeId/}','{tpl:$Tid/}','{tpl:$CId/}','{tpl:$CInfo.CreditName/}')">删除</a>
          </td>
        </tr>
        {/tpl:loop}
        </table>
          </th>
        </tr>
        {/tpl:if}
        {/tpl:loop}
        </th>
      </table>
  </tr>
    {tpl:else}
  <tr>
    <th align="center" class="rowtip">┠&nbsp;&nbsp;尚未配置任何计时点信息<a href="javascript:;" onclick="TimingPointAdd('{tpl:$RaceId/}','{tpl:$SportsTypeId/}','-1','{tpl:$SportsTypeInfo.SportsTypeName/}')">在头部添加计时点</a></th>
  </tr>
  {/tpl:if}
    {/tpl:loop}
  {tpl:else}
  <tr>
    <th align="center" class="rowtip" colspan="4">尚未配置任何分段数据
      <a href="javascript:;" onclick="SportsTypeAdd('{tpl:$RaceStageInfo.RaceStageId/}','{tpl:$RaceGroupInfo.RaceGroupId/}','{tpl:$RaceId/}','-1')">在头部添加</a>
    </th>
  </tr>
  {/tpl:if}
</table>
</fieldset>
</form>
{tpl:tpl contentFooter/}
