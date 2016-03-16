{tpl:tpl contentHeader/}
<script type="text/javascript">
  function ProductAdd(ptid){
    ProductAddBox = divBox.showBox('{tpl:$this.sign/}&ac=product.add&productTypeId=' + ptid , {title:'添加产品',width:400,height:350});
  }
  function ProductModify(pid,ptid){
    RaceModifyBox = divBox.showBox('{tpl:$this.sign/}&ac=product.modify&productId=' + pid + '&productTypeId=' + ptid, {title:'修改产品',width:400,height:350});
  }
  function ProductDelete(pid, pname){
    deleteAppBox = divBox.confirmBox({content:'是否删除 ' + pname + '?',ok:function(){location.href = '{tpl:$this.sign/}&ac=product.delete&productId=' + pid}});
  }
</script>
<form action="{tpl:$this.sign/}&ac=product.update" name="form" id="form" method="post">
  <fieldset><legend> 产品配置 </legend>
<table width="99%" align="center" class="table table-bordered table-striped">
  {tpl:if(count($ProductList[$productTypeId]))}
  <tr>
    <th align="center" class="rowtip">产品名称</th>
    <th align="center" class="rowtip">操作</th>
  </tr>
  {tpl:loop $ProductList[$productTypeId] $Pid $ProductInfo}
  <tr>
    <td align="center" class="rowtip">{tpl:$ProductInfo.ProductName/}</th> 
    <td align="center"><a href="javascript:;" onclick="ProductDelete('{tpl:$ProductInfo.ProductId/}','{tpl:$ProductInfo.ProductName/}')">删除</a> |  <a href="javascript:;" onclick="ProductModify('{tpl:$ProductInfo.ProductId/}','{tpl:$productTypeId/}');">修改</a></td>
  </tr>
  {/tpl:loop}
  <tr>
    <th align="center" class="rowtip" colspan="3">  <a href="javascript:;" onclick="ProductAdd('{tpl:$productTypeId/}')">点此添加</a>
    </th>
  </tr>
  {tpl:else}
  <tr>
    <th align="center" class="rowtip">本商品类型尚未配置任何商品  <a href="javascript:;" onclick="ProductAdd('{tpl:$productTypeId/}')">点此添加</a>
    </th>
    </th>
  </tr>
  {/tpl:if}
</table>
</fieldset>
</form>
{tpl:tpl contentFooter/}