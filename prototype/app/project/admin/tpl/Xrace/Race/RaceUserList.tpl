{tpl:tpl contentHeader/}
<script type="text/javascript">
  function RaceUserUpload(rid,rname,gid){
    RaceModifyBox = divBox.showBox('{tpl:$this.sign/}&ac=race.user.upload.submit&RaceId=' + rid + '&RaceGroupId=' + gid, {title:'批量导入报名记录-'+rname,width:400,height:250});
  }
  function UserRaceDNF(aid,uname) {
      DNFUserRaceBox = divBox.showBox('{tpl:$this.sign/}&ac=user.race.dnf.apply&ApplyId=' + aid, {title:uname+'DNF确认',width:550,height:300});
  }
  function UserRaceDNS(aid,uname) {
      DNSUserRaceBox = divBox.showBox('{tpl:$this.sign/}&ac=user.race.dns.apply&ApplyId=' + aid, {title:uname+'DNS确认',width:550,height:300});
  }
  function UserRaceStatusRestore(aid,uname){
    restoreUserRaceStatusBox = divBox.confirmBox({content:'确定将'+uname+'的比赛状态恢复?',ok:function(){location.href = '{tpl:$this.sign/}&ac=user.race.status.restore&ApplyId=' + aid;}});
  }
  function UserRaceDeleteByGroup(rid,gid,gname){
    deleteUserRaceByRaceBox = divBox.confirmBox({content:'确定'+gname+'全部退出比赛?',ok:function(){location.href = '{tpl:$this.sign/}&ac=user.race.delete.by.race&RaceId=' + rid + '&RaceGroupId=' + gid;}});
  }
  function UserRaceDelete(aid,uname){
      deleteUserRaceBox = divBox.confirmBox({content:'确定'+uname+'退出比赛?',ok:function(){location.href = '{tpl:$this.sign/}&ac=user.race.delete&ApplyId=' + aid;}});
  }
  function RaceResultList(rid,uid,rname){
    RaceResultListBox = divBox.showBox('{tpl:$this.sign/}&ac=race.result.list&RaceId=' + rid + '&RaceUserId=' + uid, {title:rname+'成绩单',width:800,height:750});
  }
</script>
<form action="{tpl:$this.sign/}&ac=race.user.list.update" name="race_user_list_update_form" id="race_user_list_update_form" method="post">
  <input type="hidden" name="RaceId" id="RaceId" value="{tpl:$RaceInfo.RaceId/}" />
