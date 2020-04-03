{tpl:tpl contentHeader/}
<div class="br_bottom"></div>
	<table width="99%" align="center" class="table table-bordered table-striped">
		<input type="hidden" name="LicenseId"  id="LicenseId" value="{tpl:$UserLicenseInfo.LicenseId/}" />
		<tr class="hover">
			<td align="center" class="rowtip">所属赛事</td>
			<td align="center" class="rowtip">{tpl:$UserLicenseInfo.RaceCatalogName/}
			</td>
		</tr>
		<tr class="hover">
			<td align="center" class="rowtip">所属组别</td>
			<td align="center" class="rowtip">{tpl:$UserLicenseInfo.RaceGroupName/}
			</td>
		</tr>
		<tr class="hover">
			<td align="center" class="rowtip">生效时间</td>
			<td align="center" class="rowtip">{tpl:$UserLicenseInfo.LicenseStartDate/} - {tpl:$UserLicenseInfo.LicenseEndDate/}
			</td>
		</tr>
		<tr class="hover">
			<td align="center" class="rowtip" colspan = 2>执照更新记录</td>
		</tr>
		{tpl:loop $UserLicenseInfo.comment.LicenseUpdateLog $UpdateLog}
		<tr class="hover">
			<td align="center" class="rowtip">{tpl:$UpdateLog.time/}</td>
			<td align="center" class="rowtip">{tpl:$UpdateLog.LogText/}</td>
		</tr>
		{/tpl:loop}
	</table>
<script type="text/javascript">
</script>
{tpl:tpl contentFooter/}