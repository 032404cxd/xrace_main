{tpl:tpl contentHeader/}
<script type="text/javascript">

</script>

<fieldset><legend>操作</legend>
</fieldset>
<form action="{tpl:$this.sign/}" name="form" id="form" method="post">
    关键字:<input type="text" class="span2" name="Keyword" id="Keyword" value="{tpl:$Keyword/}" />
    <input type="submit" name="Submit" value="查询" />
</form>
<fieldset><legend>关键字列表</legend>
<table width="99%" align="center" class="table table-bordered table-striped">
      <tr>
        <th align="center" class="rowtip">关键字</th>
        <th align="center" class="rowtip">对应分站</th>
        <th align="center" class="rowtip">操作</th>
      </tr>
    {tpl:loop $KeywordList.KeywordList $KeywordInfo}
      <tr class="hover">
        <td align="center">{tpl:$KeywordInfo.Keyword/}</td>
          <td align="center">
              {tpl:loop $KeywordInfo.RaceStageList $RaceStageId $RaceStageInfo}
                {tpl:$RaceStageInfo.RaceStageInfo.RaceStageName/}<p>
              {/tpl:loop}
          </td>
      </tr>
    {/tpl:loop}
    <tr><th colspan="10" align="center" class="rowtip">{tpl:$page_content/}</th></tr>

</table>
</fieldset>
{tpl:tpl contentFooter/}
