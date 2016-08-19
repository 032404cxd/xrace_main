{tpl:tpl contentHeader/}
<script type="text/javascript">

</script>

<fieldset><legend>操作</legend>
</fieldset>
<form action="{tpl:$this.sign/}" name="form" id="form" method="post">
    队伍名称:<input type="text" class="span2" name="RaceTeamName" value="{tpl:$params.RaceTeamName/}" />
    <input type="submit" name="Submit" value="查询" />{tpl:$export_var/}
</form>
<fieldset><legend>队伍列表</legend>
<table width="99%" align="center" class="table table-bordered table-striped">
      <tr>
        <th align="center" class="rowtip">队伍ID</th>
        <th align="center" class="rowtip">队伍名称</th>
        <th align="center" class="rowtip">开放加入</th>
        <th align="center" class="rowtip">成员数量</th>
        <th align="center" class="rowtip">创建时间</th>
      </tr>
    {tpl:loop $RaceTeamList.RaceTeamList $RaceTeamInfo}
    <tr class="hover">
        <td align="center">{tpl:$RaceTeamInfo.team_id/}</td>
        <td align="center">{tpl:$RaceTeamInfo.name/}</td>
        <td align="center">{tpl:if($RaceTeamInfo.is_open==1)}开放{tpl:else}关闭{/tpl:if}</td>
        <td align="center">{tpl:$RaceTeamInfo.members/}</td>
        <td align="center">{tpl:$RaceTeamInfo.crt_time/}</td>
    </tr>
    {/tpl:loop}
    <tr><th colspan="10" align="center" class="rowtip">{tpl:$page_content/}</th></tr>

</table>
</fieldset>
{tpl:tpl contentFooter/}
