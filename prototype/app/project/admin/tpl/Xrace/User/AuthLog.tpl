{tpl:tpl contentHeader/}
<script type="text/javascript">
    function userDetail(uid){
        userDetailBox = divBox.showBox('{tpl:$this.sign/}&ac=user.detail&UserId=' + uid, {title:'用户详情',width:600,height:400});
    }
    function userAuth(uid){
        userAuthBox = divBox.showBox('{tpl:$this.sign/}&ac=user.auth.info&UserId=' + uid, {title:'实名认证',width:600,height:400});
    }
</script>

</fieldset>
<fieldset><legend>实名认证记录</legend>
    <form action="{tpl:$this.sign/}&ac=auth.log" name="form" id="form" method="post">
        开始日期:<input type="text" name="StartDate" value="{tpl:$params.StartDate/}" class="input-medium"
                    onFocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:'yyyy-MM-dd'})" >
        结束日期:<input type="text" name="EndDate" value="{tpl:$params.EndDate/}" class="input-medium"
                    onFocus="WdatePicker({isShowClear:false,readOnly:true,dateFmt:'yyyy-MM-dd'})" >
        实名认证状态:<select name="AuthResult" class="span2" size="1">
            <option value=""  {tpl:if($params.AuthResult=="")}selected="selected"{/tpl:if}>全部</option>
            {tpl:loop $AuthLogIdStatusList $AuthResult $AuthResultName}
            <option value="{tpl:$AuthResult/}" {tpl:if($params.AuthResult==$AuthResult)}selected="selected"{/tpl:if}>{tpl:$AuthResultName/}</option>
            {/tpl:loop}
        </select>
        操作人:<select name="ManagerId" class="span2" size="1">
            <option value=""  {tpl:if($params.ManagerId==0)}selected="selected"{/tpl:if}>全部</option>
            {tpl:loop $ManagerList $ManagerId $ManagerInfo}
            <option value="{tpl:$ManagerId/}" {tpl:if($params.ManagerId==$ManagerId)}selected="selected"{/tpl:if}>{tpl:$ManagerInfo.name/}</option>
            {/tpl:loop}
        </select>

        <input type="submit" name="Submit" value="查询" />{tpl:$export_var/}
    </form>
    <table width="99%" align="center" class="table table-bordered table-striped">
      <tr>
        <th align="center" class="rowtip">认证记录Id</th>
        <th align="center" class="rowtip">提交认证时间</th>
        <th align="center" class="rowtip">用户账号</th>
        <th align="center" class="rowtip">认证结果</th>
        <th align="center" class="rowtip">操作时间</th>
        <th align="center" class="rowtip" colspan="2">证件照片</th>
        <th align="center" class="rowtip">说明</th>
      </tr>
    {tpl:loop $AuthLog.AuthLog $LogInfo}
      <tr class="hover">
        <td align="center">{tpl:$LogInfo.auth_id/}</td>
          <td align="center">{tpl:$LogInfo.submit_time/}</td>
          <td align="center">{tpl:$LogInfo.UserName/}</td>
          <td align="center">{tpl:$LogInfo.AuthResultName/}</td>
          <td align="center">{tpl:$LogInfo.op_time/}</td>
          <td align="center">{tpl:if($LogInfo.submit_img1!="")}
          <a href="{tpl:$LogInfo.submit_img1/}" target="_blank">点此查看图片1</a>
          {tpl:else}
              图片1：无
          {/tpl:if}
          </td>
          <td align="center">{tpl:if($LogInfo.submit_img2!="")}
              <a href="{tpl:$LogInfo.submit_img2/}" target="_blank">点此查看图片2</a>
              {tpl:else}
                  图片1：无
              {/tpl:if}
          </td>
          <td align="center">{tpl:$LogInfo.auth_resp/}</td>
      </tr>
    {/tpl:loop}
    <tr><th colspan="10" align="center" class="rowtip">{tpl:$page_content/}</th></tr>

</table>
</fieldset>
{tpl:tpl contentFooter/}
