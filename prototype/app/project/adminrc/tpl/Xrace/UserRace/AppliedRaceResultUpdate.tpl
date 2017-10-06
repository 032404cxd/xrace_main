{tpl:tpl contentHeader/}
<script type="text/javascript">
    function RaceResultUpdate(uid,uname,force)
    {
        r_id = $("#RaceId").val();
        if(force==1)
        {
            if(uid==0)
            {
                RaceResultConfirmBox= divBox.confirmBox({content:'确认强制更新判定 '+uname+' 平局？',ok:function(){location.href = '{tpl:$this.sign/}&ac=applied.race.result.update.submit&Winner='+uid+'&RaceId=' + r_id + '&Force=1';}});
            }
            else
            {
                RaceResultConfirmBox= divBox.confirmBox({content:'确认强制更新判定 '+uname+' 胜利？',ok:function(){location.href = '{tpl:$this.sign/}&ac=applied.race.result.update.submit&Winner='+uid+'&RaceId=' + r_id + '&Force=1';}});
            }
        }
        else
        {
            if(uid==0)
            {
                RaceResultConfirmBox= divBox.confirmBox({content:'确认判定 '+uname+' 平局？',ok:function(){location.href = '{tpl:$this.sign/}&ac=applied.race.result.update.submit&Winner='+uid+'&RaceId=' + r_id;}});
            }
            else
            {
                RaceResultConfirmBox= divBox.confirmBox({content:'确认判定 '+uname+' 胜利？',ok:function(){location.href = '{tpl:$this.sign/}&ac=applied.race.result.update.submit&Winner='+uid+'&RaceId=' + r_id;}});
            }
        }
    }
</script>
<fieldset><legend>更新比赛结果</legend>
</fieldset>
<form action="{tpl:$this.sign/}&ac=applied.result.update.submit" name="form" id="form" method="post">
<input type="hidden" class="span2" name="RaceId" id="RaceId"  value="{tpl:$RaceInfo.RaceId/}" />

<table width="99%" align="center" class="table table-bordered table-striped">
    <tr>
        <th align="center" class="rowtip" colspan="3">比赛详情</th>
    </tr>
      <tr>
        <th align="center" class="rowtip">比赛记录ID</th>
        <th align="center" class="rowtip" colspan="2">{tpl:$RaceInfo.RaceId/}</th>
      </tr>
        <tr>
        <th align="center" class="rowtip">场地</th>
        <th align="center" class="rowtip" colspan="2">{tpl:$RaceInfo.ArenaName/}</th>
        </tr>
    <tr>
        <th align="center" class="rowtip">时间</th>
        <th align="center" class="rowtip" colspan="2">{tpl:$RaceInfo.RaceStartTime func="date('Y-m-d H:m',@@)"/} --- {tpl:$RaceInfo.RaceEndTime func="date('H:m',@@)"/}</th>
    </tr>
    <tr>
        <th align="center" class="rowtip">单人/团队</th>
        <th align="center" class="rowtip" colspan="2">{tpl:if($RaceInfo.Individual==1)}单人{tpl:else}团队{/tpl:if}</th>
    </tr>
    <tr>
        <th align="center" class="rowtip">比赛状态</th>
        <th align="center" class="rowtip" colspan="2">{tpl:$RaceInfo.RaceStatusName/}</th>
    </tr>
    {tpl:if(isset($RaceInfo.comment.Result.UpdateTime))}
    <tr>
        <th align="center" class="rowtip">更新时间</th>
        <th align="center" class="rowtip" colspan="2">{tpl:$RaceInfo.comment.Result.UpdateTime  func="date('Y-m-d H:i:s',@@)"/} - {tpl:$managerInfo.name/}</th>
    </tr>
    {/tpl:if}
    {tpl:if($RaceInfo.Individual=="1")}
    <tr>
        <th align="center" class="rowtip" colspan="3">选手列表</th>
    </tr>
    <tr>
        <th align="center" class="rowtip">姓名</th>
        <th align="center" class="rowtip">芯片ID</th>
        <th align="center" class="rowtip">昵称</th>
    </tr>
    {tpl:loop $UserList.UserRaceList $UserInfo}
      <tr class="hover">
        <td align="center">{tpl:$UserInfo.Name/} {tpl:if($RaceInfo.RaceStatus==1)}<a  href="javascript:;" onclick="RaceResultUpdate('{tpl:$UserInfo.UserId/}','{tpl:$UserInfo.Name/}','0')">胜利</a> | <a  href="javascript:;" onclick="RaceResultUpdate('0','{tpl:$UserInfo.Name/}','0')">平局</a>{tpl:else}<a  href="javascript:;" onclick="RaceResultUpdate('{tpl:$UserInfo.UserId/}','{tpl:$UserInfo.Name/}','1')">胜利</a> | <a  href="javascript:;" onclick="RaceResultUpdate('0','{tpl:$UserInfo.Name/}','1')">平局</a>{/tpl:if} [{tpl:$UserInfo.UserRaceStatusName/}]</td>
        <td align="center">{tpl:$UserInfo.ChipId/}</td>
        <td align="center">{tpl:$UserInfo.ChipName/}</td>
      </tr>
    {/tpl:if}
    {/tpl:loop}
</table>
</form>
{tpl:tpl contentFooter/}
