{tpl:tpl contentHeader/}
<script type="text/javascript">
    function userAuth(uid){
        userAuthBox = divBox.showBox('{tpl:$this.sign/}&ac=user.auth.submit&UserId=' + uid, {title:'实名认证-提交',width:500,height:600});
    }
</script>
<div class="br_bottom"></div>
    <table width="99%" align="center" class="table table-bordered table-striped" widtd="99%">
        <tr class="hover">
            <th align="center" class="rowtip" rowspan="7" colspan="2"><img src="{tpl:$UserInfo.thumb/}" width='200' height='160'/></th>
        </tr>
        <tr class="hover">
            <th align="center" class="rowtip">用户姓名</th>
            <td align="left">{tpl:$UserInfo.name/}</td>
        </tr>
        <tr class="hover">
            <th align="center" class="rowtip">用户性别</th>
            <td align="left">{tpl:$UserInfo.sex/}</td>
        </tr>
        <tr class="hover">
            <th align="center" class="rowtip">联系电话</th>
            <td align="left">{tpl:$UserInfo.phone/}</td>
        </tr>
        <tr class="hover">
            <th align="center" class="rowtip">实名认证状态</th>
            <td align="left">{tpl:$UserInfo.AuthStatus/}</td>
        </tr>
        <tr class="hover">
            <th align="center" class="rowtip">实名认证提交时间</th>
            <td align="left">{tpl:$UserAuthInfo.submit_time/}</td>
        </tr>
        <tr class="hover">
            <th align="center" class="rowtip">联系电话</th>
            <td align="left">{tpl:$UserInfo.phone/}</td>
        </tr>
        <tr class="hover">
            <th align="center" class="rowtip">
                {tpl:if($UserAuthInfo.submit_img1!="")}
                <a href="{tpl:$UserAuthInfo.submit_img1/}" target="_blank">点此查看图片1</a>
                    {tpl:else}
                        图片1：无
                    {/tpl:if}
            </th>
            <th align="center" class="rowtip">
                {tpl:if($UserAuthInfo.submit_img2!="")}
                <a href="{tpl:$UserAuthInfo.submit_img2/}" target="_blank">点此查看图片2</a>
                    {tpl:else}
                        图片2：无
                    {/tpl:if}
            </td>
            <th align="center" class="rowtip" colspan = 2>{tpl:if($UserInfo.auth_state=="1")}<a  href="javascript:;" onclick="userAuth('{tpl:$UserInfo.user_id/}')">审核</a>{/tpl:if}</th>
        </tr>
    </table>

{tpl:tpl contentFooter/}