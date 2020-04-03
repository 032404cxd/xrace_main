{tpl:tpl contentHeader/}
<div class="br_bottom"></div>
<form id="user_mobile_update_form" name="user_mobile_update_form" action="{tpl:$this.sign/}&ac=user.mobile.update" method="post">
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
            <th align="center" class="rowtip">手机号码</th>
            <td align="left"><input name="phone" type="text" class="span2" id="phone" value="{tpl:$UserInfo.phone/}" /></td>
        </tr>
        <tr class="noborder"><td></td>
            <td><button type="submit" id="user_mobile_update_submit">提交</button></td>
        </tr>
    </table>
</form>

<script type="text/javascript">
    $('#user_mobile_update_submit').click(function(){
        var options = {
            dataType:'json',
            beforeSubmit:function(formData, jqForm, options) {
            },
            success:function(jsonResponse) {
                if (jsonResponse.errno) {
                    var errors = [];
                    errors[4] = '无此用户，请修正后再次提交';
                    errors[3] = '手机号码长度必须8-12位之间，请修正后再次提交';
                    errors[5] = '与原手机号码一致，无需修改';
                    divBox.alertBox(errors[jsonResponse.errno],function(){});
                } else {
                    var message = '手机号码更新成功';
                    divBox.confirmBox({content:message,ok:function(){windowParent.getRightHtml('{tpl:$this.sign/}');}});
                }
            }
        };
        $('#user_mobile_update_form').ajaxForm(options);
    });
</script>
{tpl:tpl contentFooter/}