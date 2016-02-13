{tpl:tpl contentHeader/}
<div class="br_bottom"></div>
<form id="user_auth_form" name="user_auth_form" action="{tpl:$this.sign/}&ac=user.auth" metdod="post">
    <input type="hidden" name="UserId" value="{tpl:$UserInfo.user_id/}" />
    <table width="99%" align="center" class="table table-bordered table-striped" widtd="99%">
        <tr class="hover">
            <th align="center" class="rowtip">用户昵称</th>
            <td align="left">{tpl:$UserInfo.nick_name/}</td>
        </tr>
        <tr class="hover">
            <th align="center" class="rowtip">真实姓名</th>
            <td align="left"><input name="UserRealName" type="text" class="span2" id="UserRealName" value="{tpl:$UserInfo.name/}" size="50" /></td>
        </tr>
        <tr class="hover">
            <th align="center" class="rowtip">用户性别</th>
            <td align="left">
                <select name="UserSex" class="span2" size="1">
                    {tpl:loop $SexList $SexSymble $SexName}
                    <option value="{tpl:$SexSymble/}" {tpl:if($UserInfo.sex==$SexSymble)}selected="selected"{/tpl:if}>{tpl:$SexName/}</option>
                    {/tpl:loop}
        </tr>
        <tr class="hover">
            <th align="center" class="rowtip">实名认证状态</th>
            <td align="left">
                <select name="UserAuthStatus" class="span2" size="1">
                    {tpl:loop $AuthStatusList $AuthStatus $AuthStatusName}
                    <option value="{tpl:$AuthStatus/}" {tpl:if($UserInfo.auth_state==$AuthStatus)}selected="selected"{/tpl:if}>{tpl:$AuthStatusName/}</option>
                    {/tpl:loop}
                </select>
            </td>
        </tr>
        <tr class="hover">
            <th align="center" class="rowtip">证件类型</th>
            <td align="left">
                <select name="UserAuthIdType" class="span2" size="1">
                    {tpl:loop $AuthIdTypesList $AuthIdTypeSymble $AuthIdTypeName}
                    <option value="{tpl:$AuthIdTypeSymble/}" >{tpl:$AuthIdTypeName/}</option>
                    {/tpl:loop}
        </tr>
        <tr class="hover">
            <th align="center" class="rowtip">证件号码</th>
            <td align="left"><input name="UserAuthIdNo" type="text" class="span4" id="UserAuthIdNo" value="" size="50" /></td>
        </tr>
        <tr class="hover">
            <th align="center" class="rowtip">用户生日</th>
            <td align="left">
                <input type="text" name="UserBirthDay" value="{tpl:$UserInfo.birth_day/}" class="input-medium"
                       onFocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:'yyyy-MM-dd'})" >
            </td>
        </tr>
        <tr class="hover">
            <th align="center" class="rowtip">证件有效期</th>
            <td align="left">
                <input type="text" name="UserAuthExpireDay" value="{tpl:$UserInfo.expire_day/}" class="input-medium"
                       onFocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:'yyyy-MM-dd'})" >
            </td>
        </tr>
        <tr class="hover">
            <th align="center" class="rowtip">说明</th>
            <td align="left"><input name="UserAuthReason" type="text" class="span4" id="UserAuthReason" value="" size="50" /></td>
        </tr>
        <tr class="noborder"><td></td>
            <td><button type="submit" id="user_auth_submit">提交</button></td>
        </tr>
    </table>
</form>

<script type="text/javascript">
    $('#user_auth_submit').click(function(){
        var options = {
            dataType:'json',
            beforeSubmit:function(formData, jqForm, options) {
            },
            success:function(jsonResponse) {
                if (jsonResponse.errno) {
                    var errors = [];
                    errors[1] = '无此用户，请修正后再次提交';
                    errors[2] = '必须选择一个性别，请修正后再次提交';
                    errors[3] = '必须选择一个证件类型，请修正后再次提交';
                    errors[4] = '必须选择一个审核结果，请修正后再次提交';
                    errors[5] = '必须填写一个有效的证件号码，请修正后再次提交';
                    errors[6] = '必须填写一个有效的生日，请修正后再次提交';
                    errors[7] = '必须填写一个有效的证件有效期，请修正后再次提交';
                    errors[8] = '必须填写拒绝理由，请修正后再次提交';
                    errors[9] = '入库失败，请修正后再次提交';
                    errors[8] = '该用户已经实名认证过，无需多次认证';
                    divBox.alertBox(errors[jsonResponse.errno],function(){});
                } else {
                    var message = '实名认证提交成功';
                    divBox.confirmBox({content:message,ok:function(){windowParent.getRightHtml('{tpl:$this.sign/}');}});
                }
            }
        };
        $('#user_auth_form').ajaxForm(options);
    });
</script>
{tpl:tpl contentFooter/}