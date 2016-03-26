{tpl:tpl contentHeader/}
<script type="text/javascript">
  function LicenseAdd(uid){
    LicenseAddBox = divBox.showBox('{tpl:$this.sign/}&ac=license.add&UserId=' + uid , {title:'添加执照',width:400,height:350});
  }
  function LicenseModify(lid,uid){
    RaceModifyBox = divBox.showBox('{tpl:$this.sign/}&ac=license.modify&LicenseId=' + lid + '&UserId=' + uid , {title:'修改执照',width:400,height:350});
  }
  function LicenseDelete(lid,uid){
    deleteAppBox = divBox.showBox('{tpl:$this.sign/}&ac=license.unset&LicenseId=' + lid+ '&UserId=' + uid, {title:'删除执照',width:400,height:350});
  }
</script>
<form action="{tpl:$this.sign/}&ac=license.update" name="form" id="form" method="post">
  <fieldset><legend> 执照配置 </legend>
<table width="99%" align="center" class="table table-bordered table-striped">
  {tpl:if(count($UserLicenseList['UserLicense']))}
  <tr>
    <th align="center" class="rowtip">执照ID</th>
    <th align="center" class="rowtip">所属赛事组别</th>
    <th align="center" class="rowtip">发放人员</th>
    <th align="center" class="rowtip">执照发放时间</th>
    <th align="center" class="rowtip">执照有效期时</th>
    <th align="center" class="rowtip">执照状态</th>
    <th align="center" class="rowtip">操作</th>
    
  </tr>
  {tpl:loop $UserLicenseList['UserLicense'] $LicenseId $LicenseInfo}
  <tr>
    <td align="center" class="rowtip">{tpl:$LicenseInfo.LicenseId/}</th>
    <td align="center" class="rowtip">{tpl:$LicenseInfo.GroupName/}</th>        
    <td align="center" class="rowtip">{tpl:$LicenseInfo.ManagerName/}</th>
    <td align="center" class="rowtip">{tpl:$LicenseInfo.LicenseAddDate/}</th>
    <td align="center" class="rowtip">{tpl:$LicenseInfo.LicenseStartDate/} - {tpl:$LicenseInfo.LicenseEndDate/}</th>
    <td align="center" class="rowtip">{tpl:$LicenseInfo.LicenseStatusName/}</th>        
    <td align="center">{tpl:if($LicenseInfo.LicenseStatus != 2)}<a href="javascript:;" onclick="LicenseDelete('{tpl:$LicenseInfo.LicenseId/}','{tpl:$UserId/}')">删除</a>{/tpl:if}{tpl:if($LicenseInfo.LicenseStatus == 0)}| <a href="javascript:;" onclick="LicenseModify('{tpl:$LicenseInfo.LicenseId/}','{tpl:$UserId/}')">修改</a>{/tpl:if}</td>
  </tr>
  {/tpl:loop}
  <tr>
    <th align="center" class="rowtip" colspan="7">  <a href="javascript:;" onclick="LicenseAdd('{tpl:$UserId/}')">点此添加</a>
    </th>
  </tr>
  {tpl:else}
  <tr>
    <th align="center" class="rowtip">本用户尚未配置任何执照  <a href="javascript:;" onclick="LicenseAdd('{tpl:$UserId/}')">点此添加</a>
    </th>
    </th>
  </tr>
  {/tpl:if}
</table>
</fieldset>
</form>
{tpl:tpl contentFooter/}