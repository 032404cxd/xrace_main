{tpl:tpl contentHeader/}
<script type="text/javascript">
$(document).ready(function(){
	$('#add_app_os').click(function(){
		addAppBox = divBox.showBox('{tpl:$this.sign/}&ac=app.os.add', {title:'添加APP系统',width:800,height:600});
	});
});

function appOSDelete(t_id, t_name){
	deleteAppOSBox = divBox.confirmBox({content:'是否删除 ' + t_name + '?',ok:function(){location.href = '{tpl:$this.sign/}&ac=app.os.delete&AppOSId=' + t_id;}});
}

function appOSModify(mid){
	modifyAppOSBox = divBox.showBox('{tpl:$this.sign/}&ac=app.os.modify&AppOSId=' + mid, {title:'修改APP系统',width:800,height:600});
}


</script>

<fieldset><legend>操作</legend>
[ <a href="javascript:;" id="add_app_os">添加APP系统</a> ]
</fieldset>

<fieldset><legend>APP系统列表</legend>
<table width="99%" align="center" class="table table-bordered table-striped">
  <tr>
    <th align="center" class="rowtip">APP系统ID</th>
    <th align="center" class="rowtip">APP系统名称</th>
    <th align="center" class="rowtip">操作</th>
  </tr>

{tpl:loop $AppOSList $AppOSInfo}
  <tr class="hover">
    <td align="center">{tpl:$AppOSInfo.AppOSId/}</td>
    <td align="center">{tpl:$AppOSInfo.AppOSName/}</td>
    <td align="center"><a  href="javascript:;" onclick="appOSDelete('{tpl:$AppOSInfo.AppOSId/}','{tpl:$AppOSInfo.AppOSName/}')">删除</a> |  <a href="javascript:;" onclick="appOSModify('{tpl:$AppOSInfo.AppOSId/}');">修改</a></td>
  </tr>
{/tpl:loop}
</table>
</fieldset>
{tpl:tpl contentFooter/}
