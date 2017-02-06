{tpl:tpl contentHeader/}
<script type="text/javascript">
    $(document).ready(function(){
        $('#add_team').click(function(){
            addTeamBox= divBox.showBox('{tpl:$this.sign/}&ac=team.add', {title:'添加队伍',width:600,height:400});
        });
    });
</script>

<fieldset><legend>操作</legend>
    [ <a href="javascript:;" id="add_team">添加队伍</a> ]
</fieldset>
<form action="{tpl:$this.sign/}" name="form" id="form" method="post">
    队伍名称:<input type="text" class="span2" name="TeamName" value="{tpl:$params.TeamName/}" />
    <input type="submit" name="Submit" value="查询" />{tpl:$export_var/}
</form>
<fieldset><legend>队伍列表</legend>
<table width="99%" align="center" class="table table-bordered table-striped">
      <tr>
        <th align="center" class="rowtip">队伍ID</th>
        <th align="center" class="rowtip">队伍名称</th>
        <th align="center" class="rowtip">临时</th>
          <th align="center" class="rowtip">创建用户</th>
          <th align="center" class="rowtip">创建时间</th>
      </tr>
    {tpl:loop $TeamList.TeamList $TeamInfo}
    <tr class="hover">
        <td align="center">{tpl:$TeamInfo.TeamId/}</td>
        <td align="center">{tpl:$TeamInfo.TeamName/}</td>
        <td align="center">{tpl:if($TeamInfo.IsTemp==1)}{tpl:$TeamInfo.RaceCatalogName/}/{tpl:$TeamInfo.RaceStageName/}{tpl:else}否{/tpl:if}</td>
        <td align="center">{tpl:$TeamInfo.CreateUserName/}</td>
        <td align="center">{tpl:$TeamInfo.CreateTime/}</td>
    </tr>
    {/tpl:loop}
    <tr><th colspan="10" align="center" class="rowtip">{tpl:$page_content/}</th></tr>

</table>
</fieldset>
{tpl:tpl contentFooter/}
