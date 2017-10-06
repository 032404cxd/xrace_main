{tpl:tpl contentHeader/}
<script type="text/javascript">
    function RaceResultUpdate(rid)
    {
        RaceAddBox = divBox.showBox('{tpl:$this.sign/}&ac=applied.race.result.update&RaceId=' + rid, {title:'输入比赛结果',width:600,height:600});
    }
</script>
<fieldset><legend>操作</legend>
</fieldset>
<form action="{tpl:$this.sign/}" name="form" id="form" method="post">
    比赛ID:<input type="text" class="span2" name="RaceId" value="{tpl:$params.RaceId/}" />
    场地:<select name="ArenaId" class="span2" size="1">
        <option value="0" {tpl:if($params.ArenaId==0)}selected="selected"{/tpl:if}>全部</option>
        {tpl:loop $ArenaList $ArenaId $ArenaInfo}
        <option value="{tpl:$ArenaId/}" {tpl:if($params.ArenaId==$ArenaId)}selected="selected"{/tpl:if}>{tpl:$ArenaInfo.ArenaName/}</option>
        {/tpl:loop}
    </select>
    状态:<select name="RaceStatus" class="span2" size="1">
        <option value="0" {tpl:if($params.RaceStatus==0)}selected="selected"{/tpl:if}>全部</option>
        {tpl:loop $RaceStatusList $RaceStatus $RaceStatusName}
        <option value="{tpl:$RaceStatus/}" {tpl:if($params.RaceStatus==$RaceStatus)}{/tpl:if}>{tpl:$RaceStatusName/}</option>
        {/tpl:loop}
    </select>
    <input type="submit" name="Submit" value="查询" />{tpl:$export_var/}
</form>
<fieldset><legend>约战队列</legend>
<table width="99%" align="center" class="table table-bordered table-striped">
      <tr>
        <th align="center" class="rowtip">比赛记录ID</th>
        <th align="center" class="rowtip">场地</th>
        <th align="center" class="rowtip">时间</th>
        <th align="center" class="rowtip">单人/团队</th>
        <th align="center" class="rowtip">比赛状态</th>
        <th align="center" class="rowtip">操作</th>
      </tr>
    {tpl:loop $UserAppliedRaceList.UserRaceList $RaceInfo}
      <tr class="hover">
        <td align="center">{tpl:$RaceInfo.RaceId/}</td>
        <td align="center">{tpl:$RaceInfo.ArenaName/}</td>
        <td align="center">{tpl:$RaceInfo.RaceStartTime func="date('Y-m-d H:m',@@)"/} --- {tpl:$RaceInfo.RaceEndTime func="date('H:m',@@)"/}</td>
        <td align="center">{tpl:if($RaceInfo.Individual==1)}单人{tpl:else}团队{/tpl:if}</td>
        <td align="center">{tpl:$RaceInfo.RaceStatusName/}</td>
        <td align="center"><a href="javascript:;" onclick="RaceResultUpdate('{tpl:$RaceInfo.RaceId/}')">详情</a></td>
      </tr>
    {/tpl:loop}
    <tr><th colspan="10" align="center" class="rowtip">{tpl:$page_content/}</th></tr>

</table>
</fieldset>
{tpl:tpl contentFooter/}
