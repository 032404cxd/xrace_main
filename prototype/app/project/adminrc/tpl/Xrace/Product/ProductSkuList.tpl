{tpl:tpl contentHeader/}
<script type="text/javascript">
  function ProductSkuAdd(pid){
    ProductSkuAddBox = divBox.showBox('{tpl:$this.sign/}&ac=product.sku.add&ProductId=' + pid , {title:'添加产品Sku',width:400,height:350});
  }
  function ProductSkuModify(pid){
    ProductSkuModifyBox = divBox.showBox('{tpl:$this.sign/}&ac=product.sku.modify&ProductSkuId=' + pid, {title:'修改产品Sku',width:400,height:350});
  }
  function ProductSkuDelete(pid, pname,sname){
    ProductSkuDeleteBox = divBox.confirmBox({content:'是否删除 ' + pname + sname + '?',ok:function(){location.href = '{tpl:$this.sign/}&ac=product.sku.delete&ProductSkuId=' + pid}});
  }
</script>
  <fieldset><legend> {tpl:$ProductInfo.ProductName/} 产品配置 </legend>
<table width="99%" align="center" class="table table-bordered table-striped">
  {tpl:if(count($ProductSkuList[$ProductId]))}
  <tr>
    <th align="center" class="rowtip">产品SkuID</th>
    <th align="center" class="rowtip">产品Sku名称</th>
    <th align="center" class="rowtip">产品Sku说明</th>
    <th align="center" class="rowtip">操作</th>
  </tr>
  {tpl:loop $ProductSkuList[$ProductId] $Pid $ProductSkuInfo}
  <tr>
    <td align="center" class="rowtip">{tpl:$ProductSkuInfo.ProductSkuId/}</th>
    <td align="center" class="rowtip">{tpl:$ProductSkuInfo.ProductSkuName/}</th>
    <td align="center" class="rowtip">{tpl:$ProductSkuInfo.comment.ProductSkuComment/}</th>
    <td align="center"><a href="javascript:;" onclick="ProductSkuDelete('{tpl:$ProductSkuInfo.ProductSkuId/}','{tpl:$ProductInfo.ProductName/}','{tpl:$ProductSkuInfo.ProductSkuName/}')">删除</a> |  <a href="javascript:;" onclick="ProductSkuModify('{tpl:$ProductSkuInfo.ProductSkuId/}');">修改</a></td>
  </tr>
  {/tpl:loop}
  <tr>
    <th align="center" class="rowtip" colspan="4">  <a href="javascript:;" onclick="ProductSkuAdd('{tpl:$ProductId/}')">点此添加商品SKU</a>
    </th>
  </tr>
  {tpl:else}
  <tr>
    <th align="center" class="rowtip">本商品类型尚未配置任何商品<a href="javascript:;" onclick="ProductSkuAdd('{tpl:$ProductId/}')">点此添加商品SKU</a>
    </th>
    </th>
  </tr>
  {/tpl:if}
</table>
</fieldset>
{tpl:tpl contentFooter/}