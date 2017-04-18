<?php
/**
 * 订单管理相关mod层
 * @author 陈晓东 <cxd032404@hotmail.com>
 */


class Xrace_Mylaps extends Base_Widget
{
	//声明所用到的表
	protected $table = 'times';
    protected $table_sorted = 'times_sorted';

    public function getTimingData($params,$fields=array("*"))
    {
        //生成查询列
        $fields = Base_common::getSqlFields($fields);
        //获取需要用到的表名
        $table_to_process = Base_Widget::getDbTable($this->table);
        $table_to_process = str_replace($this->table,$params['prefix'].$this->table,$table_to_process)."_sorted";
        //获得芯片ID
        $whereChip = isset($params['Chip'])?" Chip = '".$params['Chip']."' ":"";
        $whereChipList = isset($params['ChipList'])?" Chip in (".$params['ChipList'].") ":"";
        $whereStart = isset($params['LastId'])?" Id >".$params['LastId']." ":"";
        $whereStartTime = isset($params['StartTime'])?" ChipTime >'".$params['StartTime']."' ":"";
        $whereEndTime = isset($params['EndTime'])?" ChipTime <='".$params['EndTime']."' ":"";

        $Limit = " limit 0,".$params['pageSize'];
        //所有查询条件置入数组
        $whereCondition = array($whereChip,$whereChipList,$whereStart,$whereStartTime,$whereEndTime);
        //生成条件列
        $where = Base_common::getSqlWhere($whereCondition);
        $sql = "SELECT $fields FROM $table_to_process where 1 ".$where." order by Id asc".$Limit;
        $return = $this->db->getAll($sql);
        return array("Record"=>$return,"sql"=>$sql);
    }
	//根据比赛ID生成该场比赛的MYLAPS计时数据
	public function genMylapsTimingInfo($RaceId,$Force = 0,$Cache = 0)
	{
        $Text = "RaceId:".$RaceId."\n";
        $oUser = new Xrace_UserInfo();
        $oRace = new Xrace_Race();
        $oCredit = new Xrace_Credit();
        //获取积分总表
        $CreditList = $oCredit->getCreditList(0,"CreditId,CreditName");
	     //总记录数量
        $TotalCount = 0;
		//程序执行开始时间
        $GenStart = microtime(true);
		//获取比赛信息
		$RaceInfo = $oRace->getRace($RaceId);
        if($RaceInfo['ToProcess']==1)
        {
            $update = $oRace->updateRace($RaceId,array("ToProcess"=>0));
        }
        //解包压缩的数据
		$RaceInfo['comment'] = json_decode($RaceInfo['comment'],true);
        //解包路径相关的信息
        $RaceInfo['RouteInfo'] = json_decode($RaceInfo['RouteInfo'],true);
        //从文件载入计时信息
        $UserRaceTimingInfo = $oRace->GetRaceTimingOriginalInfo($RaceId,0);
        $TimingRecordTable = $this->getTimingRecord($RaceInfo['RouteInfo']['MylapsPrefix'],$UserRaceTimingInfo['LastUpdateRecordCount'],$Force);
        if($TimingRecordTable['return']!=1)
        {
            echo "not rebuild";
            die();
        }
        //如果强制重新更新计时数
        if($Force==1 || $TimingRecordTable['return'] == 1)
        {
            //重新生成选手的mylaps排名数据
            $oRace->genRaceLogToText($RaceId);
        }
        $UserRaceTimingInfo = $oRace->GetRaceTimingOriginalInfo($RaceId,0);
        //初始化比赛时间列表
        $TimeList = array();
        //如果有配置各个分组的比赛时间
        if(isset($RaceInfo['comment']['SelectedRaceGroup']))
        {
            //循环分组列表
            foreach($RaceInfo['comment']['SelectedRaceGroup'] as $RaceGroupId => $RaceGroupInfo)
            {
                //保存比赛时间
                $TimeList[$RaceGroupId]['RaceStartTime'] = strtotime($RaceGroupInfo['StartTime']) + $RaceGroupInfo['RaceStartMicro']/1000;
                $TimeList[$RaceGroupId]['RaceEndTime'] = strtotime($RaceGroupInfo['EndTime']);
                $TimeList[$RaceGroupId]['CreditRatio'] = ($RaceGroupInfo['CreditRatio']);
            }
        }
        else
        {
            //预存比赛的开始和结束时间
            $TimeList[$RaceInfo['RaceGroupId']]['RaceStartTime'] = strtotime($RaceInfo['StartTime']) + $RaceInfo['comment']['RaceStartMicro']/1000;
            $TimeList[$RaceInfo['RaceGroupId']]['RaceEndTime'] = strtotime($RaceInfo['EndTime']);
        }
        //车队排名的名次数据
        $RaceInfo['comment']['TeamResultRank'] = isset($RaceInfo['comment']['TeamResultRank'])?$RaceInfo['comment']['TeamResultRank']:3;
		//预存比赛的开始和结束时间
		$RaceStartTime = strtotime($RaceInfo['StartTime']) + $RaceInfo['comment']['RaceStartMicro']/1000;
		$RaceEndTime = strtotime($RaceInfo['EndTime']);

		//初始化单个计时点的最大等待时间（超过这个时间才认为是新的一次进入）
		$RaceInfo['RouteInfo']['MylapsTolaranceTime'] = isset($RaceInfo['RouteInfo']['MylapsTolaranceTime'])?$RaceInfo['RouteInfo']['MylapsTolaranceTime']:30;
		//初始化计时点成绩计算的方式（发枪时刻/第一次经过起始点）
		$ResultType = ((isset($RaceInfo['RouteInfo']['RaceTimingResultType']) && ($RaceInfo['RouteInfo']['RaceTimingResultType']=="gunshot"))||!isset($RaceInfo['RouteInfo']['RaceTimingResultType']))?"gunshot":"net";
        //初始化计时点成绩计算的方式（发枪时刻/第一次经过起始点/积分）
        $FinalResultType = isset($RaceInfo['RouteInfo']['FinalResultType'])?$RaceInfo['RouteInfo']['FinalResultType']:"gunshot";

		echo "计时点计算:".$oRace->getRaceTimingResultType($ResultType)."\n<br>";
        echo "总成绩计算:".$oRace->getFinalResultType($FinalResultType)."\n<br>";
		//获取选手和车队名单
		//$RaceUserList = $oUser->getRaceUserListByRace($RaceId,0,0,0);
        $RaceUserList = $oRace->getRaceUserListByFile($RaceId);
        //初始化空的芯片列表
		$ChipList = array();
		//初始化空的用户列表
		$UserList = array();
		//循环报名记录
		foreach ($RaceUserList as $ApplyId => $ApplyInfo)
		{
			//如果有配置芯片数据和BIB
			if (trim($ApplyInfo['ChipId']) && trim($ApplyInfo['BIB']))
			{
				//拼接字符串加入到芯片列表
				$ChipList[] = "'" . $ApplyInfo['ChipId'] . "'";
				//分别保存用户的ID,姓名和BIB
				$UserList[$ApplyInfo['ChipId']] = $ApplyInfo;
			}
		}
        echo "比赛时间：".$RaceInfo['StartTime'].".".sprintf("%03d",isset($RaceInfo['comment']['RaceStartMicro'])?$RaceInfo['comment']['RaceStartMicro']:0)."~".$RaceInfo['EndTime']."<br>\n";
		echo "芯片列表：".implode(",",$ChipList)."\n";
        //获取文件最后的
        $LastId = $UserRaceTimingInfo['LastId'];
        //单页记录数量
		$pageSize = 1000;
		//默认第一次有获取到
		$Count = $pageSize;
		//初始化当前芯片（选手）
		$currentChip = "";
		while ($Count == $pageSize)
		{
		    //拼接获取计时数据的参数，注意芯片列表为空时的数据拼接
			$params = array('StartTime'=>date("Y-m-d H:i:s",strtotime($RaceInfo['StartTime'])+8*3600),'EndTime'=>date("Y-m-d H:i:s",strtotime($RaceInfo['EndTime'])+8*3600),'prefix'=>$RaceInfo['RouteInfo']['MylapsPrefix'],'LastId'=>$LastId, 'pageSize'=>$pageSize, 'ChipList'=>count($ChipList) ? implode(",",$ChipList):"0");
			//获取计时数据
			$TimingList = $this->getTimingData($params);
			//依次循环计时数据
			foreach ($TimingList['Record'] as $Key => $TimingInfo)
			{
                //最后获取到的记录ID
			    $LastId = $TimingInfo['Id'];
				//mylaps系统中生成的时间一直比当前时间晚8小时，修正
				$TimingInfo['ChipTime'] = strtotime($TimingInfo['ChipTime']) - 8 * 3600;
				//调试信息
				$ChipTime = $TimingInfo['ChipTime'] + substr($TimingInfo['MilliSecs'], -3) / 1000;
				//对于毫秒数据进行四舍五入
				$miliSec = substr($TimingInfo['MilliSecs'], -3) / 1000;
				//计算实际的时间
				$TimingInfo['ChipTime'] = $miliSec>=0.5?($TimingInfo['ChipTime']-1):$TimingInfo['ChipTime'];
				//时间进行累加
				$ChipTime = $TimingInfo['ChipTime']+$miliSec;
				//格式化成过线时间
                $inTime = sprintf("%0.3f", $ChipTime);
                //如果时间在比赛的开始时间和结束时间之内
                $RaceStartTime = $TimeList[$UserList[$TimingInfo['Chip']]['RaceGroupId']]['RaceStartTime'];
                $RaceEndTime = $TimeList[$UserList[$TimingInfo['Chip']]['RaceGroupId']]['RaceEndTime'];
                //如果当前芯片 和 循环到的计时数据不同 （说明已经结束了上一个选手的循环）
                if ($currentChip != $TimingInfo['Chip'])
                {
                    $num=1;
                    //将当前位置置为循环到的计时点
                    $currentChip = $TimingInfo['Chip'];
                    //调试信息
                    if(isset($UserList[$TimingInfo['Chip']]))
                    {
                        echo "芯片:".$currentChip . ",用户ID:".$UserList[$TimingInfo['Chip']]['RaceUserId'].",姓名:".$UserList[$TimingInfo['Chip']]['Name'] .",队伍:" . $UserList[$TimingInfo['Chip']]['TeamName'] ."-号码:" . $UserList[$TimingInfo['Chip']]['BIB']."用户分组:".$UserList[$TimingInfo['Chip']]['RaceGroupId']."\n<br>";
                    }
                    else
                    {
                        echo "芯片:".$currentChip . ",用户 Undifined:". "\n";
                    }
                }
                //比赛前数据
                if ($ChipTime < $RaceStartTime)
                {
                    echo $num."-".$TimingInfo['Location']."-".($ChipTime)."-".date("Y-m-d H:i:s", $TimingInfo['ChipTime']).".".(substr($miliSec,2))."赛前数据跳过<br>\n";
                }
                else
                {
                    //比赛中数据
                    if ($ChipTime <= $RaceEndTime)
                    {
                        echo $num."-".$TimingInfo['Location']."-".($ChipTime)."-".date("Y-m-d H:i:s", $TimingInfo['ChipTime']).".".(substr($miliSec,2))."<br>\n";
                        //获取选手的比赛信息（计时）
                        $UserRaceInfo = $oRace->getUserRaceTimingOriginalInfo($RaceId, $UserList[$TimingInfo['Chip']]['RaceUserId'],$Cache);
                        //如果没有标记当前位置（第一个点）
                        if (!isset($UserRaceInfo['CurrentPoint']))
                        {
                            //初始位置为1号点
                            $i = 1;
                            //获取第一个点的数据
                            $FirstPointInfo = $UserRaceInfo['Point'][$i];
                            //比对第一个点的芯片ID和当前获得点的ID是否符合
                            if ($FirstPointInfo['ChipId'] == $TimingInfo['Location'])
                            {
                                //记录当前经过的点的位置
                                $UserRaceInfo['CurrentPoint'] = $i;
                                //记录经过时间
                                $UserRaceInfo['Point'][$i]['inTime'] = $inTime;
                                //如果前一点的距离为非负数，则取当前时间和前一点差值作为经过时间，否则不计时
                                $UserRaceInfo['Point'][$i]['PointTime'] = isset($UserRaceInfo['Point'][$i]['ToPrevious'])&&intval($UserRaceInfo['Point'][$i]['ToPrevious'])>=0?(isset($UserRaceInfo['Point'][$i-1]['inTime'])?sprintf("%0.3f",($UserRaceInfo['Point'][$i]['inTime']-$UserRaceInfo['Point'][$i-1]['inTime'])):0):0;
                                $UserRaceInfo['Point'][$i]['PointSpeed'] = isset($UserRaceInfo['Point'][$i])?(Base_Common::speedDisplayParth($UserRaceInfo['Point'][$i]['SpeedDisplayType'],$UserRaceInfo['Point'][$i]['PointTime'],($UserRaceInfo['Point'][$i]['ToPrevious'])>=0?$UserRaceInfo['Point'][$i]['ToPrevious']:0)):"";
                                $UserRaceInfo['Point'][$i]['SportsTime'] = sprintf("%0.3f",$UserRaceInfo['Point'][$i]['CurrentDistanse']==$UserRaceInfo['Point'][$i]['ToPrevious']?$UserRaceInfo['Point'][$i]['PointTime']:($UserRaceInfo['Point'][$i]['PointTime']+$UserRaceInfo['Point'][$i-1]['SportsTime']));
                                $UserRaceInfo['Point'][$i]['SportsSpeed'] = Base_Common::speedDisplayParth($UserRaceInfo['Point'][$i]['SpeedDisplayType'],$UserRaceInfo['Point'][$i]['SportsTime'],$UserRaceInfo['Point'][$i]['CurrentDistanse']);

                                $TotalNetTime = isset($UserRaceInfo['Point'][$i-1]['TotalNetTime'])?sprintf("%0.3f",($UserRaceInfo['Point'][$i-1]['TotalNetTime']+$UserRaceInfo['Point'][$i]['PointTime'])):sprintf("%0.3f",$UserRaceInfo['Point'][$i]['PointTime']);
                                if($i==1)
                                {
                                    $TotalTime = sprintf("%0.3f",$UserRaceInfo['Point'][$i]['inTime']-$RaceStartTime);
                                }
                                else
                                {
                                    $TotalTime = sprintf("%0.3f",($UserRaceInfo['Point'][$i-1]['TotalTime'])?$UserRaceInfo['Point'][$i-1]['TotalTime']+$UserRaceInfo['Point'][$i]['PointTime']:$UserRaceInfo['Point'][$i]['PointTime']);
                                }
                                $UserRaceInfo['Point'][$i]['TotalTime'] = $TotalTime;
                                $UserRaceInfo['Point'][$i]['TotalNetTime'] = $TotalNetTime;

                                unset($UserRaceInfo['Point'][$i]['UserList']);
                                //保存个人当前计时点的信息
                                $oRace->UserTimgingDataSave($RaceInfo['RaceId'],$UserList[$TimingInfo['Chip']]['RaceUserId'],$UserRaceInfo,$Cache);
                                //获取所有选手的比赛信息（计时）
                                $UserRaceInfoList = $oRace->GetRaceTimingOriginalInfo($RaceId,$Cache);
                                echo "SaveDataCount1:".count($UserRaceInfoList)."<br>";
                                //将当前计时点最小的过线记录保存
                                $UserRaceInfoList['Point'][$i]['inTime'] = $UserRaceInfoList['Point'][$i]['inTime'] == 0 ? $inTime : sprintf("%0.3f", min($UserRaceInfoList['Point'][$i]['inTime'], $ChipTime));
                                //新增当前点的过线记录
                                $UserRaceInfoList['Point'][$i]['UserList'][count($UserRaceInfoList['Point'][$i]['UserList'])] = array("PointTime"=>$UserRaceInfo['Point'][$i]['PointTime'],"PointSpeed"=>$UserRaceInfo['Point'][$i]['PointSpeed'] ,"TotalTime" => $TotalTime,"TotalNetTime"=>0, "Name" => $UserList[$TimingInfo['Chip']]['Name'], "BIB" => $UserList[$TimingInfo['Chip']]['BIB'], "inTime" => sprintf("%0.3f",$ChipTime), 'RaceUserId' => $UserList[$TimingInfo['Chip']]['RaceUserId'],'TeamName'=>$UserList[$TimingInfo['Chip']]['TeamName'],'RaceGroupId'=>$UserList[$TimingInfo['Chip']]['RaceGroupId']);
                                //初始化一个空的数组
                                $t = array();
                                //循环每个人的过线记录
                                foreach ($UserRaceInfoList['Point'][$i]['UserList'] as $k => $v)
                                {
                                    //计算每个人的过线记录和当前点最早记录的时间差
                                    $UserRaceInfoList['Point'][$i]['UserList'][$k]['TimeLag'] = sprintf("%0.3f",abs(sprintf("%0.3f", $UserRaceInfoList['Point'][$i]['inTime']) - sprintf("%0.3f", $v['inTime'])));
                                    //生成排序数组
                                    $t[$k] = $UserRaceInfoList['Point'][$i]['UserList'][$k]['TimeLag'];
                                }
                                //根据时间差进行升序排序
                                array_multisort($t, SORT_ASC, $UserRaceInfoList['Point'][$i]['UserList']);
                                //初始化一个空的分组排名数组
                                $DivisionList = array();
                                //循环当前计时点排名
                                foreach($UserRaceInfoList['Point'][$i]['UserList'] as $key => $UserInfo)
                                {
                                    // 生成总排名
                                    $UserRaceInfoList['Point'][$i]['UserList'][$key]['Rank'] = $key+1;
                                    //依次填入分组数据
                                    $DivisionList[$UserInfo['RaceGroupId']][] = $UserInfo['BIB'];
                                    //排名保存
                                    $UserRaceInfoList['Point'][$i]['UserList'][$key]['GroupRank'] = count($DivisionList[$UserInfo['RaceGroupId']]);
                                    $UserInfo['GroupRank'] = $UserRaceInfoList['Point'][$i]['UserList'][$key]['GroupRank'];
                                    //清除原来的积分
                                    unset($UserRaceInfoList['Point'][$i]['UserList'][$key]['Credit']);
                                    if(isset($UserRaceInfoList['Point'][$i]['CreditList']))
                                    {
                                        //循环积分列表
                                        foreach($UserRaceInfoList['Point'][$i]['CreditList'] as $CreditId => $CreditInfo)
                                        {
                                            //生成积分序列
                                            $CreditSequence = Base_Common::ParthSequence($CreditInfo['CreditRule']);
                                            //如果名次匹配
                                            if(isset($CreditSequence[$UserInfo['GroupRank']]))
                                            {
                                                //积分相应累加
                                                $UserRaceInfoList['Point'][$i]['UserList'][$key]['Credit'][$CreditId] = array("Credit"=>round($CreditSequence[$UserRaceInfoList['Point'][$i]['UserList'][$key]['GroupRank']]*$TimeList[$UserInfo['RaceGroupId']]['CreditRatio']),"CreditName"=>$CreditList[$CreditId]['CreditName']);
                                            }
                                        }
                                    }

                                }
                                //如果现在已有总排名数组
                                if (isset($UserRaceInfoList['Total']) && count($UserRaceInfoList['Total']))
                                {
                                    //初始设定为未找到
                                    $found = 0;
                                    //循环已有的排名数据
                                    foreach ($UserRaceInfoList['Total'] as $k => $v)
                                    {
                                        //依次比对现在的用户ID，如果找到了，则更新
                                        if ($v['RaceUserId'] == $UserList[$TimingInfo['Chip']]['RaceUserId'])
                                        {
                                            $UserRaceInfoList['Total'][$k] = array("CurrentPosition" => 1,"CurrentPositionName" => $UserRaceInfoList['Point'][1]['TName'], "TotalTime" => $TotalTime,"TotalNetTime"=>0, "Name" => $UserList[$TimingInfo['Chip']]['Name'], "BIB" => $UserList[$TimingInfo['Chip']]['BIB'], "inTime" => sprintf("%0.3f",$ChipTime), 'RaceUserId' => $UserList[$TimingInfo['Chip']]['RaceUserId'],'TeamName'=>$UserList[$TimingInfo['Chip']]['TeamName'],'TeamId'=>$UserList[$TimingInfo['Chip']]['TeamId'],'RaceGroupId'=>$UserList[$TimingInfo['Chip']]['RaceGroupId']);
                                            $found = 1;
                                            break;
                                        }
                                    }
                                    //如果未找到，则新增
                                    if ($found == 0)
                                    {
                                        $UserRaceInfoList['Total'][count($UserRaceInfoList['Total'])] = array("CurrentPosition" => 1,"CurrentPositionName" => $UserRaceInfoList['Point'][1]['TName'],"TotalTime" => $TotalTime,"TotalNetTime"=>0, "Name" => $UserList[$TimingInfo['Chip']]['Name'], "BIB" => $UserList[$TimingInfo['Chip']]['BIB'], "inTime" => sprintf("%0.3f",$ChipTime), 'RaceUserId' => $UserList[$TimingInfo['Chip']]['RaceUserId'],'TeamName'=>$UserList[$TimingInfo['Chip']]['TeamName'],'TeamId'=>$UserList[$TimingInfo['Chip']]['TeamId'],'RaceGroupId'=>$UserList[$TimingInfo['Chip']]['RaceGroupId']);
                                    }
                                }
                                //新建排名数据
                                else
                                {
                                    $UserRaceInfoList['Total'][0] = array("CurrentPosition" => 1,"CurrentPositionName" => $UserRaceInfoList['Point'][1]['TName'], "TotalTime" => $TotalTime,"TotalNetTime"=>0, "Name" => $UserList[$TimingInfo['Chip']]['Name'], "BIB" => $UserList[$TimingInfo['Chip']]['BIB'], "inTime" => sprintf("%0.3f",$ChipTime), 'RaceUserId' => $UserList[$TimingInfo['Chip']]['RaceUserId'],'TeamName'=>$UserList[$TimingInfo['Chip']]['TeamName'],'TeamId'=>$UserList[$TimingInfo['Chip']]['TeamId'],'RaceGroupId'=>$UserList[$TimingInfo['Chip']]['RaceGroupId']);
                                }
                                $t = array();
                                //再次循环排名数组，依次取出当前位置，总时间，总净时间做排序依据
                                foreach ($UserRaceInfoList['Total'] as $k => $v)
                                {
                                    $t1[$k] = $v['CurrentPosition'];
                                    $t2[$k] = $v['TotalTime'];
                                    $t3[$k] = $v['TotalNetTime'];
                                }
                                //根据不同的计时类型进行排序
                                if($ResultType=="gunshot")
                                {
                                    array_multisort($t1, SORT_DESC, $t2, SORT_ASC, $UserRaceInfoList['Total']);
                                }
                                else
                                {
                                    array_multisort($t1, SORT_DESC, $t3, SORT_ASC, $UserRaceInfoList['Total']);
                                }
                                $DivisionList = array();
                                foreach($UserRaceInfoList['Total'] as $key => $UserInfo)
                                {
                                    $DivisionList[$UserInfo['RaceGroupId']][] = $UserInfo['BIB'];
                                    $UserRaceInfoList['Total'][$key]['GroupRank'] = count($DivisionList[$UserInfo['RaceGroupId']]);
                                    // 生成总排名
                                    $UserRaceInfoList['Total'][$key]['Rank'] = $key+1;
                                }
                                $UserRaceInfoList['LastId'] = $TimingInfo['Id'];
                                $UserRaceInfoList['LastUpdateRecordCount'] = $TimingRecordTable['RecordCount'];
                                //保存配置文件
                                $oRace->TimgingDataSave($RaceInfo['RaceId'],$UserRaceInfoList,$Cache);
                                echo "getdataCount1:".count($UserRaceInfoList)."<br>";
                                $num++;
                            }
                            else
                            {
                                //ToDo 首个点未匹配上
                                //如果比赛配置为无起点
                                if(isset($RaceInfo['comment']['NoStart']) && ($RaceInfo['comment']['NoStart']==1))
                                {
                                    //记录当前经过的点的位置
                                    $UserRaceInfo['CurrentPoint'] = $i;
                                    //将经过时间统一写成比赛开始时间
                                    $ChipTime=$RaceStartTime;
                                    //记录经过时间
                                    $UserRaceInfo['Point'][$i]['inTime'] = $inTime;
                                    //如果前一点的距离为非负数，则取当前时间和前一点差值作为经过时间，否则不计时
                                    $UserRaceInfo['Point'][$i]['PointTime'] = intval($UserRaceInfo['Point'][$i]['ToPrevious'])>=0?(sprintf("%0.3f",($UserRaceInfo['Point'][$i-1]['inTime'])?$UserRaceInfo['Point'][$i]['inTime']-$UserRaceInfo['Point'][$i-1]['inTime']:0)):0;
                                    $UserRaceInfo['Point'][$i]['PointSpeed'] = Base_Common::speedDisplayParth($UserRaceInfo['Point'][$i]['SpeedDisplayType'],$UserRaceInfo['Point'][$i]['PointTime'],($UserRaceInfo['Point'][$i]['ToPrevious'])>=0?$UserRaceInfo['Point'][$i]['ToPrevious']:0);
                                    $UserRaceInfo['Point'][$i]['SportsTime'] = sprintf("%0.3f",$UserRaceInfo['Point'][$i]['CurrentDistanse']==$UserRaceInfo['Point'][$i]['ToPrevious']?$UserRaceInfo['Point'][$i]['PointTime']:($UserRaceInfo['Point'][$i]['PointTime']+$UserRaceInfo['Point'][$i-1]['SportsTime']));
                                    $UserRaceInfo['Point'][$i]['SportsSpeed'] = Base_Common::speedDisplayParth($UserRaceInfo['Point'][$i]['SpeedDisplayType'],$UserRaceInfo['Point'][$i]['SportsTime'],$UserRaceInfo['Point'][$i]['CurrentDistanse']);
                                    $TotalNetTime = isset($UserRaceInfo['Point'][$i-1]['TotalNetTime'])?sprintf("%0.3f",($UserRaceInfo['Point'][$i-1]['TotalNetTime']+$UserRaceInfo['Point'][$i]['PointTime'])):sprintf("%0.3f",$UserRaceInfo['Point'][$i]['PointTime']);
                                    if($i==1)
                                    {
                                        $TotalTime = sprintf("%0.3f",$UserRaceInfo['Point'][$i]['inTime']-$RaceStartTime);
                                    }
                                    else
                                    {
                                        $TotalTime= sprintf("%0.3f",($UserRaceInfo['Point'][$i-1]['TotalTime'])?$UserRaceInfo['Point'][$i-1]['TotalTime']+$UserRaceInfo['Point'][$i]['PointTime']:$UserRaceInfo['Point'][$i]['PointTime']);
                                    }
                                    $UserRaceInfo['Point'][$i]['TotalTime'] = $TotalTime;
                                    $UserRaceInfo['Point'][$i]['TotalNetTime'] = $TotalNetTime;
                                    unset($UserRaceInfo['Point'][$i]['UserList']);
                                    //保存个人当前计时点的信息
                                    $oRace->UserTimgingDataSave($RaceInfo['RaceId'],$UserList[$TimingInfo['Chip']]['RaceUserId'],$UserRaceInfo,$Cache);
                                    //获取所有选手的比赛信息（计时）
                                    $UserRaceInfoList = $oRace->GetRaceTimingOriginalInfo($RaceId,$Cache);
                                    echo "SavePointCount3:".count($UserRaceInfoList)."<br>";
                                    //将当前计时点最小的过线记录保存
                                    $UserRaceInfoList['Point'][$i]['inTime'] = $UserRaceInfoList['Point'][$i]['inTime'] == 0 ? $inTime : sprintf("%0.3f", min($UserRaceInfoList['Point'][$i]['inTime'], $ChipTime));
                                    //新增当前点的过线记录
                                    $UserRaceInfoList['Point'][$i]['UserList'][count($UserRaceInfoList['Point'][$i]['UserList'])] = array("PointTime"=>$UserRaceInfo['Point'][$i]['PointTime'],"PointSpeed"=>$UserRaceInfo['Point'][$i]['PointSpeed'] ,"TotalTime" => $TotalTime,"TotalNetTime"=>0, "Name" => $UserList[$TimingInfo['Chip']]['Name'], "BIB" => $UserList[$TimingInfo['Chip']]['BIB'], "inTime" => sprintf("%0.3f",$ChipTime), 'RaceUserId' => $UserList[$TimingInfo['Chip']]['RaceUserId'],'TeamName'=>$UserList[$TimingInfo['Chip']]['TeamName']);
                                    //初始化一个空的数组
                                    $t = array();
                                    //循环每个人的过线记录
                                    foreach ($UserRaceInfoList['Point'][$i]['UserList'] as $k => $v)
                                    {
                                        //计算每个人的过线记录和当前点最早记录的时间差
                                        $UserRaceInfoList['Point'][$i]['UserList'][$k]['TimeLag'] = sprintf("%0.3f",abs(sprintf("%0.3f", $UserRaceInfoList['Point'][$i]['inTime']) - sprintf("%0.3f", $v['inTime'])));
                                        //生成排序数组
                                        $t[$k] = $UserRaceInfoList['Point'][$i]['UserList'][$k]['TimeLag'];
                                    }
                                    //根据时间差进行升序排序
                                    array_multisort($t, SORT_ASC, $UserRaceInfoList['Point'][$i]['UserList']);
                                    //初始化一个空的分组排名数组
                                    $DivisionList = array();
                                    //循环当前计时点排名
                                    foreach($UserRaceInfoList['Point'][$i]['UserList'] as $key => $UserInfo)
                                    {
                                        // 生成总排名
                                        $UserRaceInfoList['Point'][$i]['UserList'][$key]['Rank'] = $key+1;
                                        //依次填入分组数据
                                        $DivisionList[$UserInfo['RaceGroupId']][] = $UserInfo['BIB'];
                                        //排名保存
                                        $UserRaceInfoList['Point'][$i]['UserList'][$key]['GroupRank'] = count($DivisionList[$UserInfo['RaceGroupId']]);
                                        $UserInfo['GroupRank'] = $UserRaceInfoList['Point'][$i]['UserList'][$key]['GroupRank'];
                                        //清除原来的积分
                                        unset($UserRaceInfoList['Point'][$i]['UserList'][$key]['Credit']);
                                        //循环积分列表
                                        foreach($UserRaceInfoList['Point'][$i]['CreditList'] as $CreditId => $CreditInfo)
                                        {
                                            //生成积分序列
                                            $CreditSequence = Base_Common::ParthSequence($CreditInfo['CreditRule']);
                                            //如果名次匹配
                                            if(isset($CreditSequence[$UserInfo['GroupRank']]))
                                            {
                                                //积分相应累加
                                                $UserRaceInfoList['Point'][$i]['UserList'][$key]['Credit'][$CreditId] = array("Credit"=>round($CreditSequence[$UserRaceInfoList['Point'][$i]['UserList'][$key]['GroupRank']]*$TimeList[$UserInfo['RaceGroupId']]['CreditRatio']),"CreditName"=>$CreditList[$CreditId]['CreditName']);;
                                            }
                                        }
                                    }
                                    unset($UserRaceInfo['Total']['Credit']);
                                    foreach($UserRaceInfo['Point'] as $p => $pInfo)
                                    {
                                        if(isset($pInfo['Credit']))
                                        {
                                            foreach($pInfo['Credit'] as $c => $cInfo)
                                            {
                                                if(isset($UserRaceInfo['Total']['Credit'][$c]))
                                                {
                                                    $UserRaceInfo['Total']['Credit'][$c]['Credit'] +=  $cInfo['Credit'];
                                                }
                                                else
                                                {
                                                    $UserRaceInfo['Total']['Credit'][$c] = $cInfo;
                                                }
                                            }
                                        }
                                    }
                                    //如果现在已有总排名数组
                                    if (isset($UserRaceInfoList['Total']) && count($UserRaceInfoList['Total']))
                                    {
                                        //初始设定为未找到
                                        $found = 0;
                                        //循环已有的排名数据
                                        foreach ($UserRaceInfoList['Total'] as $k => $v)
                                        {
                                            //依次比对现在的用户ID，如果找到了，则更新
                                            if ($v['RaceUserId'] == $UserList[$TimingInfo['Chip']]['RaceUserId'])
                                            {
                                                $UserRaceInfoList['Total'][$k] = array("CurrentPosition" => 1,"CurrentPositionName" => $UserRaceInfoList['Point'][1]['TName'], "TotalTime" => $TotalTime,"TotalNetTime"=>0, "Name" => $UserList[$TimingInfo['Chip']]['Name'], "BIB" => $UserList[$TimingInfo['Chip']]['BIB'], "inTime" => sprintf("%0.3f",$ChipTime), 'RaceUserId' => $UserList[$TimingInfo['Chip']]['RaceUserId'],'TeamName'=>$UserList[$TimingInfo['Chip']]['TeamName'],'TeamId'=>$UserList[$TimingInfo['Chip']]['TeamId'],'RaceGroupId'=>$UserList[$TimingInfo['Chip']]['RaceGroupId']);
                                                $found = 1;
                                                break;
                                            }
                                        }
                                        //如果未找到，则新增
                                        if ($found == 0)
                                        {
                                            $UserRaceInfoList['Total'][count($UserRaceInfoList['Total'])] = array("CurrentPosition" => 1,"CurrentPositionName" => $UserRaceInfoList['Point'][1]['TName'],"TotalTime" => $TotalTime,"TotalNetTime"=>0, "Name" => $UserList[$TimingInfo['Chip']]['Name'], "BIB" => $UserList[$TimingInfo['Chip']]['BIB'], "inTime" => sprintf("%0.3f",$ChipTime), 'RaceUserId' => $UserList[$TimingInfo['Chip']]['RaceUserId'],'TeamName'=>$UserList[$TimingInfo['Chip']]['TeamName'],'TeamId'=>$UserList[$TimingInfo['Chip']]['TeamId'],'RaceGroupId'=>$UserList[$TimingInfo['Chip']]['RaceGroupId']);
                                        }
                                    }
                                    //新建排名数据
                                    else
                                    {
                                        $UserRaceInfoList['Total'][0] = array("CurrentPosition" => 1,"CurrentPositionName" => $UserRaceInfoList['Point'][1]['TName'], "TotalTime" => $TotalTime,"TotalNetTime"=>0, "Name" => $UserList[$TimingInfo['Chip']]['Name'], "BIB" => $UserList[$TimingInfo['Chip']]['BIB'], "inTime" => sprintf("%0.3f",$ChipTime), 'RaceUserId' => $UserList[$TimingInfo['Chip']]['RaceUserId'],'TeamName'=>$UserList[$TimingInfo['Chip']]['TeamName'],'TeamId'=>$UserList[$TimingInfo['Chip']]['TeamId'],'RaceGroupId'=>$UserList[$TimingInfo['Chip']]['RaceGroupId']);
                                    }
                                    $t = array();
                                    //再次循环排名数组，依次取出当前位置，总时间，总净时间做排序依据
                                    foreach ($UserRaceInfoList['Total'] as $k => $v)
                                    {
                                        $t1[$k] = $v['CurrentPosition'];
                                        $t2[$k] = $v['TotalTime'];
                                        $t3[$k] = $v['TotalNetTime'];
                                    }
                                    //根据不同的计时类型进行排序
                                    if($ResultType=="gunshot")
                                    {
                                        array_multisort($t1, SORT_DESC, $t2, SORT_ASC, $UserRaceInfoList['Total']);
                                    }
                                    else
                                    {
                                        array_multisort($t1, SORT_DESC, $t3, SORT_ASC, $UserRaceInfoList['Total']);
                                    }
                                    $DivisionList = array();
                                    foreach($UserRaceInfoList['Total'] as $key => $UserInfo)
                                    {
                                        $DivisionList[$UserInfo['RaceGroupId']][] = $UserInfo['BIB'];
                                        $UserRaceInfoList['Total'][$key]['GroupRank'] = count($DivisionList[$UserInfo['RaceGroupId']]);
                                        // 生成总排名
                                        $UserRaceInfoList['Total'][$key][$key]['Rank'] = $key+1;
                                    }
                                    $UserRaceInfoList['LastId'] = $TimingInfo['Id'];
                                    $UserRaceInfoList['LastUpdateRecordCount'] = $TimingRecordTable['RecordCount'];
                                    //保存配置文件
                                    $oRace->TimgingDataSave($RaceInfo['RaceId'],$UserRaceInfoList,$Cache);
                                    echo "getdataCount2:".count($UserRaceInfoList)."<br>";

                                    $num++;
                                }
                                else
                                {

                                }
                            }
                        }
                        else
                        {
                            //净时间为如果首次过线时间存在则用，否则去比赛统一开始时间
                            $StartTime = $UserRaceInfo['Point'][1]['inTime']>0?$UserRaceInfo['Point'][1]['inTime']:$RaceStartTime;
                            //保存当前点的位置
                            $c = $UserRaceInfo['CurrentPoint'];
                            //循环
                            do {
                                //如果当前点存在 且 下一点也存在
                                if (isset($UserRaceInfo['Point'][$UserRaceInfo['CurrentPoint']]) && isset($UserRaceInfo['Point'][$UserRaceInfo['CurrentPoint']+1]) && ($UserRaceInfo['Point'][$UserRaceInfo['CurrentPoint']+1]['ChipId']==$TimingInfo['Location']))
                                {
                                    //暂存当前点信息
                                    $CurrentPointInfo = $UserRaceInfo['Point'][$UserRaceInfo['CurrentPoint']];
                                    //计算本条计时信息和当前点过线时间的时间差
                                    $timeLag = sprintf("%0.3f", ($CurrentPointInfo['inTime'] - $ChipTime));
                                    //如果时间差小于配置的容忍时间（短时间内多次过线）
                                    $CurrentPointInfo['TolaranceTime'] = isset($CurrentPointInfo['TolaranceTime'])?$CurrentPointInfo['TolaranceTime']:$RaceInfo['RouteInfo']['MylapsTolaranceTime'];
                                    if (abs($timeLag) <= $CurrentPointInfo['TolaranceTime'])
                                    {
                                        echo $CurrentPointInfo['TolaranceTime']." Second TimeOut Pass\n";
                                        //本条记录废除
                                        break;
                                    }
                                }
                                else
                                {
                                    //echo "Reach The Buttom<br>";
                                    //循环结束
                                    break;
                                }
                            }
                            while
                            (
                                //(如果芯片的位置不符合 或 （位置符合 且 已经记录的过线时间为空） 且 向上累加)
                                (($CurrentPointInfo['ChipId'] != $TimingInfo['Location']) || (($CurrentPointInfo['ChipId'] == $TimingInfo['Location']) && ($CurrentPointInfo['inTime'] != ""))) && ($UserRaceInfo['CurrentPoint']++)
                            );
                            //如果当前点信息内有包含芯片ID(位置合法 且 当前点位置和循环查找之前的不相同（确认移位）)
                            if ($CurrentPointInfo['ChipId'] && $c != $UserRaceInfo['CurrentPoint'])
                            {
                                $UserRaceInfo['Point'][$UserRaceInfo['CurrentPoint']]['inTime'] = sprintf("%0.3f",$ChipTime);
                                //如果前一点的距离为非负数，则取当前时间和前一点差值作为经过时间，否则不计时
                                $UserRaceInfo['Point'][$UserRaceInfo['CurrentPoint']]['PointTime'] = ($UserRaceInfo['Point'][$UserRaceInfo['CurrentPoint']]['ToPrevious'])>=0?(sprintf("%0.3f",($UserRaceInfo['Point'][$UserRaceInfo['CurrentPoint']-1]['inTime'])?$UserRaceInfo['Point'][$UserRaceInfo['CurrentPoint']]['inTime']-$UserRaceInfo['Point'][$UserRaceInfo['CurrentPoint']-1]['inTime']:0)):0;
                                $UserRaceInfo['Point'][$UserRaceInfo['CurrentPoint']]['PointSpeed'] = Base_Common::speedDisplayParth($UserRaceInfo['Point'][$UserRaceInfo['CurrentPoint']]['SpeedDisplayType'],$UserRaceInfo['Point'][$UserRaceInfo['CurrentPoint']]['PointTime'],($UserRaceInfo['Point'][$UserRaceInfo['CurrentPoint']]['ToPrevious'])>=0?$UserRaceInfo['Point'][$UserRaceInfo['CurrentPoint']]['ToPrevious']:0);
                                $UserRaceInfo['Point'][$UserRaceInfo['CurrentPoint']]['SportsTime'] = sprintf("%0.3f",$UserRaceInfo['Point'][$UserRaceInfo['CurrentPoint']]['CurrentDistanse']==$UserRaceInfo['Point'][$UserRaceInfo['CurrentPoint']]['ToPrevious']?$UserRaceInfo['Point'][$UserRaceInfo['CurrentPoint']]['PointTime']:($UserRaceInfo['Point'][$UserRaceInfo['CurrentPoint']]['PointTime']+$UserRaceInfo['Point'][$UserRaceInfo['CurrentPoint']-1]['SportsTime']));
                                $UserRaceInfo['Point'][$UserRaceInfo['CurrentPoint']]['SportsSpeed'] = Base_Common::speedDisplayParth($UserRaceInfo['Point'][$UserRaceInfo['CurrentPoint']]['SpeedDisplayType'],$UserRaceInfo['Point'][$UserRaceInfo['CurrentPoint']]['SportsTime'],$UserRaceInfo['Point'][$UserRaceInfo['CurrentPoint']]['CurrentDistanse']);
                                $TotalTime = sprintf("%0.3f",($UserRaceInfo['Point'][$UserRaceInfo['CurrentPoint']-1]['TotalTime'])?$UserRaceInfo['Point'][$UserRaceInfo['CurrentPoint']-1]['TotalTime']+$UserRaceInfo['Point'][$UserRaceInfo['CurrentPoint']]['PointTime']:$UserRaceInfo['Point'][$UserRaceInfo['CurrentPoint']]['PointTime']);
                                $TotalNetTime = isset($UserRaceInfo['Point'][$UserRaceInfo['CurrentPoint']-1]['TotalNetTime'])?sprintf("%0.3f",($UserRaceInfo['Point'][$UserRaceInfo['CurrentPoint']-1]['TotalNetTime']+$UserRaceInfo['Point'][$UserRaceInfo['CurrentPoint']]['PointTime'])):sprintf("%0.3f",$UserRaceInfo['Point'][$UserRaceInfo['CurrentPoint']]['PointTime']);
                                $UserRaceInfo['Point'][$UserRaceInfo['CurrentPoint']]['TotalTime'] = $TotalTime;
                                $UserRaceInfo['Point'][$UserRaceInfo['CurrentPoint']]['TotalNetTime'] = $TotalNetTime;

                                unset($UserRaceInfo['Point'][$UserRaceInfo['CurrentPoint']]['UserList']);
                                //保存个人当前计时点的信息
                                $oRace->UserTimgingDataSave($RaceInfo['RaceId'],$UserList[$TimingInfo['Chip']]['RaceUserId'],$UserRaceInfo,$Cache);

                                //如果原过线时间有数值 则获取与现过线时间的较小值，否则就用现过线时间
                                $UserRaceInfoList['Point'][$UserRaceInfo['CurrentPoint']]['inTime'] = $UserRaceInfoList['Point'][$UserRaceInfo['CurrentPoint']]['inTime'] == 0 ? sprintf("%0.3f",$ChipTime) : min(sprintf("%0.3f", $UserRaceInfoList['Point'][$UserRaceInfo['CurrentPoint']]['inTime']), sprintf("%0.3f",$ChipTime));
                                //如果当前点的过线选手列表存在 且 已经有选手过线
                                if (isset($UserRaceInfoList['Point'][$UserRaceInfo['CurrentPoint']]['UserList']) && count($UserRaceInfoList['Point'][$UserRaceInfo['CurrentPoint']]['UserList']))
                                {
                                    $UserRaceInfoList['Point'][$UserRaceInfo['CurrentPoint']]['UserList'][count($UserRaceInfoList['Point'][$UserRaceInfo['CurrentPoint']]['UserList'])] = array("PointTime"=>$UserRaceInfo['Point'][$UserRaceInfo['CurrentPoint']]['PointTime'],"PointSpeed"=>$UserRaceInfo['Point'][$UserRaceInfo['CurrentPoint']]['PointSpeed'] ,"TotalTime" => $TotalTime,"TotalNetTime" => $TotalNetTime, "Name" => $UserList[$TimingInfo['Chip']]['Name'], "BIB" => $UserList[$TimingInfo['Chip']]['BIB'], "inTime" => sprintf("%0.3f",$ChipTime), 'RaceUserId' => $UserList[$TimingInfo['Chip']]['RaceUserId'],'TeamName'=>$UserList[$TimingInfo['Chip']]['TeamName'],'RaceGroupId'=>$UserList[$TimingInfo['Chip']]['RaceGroupId']);
                                }
                                else
                                {
                                    $UserRaceInfoList['Point'][$UserRaceInfo['CurrentPoint']]['UserList'][0] = array("PointTime"=>$UserRaceInfo['Point'][$UserRaceInfo['CurrentPoint']]['PointTime'],"PointSpeed"=>$UserRaceInfo['Point'][$UserRaceInfo['CurrentPoint']]['PointSpeed'],"TotalTime" => $TotalTime,"TotalNetTime" => $TotalNetTime, "Name" => $UserList[$TimingInfo['Chip']]['Name'], "BIB" => $UserList[$TimingInfo['Chip']]['BIB'], "inTime" => sprintf("%0.3f",$ChipTime), 'RaceUserId' => $UserList[$TimingInfo['Chip']]['RaceUserId'],'TeamName'=>$UserList[$TimingInfo['Chip']]['TeamName'],'RaceGroupId'=>$UserList[$TimingInfo['Chip']]['RaceGroupId']);
                                }
                                //初始化一个空数组
                                $t1 = array();
                                $t2 = array();
                                //循环当前计时点的过线数据
                                foreach ($UserRaceInfoList['Point'][$UserRaceInfo['CurrentPoint']]['UserList'] as $k => $v)
                                {
                                    //计算与本计时点第一位的时间差
                                    $UserRaceInfoList['Point'][$UserRaceInfo['CurrentPoint']]['UserList'][$k]['TimeLag'] = sprintf("%0.3f",abs($UserRaceInfoList['Point'][$UserRaceInfo['CurrentPoint']]['inTime'] - $v['inTime']));
                                    //将时间差放入排序用的数组
                                    $t1[$k] = $UserRaceInfoList['Point'][$UserRaceInfo['CurrentPoint']]['UserList'][$k]['TotalTime'];
                                    $t2[$k] = $UserRaceInfoList['Point'][$UserRaceInfo['CurrentPoint']]['UserList'][$k]['TotalNetTime'];
                                }
                                if($ResultType=="gunshot")
                                {
                                    array_multisort($t1, SORT_ASC, $UserRaceInfoList['Point'][$UserRaceInfo['CurrentPoint']]['UserList']);
                                }
                                else
                                {
                                    array_multisort($t2, SORT_ASC, $UserRaceInfoList['Point'][$UserRaceInfo['CurrentPoint']]['UserList']);
                                }
                                //初始化一个空的分组排名数组
                                $DivisionList = array();
                                //循环当前计时点排名
                                foreach($UserRaceInfoList['Point'][$UserRaceInfo['CurrentPoint']]['UserList'] as $key => $UserInfo)
                                {
                                    // 生成总排名
                                    $UserRaceInfoList['Point'][$UserRaceInfo['CurrentPoint']]['UserList'][$key]['Rank'] = $key+1;
                                    //依次填入分组数据
                                    $DivisionList[$UserInfo['RaceGroupId']][] = $UserInfo['BIB'];
                                    //排名保存
                                    $UserRaceInfoList['Point'][$UserRaceInfo['CurrentPoint']]['UserList'][$key]['GroupRank'] = count($DivisionList[$UserInfo['RaceGroupId']]);
                                    $UserInfo['GroupRank'] = $UserRaceInfoList['Point'][$UserRaceInfo['CurrentPoint']]['UserList'][$key]['GroupRank'];
                                    //清除原来的积分
                                    unset($UserRaceInfoList['Point'][$UserRaceInfo['CurrentPoint']]['UserList'][$key]['Credit']);
                                    //循环积分列表
                                    if(isset($UserRaceInfoList['Point'][$UserRaceInfo['CurrentPoint']]['CreditList']))
                                    {
                                        foreach($UserRaceInfoList['Point'][$UserRaceInfo['CurrentPoint']]['CreditList'] as $CreditId => $CreditInfo)
                                        {

                                            //生成积分序列
                                            $CreditSequence = Base_Common::ParthSequence($CreditInfo['CreditRule']);
                                            //如果名次匹配
                                            if(isset($CreditSequence[$UserInfo['GroupRank']]))
                                            {
                                                //积分相应累加
                                                $UserRaceInfoList['Point'][$UserRaceInfo['CurrentPoint']]['UserList'][$key]['Credit'][$CreditId] = array("Credit"=>round($CreditSequence[$UserRaceInfoList['Point'][$UserRaceInfo['CurrentPoint']]['UserList'][$key]['GroupRank']]*$TimeList[$UserInfo['RaceGroupId']]['CreditRatio']),"CreditName"=>$CreditList[$CreditId]['CreditName']);
                                            }
                                        }
                                    }

                                }
                                //循环总成绩数组
                                if (isset($UserRaceInfoList['Total']) && count($UserRaceInfoList['Total']))
                                {
                                    //初始设定为未找到
                                    $found = 0;
                                    //循环已有的排名数据
                                    foreach ($UserRaceInfoList['Total'] as $k => $v)
                                    {
                                        //依次比对现在的用户ID，如果找到了，则更新
                                        if ($v['RaceUserId'] == $UserList[$TimingInfo['Chip']]['RaceUserId'])
                                        {
                                            $UserRaceInfoList['Total'][$k] = array("CurrentPosition" => $UserRaceInfo['CurrentPoint'], "CurrentPositionName" => $UserRaceInfoList['Point'][$UserRaceInfo['CurrentPoint']]['TName'],"TotalTime" => $TotalTime,"TotalNetTime" => $TotalNetTime, "Name" => $UserList[$TimingInfo['Chip']]['Name'], "BIB" => $UserList[$TimingInfo['Chip']]['BIB'], "inTime" => sprintf("%0.3f",$ChipTime), 'RaceUserId' => $UserList[$TimingInfo['Chip']]['RaceUserId'],'TeamName'=>$UserList[$TimingInfo['Chip']]['TeamName'],'TeamId'=>$UserList[$TimingInfo['Chip']]['TeamId'],'RaceGroupId'=>$UserList[$TimingInfo['Chip']]['RaceGroupId']);
                                            $found = 1;
                                            break;
                                        }
                                    }
                                    //如果未找到，则新增
                                    if ($found == 0)
                                    {
                                        $UserRaceInfoList['Total'][count($UserRaceInfoList['Total'])] = array("CurrentPosition" => $UserRaceInfo['CurrentPoint'], "CurrentPositionName" => $UserRaceInfoList['Point'][$UserRaceInfo['CurrentPoint']]['TName'],"TotalTime" => $TotalTime,"TotalNetTime" => $TotalNetTime, "Name" => $UserList[$TimingInfo['Chip']]['Name'], "BIB" => $UserList[$TimingInfo['Chip']]['BIB'], "inTime" => $TimingInfo['ChipTime'] + $miliSec, 'RaceUserId' => $UserList[$TimingInfo['Chip']]['RaceUserId'],'TeamName'=>$UserList[$TimingInfo['Chip']]['TeamName'],'TeamId'=>$UserList[$TimingInfo['Chip']]['TeamId'],'RaceGroupId'=>$UserList[$TimingInfo['Chip']]['RaceGroupId']);
                                    }
                                }
                                //新建排名数据
                                else
                                {
                                    $UserRaceInfoList['Total'][0] = array("CurrentPosition" => $UserRaceInfo['CurrentPoint'], "CurrentPositionName" => $UserRaceInfoList['Point'][$UserRaceInfo['CurrentPoint']]['TName'],"TotalTime" => $TotalTime,"TotalNetTime" => $TotalNetTime, "Name" => $UserList[$TimingInfo['Chip']]['Name'], "BIB" => $UserList[$TimingInfo['Chip']]['BIB'], "inTime" => $TimingInfo['ChipTime'] + $miliSec, 'RaceUserId' => $UserList[$TimingInfo['Chip']]['RaceUserId'],'TeamName'=>$UserList[$TimingInfo['Chip']]['TeamName'],'TeamId'=>$UserList[$TimingInfo['Chip']]['TeamId'],'RaceGroupId'=>$UserList[$TimingInfo['Chip']]['RaceGroupId']);
                                }
                                $t1 = array();
                                $t2 = array();
                                $t3 = array();
                                foreach ($UserRaceInfoList['Total'] as $k => $v)
                                {
                                    $t1[$k] = $v['CurrentPosition'];
                                    $t2[$k] = $v['TotalTime'];
                                    $t3[$k] = $v['TotalNetTime'];
                                }
                                //根据不同的计时类型进行排序
                                if($ResultType=="gunshot")
                                {
                                    array_multisort($t1, SORT_DESC, $t2, SORT_ASC, $UserRaceInfoList['Total']);
                                }
                                else
                                {
                                    array_multisort($t1, SORT_DESC, $t3, SORT_ASC, $UserRaceInfoList['Total']);
                                }
                                foreach ($UserRaceInfoList['Point'][$UserRaceInfo['CurrentPoint']]['UserList'] as $k => $v)
                                {
                                    if($k!=0)
                                    {
                                        $UserRaceInfoList['Point'][$UserRaceInfo['CurrentPoint']]['UserList'][$k]['NetTimeLag']= sprintf("%0.3f",$v['TotalNetTime']-$UserRaceInfoList['Point'][$UserRaceInfo['CurrentPoint']]['UserList'][1]['TotalNetTime']);
                                    }
                                    else
                                    {
                                        $UserRaceInfoList['Point'][$UserRaceInfo['CurrentPoint']]['UserList'][$k]['NetTimeLag']= 0;
                                    }
                                }
                                $num++;
                                $DivisionList = array();
                                foreach($UserRaceInfoList['Total'] as $key => $UserInfo)
                                {
                                    $DivisionList[$UserInfo['RaceGroupId']][] = $UserInfo['BIB'];
                                    $UserRaceInfoList['Total'][$key]['GroupRank'] = count($DivisionList[$UserInfo['RaceGroupId']]);
                                    // 生成总排名
                                    $UserRaceInfoList['Total'][$key]['Rank'] = $key+1;
                                }
                                $UserRaceInfoList['LastId'] = $TimingInfo['Id'];
                                $UserRaceInfoList['LastUpdateRecordCount'] = $TimingRecordTable['RecordCount'];
                                //保存配置文件
                                $oRace->TimgingDataSave($RaceInfo['RaceId'],$UserRaceInfoList,$Cache);
                                echo "getdataCount3:".count($UserRaceInfoList)."<br>";

                                $UserRaceInfo['NextPoint'] = $UserRaceInfo['CurrentPoint'];
                            }
                        }
                    }
                    else
                    {
                        echo $num."-".$TimingInfo['Location']."-".($ChipTime)."-".date("Y-m-d H:i:s", $TimingInfo['ChipTime']).".".(substr($miliSec,2))."超时跳过<br>\n";
                    }
                }

			}
			$Count = count($TimingList['Record']);
            $Text.= "Sql:".$TimingList['sql']."\n";
            $Text.= "RecordCount:".$Count."\n";
			$TotalCount+=$Count;
		}
		//重新获取比赛详情
		$TeamRankList = array();
		$UserRaceTimingInfo = $oRace->GetRaceTimingOriginalInfo($RaceId,$Cache);
        echo "SavePointCount3:".count($UserRaceInfoList)."<br>";

        foreach($UserRaceTimingInfo['Total'] as $k => $v)
		{
		    $UInfo = $oRace->getUserRaceTimingOriginalInfo($RaceId, $v['RaceUserId'],$Cache);
			foreach($UserRaceInfoList['Point'] as $Point => $PointInfo)
            {
                unset($UInfo['Point'][$Point]['Credit']);
                foreach($PointInfo['UserList'] as $R => $RInfo)
                {
                    if(($RInfo['RaceUserId']==$v['RaceUserId']) && isset($RInfo['Credit']))
                    {
                        $UInfo['Point'][$Point]['Credit'] = $RInfo['Credit'];
                        foreach($RInfo['Credit'] as $P => $PInfo)
                        {
                            if(isset($UserRaceTimingInfo['Total'][$k]['Credit'][$P]))
                            {
                                $UserRaceTimingInfo['Total'][$k]['Credit'][$P]['Credit'] += $PInfo['Credit'];
                            }
                            else
                            {
                                $UserRaceTimingInfo['Total'][$k]['Credit'][$P] = array("Credit"=>$PInfo['Credit'],"CreditName"=>$PInfo['CreditName']);
                            }
                        }
                        $UInfo['Total']['Credit'] = $UserRaceTimingInfo['Total'][$k]['Credit'];
                    }
                    if(($RInfo['RaceUserId']==$v['RaceUserId']))
                    {
                        $UInfo['Point'][$Point]['Rank'] = $RInfo['Rank'];
                        $UInfo['Point'][$Point]['GroupRank'] = $RInfo['GroupRank'];
                    }
                }
            }
            foreach($UInfo['Point'] as $p => $pInfo)
            {
                foreach($UserRaceTimingInfo['Point'][$p]['UserList'] as $k2 => $v2)
                {
                    if($v2['RaceUserId'] = $v['RaceUserId'])
                    {
                        $UserRaceTimingInfo['Point'][$p]['UserList'][$k2]['PointSpeed'] = $pInfo['PointSpeed'];
                    }
                }
            }
            //保存个人当前计时点的信息
            $oRace->UserTimgingDataSave($RaceInfo['RaceId'],$v['RaceUserId'],$UInfo,0);
		    if(($v['TeamId']>0) && ($v['CurrentPosition'] == count($UserRaceTimingInfo['Point'])))
			{
			    if(isset($TeamRankList[$v['RaceGroupId']][$v['TeamId']]) && count($TeamRankList[$v['RaceGroupId']][$v['TeamId']]['UserList'])<$RaceInfo['comment']['TeamResultRank'])
				{
					$TeamRankList[$v['RaceGroupId']][$v['TeamId']]['UserList'][count($TeamRankList[$v['RaceGroupId']][$v['TeamId']]['UserList'])+1] = $v;
				}
				else
				{
					$TeamRankList[$v['RaceGroupId']][$v['TeamId']]['TeamName'] = $v['TeamName'];
					$TeamRankList[$v['RaceGroupId']][$v['TeamId']]['UserList'][1] = $v;
				}
			}
		}
		$TeamRank = array();$i=1;
		$t1 = array();$t2=array();
        foreach($TeamRankList as $GroupId => $GroupInfo)
        {
            foreach($GroupInfo as $k => $v)
            {
                if(isset($TeamRankList[$GroupId][$k]['UserList'][$RaceInfo['comment']['TeamResultRank']]))
                {
                    $TeamRank[$GroupId][$i] = $TeamRankList[$GroupId][$k]['UserList'][$RaceInfo['comment']['TeamResultRank']];
                    $t1[$GroupId][$k] = $TeamRankList[$GroupId][$k]['UserList'][$RaceInfo['comment']['TeamResultRank']]['TotalTime'];
                    $t2[$GroupId][$k] = $TeamRankList[$GroupId][$k]['UserList'][$RaceInfo['comment']['TeamResultRank']]['TotalNetTime'];
                    //根据不同的计时类型进行排序
                    if($ResultType=="gunshot")
                    {
                        array_multisort( $t1, SORT_ASC, $TeamRank[$GroupId]);
                    }
                    else
                    {
                        array_multisort( $t2, SORT_ASC, $TeamRank[$GroupId]);
                    }
                    $i++;
                }
            }
        }
        foreach($TeamRank as $GroupId => $GroupInfo)
        {
            foreach($GroupInfo as $k => $v)
            {
                if($k>0)
                {
                    //根据不同的计时类型进行排序
                    if($ResultType=="gunshot")
                    {
                        $TeamRank[$GroupId][$k]['TimeLag'] = sprintf("%0.3f",$TeamRank[$GroupId][$k]['TotalTime']- $TeamRank[$GroupId][0]['TotalTime']);
                    }
                    else
                    {
                        $TeamRank[$GroupId][$k]['NetTimeLag'] = sprintf("%0.3f",$TeamRank[$GroupId][$k]['TotalNetTime']- $TeamRank[$GroupId][0]['TotalNetTime']);
                    }
                }
                else
                {
                    //根据不同的计时类型进行排序
                    if($ResultType=="gunshot")
                    {
                        $TeamRank[$GroupId][0]['TimeLag'] = 0;
                    }
                    else
                    {
                        $TeamRank[$GroupId][0]['NetTimeLag'] = 0;
                    }
                }
            }
        }
		$UserRaceTimingInfo['Team'] = $TeamRank;
        //$UserRaceInfoList['LastId'] = $TimingInfo['Id'];
        $num++;
		//保存配置文件
        $oRace->TimgingDataSave($RaceInfo['RaceId'],$UserRaceTimingInfo,0);

		$GenEnd = microtime(true);

		$Text.= "TotalCount:".$TotalCount."\n";
		$Text.= date("Y-m-d H:i:s",$GenStart)."~~".date("Y-m-d H:i:s",$GenEnd)."\n";
		$Text.="TotalCost:".Base_Common::parthTimeLag($GenEnd-$GenStart)."\n";

        $filePath = __APP_ROOT_DIR__ . "Timing" . "/" . $RaceInfo['RaceId'] . "";
        $fileDestinationPath = __APP_ROOT_DIR__ . "Timing" . "/" . $RaceInfo['RaceId'] . "_Data";
        Base_Common::copy_dir($filePath,$fileDestinationPath);

		$filePath = __APP_ROOT_DIR__."log/Timing/";
		$fileName = date("Y-m-d",$GenStart).".log";
		//写入日志文件
		Base_Common::appendLog($filePath,$fileName,$Text);
	}
	public function getTimingRecord($TableName,$LastUpdateRecordCount,$Force)
    {
        echo "LastUpdateRecordCount:".$LastUpdateRecordCount."<br>";
        //获取需要用到的表名
        $table_to_process = Base_Widget::getDbTable($this->table);
        $table_to_process = str_replace($this->table,$TableName.$this->table,$table_to_process);
        //检查需要有可能需要重建的表名（排序后）
        $table_to_copy = $this->db->copyTable($this->table_sorted,$table_to_process."_sorted");
        //原始记录数量
        $RecordCount = $this->db->getTableRecoudCount($table_to_process);
        //排序后的记录数量
        $RecordCountSorted = $this->db->getTableRecoudCount($table_to_copy);
        //如果排序后记录数量比较少 或 上次处理数据时候的记录数量与当前不一致 或 要求强制更新
        if(($RecordCount['count'] > $RecordCountSorted['count']) || ($RecordCountSorted['count'] != $LastUpdateRecordCount) || $Force == 1)
        {
            echo "StartToRebuild";
            //事务开始
            $this->db->begin();
            //清空数据
            $truncate = $this->db->query("truncate ".$table_to_copy);
            //排序/重建数据
            $rebuild_sql = "insert into ".$table_to_copy." (chip,chiptime,chiptype,pc,reader,Antenna,MilliSecs,Location,LapRaw) select chip,chiptime,chiptype,pc,reader,Antenna,MilliSecs,Location,LapRaw from ".$table_to_process." order by ChipTime,right(MilliSecs,3)";
            $rebuild = $this->db->query($rebuild_sql);
            if($truncate && $rebuild)
            {
                //返回成功
                $this->db->commit();
                $RecordCountSorted = $this->db->getTableRecoudCount($table_to_copy);
                return array('return'=>true,'RecordCount'=>$RecordCountSorted['count'],'LastUpdateTime'=>$RecordCountSorted['LastUpdateTime']);
            }
            else
            {
                echo "fail";
                //返回失败
                $this->db->rollBack();
                return array('return'=>false,'LastUpdateTime'=>$RecordCountSorted['LastUpdateTime']);
            }
        }
        else
        {
            //返回失败
            return array('return'=>false,'LastUpdateTime'=>$RecordCountSorted['LastUpdateTime']);
        }
    }
}
