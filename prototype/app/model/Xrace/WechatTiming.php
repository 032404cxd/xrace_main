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
                        return -4;
                    }
                    else
                    {
                        //获取计时记录
                        $UserRaceInfo = $oRace->getUserRaceInfo($RaceInfo["RaceId"],$UserInfo['RaceUserId']);
                        $Pointound = 0;
                        //循环计时点
                        foreach($UserRaceInfo["Point"] as $Point => $PointInfo)
                        {
                            if($PointInfo["ChipId"] == $Timing["Location"])
                            {
                                $Pointound = 1;
                                //如果已经经过
                                if($PointInfo["inTime"]<0)
                                {
                                    return 1;
                                }
                                else
                                {
                                    break;
                                }
                            }
                        }
                        if($Pointound == 0)
                        {
                            return -5;
                        }
                        $Distance =  Base_Common::getDistance($Timing["TencentX"],$Timing["TencentY"],$PointInfo["TencentX"],$PointInfo["TencentY"]);
                        if($Distance >= 100)
                        {
                            return -6;
                        }
                    }
                }
                else
                {
                    //返回错误
                    return -1;
                }
                if($found == 1)
                {
                    //数据解包
                    $RaceInfo["RouteInfo"] = json_decode($RaceInfo["RouteInfo"],true);
                    //检测计时记录表是否存在
                    $table_timing = $this->db->createTable($this->table,$RaceInfo["RouteInfo"]["TimePrefix"]);
                    //初始化计时记录
                    $TimingLog = array("time"=>$Timing["Time"],"Location"=>$Timing["Location"],"RaceUserId"=>$UserInfo['RaceUserId'],"comment"=>json_encode(array("Position"=>array("X"=>$Timing["TencentX"],"Y"=>$Timing["TencentY"]))));
                    //加入计时记录
                    $Id = $this->insertTiming($table_timing,$TimingLog);
                    return $Id;
                }
            }
            else
            {
                //返回错误
                return -2;
            }

        }
        else
        {
            //返回错误
            return -3;
        }
    }
}
