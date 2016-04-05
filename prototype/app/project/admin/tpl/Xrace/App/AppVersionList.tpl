{tpl:tpl contentHeader/}
<script type="text/javascript">
$(document).ready(function(){
	$('#add_app_type').click(function(){
		addAppVersionBox = divBox.showBox('{tpl:$this.sign/}&ac=app.version.add', {title:'添加APP版本',width:500,height:500});
	});
});

function appVersionDelete(t_id, t_name){
	deleteAppBox = divBox.confirmBox({content:'是否删除 ' + t_name + '?',ok:function(){location.href = '{tpl:$this.sign/}&ac=app.version.delete&AppVersionId=' + t_id;}});
}

function appVersionModify(mid){
	modifyAppVersionBox = divBox.showBox('{tpl:$this.sign/}&ac=app.version.modify&AppVersionId=' + mid, {title:'修改APP版本',width:500,height:500});
}


</script>

<fieldset><legend>操作</legend>
[ <a href="javascript:;" id="add_app_type">添加APP版本</a> ]
</fieldset>
<form action="{tpl:$this.sign/}" name="form" id="form" method="post">
    <select name="AppTypeId" size="1">
        <option value="0">全部</option>
        {tpl:loop $AppTypeList $AppTypeInfo}
        <option value="{tpl:$AppTypeInfo.AppTypeId/}" {tpl:if($AppTypeInfo.AppTypeId==$AppTypeId)}selected="selected"{/tpl:if}>{tpl:$AppTypeInfo.AppTypeName/}</option>
        {/tpl:loop}
    </select>
    <select name="AppOSId" size="1">
        <option value="0">全部</option>
        {tpl:loop $AppOSList $AppOSInfo}
        <option value="{tpl:$AppOSInfo.AppOSId/}" {tpl:if($AppOSInfo.AppOSId==$AppOSId)}selected="selected"{/tpl:if}>{tpl:$AppOSInfo.AppOSName/}</option>
        {/tpl:loop}
    </select>
    <input type="submit" name="Submit" value="查询" />
</form>
<fieldset><legend>APP版本列表</legend>
<table width="99%" align="center" class="table table-bordered table-striped">
  <tr>
    <th align="center" class="rowtip">APP版本ID</th>
    <th align="center" class="rowtip">APP版本名称</th>
    <th align="center" class="rowtip">APP系统</th>
    <th align="center" class="rowtip">APP类型</th>
    <th align="center" class="rowtip">APP下载路径</th>
    <th align="center" class="rowtip">操作</th>
  </tr>

{tpl:loop $AppVersionList $AppVersionInfo}
  <tr class="hover">
    <td align="center">{tpl:$AppVersionInfo.AppVersionId/} {tpl:if($AppVersionInfo.NewestVersion==1)}(最新){tpl:else}{/tpl:if}</td>
    <td align="center">{tpl:$AppVersionInfo.AppVersion/}</td>
    <td align="center">{tpl:$AppVersionInfo.AppOSName/}</td>
    <td align="center">{tpl:$AppVersionInfo.AppTypeName/}</td>
    <td align="center">{tpl:$AppVersionInfo.AppDownloadUrl/}</td>
    <td align="center"><a  href="javascript:;" onclick="appVersionDelete('{tpl:$AppVersionInfo.AppVersionId/}','{tpl:$AppVersionInfo.AppVersion/}')">删除</a> |  <a href="javascript:;" onclick="appVersionModify('{tpl:$AppVersionInfo.AppVersionId/}');">修改</a></td>
  </tr>
{/tpl:loop}
</table>
</fieldset>
{tpl:tpl contentFooter/}
