{tpl:tpl contentHeader/}
<script type="text/javascript">
  function LicenseAdd(uid,gid){
    ProductAddBox = divBox.showBox('{tpl:$this.sign/}&ac=license.add&UserId=' + uid , {title:'添加执照',width:400,height:350});
  }
  function LicenseModify(uid,gid){
    RaceModifyBox = divBox.showBox('{tpl:$this.sign/}&ac=product.modify&productId=' + pid + '&productTypeId=' + ptid, {title:'修改产品',width:400,height:350});
  }
  function LicenseDelete(uid,gid){
    deleteAppBox = divBox.confirmBox({content:'是否删除 ' + pname + '?',ok:function(){location.href = '{tpl:$this.sign/}&ac=product.delete&productId=' + pid}});
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
    <td align="center"><a href="javascript:;" onclick="">删除</a> |  <a href="javascript:;" onclick="">修改</a></td>
  </tr>
  {/tpl:loop}
  <tr>
    <th align="center" class="rowtip" colspan="7">  <a href="javascript:;" onclick="ProductAdd('{tpl:$productTypeId/}')">点此添加</a>
    </th>
  </tr>
  {tpl:else}
  <tr>
    <th align="center" class="rowtip">本用户尚未配置任何执照  <a href="javascript:;" onclick="ProductAdd('{tpl:$productTypeId/}')">点此添加</a>
    </th>
    </th>
  </tr>
  {/tpl:if}
</table>
</fieldset>
</form>
{tpl:tpl contentFooter/}