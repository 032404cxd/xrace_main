{tpl:tpl contentHeader/}
<script type="text/javascript">
    function getStageByCatalog()
    {
        catalog=$("#RaceCatalogId");
        $.ajax
        ({
            type: "GET",
            url: "?ctl=xrace/race.stage&ac=get.stage.by.catalog&RaceCatalogId="+catalog.val(),
            success: function(msg)
            {
                $("#RaceStageId").html(msg);
                $("#RaceId").html("");
                $("#RaceGroupId").html("");
            }});
    }
    function getSecondByStage()
    {
        stage=$("#RaceStageId");
        $.ajax
        ({
            type: "GET",
            dataType:'json',
            url: "?ctl=xrace/race.stage&ac=get.second.level.by.stage&RaceStageId="+stage.val(),
            success: function(jsonResponse)
            {
                if(jsonResponse.S == "group")
                    {
                        $("#RaceGroupId").html(jsonResponse.text);
                        $("#RaceId").html("");
                    }
                else if(jsonResponse.S == "race")
                    {
                        $("#RaceId").html(jsonResponse.text);
                        $("#RaceGroupId").html("");
                    }

            }});
    }
    function getThirdByStage(type)
    {
        stage=$("#RaceStageId");
        group=$("#RaceGroupId");
        race=$("#RaceId");
                $.ajax
                ({
                    type: "GET",
                    dataType:'json',
                    url: "?ctl=xrace/race.stage&ac=get.third.level.by.stage&RaceStageId="+stage.val() + "&RaceId="+race.val() + "&RaceGroupId="+ group.val(),
                    success: function(jsonResponse)
                    {
                        if(jsonResponse.S == "race")
                        {
                            if(type == "race")
                                {
                                    $("#RaceGroupId").html(jsonResponse.text);
                                }
                        }
                        else if(jsonResponse.S == "group")
                        {
                            if(type == "group")
                            {
                                $("#RaceId").html(jsonResponse.text);
                            }
                        }

                    }});

    }
</script>


<fieldset><legend>操作</legend>
</fieldset>
<form action="{tpl:$this.sign/}&ac=user.race.list" name="form" id="form" method="post">
赛事:    <select name="RaceCatalogId" id="RaceCatalogId" size="1" onchange="getStageByCatalog()">
            <option value="0" {tpl:if(0==$RaceCatalogId)}selected="selected"{/tpl:if}>全部</option>
            {tpl:loop $RaceCatalogList $RaceCatalogInfo}
            <option value="{tpl:$RaceCatalogInfo.RaceCatalogId/}" {tpl:if($RaceCatalogInfo.RaceCatalogId==$RaceCatalogId)}selected="selected"{/tpl:if}>{tpl:$RaceCatalogInfo.RaceCatalogName/}</option>
            {/tpl:loop}
        </select>
分站： <select name="RaceStageId" id="RaceStageId" size="1" class="span2" onchange="getSecondByStage()">
            <option value="0" {tpl:if(0==$RaceStageId)}selected="selected"{/tpl:if}>全部</option>
            {tpl:loop $RaceStageArr $RaceStageInfo}
            <option value="{tpl:$RaceStageInfo.RaceStageId/}" {tpl:if($RaceStageInfo.RaceStageId==$RaceStageId)}selected="selected"{/tpl:if}>{tpl:$RaceStageInfo.RaceStageName/}</option>
            {/tpl:loop}
        </select>
比赛： <select name="RaceId" id="RaceId" size="1" class="span2" onchange="getThirdByStage('race')">
        </select>
分组： <select name="RaceGroupId" id="RaceGroupId" size="1" class="span2" onchange="getThirdByStage('group')">
        </select>
    <p><input type="submit" name="Submit" value="查询" />{tpl:$export_var/}
</form>
<fieldset><legend>报名记录</legend>
<table width="99%" align="center" class="table table-bordered table-striped">
      <tr>
          <th align="center" class="rowtip">报名记录ID</th>
          <th align="center" class="rowtip">用户</th>
        <th align="center" class="rowtip">报名时间</th>
          <th align="center" class="rowtip">报名来源</th>
        <th align="center" class="rowtip">BIB</th>
          <th align="center" class="rowtip">芯片</th>
          <th align="center" class="rowtip">比赛</th>
        <th align="center" class="rowtip">组别</th>
        <th align="center" class="rowtip">状态</th>
      </tr>
    {tpl:loop $RaceUserList.RaceUserList $LogInfo}
      <tr class="hover">
        <td align="center">{tpl:$LogInfo.ApplyId/}</td>
        <td align="center">{tpl:$LogInfo.Name/}</td>
        <td align="center">{tpl:$LogInfo.ApplyTime/}</td>
        <td align="center">{tpl:$LogInfo.ApplySourceName/}</td>
          <td align="center">{tpl:$LogInfo.BIB/}</td>
          <td align="center">{tpl:$LogInfo.ChipId/}</td>
          <td align="center">{tpl:$LogInfo.RaceName/}</td>
          <td align="center">{tpl:$LogInfo.RaceGroupName/}</td>
          <td align="center">{tpl:if(1==$LogInfo.Status)}已生效{tpl:else}待确认{/tpl:if}</td>
      </tr>
    {/tpl:loop}
    <tr><th colspan="10" align="center" class="rowtip">{tpl:$page_content/}</th></tr>

</table>
</fieldset>
{tpl:tpl contentFooter/}
