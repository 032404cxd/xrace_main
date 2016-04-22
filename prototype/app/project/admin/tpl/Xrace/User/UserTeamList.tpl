{tpl:tpl contentHeader/}
<script type="text/javascript">
    function UserTeamAdd(uid){
        userTeamAddBox = divBox.showBox('{tpl:$this.sign/}&ac=user.team.add&UserId=' + uid, {title:"加入队伍",width:400,height:300});
    }
</script>
    <input type="hidden" name="UserId" id="UserId" value="{tpl:$UserInfo.UserId/}" />
    <table width="99%" align="center" class="table table-bordered table-striped">
        {tpl:if(count($UserTeamList))}
        <tr>
            <th align="center" class="rowtip">所属队伍</th>
            <th align="center" class="rowtip">所属赛事</th>
            <th align="center" class="rowtip">所属组别</th>
            <th align="center" class="rowtip">入队时间</th>
            <th align="center" class="rowtip">操作</th>
        </tr>
        {tpl:loop $UserTeamList $Aid $UserTeamInfo}
        <tr>
            <th align="center" class="rowtip">{tpl:$UserTeamInfo.RaceTeamName/}</th>
            <th align="center" class="rowtip">{tpl:$UserTeamInfo.RaceCatalogName/}</th>
            <th align="center" class="rowtip">{tpl:$UserTeamInfo.RaceGroupName/}</th>
            <th align="center" class="rowtip">{tpl:$UserTeamInfo.InTime/}</th>
            <th align="center" class="rowtip">{tpl:$UserTeamInfo.UserTeamDelete/}</th>
        </tr>
        {/tpl:loop}
        <tr>
            <th align="center" class="rowtip" colspan="5"><a href="javascript:;" onclick="UserTeamAdd('{tpl:$UserInfo.user_id/}')">点此参加</a></th>
        </tr>
        {tpl:else}
        <tr>
            <th align="center" class="rowtip">尚未参加任何队伍<a href="javascript:;" onclick="UserTeamAdd('{tpl:$UserInfo.user_id/}')">点此参加</a></th>
        </tr>
        {/tpl:if}
    </table>
{tpl:tpl contentFooter/}
