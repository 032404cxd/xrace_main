{tpl:tpl contentHeader/}
<script type="text/javascript">
    function userDetail(uid){
        userDetailBox = divBox.showBox('{tpl:$this.sign/}&ac=user.detail&OrderId=' + uid, {title:'用户详情',width:600,height:400});
    }
    function userAuth(uid){
        userAuthBox = divBox.showBox('{tpl:$this.sign/}&ac=user.auth.info&OrderId=' + uid, {title:'实名认证',width:600,height:400});
    }
    function userTeamList(uid,uname){
        userTeamListBox = divBox.showBox('{tpl:$this.sign/}&ac=user.team.list&OrderId=' + uid, {title:uname+'的队伍列表',width:600,height:400});
    }
</script>

<fieldset><legend>操作</legend>
</fieldset>
<form action="{tpl:$this.sign/}" name="form" id="form" method="post">
    订单号:<input type="text" class="span2" name="OrderId" value="{tpl:$params.OrderId/}" />
    支付号:<input type="text" class="span2" name="PayId" value="{tpl:$params.PayId/}" />
    用户姓名:<input type="text" class="span2" name="Name" value="{tpl:$params.Name/}" />
    支付状态:<select name="IsPay" class="span2" size="1">
        <option value="-1">全部</option>
        {tpl:loop $PayStatusList $PayStatus $PayStatusName}
        <option value="{tpl:$PayStatus/}" {tpl:if($params.IsPay==$PayStatus)}selected="selected"{/tpl:if}>{tpl:$PayStatusName/}</option>
        {/tpl:loop}
    </select>
    取消状态:<select name="IsCancel" class="span2" size="1">
        <option value="-1">全部</option>
        {tpl:loop $CancelStatusList $CancelStatus $CancelStatusName}
        <option value="{tpl:$CancelStatus/}" {tpl:if($params.IsCancel==$CancelStatus)}selected="selected"{/tpl:if}>{tpl:$CancelStatusName/}</option>
        {/tpl:loop}
    </select>

    <input type="submit" name="Submit" value="查询" />{tpl:$export_var/}
</form>
<fieldset><legend>订单列表</legend>
<table width="99%" align="center" class="table table-bordered table-striped">
      <tr>
        <th align="center" class="rowtip">订单ID</th>
        <th align="center" class="rowtip">支付ID</th>
        <th align="center" class="rowtip">用户姓名</th>
        <th align="center" class="rowtip">支付金额</th>
        <th align="center" class="rowtip">支付状态</th>
        <th align="center" class="rowtip">取消状态</th>
        <th align="center" class="rowtip">联系电话</th>
          <th align="center" class="rowtip">下单时间</th>
          <th align="center" class="rowtip">支付时间</th>
      </tr>
    {tpl:loop $OrderList.OrderList $OrderInfo}
      <tr class="hover">
        <td align="center">{tpl:$OrderInfo.order_no/}</td>
        <td align="center">{tpl:$OrderInfo.trade_no/}</td>
        <td align="center">{tpl:$OrderInfo.Name/}</td>
        <td align="center">{tpl:$OrderInfo.amount_total/}</td>
        <td align="center">{tpl:$OrderInfo.PayStatusName/}</td>
        <td align="center">{tpl:$OrderInfo.CancelStatusName/}</td>
        <td align="center">{tpl:$OrderInfo.m_mobile/}</td>
        <td align="center">{tpl:$OrderInfo.createDateTime/}</td>
        <td align="center">{tpl:if($OrderInfo.isPay==1)}{tpl:$OrderInfo.payDateTime/}{tpl:else}尚未支付{/tpl:if}</td>
        <td align="center"><a  href="javascript:;" onclick="userDetail('{tpl:$OrderInfo.user_id/}')">详细</a>{tpl:if($OrderInfo.auth_state==1)} | <a  href="javascript:;" onclick="userAuth('{tpl:$OrderInfo.user_id/}')">审核</a>{/tpl:if} | {tpl:$OrderInfo.License/} | {tpl:$OrderInfo.Team/}</td>
      </tr>
    {/tpl:loop}
    <tr><th colspan="10" align="center" class="rowtip">{tpl:$page_content/}</th></tr>

</table>
</fieldset>
{tpl:tpl contentFooter/}
