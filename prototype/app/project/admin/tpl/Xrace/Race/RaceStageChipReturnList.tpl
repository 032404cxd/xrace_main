{tpl:tpl contentHeader/}
<script type="text/javascript">
    function ChipStatus(rid)
    {
        ChipId=$("#ChipId");
        RaceUserInfoBox = divBox.showBox('{tpl:$this.sign/}&ac=chip.status&ChipId=' + ChipId.val() + '&RaceStageId=' + rid, {title:'芯片使用状态',width:800,height:350});
        return false;
    }

</script>
<fieldset><legend>{tpl:$RaceStageInfo.RaceStageName/} 芯片归还状态 <a href="{tpl:$this.sign/}">返回</a></legend>
  <form id="chip_return_form" name="chip_return_form" action="{tpl:$this.sign/}&ac=chip.status" method="post">
    <input type="hidden" name="RaceStageId" id="RaceStageId" value="{tpl:$RaceStageId/}" />
    <table width="99%" align="center" class="table table-bordered table-striped">
      <tr class="hover">
        <td colspan="4"><input type="text" class="span4" name="ChipId"  id="ChipId" value="" /> <a href="javascript:;" onclick="ChipStatus('{tpl:$RaceStageInfo.RaceStageId/}')">点击提交</a></td>
      </tr>
    </table>
  </form>

  <table width="99%" align="center" class="table table-bordered table-striped">
    <tr><th align="center" class="rowtip" colspan="5">
        {tpl:loop $ReturnStatusList.StatusList $Cid $ReturnStatusInfo}
            {tpl:$ReturnStatusInfo.StatusName/}：{tpl:$ReturnStatusInfo.StatusUrl/}
        {/tpl:loop}
      </th></tr>
  <tr>
    <th align="center" class="rowtip">芯片</th>
    <th align="center" class="rowtip">选手</th>
    <th align="center" class="rowtip">比赛</th>
    <th align="center" class="rowtip">分组</th>
    <th align="center" class="rowtip">BIB</th>
  </tr>
  {tpl:loop $ReturnStatusList.ChipList $Rid $ReturnedList}
    <tr>
      <th align="center" class="rowtip" colspan="5" align="center">
          {tpl:loop $ReturnStatusList.StatusList $Sid $ReturnStatusInfo}
          {tpl:if($Sid==$Rid && (strlen($Sid)==strlen($Rid)))}{tpl:$ReturnStatusInfo.StatusName/}{/tpl:if}
          {/tpl:loop}
      </th>
    </tr>
      {tpl:loop $ReturnedList $ChipId $ChipInfo}
      <tr>
      <th align="center" class="rowtip" rowspan={tpl:$ChipInfo func="count(@@)"/}>{tpl:$ChipId/}</th>
          {tpl:loop $ChipInfo $ApplyId $ApplyInfo}
        <th align="center" class="rowtip" >{tpl:$ApplyInfo.Name/}</th>
        <th align="center" class="rowtip">{tpl:$ApplyInfo.RaceName/}</th>
        <th align="center" class="rowtip">{tpl:$ApplyInfo.RaceGroupName/}</th>
          <th align="center" class="rowtip">{tpl:$ApplyInfo.BIB/}</th>

      </tr>
        {/tpl:loop}
      </tr>
    {/tpl:loop}
  {/tpl:loop}
</table>
    {tpl:$page_content/}
    <script type="text/javascript">
        $(function(){
            $('#ChipId').focus();
        });
    </script>
{tpl:tpl contentFooter/}