<input type="hidden" name="CurrentRaceGroupId" id="CurrentRaceGroupId" value="{tpl:$RaceGroupId/}" />
  <fieldset><legend> 选手名单 {tpl:if($ReturnType==1)}  <a href="{tpl:$this.sign/}&ac=race.list&RaceStageId={tpl:$RaceInfo.RaceStageId/}">返回比赛列表</a>{/tpl:if}  {tpl:$DownloadUrl/}</legend>
  <table width="99%" align="center" class="table table-bordered table-striped">
    <tr><th align="center" class="rowtip" colspan="5">
            {tpl:loop $RaceUserList.RaceStatus $Status $StatusInfo} {tpl:$StatusInfo.StatusName/}<a href="{tpl:$this.sign/}&ac=race.user.list&RaceId={tpl:$RaceInfo.RaceId/}&RaceStatus={tpl:$Status/}&ReturnType={tpl:$ReturnType/}">{tpl:$StatusInfo.UserCount/}人</a>{/tpl:loop}</th></tr>
    {tpl:if(count($RaceUserList))}
  <tr>
    <th align="center" class="rowtip">姓名</th>
    <th align="center" class="rowtip">报名来源</th>
    <th align="center" class="rowtip">分组</th>
    <th align="center" class="rowtip">所属队伍</th>
    <th align="center" class="rowtip">报名时间</th>
    <th align="center" class="rowtip">选手号码</th>
    <th align="center" class="rowtip">计时芯片ID</th>
    <th align="center" class="rowtip">操作</th>
  </tr>
  {tpl:loop $RaceUserList.RaceUserList $Aid $UserInfo}
  <tr>
    <input type="hidden" name="UserList[{tpl:$Aid/}][ApplyId]" id="UserList[{tpl:$UserInfo.UserId/}][ApplyId]" value="{tpl:$UserInfo.ApplyId/}" />
    <th align="center" class="rowtip"><a href="javascript:;" onclick="RaceResultList('{tpl:$RaceInfo.RaceId/}','{tpl:$UserInfo.RaceUserId/}','{tpl:$RaceInfo.RaceName/}')">{tpl:$UserInfo.Name/}</a></th>
    <th align="center" class="rowtip">{tpl:$UserInfo.ApplySourceName/}</th>
    <th align="center" class="rowtip">
        {tpl:if(count($RaceInfo.comment.SelectedRaceGroup)>0)}
      <select name="UserList[{tpl:$Aid/}][RaceGroupId]" class="span2" size="1">
        {tpl:loop $RaceInfo.comment.SelectedRaceGroup $G $GInfo}
        <option value="{tpl:$G/}" {tpl:if($G==$UserInfo.RaceGroupId)}selected="selected"{/tpl:if}>{tpl:$GInfo.RaceGroupInfo.RaceGroupName/}</option>
          {/tpl:loop}
        {tpl:else}
        {tpl:$UserInfo.RaceGroupName/}
        {/tpl:if}
        {tpl:if($UserInfo.RaceGroupId>0)}<a href="javascript:void(0);" onclick="UserRaceDeleteByGroup('{tpl:$RaceInfo.RaceId/}','{tpl:$UserInfo.RaceGroupId/}','{tpl:$UserInfo.RaceGroupName/}')">退赛</a>{/tpl:if}</th>
    <th align="center" class="rowtip">{tpl:$UserInfo.TeamName/}</th>
    <th align="center" class="rowtip">{tpl:$UserInfo.ApplyTime/}</th>
    <th align="center" class="rowtip"><input type="text" class="span1" name="UserList[{tpl:$Aid/}][BIB]" id="UserList[{tpl:$UserInfo.UserId/}][BIB]" value="{tpl:$UserInfo.BIB/}" />{tpl:if($UserInfo.TBD>0)}{tpl:if($UserInfo.TBD==1)}待确认{tpl:else}其他{/tpl:if}{/tpl:if}</th>
    <th align="center" class="rowtip"><input type="text" class="span2" name="UserList[{tpl:$Aid/}][ChipId]" id="UserList[{tpl:$UserInfo.UserId/}][ChipId]" value="{tpl:$UserInfo.ChipId/}" /></th>
    <th align="center" class="rowtip">{tpl:if($UserInfo.RaceStatus==2)}<abbr title="{tpl:$UserInfo.comment.DNF.Reason/}&#10;{tpl:$UserInfo.comment.DNF.Time func="date('Y-m-d H:i:s',@@)"/}">DNF</abbr>{tpl:else}<a href="javascript:;" onclick="UserRaceDNF('{tpl:$UserInfo.ApplyId/}','{tpl:$UserInfo.Name/}')">DNF</a>{/tpl:if} | {tpl:if($UserInfo.RaceStatus==1)}<abbr title="{tpl:$UserInfo.comment.DNS.Reason/}&#10;{tpl:$UserInfo.comment.DNS.Time func="date('Y-m-d H:i:s',@@)"/}">DNS</abbr>{tpl:else}<a href="javascript:;" onclick="UserRaceDNS('{tpl:$UserInfo.ApplyId/}','{tpl:$UserInfo.Name/}')">DNS</a>{/tpl:if}{tpl:if($UserInfo.RaceStatus!=0)} | <a href="javascript:;" onclick="UserRaceStatusRestore('{tpl:$UserInfo.ApplyId/}','{tpl:$UserInfo.Name/}')">恢复</a>{/tpl:if} | <a href="javascript:void(0);" onclick="UserRaceDelete('{tpl:$UserInfo.ApplyId/}','{tpl:$UserInfo.Name/}')">退赛</a></th>
  </tr>
  {/tpl:loop}
  <tr class="noborder"><td colspan = 7><button type="submit" id="race_user_list_update_submit">提交更新</button></td>
  </tr>
  {tpl:else}
  <tr>
    <th align="center" class="rowtip">尚未有选手报名<a href="javascript:;" onclick="RaceUserUpload('{tpl:$RaceInfo.RaceId/}','{tpl:$RaceInfo.RaceName/}','{tpl:$RaceGroupId/}')">点此导入报名</a>
    </th>
    </th>
  </tr>
  {/tpl:if}
</table>
</form>
{tpl:tpl contentFooter/}
