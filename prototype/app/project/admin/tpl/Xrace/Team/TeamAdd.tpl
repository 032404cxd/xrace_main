{tpl:tpl contentHeader/}
<div class="br_bottom"></div>
<form id="team_add_form" name="team_add_form" action="{tpl:$this.sign/}&ac=team.insert" method="post">
    <table width="99%" align="center" class="table table-bordered table-striped">
        <tr class="hover">
            <td>队伍名称</td>
            <td align="left"><input type="text"  class="span3"  name="TeamName"  id="TeamName" value=""  /></td>
        </tr>
        <tr class="hover">
            <td>txt文件导入队员</td>
            <td align="left"><input name="UserList[1]" type="file" class="span2" id="UserList[1]" /></td>
        </tr>
        <tr class="hover">
            <td></td>
            <td><button type="submit" id="team_add_submit">提交</button></td>
        </tr>
    </table>
</form>

<script type="text/javascript">
    $('#team_add_submit').click(function(){
        var options = {
            dataType:'json',
            beforeSubmit:function(formData, jqForm, options) {},
            success:function(jsonResponse) {
                if (jsonResponse.errno) {
                    var errors = [];
                    errors[1] = '队伍名称不能为空，请修正后再次提交';
                    errors[2] = '队伍名称已经被使用，请修正后再次提交';
                    errors[3] = '创建者的信息错误，无法找到用户，请修正后再次提交';
                    errors[9] = '入库失败，请修正后再次提交';
                    divBox.alertBox(errors[jsonResponse.errno],function(){});
                } else {
                    var message = '导入完毕，更新';
                    divBox.confirmBox({content:message,ok:function(){windowParent.getRightHtml('{tpl:$this.sign/}');}});
                }
            }
        };
        $('#team_add_form').ajaxForm(options);
    });
</script>
{tpl:tpl contentFooter/}

