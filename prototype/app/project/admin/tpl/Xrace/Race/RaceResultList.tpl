{tpl:tpl contentHeader/}
<script type="text/javascript">
  function RaceResultList(rid,uid,rname){
    RaceResultListBox = divBox.showBox('{tpl:$this.sign/}&ac=race.result.list&RaceId=' + rid + '&RaceUserId=' + uid, {title:rname+'成绩单',width:800,height:750});
  }
</script>
<form action="{tpl:$this.sign/}&ac=race.update" name="form" id="form" method="post">
<input type="hidden" name="RaceStageId" id="RaceStageId" value="{tpl:$RaceStageInfo.RaceStageId/}" />
  <input type="hidden" name="RaceGroupId" id="RaceGroupId" value="{tpl:$RaceGroupId/}" />
  <fieldset><legend>{tpl:$RaceInfo.RaceName/} 成绩单{tpl:if(isset($UserInfo.RaceUserId))} - {tpl:$UserInfo.Name/}{/tpl:if}</legend>
   {tpl:if(count($RaceGroupList))}
    <fieldset><legend>
        {tpl:loop $RaceGroupList $GInfo}
        {tpl:$GInfo.DownloadUrl/}/
        {/tpl:loop}
      </legend>
    {/tpl:if}

      <table width="99%" align="center" class="table table-bordered table-striped">
  <tr>
    <th align="center" class="rowtip" colspan="{tpl:$RaceResultList.UserRaceInfo.Total func="count(@@)+2"/}">最后更新时间{tpl:$RaceResultList.UserRaceInfo.LastUpdateTime func="date('Y-m-d H:i:s.u',@@)"/}</th>
  </tr>
        <tr>
          <th align="center" class="rowtip" colspan="{tpl:$RaceResultList.UserRaceInfo.Total func="count(@@)+2"/}">执行时间{tpl:$RaceResultList.UserRaceInfo.ProcessingTime/}</th>
        </tr>
  <tr>
    <th align="center" class="rowtip" colspan="2">计时点</th>
    <th align="center" class="rowtip" colspan="{tpl:$RaceResultList.UserRaceInfo.Total func="count(@@)"/}">选手名次</th>
  </tr>

  {tpl:loop $RaceResultList.UserRaceInfo.Sports $Sid $SInfo}
  <tr><th align="center" class="rowtip" rowspan="{tpl:$SInfo.TimingPointList func="count(@@)"/}">{tpl:$SInfo.SportsTypeInfo.SportsTypeName/}</th>
    {tpl:loop $SInfo.TimingPointList $id $Tid}
    {tpl:loop $RaceResultList.UserRaceInfo.Point $Pid $PointInfo}
    {tpl:if($Tid==$Pid)}
    <th align="center" class="rowtip">{tpl:$PointInfo.TName/}<br>{tpl:$PointInfo.ChipId/}<p>{tpl:$PointInfo.CurrentDistance/}米<p>{tpl:if($PointInfo.ToPrevious>=0)}{tpl:$PointInfo.TotalDistance/}{tpl:else}不计时{/tpl:if}<p>{tpl:$PointInfo.UserList func="count(@@)"/}人通过</th>
    {tpl:if(count($PointInfo.UserList)>=1)}
    {tpl:loop $PointInfo.UserList $id $RaceUserInfo}
    {tpl:if((isset($UserInfo.RaceUserId) && ($UserInfo.RaceUserId==$RaceUserInfo.RaceUserId)) || !isset($UserInfo.RaceUserId))}
    <th align="center" class="rowtip"><a href="javascript:;" onclick="RaceResultList('{tpl:$RaceInfo.RaceId/}','{tpl:$RaceUserInfo.RaceUserId/}','{tpl:$RaceInfo.RaceName/}')">{tpl:$RaceUserInfo.Name/}</a><p>{tpl:$RaceUserInfo.TeamName/}<p>{tpl:$RaceUserInfo.inTime func="Base_Common::parthTime(@@)"/}<p>总时间：{tpl:$RaceUserInfo.TotalTime func="Base_Common::parthTimeLag(@@)"/}<p>净时间：{tpl:$RaceUserInfo.TotalNetTime func="Base_Common::parthTimeLag(@@)"/}<p>分段时间：{tpl:$RaceUserInfo.PointTime func="Base_Common::parthTimeLag(@@)"/}<p>{tpl:$RaceUserInfo.GroupRank/}/{tpl:$RaceUserInfo.Rank/}{tpl:if($RaceUserInfo.TimeLag>0)}/+{tpl:$RaceUserInfo.TimeLag func="Base_Common::parthTimeLag(@@)"/}{/tpl:if}{tpl:if($RaceUserInfo.NetTimeLag>0)}/+{tpl:$RaceUserInfo.NetTimeLag func="Base_Common::parthTimeLag(@@)"/}{/tpl:if}
      <p>{tpl:$RaceUserInfo.PointSpeed/}
        {tpl:if(isset($RaceUserInfo))}
        {tpl:loop $RaceUserInfo.Credit  $CreditInfo}
        <p>{tpl:$CreditInfo.CreditName/} : {tpl:$CreditInfo.Credit/}
        {/tpl:loop}
        {/tpl:if}
    </th>
  {/tpl:if}
  {/tpl:loop}
    {tpl:else}
    <th align="center" class="rowtip">尚未通过</th>
  {/tpl:if}
    {/tpl:if}

    {/tpl:loop}
  </tr>
{/tpl:loop}
  {/tpl:loop}
  <tr><th  align="center" class="rowtip" colspan="{tpl:$RaceResultList.UserRaceInfo.Total func="count(@@)+2"/}">个人排名</th></tr>
  <tr><th  align="center" class="rowtip">名次</th>
    <th  align="center" class="rowtip" colspan="2">姓名</th>
    <th  align="center" class="rowtip" colspan="2">总时间</th>
    <th  align="center" class="rowtip" colspan="2">总净时间</th>
    <th  align="center" class="rowtip" colspan="2">BIB</th>
    <th  align="center" class="rowtip" colspan="2">队伍</th>
    <th  align="center" class="rowtip" colspan="2">当前位置</th>
    <th  align="center" class="rowtip" colspan="2">积分获取</th>
    <th  align="center" class="rowtip" colspan="2">总积分</th>

  </tr>
  {tpl:loop $RaceResultList.UserRaceInfo.Total $Tid $TInfo}
  <tr>
    <th  align="center" class="rowtip">{tpl:$TInfo.GroupRank/}</th>
    <th  align="center" class="rowtip" colspan="2">{tpl:$TInfo.Name/}{tpl:if($TInfo.RaceStatus>0)}  {tpl:if($TInfo.RaceStatus==1)}(DNS){tpl:else}(DNF){/tpl:if}{/tpl:if}</th>
    <th  align="center" class="rowtip" colspan="2">{tpl:$TInfo.TotalTime func="Base_Common::parthTimeLag(@@)"/}</th>
    <th  align="center" class="rowtip" colspan="2">{tpl:$TInfo.TotalNetTime func="Base_Common::parthTimeLag(@@)"/}</th>
    <th  align="center" class="rowtip" colspan="2">{tpl:$TInfo.BIB/}</th>
    <th  align="center" class="rowtip" colspan="2">{tpl:$TInfo.TeamName/}</th>
    <th  align="center" class="rowtip" colspan="2">{tpl:$TInfo.CurrentPositionName/}</th>
    <th  align="center" class="rowtip" colspan="2">
      {tpl:if(isset($TInfo.Credit))}
      {tpl:loop $TInfo.Credit  $CreditInfo}
      <p>{tpl:$CreditInfo.CreditName/} : {tpl:$CreditInfo.Credit/}
        {/tpl:loop}
        {/tpl:if}
    </th>
    <th  align="center" class="rowtip" colspan="2">{tpl:$TInfo.TotalCredit/}</th>
  </tr>
  {/tpl:loop}
        {tpl:if($RaceInfo.comment.TeamResultRankType == "Top")}
        <tr><th  align="center" class="rowtip" colspan="6">团队排名（每队第 {tpl:$RaceInfo.comment.TeamResultRank/} 人）</th></tr>
        <tr><th  align="center" class="rowtip">名次</th>
          <th  align="center" class="rowtip" colspan="2">团队</th>
          <th  align="center" class="rowtip" >总时间</th>
          <th  align="center" class="rowtip" >总净时间</th>
          <th  align="center" class="rowtip" >总时间差</th>
          <th  align="center" class="rowtip" >总净时间差</th>
          <th  align="center" class="rowtip" >总积分呢</th>
          <th  align="center" class="rowtip" >总积分差</th>
        </tr>
        {tpl:else}
        <tr><th  align="center" class="rowtip" colspan="6">团队排名（每队前 {tpl:$RaceInfo.comment.TeamResultRank/} 人）</th></tr>
        <tr><th  align="center" class="rowtip">名次</th>
          <th  align="center" class="rowtip" colspan="2">团队</th>
          <th  align="center" class="rowtip" >总时间</th>
          <th  align="center" class="rowtip" >总净时间</th>
          <th  align="center" class="rowtip" >总时间差</th>
          <th  align="center" class="rowtip" >总净时间差</th>
          <th  align="center" class="rowtip" >选手</th>
          <th  align="center" class="rowtip" >总时间</th>
          <th  align="center" class="rowtip" >总净时间</th>
        </tr>
        {/tpl:if}
  {tpl:loop $RaceResultList.UserRaceInfo.Team $Gid $GroupInfo}
        {tpl:if($RaceGroupId == $Gid)}
        {tpl:if($RaceInfo.comment.TeamResultRankType == "Top")}

        {tpl:loop $GroupInfo $Tid $TeamInfo}
        <tr>
    <th  align="center" class="rowtip" >{tpl:$Tid func="@@+1"/}</th>
    <th  align="center" class="rowtip" colspan="2">{tpl:$TeamInfo.TeamName/}({tpl:$TeamInfo.Name/})</th>
    <th  align="center" class="rowtip" >{tpl:$TeamInfo.TotalTime func="Base_Common::parthTimeLag(@@)"/}</th>
    <th  align="center" class="rowtip" >{tpl:$TeamInfo.TotalNetTime func="Base_Common::parthTimeLag(@@)"/}</th>
    <th  align="center" class="rowtip" >{tpl:$TeamInfo.TimeLag func="Base_Common::parthTimeLag(@@)"/}</th>
    <th  align="center" class="rowtip" >{tpl:$TeamInfo.NetTimeLag func="Base_Common::parthTimeLag(@@)"/}</th>
          <th  align="center" class="rowtip" >{tpl:$TeamInfo.TotalCredit func="intval(@@)"/}</th>
          <th  align="center" class="rowtip" >{tpl:$TeamInfo.CreditLag func="intval(@@)"/}</th>
        </tr>
      {/tpl:loop}

          {tpl:else}

        {tpl:loop $GroupInfo $Tid $TeamInfo}
        <tr>
          <th  align="center" class="rowtip" rowspan = {tpl:$TeamInfo.UserList func="count(@@)"/}>{tpl:$Tid func="@@+1"/}</th>
          <th  align="center" class="rowtip" colspan="2" rowspan = {tpl:$TeamInfo.UserList func="count(@@)"/}>{tpl:$TeamInfo.TeamName/}</th>
          <th  align="center" class="rowtip"  rowspan = {tpl:$TeamInfo.UserList func="count(@@)"/}>{tpl:$TeamInfo.TotalTime func="Base_Common::parthTimeLag(@@)"/}</th>
          <th  align="center" class="rowtip"  rowspan = {tpl:$TeamInfo.UserList func="count(@@)"/}>{tpl:$TeamInfo.TotalNetTime func="Base_Common::parthTimeLag(@@)"/}</th>
          <th  align="center" class="rowtip"  rowspan = {tpl:$TeamInfo.UserList func="count(@@)"/}>{tpl:$TeamInfo.TimeLag func="Base_Common::parthTimeLag(@@)"/}</th>
          <th  align="center" class="rowtip"  rowspan = {tpl:$TeamInfo.UserList func="count(@@)"/}>{tpl:$TeamInfo.NetTimeLag func="Base_Common::parthTimeLag(@@)"/}</th>
          {tpl:loop $TeamInfo.UserList $UserId $UserInfo}
          <th  align="center" class="rowtip" >{tpl:$UserInfo.Name/}</th>
          <th  align="center" class="rowtip" >{tpl:$UserInfo.TotalTime func="Base_Common::parthTimeLag(@@)"/}</th>
          <th  align="center" class="rowtip" >{tpl:$UserInfo.TotalNetTime func="Base_Common::parthTimeLag(@@)"/}</th>
        </tr>
        {/tpl:loop}
      {/tpl:loop}

          {/tpl:if}
          {/tpl:if}
  {/tpl:loop}
</table>
</fieldset>
</form>
{tpl:tpl contentFooter/}
