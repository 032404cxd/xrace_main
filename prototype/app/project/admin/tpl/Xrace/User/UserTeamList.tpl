{tpl:tpl contentHeader/}
<script type="text/javascript">
    function UserTeamAdd(uid){
        userTeamAddBox = divBox.showBox('{tpl:$this.sign/}&ac=user.team.add&UserId=' + uid, {title:"加入队伍",width:400,height:300});
    }
</script>
<form action="{tpl:$this.sign/}&ac=race.user.list.update" name="race_user_list_update_form" id="race_user_list_update_form" method="post">
    <input type="hidden" name="UserId" id="UserId" value="{tpl:$UserInfo.UserId/}" />
    <table width="99%" align="center" class="table table-bordered table-striped">
        {tpl:if(count($UserTeamList))}
        <tr>
            <th align="center" class="rowtip">所属队伍</th>
            <th align="center" class="rowtip">所属赛事</th>
            <th align="center" class="rowtip">所属组别</th>
            <th align="center" class="rowtip">入队时间</th>
        </tr>
        {tpl:loop $UserTeamList $Aid $UserTeamInfo}
        <tr>
            <th align="center" class="rowtip">{tpl:$UserTeamInfo.RaceTeamName/}</th>
            <th align="center" class="rowtip">{tpl:$UserTeamInfo.RaceCatalogName/}</th>
            <th align="center" class="rowtip">{tpl:$UserTeamInfo.RaceGroupName/}</th>
            <th align="center" class="rowtip">{tpl:$UserTeamInfo.InTime/}</th>
        </tr>
        {/tpl:loop}
        <tr>
            <th align="center" class="rowtip" colspan="4"><a href="javascript:;" onclick="UserTeamAdd('{tpl:$UserInfo.user_id/}')">点此参加</a></th>
        </tr>
        {tpl:else}
        <tr>
            <th align="center" class="rowtip">尚未参加任何队伍<a href="javascript:;" onclick="UserTeamAdd('{tpl:$UserInfo.user_id/}')">点此参加</a></th>
        </tr>
        {/tpl:if}
    </table>
</form>
<script type="text/javascript">
    $('#race_user_list_update_submit').click(function(){
        var options = {
            dataType:'json',
            beforeSubmit:function(formData, jqForm, options) {
            },
            success:function(jsonResponse) {
                if (jsonResponse.errno) {
                    var errors = [];
                    errors[1] = '赛事组别名称不能为空，请修正后再次提交';
                    errors[2] = '赛事组别ID无效，请修正后再次提交';
                    errors[3] = '请选择一个有效的赛事，请修正后再次提交';
                    errors[9] = '入库失败，请修正后再次提交';
                    divBox.alertBox(errors[jsonResponse.errno],function(){});
                } else {
                    var message = '修改赛事组别成功';
                    divBox.confirmBox({content:message,ok:function(){windowParent.getRightHtml('{tpl:$this.sign/}');}});
                }
            }
        };
        $('#race_user_list_update_form').ajaxForm(options);
    });
</script>
{tpl:tpl contentFooter/}
