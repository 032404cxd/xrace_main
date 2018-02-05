<?php
/**
 * 微信打卡计时相关mod层
 * @author 陈晓东 <cxd032404@hotmail.com>
 */


class Xrace_WechatTiming extends Base_Widget
{
	//声明所用到的表
	protected $table = 'wechat_times';
    //新增单个计时记录
    public function insertTiming($table,array $bind)
    {
        return $this->db->insert($table, $bind);
    }
    //新增一条计时记录
    public function insertTimingLog(array $Timing)
    {
        $oRace = new Xrace_Race();
        //获取比赛信息
        $RaceInfo = $oRace->getRace($Timing['RaceId']);
        //如果获取到比赛信息
        if(isset($RaceInfo['RaceId']))
        {
            //获取成绩总表
            $UserRaceInfo = $oRace->GetUserRaceTimingInfo($RaceInfo['RaceId']);
            //如果未获取到
            if(empty($UserRaceInfo))
            {
                //重建计时数据
                $oRace->genRaceLogToText($RaceInfo['RaceId']);
            }
            $oUser = new Xrace_UserInfo();
            //获取用户信息
            $UserInfo = $oUser->getUserByColumn("WechatId",$Timing['OpenId']);
            //如果获取到用户信息
            if(isset($UserInfo['UserId']))
            {
                //如果有关联的比赛用户信息
                if($UserInfo['RaceUserId']>0)
                {
                    //获取报名记录
                    $RaceUserList = $oRace->getRaceUserListByFile($RaceInfo["RaceId"]);
                    //初始化标识
                    $found = 0;
                    //循环报名记录
                    foreach($RaceUserList['RaceUserList'] as $key => $RaceApplyLog)
                    {
                        //如果找到
                        if($RaceApplyLog["RaceUserId"] == $UserInfo['RaceUserId'])
                        {
                            $found = 1;
                            break;
                        }
                    }
                    //如果最终没找到
                    if($found == 0)
                    {
                        return array("return"=>-4);
                    }
                    else
                    {
                        //获取计时记录
                        $UserRaceInfo = $oRace->getUserRaceInfoByFile($RaceInfo["RaceId"],$UserInfo['RaceUserId']);
                        $Pointound = 0;
                        //循环计时点
                        foreach($UserRaceInfo["Point"] as $Point => $PointInfo)
                        {
                            if($PointInfo["ChipId"] == $Timing["Location"])
                            {
                                $Pointound = 1;
                                //如果已经经过
                                if($PointInfo["inTime"]>0)
                                {
                                    if(isset($UserRaceInfo["Point"][$Point+1]))
                                    {
                                        $NextPoint = $UserRaceInfo["Point"][$Point+1];
                                        $NextPointInfo = array("TName"=>$NextPoint["TName"],"TencentX"=>$NextPoint["TencentX"],"TencentY"=>$NextPoint["TencentY"],"ToPrevious"=>$NextPoint["ToPrevious"]);
                                        //无需重复打卡
                                        return array("return"=>-7,"NextPoint"=>$NextPointInfo);
                                    }
                                    else
                                    {
                                        //无需重复打卡
                                        return array("return"=>-7);
                                    }
                                }
                                else
                                {
                                    break;
                                }
                            }
                        }
                        if($Pointound == 0)
                        {
                            return array("return"=>-5);
                        }
                        $Distance =  Base_Common::getDistance($Timing["TencentX"],$Timing["TencentY"],$PointInfo["TencentX"],$PointInfo["TencentY"]);
                        if($Distance >= 100)
                        {
                            return array("return"=>-6,"Distance"=>$Distance);
                        }
                    }
                }
                else
                {
                    //返回错误
                    return array("return"=>-1);
                }
                if($found == 1 && $Pointound == 1)
                {
                    //数据解包
                    $RaceInfo["RouteInfo"] = json_decode($RaceInfo["RouteInfo"],true);
                    //获取需要用到的表名
                    $table_to_process = Base_Widget::getDbTable($this->table);
                    $table_to_process = str_replace($this->table,$RaceInfo["RouteInfo"]["TimePrefix"]."_".$this->table,$table_to_process);
                    //$table_timing = $this->db->createTable($this->table,$RaceInfo["RouteInfo"]["TimePrefix"]);
                    $table_timing = $this->db->copyTable($this->table,$table_to_process);
                    //初始化计时记录
                    $TimingLog = array("time"=>$Timing["Time"],"Location"=>$Timing["Location"],"RaceUserId"=>$UserInfo['RaceUserId'],"comment"=>json_encode(array("Position"=>array("X"=>$Timing["TencentX"],"Y"=>$Timing["TencentY"]))));
                    //加入计时记录
                    $Id = $this->insertTiming($table_timing,$TimingLog);
                    $NextPoint = $UserRaceInfo["Point"][$Point+1];
                    $NextPointInfo = array("TName"=>$NextPoint["TName"],"TencentX"=>$NextPoint["TencentX"],"TencentY"=>$NextPoint["TencentY"],"ToPrevious"=>$NextPoint["ToPrevious"]);
                    return array("return"=>1,"Distance"=>$Distance,"NextPoint"=>$NextPointInfo);
                }
            }
            else
            {
                //返回错误
                return array("return"=>-2);
            }

        }
        else
        {
            //返回错误
            return array("return"=>-3);
        }
    }
    public function getTimingData($params,$fields=array("*"))
    {
        //生成查询列
        $fields = Base_common::getSqlFields($fields);
        //获取需要用到的表名
        $table_to_process = Base_Widget::getDbTable($this->table);
        if($params['sorted']!=1)
        {
            $table_to_process = str_replace($this->table,$params['prefix'].$this->table,$table_to_process);
        }
        else
        {
            $table_to_process = str_replace($this->table,$params['prefix']."_".$this->table,$table_to_process);

        }
        //获得芯片ID
        $whereUser = isset($params['RaceUser'])?" RaceUserId = '".$params['RaceUser']."' ":"";
        if(isset($params['RaceUserList']))
        {
            if($params['RaceUserList'] == "-1")
            {
                $whereUserList = "0";
            }
            else
            {
                $whereUserList = " RaceUserId in (".$params['RaceUserList'].") ";
            }
        }

        $whereStart = isset($params['LastId'])?" Id >".$params['LastId']." ":"";
        $whereStartTime = isset($params['StartTime'])?" ChipTime >'".$params['StartTime']."' ":"";
        $whereEndTime = isset($params['EndTime'])?" ChipTime <='".$params['EndTime']."' ":"";
        $Limit = " limit 0,".$params['pageSize'];
        //所有查询条件置入数组
        $whereCondition = array($whereUser,$whereUserList,$whereStart,$whereStartTime,$whereEndTime);
        //生成条件列
        $where = Base_common::getSqlWhere($whereCondition);
        $sql = "SELECT $fields FROM $table_to_process where 1 ".$where." order by Id ".($params['revert']==1?"desc":"asc").$Limit;
        echo $sql."<br>";
        $return = $this->db->getAll($sql);
        if($params['getCount']==1)
        {
            $RecordCount = $this->getTimingDataCount($params);
            return array("Record"=>$return,"RecordCount"=>$RecordCount['RecordCount'],"sql"=>$sql);
        }
        else
        {
            return array("Record"=>$return,"sql"=>$sql);
        }
    }
}
