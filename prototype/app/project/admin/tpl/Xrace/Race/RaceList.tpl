{tpl:tpl contentHeader/}
<script type="text/javascript">
  function RaceAdd(sid,gid){
    RaceAddBox = divBox.showBox('{tpl:$this.sign/}&ac=race.add&RaceGroupId=' + gid + '&RaceStageId=' + sid, {title:'添加比赛',width:1000,height:750});
  }
  function RaceModify(rid,rname,gid){
    RaceModifyBox = divBox.showBox('{tpl:$this.sign/}&ac=race.modify&RaceId=' + rid + '&RaceGroupId=' + gid, {title:'修改比赛-'+rname,width:1000,height:750});
  }
  function RaceUserUpload(rid,rname,gid){
    RaceModifyBox = divBox.showBox('{tpl:$this.sign/}&ac=race.user.upload.submit&RaceId=' + rid + '&RaceGroupId=' + gid, {title:'批量导入报名记录-'+rname,width:400,height:250});
  }
  function RaceUserList(rid,rname,gname){
    RaceUserListBox = divBox.showBox('{tpl:$this.sign/}&ac=race.user.list&RaceId=' + rid, {title:rname+'选手名单',width:1200,height:800});
  }
  function AutoAsignBIB(rid,rname){
      RaceUserListBox = divBox.showBox('{tpl:$this.sign/}&ac=race.user.list&RaceId=' + rid + '&AutoAsign=1', {title:rname+' 自动分配BIB',width:900,height:800});
  }
  function RaceResultList(rid,rname){
    RaceResultListBox = divBox.showBox('{tpl:$this.sign/}&ac=race.result.list&RaceId=' + rid , {title:rname+'成绩单',width:1000,height:750});
  }
  function RaceDelete(r_id, r_name){
    deleteAppBox = divBox.confirmBox({content:'是否删除 ' + r_name + '?<p>关联的报名记录和计时点配置将同时删除',ok:function(){location.href = '{tpl:$this.sign/}&ac=race.delete&RaceId=' + r_id;}});
  }
  function RaceCopy(rid){
    RaceResultListBox = divBox.showBox('{tpl:$this.sign/}&ac=race.copy.submit&RaceId=' + rid, {title:'复制比赛',width:400,height:200});
  }
  function RaceResultUpdate(r_id){
    deleteAppBox = divBox.confirmBox({content:'是否更新比赛记录?<p>将于几分钟内由定时任务更新',ok:function(){location.href = '{tpl:$this.sign/}&ac=race.result.update&RaceId=' + r_id;}});
  }
  function RaceResultConfirm(r_id){
      RaceResultConfirmBox= divBox.confirmBox({content:'确认成绩并发布 ?',ok:function(){location.href = '{tpl:$this.sign/}&ac=race.result.confirm&RaceId=' + r_id;}});
  }
</script>
<form action="{tpl:$this.sign/}&ac=race.update" name="form" id="form" method="post">
<input type="hidden" name="RaceStageId" id="RaceStageId" value="{tpl:$RaceStageInfo.RaceStageId/}" />
  <input type="hidden" name="RaceGroupId" id="RaceGroupId" value="{tpl:$RaceGroupId/}" />
  <fieldset><legend>{tpl:$RaceStageInfo.RaceStageName/}{tpl:if(isset($RaceGroupInfo.RaceGroupName))}-{tpl:$RaceGroupInfo.RaceGroupName/}{/tpl:if} 比赛列表 <a href="{tpl:$this.sign/}">返回</a></legend>
