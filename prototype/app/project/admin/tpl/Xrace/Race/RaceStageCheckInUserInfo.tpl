{tpl:tpl contentHeader/}
<div class="br_bottom"></div>

<form id="check_in_form" name="check_in_form" action="{tpl:$this.sign/}&ac=user.check.in" method="post">
    <input type="hidden" name="RaceStageId" id="RaceStageId" value="{tpl:$bind.RaceStageId/}" />
    <input type="hidden" name="RaceUserId" id="RaceUserId" value="{tpl:$RaceUserInfo.RaceUserId/}" />
    <input type="hidden" name="CheckInType" id="CheckInType" value="{tpl:$bind.CheckInType/}" />

    <table width="99%" align="center" class="table table-bordered table-striped" widtd="99%">
        <tr class="hover">
            <th align="center" class="rowtip" rowspan="6">选手信息</th>
            <th align="center" class="rowtip">姓名</th>
            <td align="left">{tpl:if($RaceUserInfo.RaceUserId>0)}{tpl:$RaceUserInfo.Name/}{tpl:else}无此用户{/tpl:if}</td>
        </tr>
        <tr class="hover">
            <th align="center" class="rowtip">证件类型</th>
            <td align="left">{tpl:$RaceUserInfo.IdTypeName/}</td>
        </tr>
        <tr class="hover">
            <th align="center" class="rowtip">证件号</th>
            <td align="left">{tpl:$RaceUserInfo.IdNo/}</td>
        </tr>
        <tr class="hover">
            <th align="center" class="rowtip">联系电话</th>
            <td align="left">{tpl:$RaceUserInfo.ContactMobile/}</td>
        </tr>
        <tr class="hover">
            <th align="center" class="rowtip">注册时间</th>
            <td align="left">{tpl:$RaceUserInfo.RegTime/}</td>
        </tr>
        <tr class="hover">
            <th align="center" class="rowtip">性别</th>
            <td align="left">{tpl:$RaceUserInfo.Sex/}</td>
        </tr>

        {tpl:if($RaceUserInfo.RaceUserId>0)}
        <tr class="hover">
            <th align="center" colspan="3"><button type="submit" id="check_in_submit">提交</button></th>
        </tr>
        {/tpl:if}
    </table>
</form>
<script type="text/javascript">
    $('#check_in_submit').click(function(){
        var options = {
            dataType:'json',
            beforeSubmit:function(formData, jqForm, options) {},
            success:function(jsonResponse) {
                if (jsonResponse.errno) {
                    var errors = [];
                    errors[1] = '签到失败';
                    divBox.alertBox(errors[jsonResponse.errno],function(){});
                } else {
                    var message = '签到成功';
                    RaceStageId=$("#RaceStageId");
                    CheckInType=$("#CheckInType");
                    divBox.confirmBox({content:message,ok:function(){windowParent.getRightHtml('{tpl:$this.sign/}'+ '&ac=race.stage.user.check.in.BIB&RaceStageId=' + RaceStageId.val() + '&RaceUserId=' + jsonResponse.RaceUserId + '&CheckInType=' + CheckInType.val());}});
                }
            }
        };
        $('#check_in_form').ajaxForm(options);
    });
</script>
{tpl:tpl contentFooter/}