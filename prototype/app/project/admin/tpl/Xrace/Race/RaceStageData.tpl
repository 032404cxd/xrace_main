{tpl:tpl contentHeader/}
<script type="text/javascript">
    function UpdateStageData(sid, sname){
        updateDataBox = divBox.confirmBox({content:'是否更新 ' + sname + '的成绩单?',ok:function(){location.href = '{tpl:$this.sign/}&ac=update.stage.data&RaceStageId=' + sid;}});
    }
</script>
<fieldset><legend>{tpl:$RaceStageInfo.RaceStageName/} <a href="{tpl:$this.sign/}">返回</a> | <a href="javascript:;" onclick="UpdateStageData('{tpl:$RaceStageInfo.RaceStageId/}','{tpl:$RaceStageInfo.RaceStageName/}')"> 数据更新</a></legend>
    <?php
      $FC4->renderChart();
        $FC->renderChart();
    ?>
    <table width="99%" align="center" class="table table-bordered table-striped">
        <tr>
            <th align="center" class="rowtip">比赛</th>
            <th align="center" class="rowtip">分组</th>
            <th align="center" class="rowtip">报名人数</th>
            <th align="center" class="rowtip">芯片发放</th>
            <th align="center" class="rowtip">比赛人数</th>
            <th align="center" class="rowtip">完赛人数</th>
            <th align="center" class="rowtip">参赛比例</th>
            <th align="center" class="rowtip">完赛比例</th>
        </tr>

        {tpl:loop $DataList.Data.DataList.RaceList $RaceId $RaceInfo}
        <tr>
            <th align="center" class="rowtip"  rowspan = {tpl:$RaceInfo.RaceGroupList func="count(@@)+1" /}>{tpl:$RaceInfo.RaceName/}</th>
        </tr>
        {tpl:loop $RaceInfo.RaceGroupList $RaceGroupId $RaceGroupInfo}
        <tr>
            <th align="center" class="rowtip" >{tpl:$RaceGroupInfo.RaceGroupName/}</th>
            <th align="center" class="rowtip" >{tpl:$RaceGroupInfo.Data.RaceUser/}</th>
            <th align="center" class="rowtip" >{tpl:$RaceGroupInfo.Data.ChipedUser/}</th>
            <th align="center" class="rowtip" >{tpl:$RaceGroupInfo.Data.RacedUser/}</th>
            <th align="center" class="rowtip" >{tpl:$RaceGroupInfo.Data.FinishedUser/}</th>
            <th align="center" class="rowtip" >{tpl:$RaceGroupInfo.Data.RaceRate/}</th>'
            <th align="center" class="rowtip" >{tpl:$RaceGroupInfo.Data.FinishRate/}</th>'
        </tr>
    {/tpl:loop}
        {/tpl:loop}


    </table>
</fieldset>
{tpl:tpl contentFooter/}
