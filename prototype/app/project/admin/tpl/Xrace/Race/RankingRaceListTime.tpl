{tpl:tpl contentHeader/}
<script type="text/javascript">
</script>

<form action="{tpl:$this.sign/}&ac=ranking.race.list.modify" name="ranking_race_list_update_form" id="ranking_race_list_update_form" method="post">
<fieldset><legend>比赛列表  {tpl:$RankingListUrl/}</legend>
  <input type="hidden" name="RankingId" id="RankingId" value="{tpl:$RankingInfo.RankingId/}" />

  <table width="99%" align="center" class="table table-bordered table-striped">
  <tr>
    <th align="center" class="rowtip">分站</th>
    <th align="center" class="rowtip">比赛列表</th>
  </tr>

  {tpl:loop $RaceStageList $RaceStageId $RaceStageInfo}
  <tr>
    <th align="center" class="rowtip" >{tpl:$RaceStageInfo.RaceStageName/}<br>{tpl:$RaceStageInfo.RaceStructureName/}<br>{tpl:$RaceStageInfo.StageStartDate/}<br>~<br>{tpl:$RaceStageInfo.StageEndDate/}</th>
    <th align="center" class="rowtip" > <table width="99%" align="center" class="table table-bordered table-striped">

      {tpl:if($RaceStageInfo.comment.RaceStructure=="race")}
        {tpl:loop $RaceStageInfo.RaceList $RaceId $RaceInfo}
        <tr>
          <td align="center" class="rowtip" rowspan = {tpl:$RaceInfo.RaceGroupList func="count(@@)" /}>{tpl:$RaceInfo.RaceName/}</td>
              {tpl:loop $RaceInfo.RaceGroupList $RaceGroupId $RaceGroupInfo}
          <td align="center" class="rowtip" /}>{tpl:$RaceGroupInfo.RaceGroupName/}</td>
          <td align="center" class="rowtip" /}><input type="checkbox" name="RaceList[{tpl:$RaceId/}][{tpl:$RaceGroupId/}][selected]" value="1" {tpl:if($RaceGroupInfo.selected >0)}checked{/tpl:if} /></td>
            <td align="center" class="rowtip" /}>
            <select name="RaceList[{tpl:$RaceId/}][{tpl:$RaceGroupId/}][RankingType]" class="span2" size="1">
                {tpl:loop $RankingTypeList.time $RankingType $RankingTypeName}
                <option value="{tpl:$RankingType/}" {tpl:if($RaceGroupInfo.RankingType==$RankingType)}selected="selected"{/tpl:if}>{tpl:$RankingTypeName/}</option>
                {/tpl:loop}
                </td>
        </tr>
              {/tpl:loop}
          </tr>
        {/tpl:loop}
      {tpl:else}
            {tpl:loop $RaceStageInfo.RaceGroupList $RaceGroupId $RaceGroupInfo}
        <tr>
          <td align="center" class="rowtip" rowspan = {tpl:$RaceGroupInfo.RaceList func="count(@@)"  /}>{tpl:$RaceGroupInfo.RaceGroupName/}</td>
            {tpl:loop $RaceGroupInfo.RaceList $RaceId $RaceInfo}
          <td align="center" class="rowtip" /}>{tpl:$RaceInfo.RaceName/}</td>
          <td align="center" class="rowtip" /}><input type="checkbox" name="RaceList[{tpl:$RaceId/}][{tpl:$RaceGroupId/}][selected]" value="1" {tpl:if($RaceInfo.selected >0)}checked{/tpl:if} /></td>
            <td align="center" class="rowtip" /}>
            <select name="RaceList[{tpl:$RaceId/}][{tpl:$RaceGroupId/}][RankingType]" class="span2" size="1">
                {tpl:loop $RankingTypeList.time $RankingType $RankingTypeName}
                <option value="{tpl:$RankingType/}" {tpl:if($RaceInfo.RankingType==$RankingType)}selected="selected"{/tpl:if}>{tpl:$RankingTypeName/}</option>
                {/tpl:loop}
                </td>

        </tr>
        {/tpl:loop}
        </tr>
        {/tpl:loop}
      {/tpl:if}
      </table>
    </th>
  </tr>
  {/tpl:loop}
    <tr class="noborder" >
      <td colspan = 3><button type="submit" id="ranking_race_list_update_submit">提交</button></td>
    </tr>
  </form>

</table>

</fieldset>


<script type="text/javascript">
    $('#ranking_race_list_update_submit').click(function(){
        var options = {
            dataType:'json',
            beforeSubmit:function(formData, jqForm, options) {
            },
            success:function(jsonResponse) {
                if (jsonResponse.errno) {
                    var errors = [];
                    errors[9] = '入库失败，请修正后再次提交';
                    divBox.alertBox(errors[jsonResponse.errno],function(){});
                } else {
                    var message = '修改排名对应的比赛成功';
                    RankingId=$("#RankingId");
                    divBox.confirmBox({content:message,ok:function(){windowParent.getRightHtml('{tpl:$this.sign/}'+ '&ac=ranking.race.list&RankingId=' + RankingId.val());}});
                }
            }
        };
        $('#ranking_race_list_update_form').ajaxForm(options);
    });
</script>

{tpl:tpl contentFooter/}
