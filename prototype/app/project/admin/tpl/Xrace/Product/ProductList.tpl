{tpl:tpl contentHeader/}
<script type="text/javascript">
  function ProductAdd(ptid){
    ProductAddBox = divBox.showBox('{tpl:$this.sign/}&ac=product.add&ProductTypeId=' + ptid , {title:'添加产品',width:400,height:200});
  }
  function ProductModify(pid){
    RaceModifyBox = divBox.showBox('{tpl:$this.sign/}&ac=product.modify&ProductId=' + pid, {title:'修改产品',width:400,height:200});
  }
  function ProductDelete(pid, pname){
    ProductDeleteBox = divBox.confirmBox({content:'是否删除 ' + pname + '?',ok:function(){location.href = '{tpl:$this.sign/}&ac=product.delete&ProductId=' + pid}});
  }
  function ProductSkuAdd(pid){
    ProductSkuAddBox = divBox.showBox('{tpl:$this.sign/}&ac=product.sku.add&ProductId=' + pid , {title:'添加产品Sku',width:400,height:300});
  }
</script>
<form action="{tpl:$this.sign/}&ac=product.update" name="form" id="form" method="post">
  <fieldset><legend> {tpl:$ProductTypeInfo.ProductTypeName/} 产品配置 </legend>
<table width="99%" align="center" class="table table-bordered table-striped">
  {tpl:if(count($ProductList[$ProductTypeId]))}
  <tr>
    <th align="center" class="rowtip">产品ID</th>
    <th align="center" class="rowtip">产品名称</th>
    <th align="center" class="rowtip">规格列表</th>
    <th align="center" class="rowtip">操作</th>
  </tr>
  {tpl:loop $ProductList[$ProductTypeId] $Pid $ProductInfo}
  <tr>
    <td align="center" class="rowtip">{tpl:$ProductInfo.ProductId/}</th>
    <td align="center" class="rowtip">{tpl:$ProductInfo.ProductName/}</th>
    <td align="center" class="rowtip">{tpl:if($ProductInfo.ProductSkuList!="")}{tpl:$ProductInfo.ProductSkuList/}{tpl:else}<a href="javascript:;" onclick="ProductSkuAdd('{tpl:$ProductInfo.ProductId/}')">点此添加SKU</a>{/tpl:if}</th>
    <td align="center"><a href="javascript:;" onclick="ProductDelete('{tpl:$ProductInfo.ProductId/}','{tpl:$ProductInfo.ProductName/}')">删除</a> |  <a href="javascript:;" onclick="ProductModify('{tpl:$ProductInfo.ProductId/}');">修改</a> | <a href="?ProductId={tpl:$ProductInfo.ProductId/}&ctl=xrace/product&ac=product.sku.list">产品Sku配置</a></td>
  </tr>
  {/tpl:loop}
  <tr>
    <th align="center" class="rowtip" colspan="4">  <a href="javascript:;" onclick="ProductAdd('{tpl:$ProductTypeId/}')">点此添加商品</a>
    </th>
  </tr>
  {tpl:else}
  <tr>
    <th align="center" class="rowtip">本商品类型尚未配置任何商品<a href="javascript:;" onclick="ProductAdd('{tpl:$ProductTypeId/}')">点此添加商品</a>
    </th>
    </th>
  </tr>
  {/tpl:if}
</table>
</fieldset>
</form>
{tpl:tpl contentFooter/}