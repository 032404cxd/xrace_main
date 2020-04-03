{tpl:tpl contentHeader/}
<div class="br_bottom"></div>


    <table width="99%" align="center" class="table table-bordered table-striped" widtd="99%">
        {tpl:if(count($OrderDetailList))}
        <tr class="hover">
            <th align="center" class="rowtip" colspan="4">子订单记录</th>
        </tr>
        <tr class="hover">
            <th align="center" class="rowtip">订单类型</th>
            <th align="center" class="rowtip">子订单ID</th>
            <th align="center" class="rowtip">单价</th>
            <th align="center" class="rowtip">数量</th>
            <th align="center" class="rowtip">子订单总价</th>
            <th align="center" class="rowtip">下单时间</th>
        </tr>
            {tpl:loop $OrderDetailList $OrderDetailInfo}
                <tr class="hover">
                    <td align="left">{tpl:$OrderDetailInfo.OrderType/}</th>
                    <td align="left">{tpl:$OrderDetailInfo.id/}</th>
                    <td align="left">{tpl:$OrderDetailInfo.price/}</th>
                    <td align="left">{tpl:$OrderDetailInfo.number/}</th>
                    <td align="left">{tpl:$OrderDetailInfo.price_sub/}</th>
                    <td align="left">{tpl:$OrderDetailInfo.addtime/}</th>
                </tr>
            {/tpl:loop}

        {/tpl:if}


    </table>

{tpl:tpl contentFooter/}