{tpl:tpl contentHeader/}
<div class="br_bottom"></div>
<form id="user_password_update_form" name="user_password_update_form" action="{tpl:$this.sign/}&ac=user.password.update" method="post">
    <input type="hidden" name="UserId" value="{tpl:$UserInfo.user_id/}" />
    <table width="99%" align="center" class="table table-bordered table-striped" widtd="99%">
        <tr class="hover">
            <th align="center" class="rowtip">用户昵称</th>
            <td align="left">{tpl:$UserInfo.nick_name/}</td>
        </tr>
        <tr class="hover">
            <th align="center" class="rowtip">真实姓名</th>
            <td align="left">{tpl:$UserInfo.name/}</td>
        </tr>
        <tr class="hover">
            <th align="center" class="rowtip">用户密码</th>
            <td align="left"><input name="UserPassword" type="text" class="span2" id="UserPassword"  size="50" /></td>
        </tr>
        <tr class="hover">
            <th align="center" class="rowtip">重复用户密码</th>
            <td align="left"><input name="UserPasswordRepeat" type="text" class="span2" id="UserPasswordRepeat"  size="50" /></td>
        </tr>
        <tr class="noborder"><td></td>
            <td><button type="submit" id="user_password_update_submit">提交</button></td>
        </tr>
    </table>
</form>

<script type="text/javascript">
    $('#user_password_update_submit').click(function(){
        var options = {
            dataType:'json',
            beforeSubmit:function(formData, jqForm, options) {
            },
            success:function(jsonResponse) {
                if (jsonResponse.errno) {
                    var errors = [];
                    errors[4] = '无此用户，请修正后再次提交';
                    errors[2] = '两遍密码输入不一致，请修正后再次提交';
                    errors[3] = '密码长度不能小于6位，请修正后再次提交';
                    errors[5] = '与原密码一致，无需修改';
                    divBox.alertBox(errors[jsonResponse.errno],function(){});
                } else {
                    var message = '密码更新成功';
                    divBox.confirmBox({content:message,ok:function(){windowParent.getRightHtml('{tpl:$this.sign/}');}});
                }
            }
        };
        $('#user_password_update_form').ajaxForm(options);
    });
</script>
{tpl:tpl contentFooter/}