{tpl:tpl contentHeader/}
<script type="text/javascript">
    function aidCodeGen(sid){
        aidCodeGenBox = divBox.showBox('{tpl:$this.sign/}&ac=aid.code.gen&RaceStageId=' + sid, {title:'生成补给代码',width:300,height:200});
    }
    function aidCodeTypeAdd(sid){
        aidCodeGenBox = divBox.showBox('{tpl:$this.sign/}&ac=aid.code.type.add&RaceStageId=' + sid, {title:'创建新类型',width:300,height:150});
    }
</script>

<fieldset><legend>操作</legend>
    <a href="javascript:;" onclick="aidCodeGen('{tpl:$RaceStageId/}');"><生成></a> | <a href="javascript:;" onclick="aidCodeTypeAdd('{tpl:$RaceStageId/}');"><创建新分类></a> | {tpl:$Unused_export_var/}
</fieldset>
<form action="{tpl:$this.sign/}&ac=aid.code.list" name="form" id="form" method="post">
    <input type="hidden" class="span2" name="RaceStageId" id="RaceStageId" value="{tpl:$RaceStageId/}" />
    分类:<select name="AidCodeTypeId" id="AidCodeTypeId"class="span2" size="1">
        <option value="0}" {tpl:if($AidCodeTypeId==0)}selected="selected"{/tpl:if}>全部</option>
        {tpl:loop $AidCodeTypeList $Type $TypeInfo}
        <option value="{tpl:$Type/}" {tpl:if($AidCodeTypeId==$Type)}selected="selected"{/tpl:if}>{tpl:$TypeInfo.AidCodeTypeName/}</option>
        {/tpl:loop}
    </select>
    使用状态:<select name="AidCodeStatus" id="AidCodeStatus"class="span2" size="1">
        {tpl:loop $AidCodeStatusList $Status $StatusName}
        <option value="{tpl:$Status/}" {tpl:if($AidCodeStatus==$Status)}selected="selected"{/tpl:if}>{tpl:$StatusName/}</option>
        {/tpl:loop}
    </select>

    <input type="submit" name="Submit" value="查询" />
</form>
<fieldset><legend>补给代码列表</legend>
<table width="99%" align="center" class="table table-bordered table-striped">
      <tr>
        <th align="center" class="rowtip">代码</th>
          <th align="center" class="rowtip">分类</th>
        <th align="center" class="rowtip">生成时间</th>
        <th align="center" class="rowtip">分配时间</th>
      </tr>
    {tpl:loop $AidCodeList.AidCodeList $AidCodeInfo}
      <tr class="hover">
          <td align="center">{tpl:$AidCodeInfo.AidCode/}</td>
          <td align="center">{tpl:$AidCodeInfo.AidCodeTypeName/}</td>
          <td align="center">{tpl:$AidCodeInfo.GenTime/}</td>
          <td align="center">{tpl:if($AidCodeInfo.RaceUserId>0)}{tpl:$AidCodeInfo.ApplyTime/}/{tpl:$AidCodeInfo.UserName/}{tpl:else}尚未分配{/tpl:if}</td>
      </tr>
    {/tpl:loop}
    <tr><th colspan="10" align="center" class="rowtip">{tpl:$page_content/}</th></tr>

</table>
</fieldset>
{tpl:tpl contentFooter/}
