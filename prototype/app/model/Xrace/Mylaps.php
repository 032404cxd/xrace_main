<?php
/**
 * 订单管理相关mod层
 * @author 陈晓东 <cxd032404@hotmail.com>
 */


class Xrace_Mylaps extends Base_Widget
{
	//声明所用到的表
	#protected $table = 'zs_times';
	protected $table = 'times';

	/**
	 * 获取单条记录
	 * @param integer $OrderId
	 * @param string $fields
	 * @return array
	 */
	public function getTimingData($params,$fields=array("*"))
	{
		//生成查询列
		$fields = Base_common::getSqlFields($fields);
		//获取需要用到的表名
		$table_to_process = Base_Widget::getDbTable($this->table);
		$table_to_process = str_replace($this->table,$params['prefix'].$this->table,$table_to_process);
		//获得芯片ID
		$whereChip = isset($params['Chip'])?" Chip = '".$params['Chip']."' ":"";
		$whereChipList = isset($params['ChipList'])?" Chip in (".$params['ChipList'].") ":"";
		$Limit = isset($params['page'])?(" limit ".($params['page']-1)*$params['pageSize'].",".$params['pageSize']):"";
		//所有查询条件置入数组
		$whereCondition = array($whereChip,$whereChipList);
		//生成条件列
		$where = Base_common::getSqlWhere($whereCondition);
		$sql = "SELECT $fields FROM $table_to_process where 1 ".$where." order by Chip,ChipTime,Millisecs asc".$Limit;
		$return = $this->db->getAll($sql);
		return $return;
	}
	//根据比赛ID生成该场比赛的MYLAPS计时数据
	public function genMylapsTimingInfo($RaceId)
	{
		$TotalCount = 0;
		$GenStart = microtime(true);
		$oUser = new Xrace_User();
		$oRace = new Xrace_Race();
		//获取比赛信息
		$RaceInfo = $oRace->getRace($RaceId);
		//解包压缩的数据
		$RaceInfo['comment'] = json_decode($RaceInfo['comment'],true);
		//预存比赛的开始和结束时间
		$RaceStartTime = strtotime($RaceInfo['StartTime']) + $RaceInfo['comment']['RaceStartMicro']/1000;
		$RaceEndTime = strtotime($RaceInfo['EndTime']);
		//解包路径相关的信息
		$RaceInfo['RouteInfo'] = json_decode($RaceInfo['RouteInfo'],true);
		//初始化单个计时点的最大等待时间（超过这个时间才认为是新的一次进入）
		$RaceInfo['RouteInfo']['MylapsTolaranceTime'] = isset($RaceInfo['RouteInfo']['MylapsTolaranceTime'])?$RaceInfo['RouteInfo']['MylapsTolaranceTime']:30;
		//初始化计时成绩计算的方式（发枪时刻/第一次经过起始点）
		$ResultType = ((isset($RaceInfo['RouteInfo']['RaceTimingResultType']) && ($RaceInfo['RouteInfo']['RaceTimingResultType']=="gunshot"))||!isset($RaceInfo['RouteInfo']['RaceTimingResultType']))?"gunshot":"net";
		echo "result:".$oRace->getRaceTimingResultType($ResultType)."\n";
		echo "ToloranceTime:".$RaceInfo['RouteInfo']['MylapsTolaranceTime']."\n";
		//获取选手和车队名单
		$RaceUserList = $oUser->getRaceUserListByRace($RaceId, 0, 0);
		//初始化空的芯片列表
		$ChipList = array();
		//初始化空的用户列表
		$UserList = array();
		//循环报名记录
		foreach ($RaceUserList['RaceUserList'] as $ApplyId => $ApplyInfo)
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
		echo "比赛时间：".$RaceInfo['StartTime'].".".sprintf("%03d",isset($RaceInfo['comment']['RaceStartMicro'])?$RaceInfo['comment']['RaceStartMicro']:0)."~".$RaceInfo['EndTime']."\n";
		echo "芯片列表：".implode(",",$ChipList)."\n";
		//重新生成选手的mylaps排名数据
		$oRace->genRaceLogToText($RaceId);
		//初始化页码
		$i = 1;
		//单页记录数量
		$pageSize = 1000;
		//默认第一次有获取到
		$Count = $pageSize;
		//初始化当前芯片（选手）
		$currentChip = "";
		while ($Count == $pageSize)
		{
			//拼接获取计时数据的参数，注意芯片列表为空时的数据拼接
			$params = array('prefix'=>$RaceInfo['RouteInfo']['MylapsPrefix'],'page'=>$i, 'pageSize'=>$pageSize, 'ChipList'=>count($ChipList) ? implode(",",$ChipList):"0");
			//获取计时数据
			$TimingList = $this->getTimingData($params);
			//依次循环计时数据
			foreach ($TimingList as $Key => $TimingInfo)
			{
				//如果当前芯片 和 循环到的计时数据不同 （说明已经结束了上一个选手的循环）
				if ($currentChip != $TimingInfo['Chip'])
				{
					$num=1;
					//将当前位置置为循环到的计时点
					$currentChip = $TimingInfo['Chip'];
					//调试信息
					if(isset($UserList[$TimingInfo['Chip']]))
					{
						echo "芯片:".$currentChip . ",用户ID:".$UserList[$TimingInfo['Chip']]['UserId'].",姓名:".$UserList[$TimingInfo['Chip']]['Name'] .",队伍:" . $UserList[$TimingInfo['Chip']]['RaceTeamName'] ."-号码:" . $UserList[$TimingInfo['Chip']]['BIB'].  "\n";
					}
					else
					{
						echo "芯片:".$currentChip . ",用户 Undifined:". "\n";
					}
				}
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
				//如果时间在比赛的开始时间和结束时间之内
				if ($ChipTime >= $RaceStartTime && $ChipTime <= $RaceEndTime)
				{
					echo $num."-".$TimingInfo['Location']."-".($ChipTime)."-".date("Y-m-d H:i:s", $TimingInfo['ChipTime']).".".(substr($miliSec,2))."\n";
					//获取选手的比赛信息（计时）
					$UserRaceInfo = $oRace->getUserRaceInfo($RaceId, $UserList[$TimingInfo['Chip']]['UserId']);
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
							$UserRaceInfo['Point'][$i]['inTime'] = sprintf("%0.3f", $ChipTime);
							//保存个人当前计时点的信息
							$filePath = __APP_ROOT_DIR__ . "Timing" . "/" . $RaceInfo['RaceId'] . "/" . "UserList" . "/";
							$fileName = $UserList[$TimingInfo['Chip']]['UserId'] . ".php";
							//生成配置文件
							Base_Common::rebuildConfig($filePath, $fileName, $UserRaceInfo, "Timing");

							//获取所有选手的比赛信息（计时）
							$UserRaceInfoList = $oRace->getUserRaceInfoList($RaceId);
							//将当前计时点最小的过线记录保存
							$UserRaceInfoList['Point'][$i]['inTime'] = $UserRaceInfoList['Point'][$i]['inTime'] == 0 ? sprintf("%0.3f", $ChipTime) : sprintf("%0.3f", min($UserRaceInfoList['Point'][$i]['inTime'], $ChipTime));
							//新增当前点的过线记录
							$UserRaceInfoList['Point'][$i]['UserList'][count($UserRaceInfoList['Point'][$i]['UserList'])] = array("TotalTime" => sprintf("%0.3f",($ChipTime - $RaceStartTime)),"TotalNetTime"=>0, "Name" => $UserList[$TimingInfo['Chip']]['Name'], "BIB" => $UserList[$TimingInfo['Chip']]['BIB'], "inTime" => sprintf("%0.3f",$ChipTime), 'UserId' => $UserList[$TimingInfo['Chip']]['UserId'],'RaceTeamName'=>$UserList[$TimingInfo['Chip']]['RaceTeamName']);

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
							//如果现在已有总排名数组
							if (isset($UserRaceInfoList['Total']) && count($UserRaceInfoList['Total']))
							{
								//初始设定为未找到
								$found = 0;
								//循环已有的排名数据
								foreach ($UserRaceInfoList['Total'] as $k => $v)
								{
									//依次比对现在的用户ID，如果找到了，则更新
									if ($v['UserId'] == $UserList[$TimingInfo['Chip']]['UserId'])
									{
										$UserRaceInfoList['Total'][$k] = array("CurrentPosition" => 1,"CurrentPositionName" => $UserRaceInfoList['Point'][1]['TName'], "TotalTime" => sprintf("%0.3f",($ChipTime - $RaceStartTime)),"TotalNetTime"=>0, "Name" => $UserList[$TimingInfo['Chip']]['Name'], "BIB" => $UserList[$TimingInfo['Chip']]['BIB'], "inTime" => sprintf("%0.3f",$ChipTime), 'UserId' => $UserList[$TimingInfo['Chip']]['UserId'],'RaceTeamName'=>$UserList[$TimingInfo['Chip']]['RaceTeamName']);
										$found = 1;
										break;
									}
								}
								//如果未找到，则新增
								if ($found == 0)
								{
									$UserRaceInfoList['Total'][count($UserRaceInfoList['Total'])] = array("CurrentPosition" => 1,"CurrentPositionName" => $UserRaceInfoList['Point'][1]['TName'],"TotalTime" => sprintf("%0.3f",($ChipTime - $RaceStartTime)),"TotalNetTime"=>0, "Name" => $UserList[$TimingInfo['Chip']]['Name'], "BIB" => $UserList[$TimingInfo['Chip']]['BIB'], "inTime" => sprintf("%0.3f",$ChipTime), 'UserId' => $UserList[$TimingInfo['Chip']]['UserId'],'RaceTeamName'=>$UserList[$TimingInfo['Chip']]['RaceTeamName']);
								}
							}
							//新建排名数据
							else
							{
								$UserRaceInfoList['Total'][0] = array("CurrentPosition" => 1,"CurrentPositionName" => $UserRaceInfoList['Point'][1]['TName'], "TotalTime" => sprintf("%0.3f",($ChipTime - $RaceStartTime)),"TotalNetTime"=>0, "Name" => $UserList[$TimingInfo['Chip']]['Name'], "BIB" => $UserList[$TimingInfo['Chip']]['BIB'], "inTime" => sprintf("%0.3f",$ChipTime), 'UserId' => $UserList[$TimingInfo['Chip']]['UserId'],'RaceTeamName'=>$UserList[$TimingInfo['Chip']]['RaceTeamName']);
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
							//保存数据
							$filePath = __APP_ROOT_DIR__ . "Timing" . "/" . $RaceInfo['RaceId'] . "/";
							$fileName = "Total" . ".php";
							//生成配置文件
							Base_Common::rebuildConfig($filePath, $fileName, $UserRaceInfoList, "Timing");
							$num++;
						}
						else
						{
							//ToDo 首个点未匹配上
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
							if (isset($UserRaceInfo['Point'][$UserRaceInfo['CurrentPoint']]) && isset($UserRaceInfo['Point'][$UserRaceInfo['CurrentPoint']+1]))
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
								echo "Reach The Buttom<br>";
								//循环结束
								break;
							}
						}
						while
						(
							//(如果芯片的位置不符合 或 （位置符合 且 已经记录的过线时间为空） 且 向上累加)
							(($CurrentPointInfo['ChipId'] != $TimingInfo['Location']) || (($CurrentPointInfo['ChipId'] == $TimingInfo['Location']) && ($CurrentPointInfo['inTime'] != ""))) && ($UserRaceInfo['CurrentPoint']++)
						);
						//echo "查找到位置：".$UserRaceInfo['CurrentPoint']."<br>";
						//如果当前点信息内有包含芯片ID(位置合法 且 当前点位置和循环查找之前的不相同（确认移位）)
						if ($CurrentPointInfo['ChipId'] && $c != $UserRaceInfo['CurrentPoint'])
						{
							$UserRaceInfo['Point'][$UserRaceInfo['CurrentPoint']]['inTime'] = sprintf("%0.3f",$ChipTime);
							$filePath = __APP_ROOT_DIR__ . "Timing" . "/" . $RaceInfo['RaceId'] . "/" . "UserList" . "/";
							$fileName = $UserList[$TimingInfo['Chip']]['UserId'] . ".php";
							//生成配置文件
							Base_Common::rebuildConfig($filePath, $fileName, $UserRaceInfo, "Timing");
							//如果原过线时间有数值 则获取与现过线时间的较小值，否则就用现过线时间
							$UserRaceInfoList['Point'][$UserRaceInfo['CurrentPoint']]['inTime'] = $UserRaceInfoList['Point'][$UserRaceInfo['CurrentPoint']]['inTime'] == 0 ? sprintf("%0.3f",$ChipTime) : min(sprintf("%0.3f", $UserRaceInfoList['Point'][$UserRaceInfo['CurrentPoint']]['inTime']), sprintf("%0.3f",$ChipTime));
							//如果当前点的过线选手列表存在 且 已经有选手过线
							if (isset($UserRaceInfoList['Point'][$UserRaceInfo['CurrentPoint']]['UserList']) && count($UserRaceInfoList['Point'][$UserRaceInfo['CurrentPoint']]['UserList']))
							{
								$UserRaceInfoList['Point'][$UserRaceInfo['CurrentPoint']]['UserList'][count($UserRaceInfoList['Point'][$UserRaceInfo['CurrentPoint']]['UserList'])] = array("TotalTime" => sprintf("%0.3f",($ChipTime - $RaceStartTime)),"TotalNetTime" => sprintf("%0.3f",($ChipTime - $StartTime)), "Name" => $UserList[$TimingInfo['Chip']]['Name'], "BIB" => $UserList[$TimingInfo['Chip']]['BIB'], "inTime" => sprintf("%0.3f",$ChipTime), 'UserId' => $UserList[$TimingInfo['Chip']]['UserId'],'RaceTeamName'=>$UserList[$TimingInfo['Chip']]['RaceTeamName']);
							}
							else
							{
								$UserRaceInfoList['Point'][$UserRaceInfo['CurrentPoint']]['UserList'][0] = array("TotalTime" => sprintf("%0.3f",($ChipTime - $RaceStartTime)),"TotalNetTime" => sprintf("%0.3f",($ChipTime - $StartTime)), "Name" => $UserList[$TimingInfo['Chip']]['Name'], "BIB" => $UserList[$TimingInfo['Chip']]['BIB'], "inTime" => sprintf("%0.3f",$ChipTime), 'UserId' => $UserList[$TimingInfo['Chip']]['UserId'],'RaceTeamName'=>$UserList[$TimingInfo['Chip']]['RaceTeamName']);
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
							//循环总成绩数组
							if (isset($UserRaceInfoList['Total']) && count($UserRaceInfoList['Total']))
							{
								//初始设定为未找到
								$found = 0;
								//循环已有的排名数据
								foreach ($UserRaceInfoList['Total'] as $k => $v)
								{
									//依次比对现在的用户ID，如果找到了，则更新
									if ($v['UserId'] == $UserList[$TimingInfo['Chip']]['UserId'])
									{
										$UserRaceInfoList['Total'][$k] = array("CurrentPosition" => $UserRaceInfo['CurrentPoint'], "CurrentPositionName" => $UserRaceInfoList['Point'][$UserRaceInfo['CurrentPoint']]['TName'],"TotalTime" => sprintf("%0.3f",($ChipTime - $RaceStartTime)),"TotalNetTime" => sprintf("%0.3f",($ChipTime - $StartTime)), "Name" => $UserList[$TimingInfo['Chip']]['Name'], "BIB" => $UserList[$TimingInfo['Chip']]['BIB'], "inTime" => sprintf("%0.3f",$ChipTime), 'UserId' => $UserList[$TimingInfo['Chip']]['UserId'],'RaceTeamName'=>$UserList[$TimingInfo['Chip']]['RaceTeamName']);
										$found = 1;
										break;
									}
								}
								//如果未找到，则新增
								if ($found == 0)
								{
									$UserRaceInfoList['Total'][count($UserRaceInfoList['Total'])] = array("CurrentPosition" => $UserRaceInfo['CurrentPoint'], "CurrentPositionName" => $UserRaceInfoList['Point'][$UserRaceInfo['CurrentPoint']]['TName'],"TotalTime" => sprintf("%0.3f",($ChipTime - $RaceStartTime)),"TotalNetTime" => sprintf("%0.3f",($ChipTime - $StartTime)), "Name" => $UserList[$TimingInfo['Chip']]['Name'], "BIB" => $UserList[$TimingInfo['Chip']]['BIB'], "inTime" => $TimingInfo['ChipTime'] + $miliSec, 'UserId' => $UserList[$TimingInfo['Chip']]['UserId'],'RaceTeamName'=>$UserList[$TimingInfo['Chip']]['RaceTeamName']);
								}
							}
							//新建排名数据
							else
							{
								$UserRaceInfoList['Total'][0] = array("CurrentPosition" => $UserRaceInfo['CurrentPoint'], "CurrentPositionName" => $UserRaceInfoList['Point'][$UserRaceInfo['CurrentPoint']]['TName'],"TotalTime" => sprintf("%0.3f",($ChipTime - $RaceStartTime)),"TotalNetTime" => sprintf("%0.3f",($ChipTime - $StartTime)), "Name" => $UserList[$TimingInfo['Chip']]['Name'], "BIB" => $UserList[$TimingInfo['Chip']]['BIB'], "inTime" => $TimingInfo['ChipTime'] + $miliSec, 'UserId' => $UserList[$TimingInfo['Chip']]['UserId'],'RaceTeamName'=>$UserList[$TimingInfo['Chip']]['RaceTeamName']);
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
							$filePath = __APP_ROOT_DIR__ . "Timing" . "/" . $RaceInfo['RaceId'] . "/";
							$fileName = "Total" . ".php";
							$num++;
							//生成配置文件
							Base_Common::rebuildConfig($filePath, $fileName, $UserRaceInfoList, "Timing");
							$UserRaceInfo['NextPoint'] = $UserRaceInfo['CurrentPoint'];
						}
					}
				}
				else
				{
					echo $num."-".$TimingInfo['Location']."-".($ChipTime)."-".date("Y-m-d H:i:s", $TimingInfo['ChipTime']).".".(substr($miliSec,2))."超时跳过\n";
				}
			}
			$Count = count($TimingList);
			echo "get Count:",$Count."\n";
			$TotalCount+=$Count;
			$i++;
		}
		$GenEnd = microtime(true);
		$Text = "RaceId:".$RaceId."\n";
		$Text.= "TotalCount:".$TotalCount."\n";
		$Text.= date("Y-m-d H:i:s",$GenStart)."~~".date("Y-m-d H:i:s",$GenEnd)."\n";
		$Text.="TotalCost:".Base_Common::parthTimeLag($GenEnd-$GenStart)."\n";

		$filePath = __APP_ROOT_DIR__."log/Timing/";
		$fileName = date("Y-m-d",$GenStart).".log";
		//写入日志文件
		Base_Common::appendLog($filePath,$fileName,$Text);
	}
}
