{tpl:tpl contentHeader/}
<script type="text/javascript">
  function LicenseAdd(uid){
    LicenseAddBox = divBox.showBox('{tpl:$this.sign/}&ac=license.add&UserId=' + uid , {title:'添加执照',width:400,height:350});
  }
  function LicenseModify(lid,uid){
    LicenseModifyBox = divBox.showBox('{tpl:$this.sign/}&ac=license.modify&LicenseId=' + lid  , {title:'修改执照',width:400,height:350});
  }
  function LicenseDelete(lid,uid){
    LicenseDeleteBox = divBox.showBox('{tpl:$this.sign/}&ac=license.unset&LicenseId=' + lid, {title:'删除执照',width:400,height:350});
  }
  function LicenseLog(lid,uid){
    LicenseLogBox = divBox.showBox('{tpl:$this.sign/}&ac=license.update.log&LicenseId=' + lid, {title:'操作记录',width:400,height:350});
  }
</script>
<form action="{tpl:$this.sign/}&ac=license.update" name="form" id="form" method="post">
  <fieldset><legend> {tpl:$UserInfo.name/}的执照列表 </legend>
<table width="99%" align="center" class="table table-bordered table-striped">
  {tpl:if(count($UserLicenseList['UserLicenseList']))}
  <tr>
    <th align="center" class="rowtip">执照ID</th>
    <th align="center" class="rowtip">所属赛事</th>
    <th align="center" class="rowtip">所属组别</th>
    <th align="center" class="rowtip">发放人员</th>
    <th align="center" class="rowtip">执照发放时间</th>
    <th align="center" class="rowtip">最后更新时间</th>
    <th align="center" class="rowtip">执照有效期</th>
    <th align="center" class="rowtip">执照状态</th>
    <th align="center" class="rowtip">操作</th>
  </tr>
  {tpl:loop $UserLicenseList['UserLicenseList'] $LicenseId $LicenseInfo}
  <tr>
    <td align="center" class="rowtip">{tpl:$LicenseInfo.LicenseId/}</th>
    <td align="center" class="rowtip">{tpl:$LicenseInfo.RaceCatalogName/}</th>
    <td align="center" class="rowtip">{tpl:$LicenseInfo.RaceGroupName/}</th>
    <td align="center" class="rowtip">{tpl:$LicenseInfo.ManagerName/}</th>
    <td align="center" class="rowtip">{tpl:$LicenseInfo.LicenseAddTime/}</th>
    <td align="center" class="rowtip">{tpl:$LicenseInfo.LastUpdateTime/}</th>
    <td align="center" class="rowtip">{tpl:$LicenseInfo.LicenseStartDate/} - {tpl:$LicenseInfo.LicenseEndDate/}</th>
    <td align="center" class="rowtip">{tpl:$LicenseInfo.LicenseStatusName/}</th>        
    <td align="center" class="rowtip">{tpl:if(in_array($LicenseInfo.LicenseStatus,array(1,3)))}<a href="javascript:;" onclick="LicenseDelete('{tpl:$LicenseInfo.LicenseId/}')">删除</a> | <a href="javascript:;" onclick="LicenseModify('{tpl:$LicenseInfo.LicenseId/}')">修改</a> | {/tpl:if}<a href="javascript:;" onclick="LicenseLog('{tpl:$LicenseInfo.LicenseId/}')">日志</a></td>
  </tr>
  {/tpl:loop}
  <tr>
    <th align="center" class="rowtip" colspan="9">  <a href="javascript:;" onclick="LicenseAdd('{tpl:$UserId/}')">点此添加</a>
    </th>
  </tr>
  {tpl:else}
  <tr>
    <th align="center" class="rowtip">本用户尚未发放任何执照<a href="javascript:;" onclick="LicenseAdd('{tpl:$UserId/}')">点此添加</a>
    </th>
    </th>
  </tr>
  {/tpl:if}
</table>
</fieldset>
</form>
{tpl:tpl contentFooter/}