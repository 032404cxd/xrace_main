{tpl:tpl contentHeader/}
<input type="hidden" name="RaceStageId" id="RaceStageId" value="{tpl:$RaceStageInfo.RaceStageId/}" />
  <input type="hidden" name="RaceGroupId" id="RaceGroupId" value="{tpl:$RaceGroupId/}" />
  <fieldset><legend>{tpl:$RaceStageInfo.RaceStageName/}{tpl:if(isset($RaceGroupInfo.RaceGroupName))}-{tpl:$RaceGroupInfo.RaceGroupName/}{/tpl:if} 比赛列表 <a href="{tpl:$this.sign/}">返回</a></legend>
<table width="99%" align="center" class="table table-bordered table-striped">
  <tr>
    <th align="center" class="rowtip">总排名</th>
    <th align="center" class="rowtip">选手</th>
    <th align="center" class="rowtip">总时间</th>
    <th align="center" class="rowtip" colspan="4">详细</th>
  </tr>
  {tpl:loop $RaceUserList.RaceUserList.UserList $Rid $UserInfo}
  <tr>
    <th align="center" class="rowtip" rowspan="{tpl:$UserInfo.RaceDetail func="(count(@@))"/}">{tpl:$Rid func="1+(@@)"/}</th>
    <th align="center" class="rowtip" rowspan="{tpl:$UserInfo.RaceDetail func="(count(@@))"/}"width="10%">{tpl:$UserInfo.RaceUserInfo.Name/}</th>
    <th align="center" class="rowtip" rowspan="{tpl:$UserInfo.RaceDetail func="(count(@@))"/}">{tpl:$UserInfo.Total.TotalTime func="Base_Common::parthTimeLag(@@)"/}</th>
          {tpl:loop $UserInfo.RaceDetail $id $RaceDetail}
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
              {tpl:if($RaceDetail.Finished == 1)}
                {tpl:if($RaceDetail.RankingType == "gunshot")}
                总时间：{tpl:$RaceDetail.TotalTime func="Base_Common::parthTimeLag(@@)"/}
                {tpl:else}
                总净时间：{tpl:$RaceDetail.TotalNetTime func="Base_Common::parthTimeLag(@@)"/}
                {/tpl:if}
              {tpl:else}
              {tpl:$RaceDetail.RaceStatusName /}
              {/tpl:if}
        </tr>
          {/tpl:loop}
    </th>
  </tr>
  {/tpl:loop}
</table>
</fieldset>
{tpl:tpl contentFooter/}
