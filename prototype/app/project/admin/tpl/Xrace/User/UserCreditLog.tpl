{tpl:tpl contentHeader/}
<script type="text/javascript">
    function getCreditByCatalog()
    {
        catalog=$("#RaceCatalogId");
        $.ajax
        ({
            type: "GET",
            url: "?ctl=xrace/credit&ac=get.credit.list.by.catalog&RaceCatalogId="+catalog.val(),
            success: function(msg)
            {
                $("#CreditId").html(msg);
            }});
    }
    function getStageByCatalog()
    {
        catalog=$("#RaceCatalogId2");
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
<form action="{tpl:$this.sign/}&ac=user.credit.log" name="form" id="form" method="post">
赛事:    <select name="RaceCatalogId" id="RaceCatalogId" size="1" onchange="getCreditByCatalog()">
        <option value="0" {tpl:if(0==$RaceCatalogId)}selected="selected"{/tpl:if}>全部</option>
        {tpl:loop $RaceCatalogList $RaceCatalogInfo}
        <option value="{tpl:$RaceCatalogInfo.RaceCatalogId/}" {tpl:if($RaceCatalogInfo.RaceCatalogId==$RaceCatalogId)}selected="selected"{/tpl:if}>{tpl:$RaceCatalogInfo.RaceCatalogName/}</option>
        {/tpl:loop}
    </select>
积分：    <select name="CreditId" id="CreditId" size="1">
        {tpl:loop $CreditArr $CreditInfo}
        <option value="{tpl:$CreditInfo.CreditId/}" {tpl:if($CreditInfo.CreditId==$CreditId)}selected="selected"{/tpl:if}>{tpl:$CreditInfo.CreditName/}</option>
        {/tpl:loop}
    </select>
    <p>
动作:    <select name="ActionId" id="ActionId" size="1">
            <option value="0">全部</option>
            {tpl:loop $ActionList $ActionId $ActionInfo}
        <option value="{tpl:$ActionId/}">{tpl:$ActionInfo.ActionName/}</option>
        {/tpl:loop}
    </select>
    <p>
赛事:    <select name="RaceCatalogId2" id="RaceCatalogId2" size="1" onchange="getStageByCatalog()">
            <option value="0" {tpl:if(0==$RaceCatalogId2)}selected="selected"{/tpl:if}>全部</option>
            {tpl:loop $RaceCatalogList $RaceCatalogInfo}
            <option value="{tpl:$RaceCatalogInfo.RaceCatalogId/}" {tpl:if($RaceCatalogInfo.RaceCatalogId==$RaceCatalogId2)}selected="selected"{/tpl:if}>{tpl:$RaceCatalogInfo.RaceCatalogName/}</option>
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
    <input type="submit" name="Submit" value="查询" />{tpl:$export_var/}
</form>
<fieldset><legend>积分更新记录</legend>
<table width="99%" align="center" class="table table-bordered table-striped">
      <tr>
          <th align="center" class="rowtip">更新记录</th>
          <th align="center" class="rowtip">用户</th>
        <th align="center" class="rowtip">更新时间</th>
          <th align="center" class="rowtip">变更数量</th>
        <th align="center" class="rowtip">积分</th>
        <th align="center" class="rowtip">性别</th>
          <!--<th align="center" class="rowtip">实名认证</th>-->
        <th align="center" class="rowtip">生日</th>
        <th align="center" class="rowtip">注册时间</th>
        <th align="center" class="rowtip">最后登陆</th>
        <th align="center" class="rowtip">操作</th>
      </tr>
    {tpl:loop $CreditLog.CreditLog $LogInfo}
      <tr class="hover">
        <td align="center">{tpl:$LogInfo.Id/}</td>
        <td align="center">{tpl:$LogInfo.UserId/}</td>
        <td align="center">{tpl:$LogInfo.Time/}</td>
        <td align="center">{tpl:$LogInfo.Credit/}</td>
          <td align="center">{tpl:$LogInfo.CreditName/}</td>
          <td align="center">{tpl:$LogInfo.ActionName/}</td>
        <td align="center">{tpl:$UserInfo.Birthday/}</td>
        <td align="center">{tpl:$UserInfo.RegTime/}</td>
        <td align="center">{tpl:$UserInfo.LastLoginTime/}<br>{tpl:$UserInfo.LoginSourceName/}</td>
        <!--<td align="center"><a  href="javascript:;" onclick="userDetail('{tpl:$UserInfo.UserId/}')">详细</a>{tpl:if($UserInfo.AuthStatus==1)} | <a  href="javascript:;" onclick="userAuth('{tpl:$UserInfo.UserId/}')">审核</a>{/tpl:if} | {tpl:$UserInfo.License/} | {tpl:$UserInfo.Team/} | <a  href="javascript:;" onclick="userPasswordUpdate('{tpl:$UserInfo.UserId/}')">更新密码</a> | <a  href="javascript:;" onclick="userMobileUpdate('{tpl:$UserInfo.UserId/}')">更新手机</a></td>-->
          <td align="center"><a  href="javascript:;" onclick="userDetail('{tpl:$UserInfo.UserId/}')">详细</a></td>

      </tr>
    {/tpl:loop}
    <tr><th colspan="10" align="center" class="rowtip">{tpl:$page_content/}</th></tr>

</table>
</fieldset>
{tpl:tpl contentFooter/}
