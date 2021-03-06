{tpl:tpl contentHeader/}
<script type="text/javascript">
    function orderDetail(oid){
        orderDetailBox = divBox.showBox('{tpl:$this.sign/}&ac=order.detail&OrderId=' + oid, {title:'订单详情',width:600,height:400});
    }
</script>

<fieldset><legend>操作</legend>
</fieldset>
<form action="{tpl:$this.sign/}" name="form" id="form" method="post">
    订单号:<input type="text" class="span2" name="OrderId" value="{tpl:$params.OrderId/}" />
    支付号:<input type="text" class="span2" name="PayId" value="{tpl:$params.PayId/}" />
    用户姓名:<input type="text" class="span2" name="Name" value="{tpl:$params.Name/}" />
    对应赛事:<select name="RaceCatalogId" size="1" class="span2">
        <option value="0">全部</option>
        {tpl:loop $RaceCatalogList $RaceCatalogInfo}
        <option value="{tpl:$RaceCatalogInfo.RaceCatalogId/}" {tpl:if($RaceCatalogInfo.RaceCatalogId==$params.RaceCatalogId)}selected="selected"{/tpl:if}>{tpl:$RaceCatalogInfo.RaceCatalogName/}</option>
        {/tpl:loop}
    </select>
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
        <th align="center" class="rowtip">取消状态</th>
        <th align="center" class="rowtip">联系电话</th>
          <th align="center" class="rowtip">下单时间</th>
          <th align="center" class="rowtip">支付时间</th>
          <th align="center" class="rowtip">对应赛事</th>
          <th align="center" class="rowtip">操作</th>
      </tr>
    {tpl:loop $OrderList.OrderList $OrderInfo}
      <tr class="hover">
        <td align="center">{tpl:$OrderInfo.order_no/}</td>
        <td align="center">{tpl:$OrderInfo.trade_no/}</td>
        <td align="center">{tpl:$OrderInfo.Name/}</td>
        <td align="center">{tpl:$OrderInfo.amount_total/}</td>
        <td align="center">{tpl:$OrderInfo.CancelStatusName/}</td>
        <td align="center">{tpl:$OrderInfo.m_mobile/}</td>
        <td align="center">{tpl:$OrderInfo.createDateTime/}</td>
        <td align="center">{tpl:if($OrderInfo.isPay==1)}{tpl:$OrderInfo.payDateTime/}{tpl:else}{tpl:$OrderInfo.PayStatusName/}{/tpl:if}</td>
        <td align="center">{tpl:$OrderInfo.RaceCatalogName/}</td>
        <td align="center"><a  href="javascript:;" onclick="orderDetail('{tpl:$OrderInfo.id/}')">详细</a></td>
      </tr>
    {/tpl:loop}
    <tr><th colspan="14" align="center" class="rowtip">{tpl:$page_content/}</th></tr>

</table>
</fieldset>
{tpl:tpl contentFooter/}
