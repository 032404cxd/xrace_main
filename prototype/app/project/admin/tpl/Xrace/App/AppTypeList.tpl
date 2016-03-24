{tpl:tpl contentHeader/}
<script type="text/javascript">
$(document).ready(function(){
	$('#add_app_type').click(function(){
		addAppTypeBox = divBox.showBox('{tpl:$this.sign/}&ac=app.type.add', {title:'添加APP类型',width:800,height:600});
	});
});

function appTypeDelete(t_id, t_name){
	deleteAppBox = divBox.confirmBox({content:'是否删除 ' + t_name + '?',ok:function(){location.href = '{tpl:$this.sign/}&ac=app.type.delete&AppTypeId=' + t_id;}});
}

function appTypeModify(mid){
	modifyAppTypeBox = divBox.showBox('{tpl:$this.sign/}&ac=app.type.modify&AppTypeId=' + mid, {title:'修改APP类型',width:800,height:600});
}


</script>

<fieldset><legend>操作</legend>
[ <a href="javascript:;" id="add_app_type">添加APP类型</a> ]
</fieldset>

<fieldset><legend>APP类型列表</legend>
<table width="99%" align="center" class="table table-bordered table-striped">
  <tr>
    <th align="center" class="rowtip">APP类型ID</th>
    <th align="center" class="rowtip">APP类型名称</th>
    <th align="center" class="rowtip">操作</th>
  </tr>

{tpl:loop $AppTypeList $AppTypeInfo}
  <tr class="hover">
    <td align="center">{tpl:$AppTypeInfo.AppTypeId/}</td>
    <td align="center">{tpl:$AppTypeInfo.AppTypeName/}</td>
    <td align="center"><a  href="javascript:;" onclick="appTypeDelete('{tpl:$AppTypeInfo.AppTypeId/}','{tpl:$AppTypeInfo.AppTypeName/}')">删除</a> |  <a href="javascript:;" onclick="appTypeModify('{tpl:$AppTypeInfo.AppTypeId/}');">修改</a></td>
  </tr>
{/tpl:loop}
</table>
</fieldset>
{tpl:tpl contentFooter/}
