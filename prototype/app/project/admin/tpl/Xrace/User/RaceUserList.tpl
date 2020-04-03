{tpl:tpl contentHeader/}

<fieldset><legend>操作</legend>
</fieldset>
<form action="{tpl:$this.sign/}&ac=race.user.list" name="form" id="form" method="post">
    姓名:<input type="text" class="span2" name="Name" value="{tpl:$params.Name/}" />
    昵称:<input type="text" class="span2" name="NickName" value="{tpl:$params.NickName/}" />
    性别:<select name="Sex" class="span2" size="1">
        <option value="-1" {tpl:if($params.Sex==-1)}selected="selected"{/tpl:if}>全部</option>
        {tpl:loop $SexList $SexSymble $SexName}
        <option value="{tpl:$SexSymble/}" {tpl:if($params.Sex==$SexSymble)}selected="selected"{/tpl:if}>{tpl:$SexName/}</option>
        {/tpl:loop}
    </select>

    <input type="submit" name="Submit" value="查询" />{tpl:$export_var/}
</form>
<fieldset><legend>用户列表</legend>
<table width="99%" align="center" class="table table-bordered table-striped">
      <tr>
        <th align="center" class="rowtip">用户ID</th>
        <th align="center" class="rowtip">真实姓名</th>
        <th align="center" class="rowtip">联系电话</th>
        <th align="center" class="rowtip">性别</th>
        <th align="center" class="rowtip">生日</th>
        <th align="center" class="rowtip">注册时间</th>
     </tr>
    {tpl:loop $UserList.UserList $UserInfo}
      <tr class="hover">
        <td align="center">{tpl:$UserInfo.RaceUserId/}</td>
        <td align="center">{tpl:$UserInfo.Name/}</td>
        <td align="center">{tpl:$UserInfo.ContactMobile/}</td>
        <td align="center">{tpl:$UserInfo.Sex/}</td>
        <td align="center">{tpl:$UserInfo.Birthday/}</td>
        <td align="center">{tpl:$UserInfo.RegTime/}</td>

      </tr>
    {/tpl:loop}
    <tr><th colspan="10" align="center" class="rowtip">{tpl:$page_content/}</th></tr>

</table>
</fieldset>
{tpl:tpl contentFooter/}
