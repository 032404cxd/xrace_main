{tpl:tpl contentHeader/}
<script type="text/javascript">
  function RaceUserUpload(rid,rname,gid){
    RaceModifyBox = divBox.showBox('{tpl:$this.sign/}&ac=race.user.upload.submit&RaceId=' + rid + '&RaceGroupId=' + gid, {title:'批量导入报名记录-'+rname,width:400,height:250});
  }
  function UserRaceDelete(uname,aid){
    deleteUserRaceBox = divBox.confirmBox({content:'确定将'+ uname + '退出比赛?',ok:function(){location.href = '{tpl:$this.sign/}&ac=user.race.delete&ApplyId=' + aid;}});
  }
  function UserRaceDeleteByRace(rid){
    deleteUserRaceByRaceBox = divBox.confirmBox({content:'确定全部退出比赛?',ok:function(){location.href = '{tpl:$this.sign/}&ac=user.race.delete.by.race&RaceId=' + rid;}});
  }
  function UserRaceDeleteByGroup(rid,gid,gname){
    deleteUserRaceByRaceBox = divBox.confirmBox({content:'确定'+gname+'全部退出比赛?',ok:function(){location.href = '{tpl:$this.sign/}&ac=user.race.delete.by.race&RaceId=' + rid + '&RaceGroupId=' + gid;}});
  }
  function RaceResultList(rid,uid,rname){
    RaceResultListBox = divBox.showBox('{tpl:$this.sign/}&ac=race.result.list&RaceId=' + rid + '&UserId=' + uid, {title:rname+'成绩单',width:800,height:750});
  }
</script>
<form action="{tpl:$this.sign/}&ac=race.user.list.update" name="race_user_list_update_form" id="race_user_list_update_form" method="post">
<input type="hidden" name="RaceId" id="RaceId" value="{tpl:$RaceInfo.RaceId/}" />
<input type="hidden" name="CurrentRaceGroupId" id="CurrentRaceGroupId" value="{tpl:$RaceGroupId/}" />

  <table width="99%" align="center" class="table table-bordered table-striped">
    <tr><th align="center" class="rowtip" colspan="5">共计选手{tpl:$RaceUserList.RaceUserList func="count(@@)"/}  <a href="javascript:void(0);" onclick="UserRaceDeleteByRace('{tpl:$RaceInfo.RaceId/}')">全部退赛</a></th></tr>
    {tpl:if(count($RaceUserList))}
  <tr>
    <th align="center" class="rowtip">姓名</th>
    <th align="center" class="rowtip">报名来源</th>
    <th align="center" class="rowtip">分组</th>
    <th align="center" class="rowtip">所属队伍</th>
    <th align="center" class="rowtip">报名时间</th>
    <th align="center" class="rowtip">选手号码</th>
    <th align="center" class="rowtip">计时芯片ID</th>
  </tr>
  {tpl:loop $RaceUserList.RaceUserList $Aid $UserInfo}
  <tr>
    <input type="hidden" name="UserList[{tpl:$Aid/}][ApplyId]" id="UserList[{tpl:$UserInfo.UserId/}][ApplyId]" value="{tpl:$UserInfo.ApplyId/}" />
    <th align="center" class="rowtip"><a href="javascript:;" onclick="RaceResultList('{tpl:$RaceInfo.RaceId/}','{tpl:$UserInfo.UserId/}','{tpl:$RaceInfo.RaceName/}')">{tpl:$UserInfo.Name/}</a>  <a href="javascript:void(0);" onclick="UserRaceDelete('{tpl:$UserInfo.Name/}','{tpl:$UserInfo.ApplyId/}')">退赛</a></th>
    <th align="center" class="rowtip">{tpl:$UserInfo.ApplySourceName/}</th>
    <th align="center" class="rowtip">{tpl:$UserInfo.RaceGroupName/} {tpl:if($UserInfo.RaceGroupId>0)}<a href="javascript:void(0);" onclick="UserRaceDeleteByGroup('{tpl:$RaceInfo.RaceId/}','{tpl:$UserInfo.RaceGroupId/}','{tpl:$UserInfo.RaceGroupName/}')">退赛</a>{/tpl:if}</th>
    <th align="center" class="rowtip">{tpl:$UserInfo.RaceTeamName/}</th>
    <th align="center" class="rowtip">{tpl:$UserInfo.ApplyTime/}</th>
    <th align="center" class="rowtip"><input type="text" class="span1" name="UserList[{tpl:$Aid/}][BIB]" id="UserList[{tpl:$UserInfo.UserId/}][BIB]" value="{tpl:$UserInfo.BIB/}" /></th>
    <th align="center" class="rowtip"><input type="text" class="span2" name="UserList[{tpl:$Aid/}][ChipId]" id="UserList[{tpl:$UserInfo.UserId/}][ChipId]" value="{tpl:$UserInfo.ChipId/}" /></th>
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
