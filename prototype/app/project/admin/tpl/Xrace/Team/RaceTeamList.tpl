{tpl:tpl contentHeader/}
<script type="text/javascript">
    $(document).ready(function(){
        $('#add_team').click(function(){
            addTeamBox = divBox.showBox('{tpl:$this.sign/}&ac=team.add', {title:'添加队伍',width:500,height:400});
        });
    });
    function TeamModify(pid){
        RaceModifyBox = divBox.showBox('{tpl:$this.sign/}&ac=team.modify&RaceTeamId=' + pid, {title:'修改队伍',width:500,height:400});
    }
    function TeamDelete(pid, pname){
        deleteAppBox = divBox.confirmBox({content:'是否删除 ' + pname + '?',ok:function(){location.href = '{tpl:$this.sign/}&ac=team.delete&RaceTeamId=' + pid}});
    }
</script>

<fieldset><legend>操作</legend>
    [ <a href="javascript:;" id="add_team">添加队伍</a> ]
</fieldset>
<form action="{tpl:$this.sign/}" name="form" id="form" method="post">
    队伍名称:<input type="text" class="span2" name="RaceTeamName" value="{tpl:$params.RaceTeamName/}" />
    所属赛事:<select name="RaceCatalogId" size="1">
        <option value="0">全部</option>
        {tpl:loop $RaceCatalogList $RaceCatalogInfo}
        <option value="{tpl:$RaceCatalogInfo.RaceCatalogId/}" {tpl:if($RaceCatalogInfo.RaceCatalogId==$params.RaceCatalogId)}selected="selected"{/tpl:if}>{tpl:$RaceCatalogInfo.RaceCatalogName/}</option>
        {/tpl:loop}
    </select>
    <input type="submit" name="Submit" value="查询" />{tpl:$export_var/}
</form>
<fieldset><legend>队伍列表</legend>
<table width="99%" align="center" class="table table-bordered table-striped">
      <tr>
        <th align="center" class="rowtip">队伍ID</th>
        <th align="center" class="rowtip">队伍名称</th>
        <th align="center" class="rowtip">所属赛事</th>
        <th align="center" class="rowtip">分组注册</th>
        <th align="center" class="rowtip">创建时间</th>
        <th align="center" class="rowtip">最后更新时间</th>
        <th align="center" class="rowtip">操作</th>
      </tr>
    {tpl:loop $RaceTeamList.RaceTeamList $RaceTeamInfo}
    <tr class="hover">
        <td align="center">{tpl:$RaceTeamInfo.RaceTeamId/}</td>
        <td align="center">{tpl:$RaceTeamInfo.RaceTeamName/}</td>
        <td align="center">{tpl:$RaceTeamInfo.RaceCatalogName/}</td>
        <td align="center">{tpl:$RaceTeamInfo.SelectedGroupList/}</td>
        <td align="center">{tpl:$RaceTeamInfo.CreateTime/}</td>
        <td align="center">{tpl:$RaceTeamInfo.LastUpdateTime/}</td>
        <td align="center"><a href="javascript:;" onclick="TeamDelete('{tpl:$RaceTeamInfo.RaceTeamId/}','{tpl:$RaceTeamInfo.RaceTeamName/}')">删除</a> |  <a href="javascript:;" onclick="TeamModify('{tpl:$RaceTeamInfo.RaceTeamId/}');">修改</a></td>
    </tr>
    {/tpl:loop}
    <tr><th colspan="10" align="center" class="rowtip">{tpl:$page_content/}</th></tr>

</table>
</fieldset>
{tpl:tpl contentFooter/}
