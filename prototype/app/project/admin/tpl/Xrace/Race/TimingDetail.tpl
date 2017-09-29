{tpl:tpl contentHeader/}
<script type="text/javascript">
  function RaceResultList(rid,uid,rname){
    RaceResultListBox = divBox.showBox('{tpl:$this.sign/}&ac=race.result.list&RaceId=' + rid + '&RaceUserId=' + uid, {title:rname+'成绩单',width:800,height:750});
  }
</script>
<form action="{tpl:$this.sign/}&ac=race.update" name="form" id="form" method="post">
<input type="hidden" name="RaceStageId" id="RaceStageId" value="{tpl:$RaceStageInfo.RaceStageId/}" />
  <input type="hidden" name="RaceGroupId" id="RaceGroupId" value="{tpl:$RaceGroupId/}" />
  <fieldset><legend>{tpl:$RaceInfo.RaceName/} 成绩单{tpl:if(isset($UserInfo.RaceUserId))} - {tpl:$UserInfo.Name/}{/tpl:if}</legend>
   {tpl:if(count($RaceGroupList))}
    <fieldset><legend>
        {tpl:loop $RaceGroupList $GInfo}
        {tpl:$GInfo.DownloadUrl/}/
        {/tpl:loop}
      </legend>
    {/tpl:if}
        {tpl:loop $UserUrlList $Cid $url}
        {tpl:$url/}
        {/tpl:loop}
      <table width="99%" align="center" class="table table-bordered table-striped">
    <th align="center" class="rowtip" colspan="2">芯片</th>
    <th align="center" class="rowtip" colspan="10">选手</th>
  </tr>

      {tpl:loop $TimingList.Record $id $RecordInfo}
        <tr>

    <th align="center" class="rowtip">{tpl:$RecordInfo.Chip/}</th>
          <th align="center" class="rowtip">{tpl:$RecordInfo.Name/}</th>
          <th align="center" class="rowtip">{tpl:$RecordInfo.time/}</th>
        </tr>
      {/tpl:loop}
          <tr>
              <th align="center" class="rowtip" colspan="3">{tpl:$page_content/}</th>
          </tr>
</table>

</fieldset>
</form>
{tpl:tpl contentFooter/}
