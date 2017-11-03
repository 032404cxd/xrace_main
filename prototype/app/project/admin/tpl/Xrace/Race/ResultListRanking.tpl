{tpl:tpl contentHeader/}
<input type="hidden" name="RaceStageId" id="RaceStageId" value="{tpl:$RaceStageInfo.RaceStageId/}" />
  <input type="hidden" name="RaceGroupId" id="RaceGroupId" value="{tpl:$RaceGroupId/}" />
  <fieldset><legend>{tpl:$RaceStageInfo.RaceStageName/}{tpl:if(isset($RaceGroupInfo.RaceGroupName))}-{tpl:$RaceGroupInfo.RaceGroupName/}{/tpl:if} 比赛列表 <a href="{tpl:$this.sign/}">返回</a></legend>
<table width="99%" align="center" class="table table-bordered table-striped">
  <tr>
    <th align="center" class="rowtip">选手</th>
    <th align="center" class="rowtip">总时间</th>
    <th align="center" class="rowtip">总积分</th>
    <th align="center" class="rowtip">总排名</th>
    <th align="center" class="rowtip">详细</th>
  </tr>
  {tpl:loop $RaceUserList.RaceUserList.UserList $Rid $UserInfo}
  <tr>
    <th align="center" class="rowtip">{tpl:$Rid func="1+(@@)"/}</th>
    <th align="center" class="rowtip" width="10%">{tpl:$UserInfo.Name/}</th>
    <th align="center" class="rowtip">{tpl:$UserInfo.Total.TotalTime func="Base_Common::parthTimeLag(@@)"/}</th>
    <th align="center" class="rowtip">{tpl:$UserInfo.Total.TotalCredit/}</th>
    <th align="center" class="rowtip">
      <table width="99%" align="center" class="table table-bordered table-striped">
          {tpl:loop $UserInfo.RaceDetail $id $RaceDetail}
        <tr>
          <th align="center" class="rowtip">
              {tpl:loop $RaceList $RaceId $RaceInfo}
              {tpl:if($RaceId == $RaceDetail.RaceId)}{tpl:$RaceInfo.RaceName/}  {/tpl:if}
              {/tpl:loop}
          </th>
          <th align="center" class="rowtip">
              {tpl:loop $RaceGroupList $RaceGroupId $RaceGroupInfo}
              {tpl:if($RaceGroupId == $RaceDetail.RaceGroupId)}{tpl:$RaceGroupInfo.RaceGroupName/}  {/tpl:if}
              {/tpl:loop}
          </th>


          <th align="center" class="rowtip">
              {tpl:if($RaceDetail.RankingType == "gunshot")}
                      总时间：{tpl:$RaceDetail.TotalTime func="Base_Common::parthTimeLag(@@)"/}
              {tpl:else}
                {tpl:if($RaceDetail.RankingType == "net")}
                总净时间：{tpl:$RaceDetail.TotalNetTime func="Base_Common::parthTimeLag(@@)"/}
                {tpl:else}
                  总积分：{tpl:$RaceDetail.TotalCredit func="intval(@@)"/}
                {/tpl:if}



              {/tpl:if}
          </th>
        </tr>
          {/tpl:loop}
      </table>



    </th>
  </tr>
  {/tpl:loop}
  <tr>
    <th align="center" class="rowtip" colspan="15">  <a href="javascript:;" onclick="RaceAdd('{tpl:$RaceStageInfo.RaceStageId/}','{tpl:$RaceGroupInfo.RaceGroupId/}')">点此添加比赛</a>
    </th>
  </tr>
</table>
</fieldset>
{tpl:tpl contentFooter/}