<table width="99%" align="center" class="table table-bordered table-striped">
  {tpl:if(count($RaceList))}
  <tr>
    <th align="center" class="rowtip">所属分组</th>
    <th align="center" class="rowtip">比赛ID</th>
    <th align="center" class="rowtip">比赛名称</th>
    <th align="center" class="rowtip">必选/单选</th>
    <th align="center" class="rowtip">个人/团队</th>
    <th align="center" class="rowtip">报名时间/比赛时间</th>
    <th align="center" class="rowtip">比赛进程</th>
    <th align="center" class="rowtip">操作</th>
  </tr>
  {tpl:loop $RaceList $Rid $RaceInfo}
  <tr>
    <th align="center" class="rowtip" width="10%">{tpl:$RaceInfo.RaceGroupName/}</th>
    <th align="center" class="rowtip">{tpl:$RaceInfo.RaceId/}</th>
    <th align="center" class="rowtip">{tpl:$RaceInfo.RaceName/}/{tpl:$RaceInfo.RaceTypeName/}</th>
    <th align="center" class="rowtip">{tpl:if($RaceInfo.MustSelect==1)}是{tpl:else}否{/tpl:if}/{tpl:if($RaceInfo.SingleSelect==1)}是{tpl:else}否{/tpl:if}</th>
    <th align="center" class="rowtip" width="10%">{tpl:if($RaceInfo.SingleUser==1)}{tpl:$RaceInfo.comment.SingleUserLimit/}人 {tpl:else}不接受{/tpl:if}<p>{tpl:if($RaceInfo.TeamUser==1)}{tpl:$RaceInfo.comment.TeamLimit/}队/{tpl:$RaceInfo.comment.TeamUserMin/}-{tpl:$RaceInfo.comment.TeamUserMax/}人 {tpl:else}不接受{/tpl:if}
      <p>男：{tpl:if($RaceInfo.comment.SexUser.Min.1==0)}不限{tpl:else}{tpl:$RaceInfo.comment.SexUser.Min.1/}{/tpl:if} - {tpl:if($RaceInfo.comment.SexUser.Max.1==0)}不限{tpl:else}{tpl:$RaceInfo.comment.SexUser.Max.1/}人 {/tpl:if}
      <p>女：{tpl:if($RaceInfo.comment.SexUser.Min.2==0)}不限{tpl:else}{tpl:$RaceInfo.comment.SexUser.Min.2/}{/tpl:if} - {tpl:if($RaceInfo.comment.SexUser.Max.2==0)}不限{tpl:else}{tpl:$RaceInfo.comment.SexUser.Max.2/}人 {/tpl:if}
    </th>
    <th align="center" class="rowtip" width="15%">{tpl:$RaceInfo.ApplyStartTime/}<br>~<br>{tpl:$RaceInfo.ApplyEndTime/}<p>{tpl:$RaceInfo.StartTime/}.{tpl:$RaceInfo.comment.RaceStartMicro func="sprintf('%03d',@@)"/}<br>~<br>{tpl:$RaceInfo.EndTime/}</th>
    <th align="center" class="rowtip">{tpl:$RaceInfo.RaceStatus/}</th>
    <th align="center" class="rowtip"><a href="javascript:;" onclick="RaceModify('{tpl:$RaceInfo.RaceId/}','{tpl:$RaceInfo.RaceName/}','{tpl:$RaceGroupId/}')">修改</a> | <a href="javascript:;" onclick="RaceUserUpload('{tpl:$RaceInfo.RaceId/}','{tpl:$RaceInfo.RaceName/}','{tpl:$RaceGroupId/}')">导入报名</a>
     | <a href="{tpl:$this.sign/}&ac=race.detail&RaceId={tpl:$RaceInfo.RaceId/}&RaceGroupId={tpl:$RaceInfo.RaceGroupId/}">计时点</a> | <a href="{tpl:$this.sign/}&ReturnType=1&ac=race.user.list&RaceId={tpl:$RaceInfo.RaceId/}">名单</a> | <a href="javascript:;" onclick="AutoAsignBIB('{tpl:$RaceInfo.RaceId/}','{tpl:$RaceInfo.RaceName/}')">自动分配BIB</a>
      | <a  href="javascript:;" onclick="RaceResultUpdate('{tpl:$RaceInfo.RaceId/}')">成绩更新</a> | <a href="javascript:;" onclick="RaceResultList('{tpl:$RaceInfo.RaceId/}','{tpl:$RaceInfo.RaceName/}')">成绩单</a> {tpl:if($RaceInfo.comment.ResultNeedConfirm==1)}| {tpl:if($RaceInfo.comment.RaceResultConfirm.ConfirmStatus==1)}成绩已发布{tpl:else}<a  href="javascript:;" onclick="RaceResultConfirm('{tpl:$RaceInfo.RaceId/}');">成绩确认发布</a>{/tpl:if}{/tpl:if}
      | <a  href="javascript:;" onclick="RaceDelete('{tpl:$RaceInfo.RaceId/}','{tpl:$RaceInfo.RaceName/}')">删除</a> | <a  href="javascript:;" onclick="RaceCopy('{tpl:$RaceInfo.RaceId/}')">复制</a> | <a href="{tpl:$this.sign/}&ac=race.check.in.submit&RaceId={tpl:$RaceInfo.RaceId/}" target="_blank">检录</a></th>
    </th>
  </tr>
  {/tpl:loop}
  <tr>
    <th align="center" class="rowtip" colspan="15">  <a href="javascript:;" onclick="RaceAdd('{tpl:$RaceStageInfo.RaceStageId/}','{tpl:$RaceGroupInfo.RaceGroupId/}')">点此添加比赛</a>
    </th>
  </tr>
  {tpl:else}
  <tr>
    <th align="center" class="rowtip">本站尚未配置任何比赛<a href="javascript:;" onclick="RaceAdd('{tpl:$RaceStageInfo.RaceStageId/}','{tpl:$RaceGroupInfo.RaceGroupId/}')">点此添加比赛</a>
    </th>
    </th>
  </tr>
  {/tpl:if}
</table>
</fieldset>
</form>
{tpl:tpl contentFooter/}
