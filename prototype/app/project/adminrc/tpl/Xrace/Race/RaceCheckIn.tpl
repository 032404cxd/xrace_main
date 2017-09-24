{tpl:tpl contentHeader/}
<script type="text/javascript">
</script>
<fieldset><legend>{tpl:$RaceInfo.RaceName/} 选手检录 {tpl:$CheckInStatusUrl/}</legend>
  <form id="check_in_form" name="check_in_form" action="{tpl:$this.sign/}&ac=race.check.in" method="post">
    <input type="hidden" name="RaceId" id="RaceId" value="{tpl:$RaceId/}" />
    <table width="99%" align="center" class="table table-bordered table-striped">
      <tr class="hover">
        <td><input type="text" class="span4" name="CheckInCode"  id="CheckInCode" value="" size="50" /></td>
        <td><button type="submit" id="check_in_submit">提交</button></td>
      </tr>
    </table>
  </form>
{tpl:tpl contentFooter/}
