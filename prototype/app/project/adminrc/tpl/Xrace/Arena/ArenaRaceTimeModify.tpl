{tpl:tpl contentHeader/}
<div class="br_bottom"></div>
<form id="arena_time_add_form" name="arena_time_add_form" action="{tpl:$this.sign/}&ac=arena.race.time.update" method="post">
<input type="hidden" name="ArenaId" id="ArenaId" value="{tpl:$ArenaInfo.ArenaId/}" />
<input type="hidden" name="id" id="id" value="{tpl:$id/}" />
<fieldset><legend>{tpl:$ArenaInfo.ArenaName/}-约战时间段添加</legend>
<table width="99%" align="center" class="table table-bordered table-striped" widtd="99%">
	<td>命名</td>
	<td align="left"  colspan="2"><input type="text" class="span2" name="name"  id="name" value="{tpl:$TimeInfo.name/}" size="50"/></td>
	</tr>
	<tr class="hover"><th align="center" class="rowtip">每日</th>
	<th align="center" class="rowtip" colspan = 2>
        {tpl:loop $WeekdayList $day $name}
		<input type="checkbox"  name="WeekdayList[{tpl:$day/}][Selected]"          {tpl:loop $TimeInfo.Weekday $weekday}{tpl:if($day==$weekday)}checked {/tpl:if}{/tpl:loop} value="1" /> {tpl:$name/}
        {/tpl:loop}
	</th>
</tr>
<tr class="hover"><th align="center" class="rowtip">开始时间</th><th align="center" class="rowtip">
		<select name="StartHour" size="1" class="span2">
            {tpl:loop $HourList $Hour}
				<option value="{tpl:$Hour/}" {tpl:if($TimeInfo.StartHour==$Hour)}selected="selected"{/tpl:if}>{tpl:$Hour/}</option>
            {/tpl:loop}
		</select></th>
	<th align="center" class="rowtip"><select name="StartMinute" size="1" class="span2">
            {tpl:loop $MinuteList $Minute}
			<option value="{tpl:$Minute/}" {tpl:if($TimeInfo.StartMinute==$Minute)}selected="selected"{/tpl:if}>{tpl:$Minute/}</option>
            {/tpl:loop}
		</select>
	</th>
</tr>
<tr class="hover"><th align="center" class="rowtip">结束时间</th><th align="center" class="rowtip">
		<select name="EndHour" size="1" class="span2">
			{tpl:loop $HourList $Hour}
			<option value="{tpl:$Hour/}" {tpl:if($TimeInfo.EndHour==$Hour)}selected="selected"{/tpl:if}>{tpl:$Hour/}</option>
			{/tpl:loop}
		</select></th>
	<th align="center" class="rowtip"><select name="EndMinute" size="1" class="span2">
			{tpl:loop $MinuteList $Minute}
			<option value="{tpl:$Minute/}" {tpl:if($TimeInfo.EndMinute==$Minute)}selected="selected"{/tpl:if}>{tpl:$Minute/}</option>
			{/tpl:loop}
		</select>
	</th>
</tr>
	<tr class="hover">
<tr class="noborder"><td></td>
<td   colspan = 2><button type="submit" id="arena_race_time_add_submit">提交</button></td>
</tr>
</table>
</form>

<script type="text/javascript">
$('#arena_race_time_add_submit').click(function(){
	var options = {
		dataType:'json',
		beforeSubmit:function(formData, jqForm, options) {
		},
		success:function(jsonResponse) {
			if (jsonResponse.errno) {
				var errors = [];
				errors[1] = '每日选项不能为空，请修正后再次提交';
				errors[9] = '入库失败，请修正后再次提交';
				divBox.alertBox(errors[jsonResponse.errno],function(){});
			} else {
				var message = '时间段修改成功';
                ArenaId=$("#ArenaId");
				divBox.confirmBox({content:message,ok:function(){windowParent.getRightHtml('{tpl:$this.sign/}'+ '&ac=arena.race.time.list&ArenaId=' + ArenaId.val());}});
			}
		}
	};
	$('#arena_time_add_form').ajaxForm(options);
});
</script>
{tpl:tpl contentFooter/}