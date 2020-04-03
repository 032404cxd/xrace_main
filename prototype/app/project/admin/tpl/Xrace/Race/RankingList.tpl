{tpl:tpl contentHeader/}
<script type="text/javascript">
  function RankingAdd(cid){
    RankingAddBox = divBox.showBox('{tpl:$this.sign/}&ac=ranking.add&RaceCatalogId=' + cid, {title:'添加排名',width:600,height:410});
  }
  function RankingModify(rid,rname){
    RankingModifyBox = divBox.showBox('{tpl:$this.sign/}&ac=ranking.modify&RankingId=' + rid , {title:'修改排名-'+rname,width:600,height:410});
  }
  function RankingDelete(rid, rname){
      deleteRankingBox = divBox.confirmBox({content:'是否删除 ' + rname + '?',ok:function(){location.href = '{tpl:$this.sign/}&ac=ranking.delete&RankingId=' + rid;}});
  }
  function UpdateUserListByRanking(rid, rname){
      updateUserListBox = divBox.confirmBox({content:'是否更新 ' + rname + '的成绩单?',ok:function(){location.href = '{tpl:$this.sign/}&ac=update.race.user.list.by.ranking&RankingId=' + rid;}});
  }
  function ResultList(rid, rname){
      resultListBox = divBox.showBox('{tpl:$this.sign/}&ac=get.race.user.list.by.ranking&RankingId=' + rid , {title:'成绩单-'+rname,width:800,height:800});
  }
</script>
<input type="hidden" name="RaceCatalogId" id="RaceCatalogId" value="{tpl:$RaceCatalogInfo.RaceCatalogId/}" />
  <fieldset><legend>{tpl:$RaceCatalogInfo.RaceCatalogName/} 排名列表 <a href="{tpl:$this.sign/}">返回</a></legend>
<table width="99%" align="center" class="table table-bordered table-striped">
  {tpl:if(count($RankingList))}
  <tr>
    <th align="center" class="rowtip">排名ID</th>
    <th align="center" class="rowtip">名称</th>
    <th align="center" class="rowtip">说明</th>
    <th align="center" class="rowtip">排名方式</th>

    <th align="center" class="rowtip">操作</th>
  </tr>
  {tpl:loop $RankingList $Rid $RankingInfo}
  <tr>
    <th align="center" class="rowtip" width="10%">{tpl:$RankingInfo.RankingId/}</th>
    <th align="center" class="rowtip">{tpl:$RankingInfo.RankingName/}</th>
    <th align="center" class="rowtip">{tpl:$RankingInfo.RankingComment/}</th>
    <th align="center" class="rowtip">{tpl:$RankingInfo.RankingTypeName/}</th>
    <th align="center" class="rowtip">{tpl:$RankingInfo.RankingRaceListUrl/} | <a href="javascript:;" onclick="RankingModify('{tpl:$RankingInfo.RankingId/}','{tpl:$RankingInfo.RankingName/}')">修改</a> | <a  href="javascript:;" onclick="RankingDelete('{tpl:$RankingInfo.RankingId/}','{tpl:$RankingInfo.RankingName/}')">删除</a> | <a href="javascript:;" onclick="UpdateUserListByRanking('{tpl:$RankingInfo.RankingId/}','{tpl:$RankingInfo.RankingName/}')"> 成绩更新</a> | <a href="javascript:;" onclick="ResultList('{tpl:$RankingInfo.RankingId/}','{tpl:$RankingInfo.RankingName/}')">成绩单</a></th>
    </th>
  </tr>
  {/tpl:loop}
  <tr>
    <th align="center" class="rowtip" colspan="15">  <a href="javascript:;" onclick="RankingAdd('{tpl:$RaceCatalogInfo.RaceCatalogId/}')">点此添加排名</a>
    </th>
  </tr>
  {tpl:else}
  <tr>
    <th align="center" class="rowtip">本站尚未配置任何比赛<a href="javascript:;" onclick="RankingAdd('{tpl:$RaceCatalogInfo.RaceCatalogId/}')">点此添加排名</a>
    </th>
    </th>
  </tr>
  {/tpl:if}
</table>
</fieldset>
{tpl:tpl contentFooter/}
