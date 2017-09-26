{tpl:tpl contentHeader/}

<fieldset><legend>操作</legend>
</fieldset>
<form action="{tpl:$this.sign/}" name="form" id="form" method="post">
    芯片:<input type="text" class="span2" name="ChipId" value="{tpl:$params.ChipId/}" />
    场地:<select name="ArenaId" class="span2" size="1">
        <option value="0" {tpl:if($params.ArenaId==-0)}selected="selected"{/tpl:if}>全部</option>
        {tpl:loop $ArenaList $ArenaId $ArenaInfo}
        <option value="{tpl:$ArenaId/}" {tpl:if($params.ArenaId==$ArenaId)}selected="selected"{/tpl:if}>{tpl:$ArenaInfo.ArenaName/}</option>
        {/tpl:loop}
    </select>
    <input type="submit" name="Submit" value="查询" />{tpl:$export_var/}
</form>
<fieldset><legend>约战队列</legend>
<table width="99%" align="center" class="table table-bordered table-striped">
      <tr>
        <th align="center" class="rowtip">约战记录ID</th>
        <th align="center" class="rowtip">姓名</th>
        <th align="center" class="rowtip">芯片</th>
        <th align="center" class="rowtip">场地</th>
        <th align="center" class="rowtip">时间</th>
        <th align="center" class="rowtip">申请时间</th>
        <th align="center" class="rowtip">操作</th>
      </tr>
    {tpl:loop $UserRaceApplyList.UserRaceApplyList $ApplyInfo}
      <tr class="hover">
        <td align="center">{tpl:$ApplyInfo.ApplyId/}</td>
        <td align="center">{tpl:$ApplyInfo.Name/}</td>
        <td align="center">{tpl:$ApplyInfo.ChipId/}</td>
        <td align="center">{tpl:$ApplyInfo.ArenaName/}</td>
        <td align="center">{tpl:$ApplyInfo.ApplyStartTime func="date('Y-m-d H:m',@@)"/} --- {tpl:$ApplyInfo.ApplyEndTime func="date('H:m',@@)"/}</td>
        <td align="center">{tpl:$ApplyInfo.ApplyTime func="date('Y-m-d H:m',@@)"/}</td>

      </tr>
    {/tpl:loop}
    <tr><th colspan="10" align="center" class="rowtip">{tpl:$page_content/}</th></tr>

</table>
</fieldset>
{tpl:tpl contentFooter/}
